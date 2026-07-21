<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Data',
            'Tipo',
            'Categoria',
            'Descrição',
            'Valor (R$)'
        ];
    }

    public function map($row): array
    {
        return [
            \Carbon\Carbon::parse($row->data)->format('d/m/Y'),
            ucfirst($row->tipo),
            $row->categoria,
            $row->descricao,
            number_format($row->valor, 2, ',', '')
        ];
    }
}
