@extends('layouts.app')
@section('title', 'Novo Usuário')
@section('content')

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary mb-0"><i class="fa-solid fa-user-plus me-2"></i> Novo Usuário</h2>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary fw-bold px-4 rounded-pill">
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

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">E-mail (Login) <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Senha <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <div class="form-text">Mínimo 8 caracteres.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar Senha <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="8">
                        </div>
                    </div>

                    <hr class="my-4 text-muted">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-shield-halved text-primary me-2"></i> Nível de Acesso</h5>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-semibold">Permissão <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required onchange="toggleBarberSelect()">
                                <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Funcionário (Barbeiro)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador (Acesso Total)</option>
                            </select>
                            <div class="form-text text-muted mt-2" id="role-help">
                                Funcionários só veem a própria agenda e não podem excluir dados do sistema.
                            </div>
                        </div>

                        <div class="col-md-6" id="barber-select-container">
                            <label for="barber_id" class="form-label fw-semibold">Vincular a um Perfil de Barbeiro</label>
                            <select class="form-select" id="barber_id" name="barber_id">
                                <option value="">-- Nenhum --</option>
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                        {{ $barber->nome }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">
                                Necessário para que o funcionário veja sua própria agenda.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 rounded-3">
                            <i class="fa-solid fa-check me-2"></i> Cadastrar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleBarberSelect() {
        const role = document.getElementById('role').value;
        const container = document.getElementById('barber-select-container');
        const helpText = document.getElementById('role-help');

        if (role === 'admin') {
            container.style.display = 'none';
            document.getElementById('barber_id').value = '';
            helpText.innerHTML = '<span class="text-danger fw-semibold">Atenção:</span> Administradores têm acesso total e irrestrito ao sistema.';
        } else {
            container.style.display = 'block';
            helpText.innerHTML = 'Funcionários só veem a própria agenda e não podem excluir dados do sistema.';
        }
    }

    // Executa ao carregar a página para definir o estado inicial
    document.addEventListener('DOMContentLoaded', toggleBarberSelect);
</script>
@endsection
