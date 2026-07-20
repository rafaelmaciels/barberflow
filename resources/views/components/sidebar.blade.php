<div class="border-end bg-white" id="sidebar-wrapper">
    <div class="sidebar-heading border-bottom bg-light">
        <a href="{{ route('dashboard') }}" class="text-dark text-decoration-none fw-bold">
            BarberFlow
        </a>
    </div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt fa-fw me-2"></i>Dashboard
        </a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
            <i class="fas fa-calendar-alt fa-fw me-2"></i>Agenda
        </a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ request()->routeIs('barbers.*') ? 'active' : '' }}" href="{{ route('barbers.index') }}">
            <i class="fas fa-cut fa-fw me-2"></i>Barbeiros
        </a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
            <i class="fas fa-concierge-bell fa-fw me-2"></i>Serviços
        </a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ request()->routeIs('finance.*') ? 'active' : '' }}" href="{{ route('finance.index') }}">
            <i class="fas fa-dollar-sign fa-fw me-2"></i>Financeiro
        </a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
            <i class="fas fa-file-invoice fa-fw me-2"></i>Relatórios
        </a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
            <i class="fas fa-cogs fa-fw me-2"></i>Configurações
        </a>
    </div>
</div>
