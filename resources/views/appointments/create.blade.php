@extends('layouts.app')

@section('title', 'Novo Agendamento')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary mb-0">Agendamento Manual</h2>
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

                <form action="{{ route('appointments.store') }}" method="POST">
                    @csrf

                    <h5 class="fw-bold mb-3 border-bottom pb-2">Dados do Cliente</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nome do Cliente <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" name="cliente_nome" value="{{ old('cliente_nome') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" name="cliente_whatsapp" value="{{ old('cliente_whatsapp') }}" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2 mt-4">Detalhes do Agendamento</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Barbeiro <span class="text-danger">*</span></label>
                            <select class="form-select bg-light" name="barber_id" required>
                                <option value="" disabled selected>Selecione um barbeiro...</option>
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>{{ $barber->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Serviço <span class="text-danger">*</span></label>
                            <select class="form-select bg-light" name="service_id" required>
                                <option value="" disabled selected>Selecione um serviço...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->nome }} (R$ {{ number_format($service->valor, 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-light" name="data" value="{{ old('data', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Horário <span class="text-danger">*</span></label>
                            <input type="time" class="form-control bg-light" name="hora" value="{{ old('hora', '09:00') }}" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 rounded-3">
                            <i class="fa-solid fa-check me-2"></i> Confirmar Agendamento
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection
