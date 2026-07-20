@extends('layouts.app')

@section('title', 'Serviços')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary mb-0"><i class="fa-solid fa-concierge-bell me-2"></i> Serviços</h2>
        <a href="{{ route('services.create') }}" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Novo Serviço
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="servicesTable">
                <thead class="table-light">
                    <tr>
                        <th>Nome do Serviço</th>
                        <th>Duração (min)</th>
                        <th>Valor (R$)</th>
                        <th>Ativo</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td class="fw-semibold">
                            {{ $service->nome }}
                            @if($service->descricao)
                                <div class="text-muted small fw-normal">{{ \Illuminate\Support\Str::limit($service->descricao, 50) }}</div>
                            @endif
                        </td>
                        <td>{{ $service->duracao }} min</td>
                        <td class="text-success fw-bold">R$ {{ number_format($service->valor, 2, ',', '.') }}</td>
                        <td>
                            @if($service->ativo)
                                <span class="badge bg-primary">Sim</span>
                            @else
                                <span class="badge bg-secondary">Não</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('services.edit', $service->id) }}" class="btn btn-sm btn-outline-primary rounded-3 me-2">
                                <i class="fa-solid fa-pen-to-square"></i> Editar
                            </a>
                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="d-inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3 btn-delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
            $('#servicesTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
                ordering: true,
                info: false
            });
        }

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Excluir serviço?',
                        text: "Esta ação não pode ser desfeita!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sim, excluir!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('Tem certeza que deseja excluir?')) {
                        form.submit();
                    }
                }
            });
        });
    });
</script>
@endsection
