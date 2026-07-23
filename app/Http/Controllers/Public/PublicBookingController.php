<?php

namespace App\Http\Controllers\Public;

use App\Services\AppointmentService;
use App\Services\BarberService;
use App\Services\ServiceCatalogService;
use App\Models\Setting;
use App\Mail\AppointmentCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class PublicBookingController extends Controller
{
    protected $appointmentService;
    protected $barberService;
    protected $serviceCatalog;

    public function __construct(
        AppointmentService $appointmentService,
        BarberService $barberService,
        ServiceCatalogService $serviceCatalog
    ) {
        $this->appointmentService = $appointmentService;
        $this->barberService = $barberService;
        $this->serviceCatalog = $serviceCatalog;
    }

    public function index()
    {
        $barbers = $this->barberService->getAllBarbers()->where('ativo', true);
        $services = $this->serviceCatalog->getAllServices()->where('ativo', true)->where('is_admin_only', false);
        
        $settings = Setting::all()->pluck('value', 'key');
        
        // Fila Ao Vivo: Atendimentos de hoje (agendado ou em_atendimento)
        $today = date('Y-m-d');
        $currentTime = date('H:i');
        
        $proximosAtendimentos = \App\Models\Appointment::where('data', $today)
            ->whereIn('status', ['agendado', 'em_atendimento'])
            ->where('hora', '>=', $currentTime)
            ->orderBy('hora', 'asc')
            ->get();
        
        return view('welcome', compact('barbers', 'services', 'settings', 'proximosAtendimentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_nome' => 'required|string|max:255',
            'cliente_whatsapp' => 'required|string|max:20',
            'barber_id' => 'required|exists:barbers,id',
            'service_id' => 'required|exists:services,id',
            'data' => 'required|date|after_or_equal:today',
            'hora' => 'required',
        ]);

        $exists = \App\Models\Appointment::where('barber_id', $request->barber_id)
            ->where('data', $request->data)
            ->where('hora', $request->hora)
            ->whereIn('status', ['agendado', 'concluido'])
            ->exists();
            
        if ($exists) {
            return redirect()->back()->withErrors(['hora' => 'Este horário não está mais disponível. Por favor, escolha outro.'])->withInput();
        }

        // Verificar bloqueios administrativos
        $isBlocked = \App\Models\BlockedTime::where('date', $request->data)
            ->where(function ($query) use ($request) {
                $query->whereNull('barber_id')->orWhere('barber_id', $request->barber_id);
            })
            ->where('start_time', '<=', $request->hora)
            ->where('end_time', '>', $request->hora)
            ->exists();

        if ($isBlocked) {
            return redirect()->back()->withErrors(['hora' => 'Este horário encontra-se bloqueado pela administração.'])->withInput();
        }

        $data = $request->all();
        // Garante que todo agendamento público comece com status 'agendado'
        $data['status'] = 'agendado';

        $this->appointmentService->createAppointment($data);

        // Send email via Laravel Mail (using configured SMTP in .env)
        try {
            $servico = $this->serviceCatalog->getService($data['service_id']);
            $data['nomeServico'] = $servico ? $servico->nome : 'Não informado';
            $data['valorServico'] = $servico ? 'R$ ' . number_format($servico->valor, 2, ',', '.') : 'Não informado';
            $data['dataFormatada'] = date('d/m/Y', strtotime($data['data']));

            // Send notification to the company email
            $companyEmail = \App\Models\Setting::where('key', 'company_email')->value('value') ?? env('MAIL_FROM_ADDRESS', 'admin@barberflow.com');
            
            Mail::to($companyEmail)->send(new AppointmentCreatedMail($data));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar e-mail via Laravel Mail: ' . $e->getMessage());
        }

        return redirect()->route('public.booking')->with('success', 'Uhuul! Seu agendamento foi realizado com sucesso. Esperamos por você!');
    }

    public function getAvailableTimes(Request $request)
    {
        $date = $request->get('date');
        $barberId = $request->get('barber_id');

        if (!$date || !$barberId) {
            return response()->json([]);
        }

        // Configuração de horários (08:00 às 19:00, 25 min)
        $startTime = strtotime('08:00');
        $endTime = strtotime('19:00');
        $interval = 25 * 60; // 25 minutos em segundos

        $allTimes = [];
        for ($time = $startTime; $time <= $endTime; $time += $interval) {
            $allTimes[] = date('H:i', $time);
        }

        // Buscar horários já ocupados (agendamentos reais)
        $bookedTimes = \App\Models\Appointment::where('barber_id', $barberId)
            ->where('data', $date)
            ->whereIn('status', ['agendado', 'concluido'])
            ->pluck('hora')
            ->map(function ($hora) {
                return date('H:i', strtotime($hora));
            })
            ->toArray();

        // Buscar bloqueios configurados no painel
        $blocks = \App\Models\BlockedTime::where('date', $date)
            ->where(function ($query) use ($barberId) {
                $query->whereNull('barber_id')->orWhere('barber_id', $barberId);
            })->get();

        // Filtrar horários disponíveis
        $isToday = $date === date('Y-m-d');
        $currentTime = date('H:i');

        $availableTimes = array_filter($allTimes, function ($time) use ($bookedTimes, $isToday, $currentTime, $blocks) {
            // Regra 1: Se já passou do horário de hoje
            if ($isToday && $time <= $currentTime) {
                return false;
            }
            // Regra 2: Se já existe agendamento
            if (in_array($time, $bookedTimes)) {
                return false;
            }
            // Regra 3: Se cruza com algum bloqueio administrativo
            foreach ($blocks as $block) {
                // Compara o $time com o intervalo bloqueado. 
                // Ex: Se o time é 12:00, e o block é 12:00 às 13:00, o 12:00 está bloqueado.
                // Usamos substr para remover os segundos se houver
                $blockStart = substr($block->start_time, 0, 5);
                $blockEnd = substr($block->end_time, 0, 5);
                
                if ($time >= $blockStart && $time < $blockEnd) {
                    return false; // Bloqueado
                }
            }
            
            return true;
        });

        return response()->json(array_values($availableTimes));
    }

    public function getLiveQueue()
    {
        $today = date('Y-m-d');
        $currentTime = date('H:i');
        
        $proximosAtendimentos = \App\Models\Appointment::with('barber')
            ->where('data', $today)
            ->whereIn('status', ['agendado', 'em_atendimento'])
            ->where('hora', '>=', $currentTime)
            ->orderBy('hora', 'asc')
            ->get()
            ->map(function ($apt) {
                return [
                    'cliente_nome' => explode(' ', $apt->cliente_nome)[0],
                    'barber_nome' => $apt->barber->nome,
                    'hora' => \Carbon\Carbon::parse($apt->hora)->format('H:i')
                ];
            });

        return response()->json($proximosAtendimentos);
    }
}
