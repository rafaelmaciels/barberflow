<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\BarberRepositoryInterface;
use App\Models\Barber;

class BarberRepository implements BarberRepositoryInterface
{
    public function all()
    {
        return Barber::all();
    }

    public function find(int $id)
    {
        return Barber::findOrFail($id);
    }

    public function create(array $data)
    {
        return Barber::create($data);
    }

    public function update(int $id, array $data)
    {
        $barber = Barber::findOrFail($id);
        $barber->update($data);
        return $barber;
    }

    public function delete(int $id)
    {
        return Barber::destroy($id);
    }
}
