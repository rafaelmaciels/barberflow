<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AppointmentsExport implements FromCollection, WithHeadings, WithMapping
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
            'Hora',
            'Cliente',
            'Telefone',
            'Barbeiro',
            'Serviço',
            'Valor (R$)',
            'Status'
        ];
    }

    public function map($row): array
    {
        return [
            \Carbon\Carbon::parse($row->data)->format('d/m/Y'),
            \Carbon\Carbon::parse($row->hora)->format('H:i'),
            $row->cliente_nome,
            $row->cliente_whatsapp,
            $row->barber->nome ?? 'N/D',
            $row->service->nome ?? 'N/D',
            number_format($row->service->valor ?? 0, 2, ',', ''),
            ucfirst(str_replace('_', ' ', $row->status))
        ];
    }
}
