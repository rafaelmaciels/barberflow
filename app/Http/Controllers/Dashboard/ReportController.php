<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\FinancialTransaction;
use App\Models\Service;
use App\Models\Barber;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AppointmentsExport;
use App\Exports\FinanceExport;
use App\Exports\ServicesExport;
use App\Exports\BarbersExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $barbers = Barber::all();
        $services = Service::all();
        
        $type = $request->get('type', 'appointments');
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        
        $data = $this->getReportData($request);

        return view('reports.index', compact('barbers', 'services', 'type', 'startDate', 'endDate', 'data'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $type = $request->get('type', 'appointments');
        $data = $this->getReportData($request);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', ['data' => $data, 'type' => $type, 'request' => $request]);
            return $pdf->download('relatorio_' . $type . '_' . date('YmdHis') . '.pdf');
        } else {
            // Excel
            return match ($type) {
                'appointments' => Excel::download(new AppointmentsExport($data), 'agendamentos.xlsx'),
                'finance' => Excel::download(new FinanceExport($data), 'financeiro.xlsx'),
                'services' => Excel::download(new ServicesExport($data), 'servicos.xlsx'),
                'barbers' => Excel::download(new BarbersExport($data), 'barbeiros.xlsx'),
                default => back()->with('error', 'Tipo inválido'),
            };
        }
    }

    private function getReportData(Request $request)
    {
        $type = $request->get('type', 'appointments');
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        
        $employeeBarberId = (auth()->check() && auth()->user()->isEmployee()) ? auth()->user()->barber_id : null;

        if ($type === 'appointments') {
            $query = Appointment::with(['barber', 'service'])
                ->whereBetween('data', [$startDate, $endDate]);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($employeeBarberId) {
                $query->where('barber_id', $employeeBarberId);
            } elseif ($request->filled('barber_id')) {
                $query->where('barber_id', $request->barber_id);
            }

            return $query->orderBy('data', 'desc')->orderBy('hora', 'desc')->get();
        }

        if ($type === 'finance') {
            $query = FinancialTransaction::whereBetween('data', [$startDate, $endDate]);
            
            if ($request->filled('transaction_type')) {
                $query->where('tipo', $request->transaction_type);
            }
            
            return $query->orderBy('data', 'desc')->get();
        }

        if ($type === 'services') {
            // Faturamento e qtd por serviço
            $query = Appointment::with('service')
                ->whereBetween('data', [$startDate, $endDate])
                ->where('status', 'concluido');
                
            if ($employeeBarberId) {
                $query->where('barber_id', $employeeBarberId);
            }
            
            $appointments = $query->get();
                
            $servicesData = [];
            foreach ($appointments as $apt) {
                $srvId = $apt->service_id;
                if (!isset($servicesData[$srvId])) {
                    $servicesData[$srvId] = [
                        'nome' => $apt->service->nome ?? 'Desconhecido',
                        'qtd' => 0,
                        'faturamento' => 0
                    ];
                }
                $servicesData[$srvId]['qtd']++;
                $servicesData[$srvId]['faturamento'] += ($apt->service->valor ?? 0);
            }
            
            // Transform array to collection and sort by revenue
            return collect($servicesData)->sortByDesc('faturamento')->values();
        }

        if ($type === 'barbers') {
            // Faturamento e qtd por barbeiro
            $query = Appointment::with(['barber', 'service'])
                ->whereBetween('data', [$startDate, $endDate])
                ->where('status', 'concluido');
                
            if ($employeeBarberId) {
                $query->where('barber_id', $employeeBarberId);
            }
            
            $appointments = $query->get();
                
            $barbersData = [];
            foreach ($appointments as $apt) {
                $barbId = $apt->barber_id;
                if (!isset($barbersData[$barbId])) {
                    $barbersData[$barbId] = [
                        'nome' => $apt->barber->nome ?? 'Desconhecido',
                        'qtd' => 0,
                        'faturamento' => 0
                    ];
                }
                $barbersData[$barbId]['qtd']++;
                $barbersData[$barbId]['faturamento'] += ($apt->service->valor ?? 0);
            }
            
            return collect($barbersData)->sortByDesc('faturamento')->values();
        }

        return collect([]);
    }
}
