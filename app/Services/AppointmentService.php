<?php

namespace App\Services;

use App\Interfaces\AppointmentRepositoryInterface;

class AppointmentService
{
    protected $appointmentRepository;

    public function __construct(AppointmentRepositoryInterface $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function getAllAppointments()
    {
        return $this->appointmentRepository->all();
    }

    public function getAppointment(int $id)
    {
        return $this->appointmentRepository->find($id);
    }

    public function createAppointment(array $data)
    {
        // Regras anti-spam e de tempo real (Phase 5 public) virão depois.
        return $this->appointmentRepository->create($data);
    }

    public function updateAppointment(int $id, array $data)
    {
        $appointment = $this->getAppointment($id);
        $oldStatus = $appointment->status;
        
        $updated = $this->appointmentRepository->update($id, $data);

        // Gera registro financeiro automático quando o status muda para concluído
        if ($oldStatus !== 'concluido' && isset($data['status']) && $data['status'] === 'concluido') {
            $updatedAppointment = $this->getAppointment($id);
            if ($updatedAppointment->service) {
                app(\App\Services\FinancialService::class)->createTransaction([
                    'tipo' => 'entrada',
                    'descricao' => 'Serviço: ' . $updatedAppointment->service->nome . ' (Cliente: ' . $updatedAppointment->cliente_nome . ')',
                    'valor' => $updatedAppointment->service->valor,
                    'data' => date('Y-m-d')
                ]);
            }
        }

        return $updated;
    }

    public function deleteAppointment(int $id)
    {
        return $this->appointmentRepository->delete($id);
    }

    public function getEventsForCalendar()
    {
        $appointments = $this->getAllAppointments();
        
        $events = [];
        foreach ($appointments as $apt) {
            $color = '#3788d8'; // agendado
            if ($apt->status == 'concluido') $color = '#198754';
            if ($apt->status == 'cancelado') $color = '#dc3545';
            if ($apt->status == 'nao_compareceu') $color = '#6c757d'; // Cinza

            $events[] = [
                'id' => $apt->id,
                'title' => $apt->cliente_nome . ' - ' . $apt->service->nome,
                'start' => $apt->data . 'T' . $apt->hora,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('appointments.edit', $apt->id)
            ];
        }

        return $events;
    }
}
