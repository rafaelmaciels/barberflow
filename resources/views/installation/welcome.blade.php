@extends('installation.layout') @section('step_title', 'Passo 1: Bem-vindo') @section('content')
<div class="text-center"> <h4>Bem-vindo ao BarberFlow SaaS!</h4> <p class="text-muted mt-3"> Este assistente vai configurar o seu banco de dados MySQL, E-mail (SMTP), dados da sua barbearia e criar seu usuário administrador. </p> <p class="text-danger mb-4"> <strong>Atenção:</strong> Ao prosseguir, o banco de dados configurado será apagado e reconstruído do zero. </p> <a href="{{ route('installation.database') }}" class="btn btn-dark w-100">Iniciar Instalação</a>
</div>
@endsection

