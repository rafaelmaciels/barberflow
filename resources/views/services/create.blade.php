@extends('layouts.app')

@section('title', 'Novo Serviço')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary mb-0">Adicionar Serviço</h2>
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary fw-bold rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
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

                <form action="{{ route('services.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="nome" class="form-label fw-semibold">Nome do Serviço <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" id="nome" name="nome" value="{{ old('nome') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="descricao" class="form-label fw-semibold">Descrição Breve (opcional)</label>
                            <textarea class="form-control bg-light" id="descricao" name="descricao" rows="2">{{ old('descricao') }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duracao" class="form-label fw-semibold">Duração (em minutos) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-light" id="duracao" name="duracao" value="{{ old('duracao', 30) }}" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="valor" class="form-label fw-semibold">Valor (R$) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control bg-light" id="valor" name="valor" value="{{ old('valor') }}" required placeholder="45,00">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="ativo" name="ativo" checked>
                                <label class="form-check-label fw-semibold ms-2" for="ativo">Serviço Ativo</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 rounded-3">
                            <i class="fa-solid fa-check me-2"></i> Salvar Serviço
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection
