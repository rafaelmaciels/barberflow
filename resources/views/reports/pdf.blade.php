<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório BarberFlow</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 14px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f4f4f4; }
        .success { color: green; }
        .danger { color: red; }
        .summary-box { width: 100%; border: 1px solid #000; padding: 15px; margin-bottom: 20px; }
        .summary-box h3 { margin-top: 0; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">BarberFlow - Relatório Gerencial</div>
        <div class="subtitle">Período: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
    </div>

    <div class="summary-box">
        <h3>Resumo de Operações</h3>
        <p><strong>Total de Agendamentos:</strong> {{ $metrics['total_appointments'] }}</p>
        <p><strong>Concluídos:</strong> {{ $metrics['completed'] }}</p>
        <p><strong>Cancelados:</strong> {{ $metrics['cancelled'] }}</p>
        <p><strong>Não Compareceram (Faltas):</strong> {{ $metrics['no_shows'] }}</p>
    </div>

    <div class="summary-box">
        <h3>Balanço Financeiro</h3>
        <p><strong>Receitas Brutas:</strong> R$ {{ number_format($metrics['revenue'], 2, ',', '.') }}</p>
        <p><strong>Despesas Totais:</strong> R$ {{ number_format($metrics['expenses'], 2, ',', '.') }}</p>
        <hr>
        <p><strong>LUCRO LÍQUIDO:</strong> R$ {{ number_format($metrics['profit'], 2, ',', '.') }}</p>
    </div>

    <h3>Performance dos Barbeiros</h3>
    <table>
        <thead>
            <tr>
                <th>Profissional</th>
                <th>Serviços Concluídos</th>
                <th>Receita Gerada</th>
            </tr>
        </thead>
        <tbody>
            @forelse($metrics['barbers_performance'] as $barber)
            <tr>
                <td>{{ $barber['name'] }}</td>
                <td>{{ $barber['total_services'] }}</td>
                <td>R$ {{ number_format($barber['revenue'], 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3">Nenhum dado no período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 50px; font-size: 12px; color: #999;">
        Documento gerado automaticamente pelo sistema BarberFlow em {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>
