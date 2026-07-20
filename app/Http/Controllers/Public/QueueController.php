<?php

namespace App\Http\Controllers\Public;

use App\Services\AppointmentService;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QueueController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('queue.index', compact('settings'));
    }

    public function data()
    {
        // Pega todos os agendamentos de HOJE que estão 'agendado'
        $appointments = \App\Models\Appointment::with(['barber', 'service'])
            ->where('data', date('Y-m-d'))
            ->where('status', 'agendado')
            ->orderBy('hora', 'asc')
            ->get();

        $now = date('H:i:s');
        $emAtendimento = [];
        $proximos = [];

        foreach ($appointments as $apt) {
            // Regra simples: se o horário do agendamento já passou ou é agora, está em atendimento/atrasado
            // Se for no futuro, está na fila.
            if ($apt->hora <= $now) {
                $emAtendimento[] = $apt;
            } else {
                $proximos[] = $apt;
            }
        }

        return response()->json([
            'em_atendimento' => $emAtendimento,
            'proximos' => $proximos
        ]);
    }
}
