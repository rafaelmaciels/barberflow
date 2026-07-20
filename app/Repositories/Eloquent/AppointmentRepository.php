<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\AppointmentRepositoryInterface;
use App\Models\Appointment;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function all()
    {
        return Appointment::with(['barber', 'service'])->get();
    }

    public function find(int $id)
    {
        return Appointment::with(['barber', 'service'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Appointment::create($data);
    }

    public function update(int $id, array $data)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($data);
        return $appointment;
    }

    public function delete(int $id)
    {
        return Appointment::destroy($id);
    }
}
