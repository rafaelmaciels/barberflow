<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use App\Services\FinancialService;
use App\Services\BarberService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected $appointmentService;
    protected $financialService;
    protected $barberService;

    public function __construct(
        AppointmentService $appointmentService,
        FinancialService $financialService,
        BarberService $barberService
    ) {
        $this->appointmentService = $appointmentService;
        $this->financialService = $financialService;
        $this->barberService = $barberService;
    }

    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        // 1. Dados de Agendamentos (Hoje)
        $today = date('Y-m-d');
        $allAppointments = $this->appointmentService->getAllAppointments();
        $appointmentsToday = $allAppointments->where('data', $today);
        $totalAgendamentosHoje = $appointmentsToday->count();
        $agendamentosConcluidos = $appointmentsToday->where('status', 'concluido')->count();

        // 2. Dados Financeiros (Mês Atual)
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        $transactions = $this->financialService->getAllTransactions()
            ->filter(function($trans) use ($currentMonth, $currentYear) {
                return $trans->data->format('m') === $currentMonth && 
                       $trans->data->format('Y') === $currentYear;
            });
            
        $receitaMensal = $transactions->where('tipo', 'entrada')->sum('valor');
        $despesaMensal = $transactions->where('tipo', 'saida')->sum('valor');
        $lucroMensal = $receitaMensal - $despesaMensal;

        // 3. Dados de Profissionais
        $totalBarbeirosAtivos = $this->barberService->getAllBarbers()->where('ativo', true)->count();

        // 4. Últimos 5 agendamentos gerais para exibir na tabela
        $recentAppointments = $allAppointments->sortByDesc('created_at')->take(5);

        // 5. Configuração do YouTube
        $youtubeLink = Setting::where('key', 'youtube_queue_video_raw')->value('value');

        return view('dashboard.index', compact(
            'totalAgendamentosHoje',
            'agendamentosConcluidos',
            'receitaMensal',
            'despesaMensal',
            'lucroMensal',
            'totalBarbeirosAtivos',
            'recentAppointments',
            'youtubeLink'
        ));
    }

    public function saveYoutubeLink(Request $request)
    {
        $request->validate([
            'youtube_link' => 'nullable|url'
        ]);

        $url = $request->youtube_link;
        $embedUrl = null;

        if ($url) {
            // Regex to extract Video ID
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
            
            if (isset($match[1])) {
                $videoId = $match[1];
                // Montar link para auto-play em loop, mas com controles visíveis para un-mute e navegação
                $embedUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&loop=1&playlist={$videoId}&controls=1";
            }
        }

        // Salvar a original para mostrar no form
        Setting::updateOrCreate(
            ['key' => 'youtube_queue_video_raw'],
            ['value' => $url]
        );

        // Salvar a formatada para o iframe
        Setting::updateOrCreate(
            ['key' => 'youtube_queue_video'],
            ['value' => $embedUrl]
        );

        return redirect()->back()->with('success', 'Link do YouTube atualizado com sucesso!');
    }
}
