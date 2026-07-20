<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use App\Services\FinancialService;
use App\Services\BarberService;
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

        return view('dashboard.index', compact(
            'totalAgendamentosHoje',
            'agendamentosConcluidos',
            'receitaMensal',
            'despesaMensal',
            'lucroMensal',
            'totalBarbeirosAtivos',
            'recentAppointments'
        ));
    }
}
