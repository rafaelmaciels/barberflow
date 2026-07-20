@extends('installation.layout')

@section('step_title', 'Passo 4: Dados da Barbearia')

@section('content')
<form action="{{ route('installation.setupCompany') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Nome da Barbearia</label>
        <input type="text" name="company_name" class="form-control" placeholder="Minha Barbearia" required>
    </div>
    <div class="mb-4">
        <label>WhatsApp de Contato</label>
        <input type="text" name="whatsapp" class="form-control" placeholder="(00) 00000-0000" required>
        <small class="text-muted">Este número será exibido na tela de agendamento público.</small>
    </div>
    
    <button type="submit" class="btn btn-dark w-100">Salvar e Continuar</button>
</form>
@endsection
