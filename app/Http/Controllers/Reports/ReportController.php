<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AppointmentService;
use App\Services\FinancialService;
use App\Services\BarberService;
use App\Services\ServiceCatalogService;
use Carbon\Carbon;
use PDF; // Barryvdh\DomPDF\Facade\Pdf
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GeneralReportExport;

class ReportController extends Controller
{
    protected $appointmentService;
    protected $financialService;
    protected $barberService;
    protected $serviceCatalog;

    public function __construct(
        AppointmentService $appointmentService,
        FinancialService $financialService,
        BarberService $barberService,
        ServiceCatalogService $serviceCatalog
    ) {
        $this->appointmentService = $appointmentService;
        $this->financialService = $financialService;
        $this->barberService = $barberService;
        $this->serviceCatalog = $serviceCatalog;
    }

    public function index(Request $request)
    {
        // Se não vier filtro, pega os últimos 30 dias
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $metrics = $this->generateMetrics($startDate, $endDate);

        return view('reports.index', compact('metrics', 'startDate', 'endDate'));
    }

    private function generateMetrics($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Agendamentos no período
        $appointments = $this->appointmentService->getAllAppointments()
            ->filter(function($apt) use ($start, $end) {
                $aptDate = Carbon::parse($apt->data);
                return $aptDate->between($start, $end);
            });

        // Financeiro no período
        $transactions = $this->financialService->getAllTransactions()
            ->filter(function($trans) use ($start, $end) {
                $transDate = Carbon::parse($trans->data);
                return $transDate->between($start, $end);
            });

        $totalAppointments = $appointments->count();
        $completed = $appointments->where('status', 'concluido')->count();
        $cancelled = $appointments->where('status', 'cancelado')->count();
        $noShows = $appointments->where('status', 'nao_compareceu')->count();

        $revenue = $transactions->where('tipo', 'entrada')->sum('valor');
        $expenses = $transactions->where('tipo', 'saida')->sum('valor');
        $profit = $revenue - $expenses;

        // Barbeiro mais lucrativo
        $barbersData = $appointments->where('status', 'concluido')->groupBy('barber_id')->map(function($apts) {
            return [
                'name' => $apts->first()->barber->nome,
                'total_services' => $apts->count(),
                'revenue' => $apts->sum(function($apt) {
                    return $apt->service->valor;
                })
            ];
        })->sortByDesc('revenue');
        $topBarber = $barbersData->first();

        // Serviço mais popular
        $servicesData = $appointments->where('status', 'concluido')->groupBy('service_id')->map(function($apts) {
            return [
                'name' => $apts->first()->service->nome,
                'count' => $apts->count()
            ];
        })->sortByDesc('count');
        $topService = $servicesData->first();

        return [
            'total_appointments' => $totalAppointments,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'no_shows' => $noShows,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'top_barber' => $topBarber,
            'top_service' => $topService,
            'barbers_performance' => $barbersData,
            'appointments_list' => $appointments
        ];
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $metrics = $this->generateMetrics($startDate, $endDate);
        
        $pdf = PDF::loadView('reports.pdf', compact('metrics', 'startDate', 'endDate'));
        return $pdf->download('relatorio-barberflow-'.$startDate.'-a-'.$endDate.'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $metrics = $this->generateMetrics($startDate, $endDate);
        
        return Excel::download(new GeneralReportExport($metrics), 'relatorio-barberflow-'.$startDate.'-a-'.$endDate.'.xlsx');
    }
}
