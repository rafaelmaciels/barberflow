<?php

namespace App\Services;

use App\Interfaces\ServiceRepositoryInterface;

class ServiceCatalogService
{
    protected $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getAllServices()
    {
        return $this->serviceRepository->all();
    }

    public function getService(int $id)
    {
        return $this->serviceRepository->find($id);
    }

    public function createService(array $data)
    {
        $data['ativo'] = isset($data['ativo']) ? true : false;
        $data['is_admin_only'] = isset($data['is_admin_only']) ? true : false;
        
        // Formatar valor para decimal caso o frontend mande com virgula
        if (isset($data['valor'])) {
            $data['valor'] = str_replace(',', '.', str_replace('.', '', $data['valor']));
        }

        return $this->serviceRepository->create($data);
    }

    public function updateService(int $id, array $data)
    {
        $data['ativo'] = isset($data['ativo']) ? true : false;
        $data['is_admin_only'] = isset($data['is_admin_only']) ? true : false;

        // Formatar valor para decimal
        if (isset($data['valor'])) {
            $data['valor'] = str_replace(',', '.', str_replace('.', '', $data['valor']));
        }

        return $this->serviceRepository->update($id, $data);
    }

    public function deleteService(int $id)
    {
        return $this->serviceRepository->delete($id);
    }
}
