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
}
