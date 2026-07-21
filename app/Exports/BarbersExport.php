<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BarbersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Barbeiro',
            'Atendimentos Concluídos',
            'Faturamento Gerado (R$)'
        ];
    }

    public function map($row): array
    {
        return [
            $row['nome'],
            $row['qtd'],
            number_format($row['faturamento'], 2, ',', '')
        ];
    }
}
