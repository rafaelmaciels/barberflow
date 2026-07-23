<?php

namespace App\Services;

use App\Interfaces\BarberRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class BarberService
{
    protected $barberRepository;

    public function __construct(BarberRepositoryInterface $barberRepository)
    {
        $this->barberRepository = $barberRepository;
    }

    public function getAllBarbers()
    {
        return $this->barberRepository->all();
    }

    public function getBarber(int $id)
    {
        return $this->barberRepository->find($id);
    }

    public function createBarber(array $data, $photo = null)
    {
        if ($photo) {
            $path = $photo->store('barbers', 'public');
            $data['foto'] = Storage::disk('public')->url($path);
        }

        $data['ativo'] = isset($data['ativo']) ? true : false;
        
        return $this->barberRepository->create($data);
    }

    public function updateBarber(int $id, array $data, $photo = null)
    {
        if ($photo) {
            $path = $photo->store('barbers', 'public');
            $data['foto'] = Storage::disk('public')->url($path);
        }

        $data['ativo'] = isset($data['ativo']) ? true : false;

        return $this->barberRepository->update($id, $data);
    }

    public function deleteBarber(int $id)
    {
        return $this->barberRepository->delete($id);
    }
}
