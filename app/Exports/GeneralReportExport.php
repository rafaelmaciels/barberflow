<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GeneralReportExport implements WithMultipleSheets
{
    protected $metrics;

    public function __construct(array $metrics)
    {
        $this->metrics = $metrics;
    }

    public function sheets(): array
    {
        return [
            new AppointmentsSheet($this->metrics['appointments_list']),
            new BarbersSheet($this->metrics['barbers_performance']),
        ];
    }
}

class AppointmentsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $appointments;

    public function __construct($appointments)
    {
        $this->appointments = $appointments;
    }

    public function collection()
    {
        return $this->appointments;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Data',
            'Hora',
            'Cliente',
            'Telefone',
            'Serviço',
            'Barbeiro',
            'Status'
        ];
    }

    public function map($apt): array
    {
        return [
            $apt->id,
            \Carbon\Carbon::parse($apt->data)->format('d/m/Y'),
            $apt->hora,
            $apt->cliente_nome,
            $apt->cliente_whatsapp,
            $apt->service ? $apt->service->nome : '-',
            $apt->barber ? $apt->barber->nome : '-',
            strtoupper($apt->status)
        ];
    }

    public function title(): string
    {
        return 'Agendamentos';
    }
}

class BarbersSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $barbers;

    public function __construct($barbers)
    {
        $this->barbers = $barbers;
    }

    public function collection()
    {
        return $this->barbers;
    }

    public function headings(): array
    {
        return [
            'Barbeiro',
            'Total de Serviços (Concluídos)',
            'Receita Gerada (R$)'
        ];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['total_services'],
            number_format($row['revenue'], 2, ',', '.')
        ];
    }

    public function title(): string
    {
        return 'Performance Barbeiros';
    }
}
