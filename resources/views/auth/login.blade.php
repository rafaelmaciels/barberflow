@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-5 col-lg-4">
        
        <div class="text-center mb-4">
            <h1 class="fw-bold text-primary">{{ config('app.name', 'BarberFlow') }}</h1>
            <p class="text-muted">Acesso Administrativo</p>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5">

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">E-mail</label>
                        <input type="email" name="email" id="email" class="form-control form-control-lg bg-light" value="{{ old('email') }}" required autofocus placeholder="admin@barberflow.com">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Senha</label>
                        <input type="password" name="password" id="password" class="form-control form-control-lg bg-light" required placeholder="••••••••">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-3">
                            Entrar no Sistema
                        </button>
                    </div>

                </form>

            </div>
        </div>

        <div class="text-center mt-4 text-muted small">
            BarberFlow &copy; <br>
            Desenvolvido por Rafael Maciel
        </div>

    </div>
</div>
@endsection
