<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\FinancialService;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index()
    {
        $transactions = $this->financialService->getAllTransactions();
        $summary = $this->financialService->getBalanceSummary();
        return view('finance.index', compact('transactions', 'summary'));
    }

    public function create()
    {
        return view('finance.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,saida',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|string',
            'data' => 'required|date'
        ]);

        $this->financialService->createTransaction($request->all());

        return redirect()->route('finance.index')->with('success', 'Lançamento financeiro registrado com sucesso!');
    }

    public function destroy($id)
    {
        $this->financialService->deleteTransaction($id);
        return redirect()->route('finance.index')->with('success', 'Lançamento excluído com sucesso!');
    }
}
