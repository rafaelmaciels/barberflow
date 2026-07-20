@extends('layouts.app')

@section('title', 'Editar Agendamento')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary mb-0">Gerenciar Agendamento #{{ $appointment->id }}</h2>
        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary fw-bold rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Voltar à Agenda
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="fw-bold mb-3 border-bottom pb-2">Status do Atendimento</h5>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <select name="status" class="form-select form-select-lg {{ $appointment->status == 'concluido' ? 'bg-success text-white' : ($appointment->status == 'cancelado' ? 'bg-danger text-white' : ($appointment->status == 'nao_compareceu' ? 'bg-warning text-dark' : 'bg-primary text-white')) }} fw-bold" required>
                                <option value="agendado" {{ $appointment->status == 'agendado' ? 'selected' : '' }}>AGENDADO</option>
                                <option value="concluido" {{ $appointment->status == 'concluido' ? 'selected' : '' }}>CONCLUÍDO (Serviço Realizado)</option>
                                <option value="cancelado" {{ $appointment->status == 'cancelado' ? 'selected' : '' }}>CANCELADO</option>
                                <option value="nao_compareceu" {{ $appointment->status == 'nao_compareceu' ? 'selected' : '' }}>NÃO COMPARECEU (No Show)</option>
                            </select>
                            <small class="text-muted mt-1 d-block">Alterar o status atualizará a cor na agenda instantaneamente.</small>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2 mt-4">Dados do Cliente</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nome do Cliente</label>
                            <input type="text" class="form-control bg-light" name="cliente_nome" value="{{ old('cliente_nome', $appointment->cliente_nome) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">WhatsApp</label>
                            <input type="text" class="form-control bg-light" name="cliente_whatsapp" value="{{ old('cliente_whatsapp', $appointment->cliente_whatsapp) }}" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2 mt-4">Detalhes do Agendamento</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Barbeiro</label>
                            <select class="form-select bg-light" name="barber_id" required>
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}" {{ old('barber_id', $appointment->barber_id) == $barber->id ? 'selected' : '' }}>{{ $barber->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Serviço</label>
                            <select class="form-select bg-light" name="service_id" required>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id', $appointment->service_id) == $service->id ? 'selected' : '' }}>
                                        {{ $service->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data</label>
                            <input type="date" class="form-control bg-light" name="data" value="{{ old('data', $appointment->data) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Horário</label>
                            <input type="time" class="form-control bg-light" name="hora" value="{{ old('hora', substr($appointment->hora, 0, 5)) }}" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                        <!-- Exclusão total -->
                        <button type="button" class="btn btn-outline-danger fw-bold btn-delete">
                            <i class="fa-solid fa-trash me-1"></i> Excluir Registro
                        </button>

                        <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 rounded-3">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Salvar Alterações
                        </button>
                    </div>

                </form>
                
                <!-- Form invisível para delete -->
                <form id="delete-form" action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.btn-delete').addEventListener('click', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Excluir definitivamente?',
                    text: "Se for apenas um cancelamento, altere o status para CANCELADO.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, excluir do banco!',
                    cancelButtonText: 'Manter registro'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form').submit();
                    }
                });
            } else {
                if (confirm('Tem certeza que deseja excluir do banco de dados?')) {
                    document.getElementById('delete-form').submit();
                }
            }
        });
    });
</script>
@endsection
