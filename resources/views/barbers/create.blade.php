@extends('layouts.app')

@section('title', 'Novo Barbeiro')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary mb-0">Adicionar Barbeiro</h2>
        <a href="{{ route('barbers.index') }}" class="btn btn-outline-secondary fw-bold rounded-pill px-4">
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

                <form action="{{ route('barbers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nome" class="form-label fw-semibold">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" id="nome" name="nome" value="{{ old('nome') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                            <input type="email" class="form-control bg-light" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="telefone" class="form-label fw-semibold">Telefone / WhatsApp</label>
                            <input type="text" class="form-control bg-light" id="telefone" name="telefone" value="{{ old('telefone') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="foto" class="form-label fw-semibold">Foto de Perfil</label>
                            <input class="form-control bg-light" type="file" id="foto" name="foto" accept="image/*">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="ativo" name="ativo" checked>
                                <label class="form-check-label fw-semibold ms-2" for="ativo">Barbeiro Ativo (Disponível na agenda)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 rounded-3">
                            <i class="fa-solid fa-check me-2"></i> Salvar Barbeiro
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection
