<?php

namespace App\Http\Controllers\Appointments;

use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use App\Services\BarberService;
use App\Services\ServiceCatalogService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $appointmentService;
    protected $barberService;
    protected $serviceCatalog;
    protected $financialService;

    public function __construct(
        AppointmentService $appointmentService,
        BarberService $barberService,
        ServiceCatalogService $serviceCatalog,
        \App\Services\FinancialService $financialService
    ) {
        $this->appointmentService = $appointmentService;
        $this->barberService = $barberService;
        $this->serviceCatalog = $serviceCatalog;
        $this->financialService = $financialService;
    }

    public function index()
    {
        return view('appointments.index');
    }

    public function apiEvents()
    {
        $barberId = null;
        if (auth()->check() && auth()->user()->isEmployee()) {
            $barberId = auth()->user()->barber_id;
        }
        return response()->json($this->appointmentService->getEventsForCalendar($barberId));
    }

    public function create()
    {
        $barbers = $this->barberService->getAllBarbers()->where('ativo', true);
        $services = $this->serviceCatalog->getAllServices()->where('ativo', true);
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            $services = $services->where('is_admin_only', false);
        }
        return view('appointments.create', compact('barbers', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_nome' => 'required|string|max:255',
            'cliente_whatsapp' => 'required|string|max:20',
            'barber_id' => 'required|exists:barbers,id',
            'service_id' => 'required|exists:services,id',
            'data' => 'required|date',
            'hora' => 'required',
        ]);

        $this->appointmentService->createAppointment($request->all());

        return redirect()->route('appointments.index')->with('success', 'Agendamento criado com sucesso!');
    }

    public function edit($id)
    {
        $appointment = $this->appointmentService->getAppointment($id);
        
        // Se for funcionário, não pode editar agendamento de outro barbeiro
        if (auth()->user()->isEmployee() && $appointment->barber_id != auth()->user()->barber_id) {
            abort(403, 'Acesso negado');
        }

        $barbers = $this->barberService->getAllBarbers()->where('ativo', true);
        $services = $this->serviceCatalog->getAllServices()->where('ativo', true);
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            $services = $services->where('is_admin_only', false);
        }
        
        return view('appointments.edit', compact('appointment', 'barbers', 'services'));
    }

    public function update(Request $request, $id)
    {
        $appointment = $this->appointmentService->getAppointment($id);
        if (auth()->user()->isEmployee() && $appointment->barber_id != auth()->user()->barber_id) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'cliente_nome' => 'required|string|max:255',
            'cliente_whatsapp' => 'required|string|max:20',
            'barber_id' => 'required|exists:barbers,id',
            'service_id' => 'required|exists:services,id',
            'data' => 'required|date',
            'hora' => 'required',
            'status' => 'required|in:agendado,concluido,cancelado,nao_compareceu'
        ]);

        $this->appointmentService->updateAppointment($id, $request->all());

        return redirect()->route('appointments.index')->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function destroy($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('admin');
        
        $this->appointmentService->deleteAppointment($id);
        return redirect()->route('appointments.index')->with('success', 'Agendamento removido.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:agendado,em_atendimento,concluido,cancelado,remarcado'
        ]);

        $appointment = $this->appointmentService->getAppointment($id);
        if (auth()->user()->isEmployee() && $appointment->barber_id != auth()->user()->barber_id) {
            return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
        }

        $oldStatus = $appointment->status;
        $appointment->status = $request->status;
        $appointment->save();

        // Se o status mudou para concluído, gera a entrada financeira automaticamente
        if ($oldStatus !== 'concluido' && $request->status === 'concluido') {
            $desc = "Atendimento: {$appointment->service->nome} - {$appointment->cliente_nome}";
            
            // Verifica se já existe uma transação idêntica (mesma descrição, valor e data) para evitar duplicatas em cliques rápidos ou toggles
            $exists = $this->financialService->getAllTransactions()
                ->where('descricao', $desc)
                ->where('data', date('Y-m-d'))
                ->where('valor', (string)$appointment->service->valor)
                ->first();

            if (!$exists) {
                $this->financialService->createTransaction([
                    'tipo' => 'entrada',
                    'descricao' => $desc,
                    'valor' => (string)$appointment->service->valor,
                    'data' => date('Y-m-d')
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!'
        ]);
    }
}
