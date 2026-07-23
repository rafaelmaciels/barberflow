<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\FinancialRepositoryInterface;
use App\Models\FinancialTransaction;

class FinancialRepository implements FinancialRepositoryInterface
{
    public function all()
    {
        // Ordena por data decrescente e id decrescente (mais recentes primeiro)
        return FinancialTransaction::orderBy('data', 'desc')->orderBy('id', 'desc')->get();
    }

    public function find(int $id)
    {
        return FinancialTransaction::findOrFail($id);
    }

    public function create(array $data)
    {
        return FinancialTransaction::create($data);
    }

    public function update(int $id, array $data)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $transaction->update($data);
        return $transaction;
    }

    public function delete(int $id)
    {
        return FinancialTransaction::destroy($id);
    }
}
