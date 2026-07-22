@extends('installation.layout') @section('step_title', 'Passo 5: Conta de Administrador') @section('content')
<form action="{{ route('installation.setupAdmin') }}" method="POST"> @csrf <div class="mb-3"> <label>Seu Nome</label> <input type="text" name="name" class="form-control" required> </div> <div class="mb-3"> <label>Seu E-mail (Login)</label> <input type="email" name="email" class="form-control" required> </div> <div class="mb-3"> <label>Senha</label> <input type="password" name="password" class="form-control" required minlength="6"> </div> <div class="mb-4"> <label>Confirme a Senha</label> <input type="password" name="password_confirmation" class="form-control" required minlength="6"> </div> <button type="submit" class="btn btn-dark w-100">Criar Conta e Finalizar Instalação</button>
</form>
@endsection

