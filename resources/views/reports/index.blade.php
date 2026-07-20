@extends('layouts.app')

@section('title', 'Relatórios Gerenciais')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-primary mb-0"><i class="fa-solid fa-file-invoice me-2"></i> Inteligência e Relatórios</h2>
        <p class="text-muted">Filtre as datas para gerar balanços precisos da sua barbearia.</p>
    </div>
</div>

<!-- Filtro -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4 bg-light bg-opacity-50">
        <form action="{{ route('reports.index') }}" method="GET" class="row align-items-end g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Data Inicial</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Data Final</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="fa-solid fa-filter me-2"></i> Filtrar Período
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resumo do Período Filtrado -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-primary border-4">
            <div class="card-body">
                <div class="text-muted small fw-bold mb-1 uppercase">Total Agendamentos</div>
                <h3 class="fw-bold mb-0">{{ $metrics['total_appointments'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="text-muted small fw-bold mb-1 uppercase">Concluídos (Sucesso)</div>
                <h3 class="fw-bold mb-0 text-success">{{ $metrics['completed'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-danger border-4">
            <div class="card-body">
                <div class="text-muted small fw-bold mb-1 uppercase">Cancelamentos</div>
                <h3 class="fw-bold mb-0 text-danger">{{ $metrics['cancelled'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-warning border-4">
            <div class="card-body">
                <div class="text-muted small fw-bold mb-1 uppercase">Não Compareceu (Faltas)</div>
                <h3 class="fw-bold mb-0 text-warning">{{ $metrics['no_shows'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Mais Métricas -->
<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold text-success mb-0"><i class="fa-solid fa-dollar-sign me-2"></i> Balanço Financeiro do Período</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0 py-3">
                        <span class="text-muted">Receitas Brutas</span>
                        <span class="fw-bold text-success">R$ {{ number_format($metrics['revenue'], 2, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-3">
                        <span class="text-muted">Despesas Variáveis/Fixas</span>
                        <span class="fw-bold text-danger">R$ {{ number_format($metrics['expenses'], 2, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-3 bg-light rounded mt-2">
                        <span class="fw-bold text-dark">LUCRO LÍQUIDO</span>
                        <span class="fw-bold fs-5 {{ $metrics['profit'] < 0 ? 'text-danger' : 'text-primary' }}">
                            R$ {{ number_format($metrics['profit'], 2, ',', '.') }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold text-warning mb-0"><i class="fa-solid fa-trophy me-2"></i> Destaques do Período</h5>
            </div>
            <div class="card-body px-4 pb-4">
                
                <div class="mb-4">
                    <div class="text-muted small mb-1">Profissional Mais Lucrativo</div>
                    @if($metrics['top_barber'])
                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                            <div class="fw-bold fs-5">{{ $metrics['top_barber']['name'] }}</div>
                            <div class="text-success fw-bold">R$ {{ number_format($metrics['top_barber']['revenue'], 2, ',', '.') }}</div>
                        </div>
                    @else
                        <div class="text-muted italic">Nenhum dado no período.</div>
                    @endif
                </div>

                <div>
                    <div class="text-muted small mb-1">Serviço Mais Vendido</div>
                    @if($metrics['top_service'])
                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                            <div class="fw-bold fs-5">{{ $metrics['top_service']['name'] }}</div>
                            <div class="badge bg-primary rounded-pill">{{ $metrics['top_service']['count'] }} execuções</div>
                        </div>
                    @else
                        <div class="text-muted italic">Nenhum dado no período.</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Exportações -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
            <div class="card-body p-4 p-md-5 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="mb-4 mb-md-0">
                    <h3 class="fw-bold mb-2">Exportar Relatórios Oficiais</h3>
                    <p class="mb-0 opacity-75">Baixe o balanço consolidado deste período ({{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})</p>
                </div>
                <div class="d-flex gap-3 flex-column flex-sm-row">
                    <!-- Botão Excel -->
                    <form action="{{ route('reports.excel') }}" method="GET">
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                        <button type="submit" class="btn btn-light text-success fw-bold px-4 py-3 rounded-3 shadow-sm w-100">
                            <i class="fa-solid fa-file-excel fs-4 mb-2 d-block"></i>
                            Exportar Excel
                        </button>
                    </form>
                    
                    <!-- Botão PDF -->
                    <form action="{{ route('reports.pdf') }}" method="GET">
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                        <button type="submit" class="btn btn-light text-danger fw-bold px-4 py-3 rounded-3 shadow-sm w-100">
                            <i class="fa-solid fa-file-pdf fs-4 mb-2 d-block"></i>
                            Exportar PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
