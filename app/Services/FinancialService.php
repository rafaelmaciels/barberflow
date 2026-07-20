<?php

namespace App\Services;

use App\Interfaces\FinancialRepositoryInterface;

class FinancialService
{
    protected $financialRepository;

    public function __construct(FinancialRepositoryInterface $financialRepository)
    {
        $this->financialRepository = $financialRepository;
    }

    public function getAllTransactions()
    {
        return $this->financialRepository->all();
    }

    public function getTransaction(int $id)
    {
        return $this->financialRepository->find($id);
    }

    public function createTransaction(array $data)
    {
        if (isset($data['valor']) && str_contains($data['valor'], ',')) {
            $data['valor'] = str_replace(',', '.', str_replace('.', '', $data['valor']));
        }
        
        return $this->financialRepository->create($data);
    }

    public function updateTransaction(int $id, array $data)
    {
        if (isset($data['valor']) && str_contains($data['valor'], ',')) {
            $data['valor'] = str_replace(',', '.', str_replace('.', '', $data['valor']));
        }
        
        return $this->financialRepository->update($id, $data);
    }

    public function deleteTransaction(int $id)
    {
        return $this->financialRepository->delete($id);
    }

    public function getBalanceSummary()
    {
        $transactions = $this->getAllTransactions();
        
        $entradas = $transactions->where('tipo', 'entrada')->sum('valor');
        $saidas = $transactions->where('tipo', 'saida')->sum('valor');
        $saldo = $entradas - $saidas;

        return [
            'entradas' => $entradas,
            'saidas' => $saidas,
            'saldo' => $saldo
        ];
    }
}
