@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-primary mb-0"><i class="fa-solid fa-chart-line me-2"></i> Visão Geral</h2>
        <p class="text-muted">Bem-vindo de volta! Aqui está o resumo da sua barbearia hoje.</p>
    </div>
</div>

<!-- Cards de Resumo -->
<div class="row g-4 mb-5">
    
    <!-- Agendamentos Hoje -->
    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold uppercase tracking-wider small">Agendamentos (Hoje)</div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-calendar-day fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">{{ $totalAgendamentosHoje }}</h2>
                <p class="text-success small mb-0"><i class="fa-solid fa-check me-1"></i> {{ $agendamentosConcluidos }} concluídos</p>
            </div>
            <div class="bg-primary" style="height: 4px; width: 100%;"></div>
        </div>
    </div>

    <!-- Receita Mensal -->
    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold uppercase tracking-wider small">Receita (Mês)</div>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-arrow-trend-up fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">R$ {{ number_format($receitaMensal, 2, ',', '.') }}</h2>
                <p class="text-muted small mb-0">Entradas brutas no mês atual</p>
            </div>
            <div class="bg-success" style="height: 4px; width: 100%;"></div>
        </div>
    </div>

    <!-- Despesa Mensal -->
    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold uppercase tracking-wider small">Despesas (Mês)</div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-arrow-trend-down fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">R$ {{ number_format($despesaMensal, 2, ',', '.') }}</h2>
                <p class="text-muted small mb-0">Saídas e custos no mês atual</p>
            </div>
            <div class="bg-danger" style="height: 4px; width: 100%;"></div>
        </div>
    </div>

    <!-- Lucro Líquido -->
    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold uppercase tracking-wider small">Lucro Líquido (Mês)</div>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-sack-dollar fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1 {{ $lucroMensal < 0 ? 'text-danger' : 'text-info' }}">R$ {{ number_format($lucroMensal, 2, ',', '.') }}</h2>
                <p class="text-muted small mb-0">Receitas - Despesas</p>
            </div>
            <div class="bg-info" style="height: 4px; width: 100%;"></div>
        </div>
    </div>

</div>

<!-- Sessão de Detalhes -->
<div class="row g-4">
    <!-- Últimos Agendamentos -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Últimos Agendamentos</h5>
                    <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Ver Todos</a>
                </div>
            </div>
            <div class="card-body p-4">
                @if($recentAppointments->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fa-regular fa-calendar-xmark fa-3x mb-3 opacity-50"></i>
                        <p>Nenhum agendamento registrado ainda.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Serviço</th>
                                    <th>Profissional</th>
                                    <th>Data/Hora</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAppointments as $apt)
                                <tr>
                                    <td class="fw-semibold">{{ $apt->cliente_nome }}</td>
                                    <td>{{ $apt->service->nome }}</td>
                                    <td>{{ $apt->barber->nome }}</td>
                                    <td>
                                        <span class="d-block">{{ \Carbon\Carbon::parse($apt->data)->format('d/m/Y') }}</span>
                                        <span class="small text-muted">{{ \Carbon\Carbon::parse($apt->hora)->format('H:i') }}</span>
                                    </td>
                                    <td>
                                        @if($apt->status == 'agendado')
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill"><i class="fa-solid fa-calendar-check me-1"></i> Agendado</span>
                                        @elseif($apt->status == 'concluido')
                                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill"><i class="fa-solid fa-check-double me-1"></i> Concluído</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill"><i class="fa-solid fa-xmark me-1"></i> Cancelado</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Dashboard -->
    <div class="col-lg-4">
        <!-- Status Profissionais -->
        <div class="card shadow-sm border-0 rounded-4 mb-4 bg-primary text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
            <div class="card-body p-4 d-flex flex-column justify-content-center text-center">
                <div class="mb-4">
                    <i class="fa-solid fa-users fa-4x opacity-75"></i>
                </div>
                <h1 class="display-4 fw-bold mb-2">{{ $totalBarbeirosAtivos }}</h1>
                <h4 class="fw-light mb-4">Profissionais Ativos</h4>
                <p class="small opacity-75 mb-0">Sua equipe configurada e pronta para os atendimentos desta semana.</p>
                
                <div class="mt-4 pt-4 border-top border-light border-opacity-25">
                    <a href="{{ route('barbers.index') }}" class="btn btn-light text-primary rounded-pill fw-bold px-4 shadow-sm">
                        Gerenciar Equipe
                    </a>
                </div>
            </div>
        </div>

        <!-- TV Recepção -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0"><i class="fa-brands fa-youtube text-danger me-2"></i> TV da Recepção</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('dashboard.youtube') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Link do Vídeo (YouTube)</label>
                        <div class="input-group">
                            <input type="url" name="youtube_link" class="form-control form-control-sm" placeholder="https://youtube.com/watch?v=..." value="{{ $youtubeLink }}">
                            <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
                        </div>
                        <div class="form-text small">Cole o link para exibir na tela de chamadas.</div>
                    </div>
                </form>
                
                <div class="d-grid mt-3">
                    <a href="{{ route('queue.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fa-solid fa-tv me-1"></i> Abrir Tela da TV</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .uppercase { text-transform: uppercase; }
    .tracking-wider { letter-spacing: 0.05em; }
</style>
@endsection
