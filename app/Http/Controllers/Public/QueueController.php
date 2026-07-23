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
        // Limpeza automática de agendamentos vencidos há mais de 1h
        \App\Models\Appointment::autoCancelExpired();

        // Pega todos os agendamentos de HOJE que não estão concluídos ou cancelados
        $appointments = \App\Models\Appointment::with(['barber', 'service'])
            ->where('data', date('Y-m-d'))
            ->whereIn('status', ['agendado', 'em_atendimento', 'remarcado'])
            ->orderBy('hora', 'asc')
            ->get();

        $emAtendimento = [];
        $proximos = [];

        foreach ($appointments as $apt) {
            if ($apt->status === 'em_atendimento') {
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
