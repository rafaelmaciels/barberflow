@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-4">
            <div class="text-center mb-4">
                <h1 class="h3 mb-3 fw-normal">BarberFlow</h1>
                <p>Acesse o painel administrativo</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="nome@exemplo.com" value="{{ old('email') }}" required autofocus>
                    <label for="email">Email</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required>
                    <label for="password">Senha</label>
                </div>

                <div class="form-check text-start my-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                    <label class="form-check-label" for="remember_me">
                        Lembrar-me
                    </label>
                </div>

                <button class="w-100 btn btn-lg btn-primary" type="submit">Entrar</button>

                <p class="mt-5 mb-3 text-muted text-center">
                    BarberFlow &copy; {{ date('Y') }}<br>
                    Desenvolvido por <a href="https://rafaelmaciel.net">Rafael Maciel
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
