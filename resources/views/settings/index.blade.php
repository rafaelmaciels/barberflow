@extends('layouts.app')
@section('title', 'Configurações da Empresa')
@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary mb-0"><i class="fa-solid fa-gear me-2"></i> Configurações da Empresa</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Informações Principais</h5>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="company_name" class="form-label fw-semibold">Nome da Barbearia</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $settings['company_name'] ?? 'Minha Barbearia' }}" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label for="logo" class="form-label fw-semibold">Logo da Empresa</label>
                            <input class="form-control" type="file" id="logo" name="logo" accept="image/*">
                            @if(isset($settings['company_logo']))
                            <div class="mt-3">
                                <p class="mb-1 text-muted small">Logo atual:</p>
                                <img src="{{ asset($settings['company_logo']) }}" alt="Logo Atual" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                            @endif
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2 mt-4">Dados Comerciais</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="company_phone" class="form-label fw-semibold">Telefone / WhatsApp</label>
                            <input type="text" class="form-control" id="company_phone" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="company_email" class="form-label fw-semibold">E-mail de Contato (Destinatário de Avisos)</label>
                            <input type="email" class="form-control" id="company_email" name="company_email" value="{{ $settings['company_email'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="company_address" class="form-label fw-semibold">Endereço Completo</label>
                            <textarea class="form-control" id="company_address" name="company_address" rows="2">{{ $settings['company_address'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2 mt-4">Servidor de E-mail (SMTP)</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mail_host" class="form-label fw-semibold">Servidor (Host)</label>
                            <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ $env['MAIL_HOST'] ?? '' }}" placeholder="smtp.exemplo.com">
                        </div>
                        <div class="col-md-3">
                            <label for="mail_port" class="form-label fw-semibold">Porta</label>
                            <input type="text" class="form-control" id="mail_port" name="mail_port" value="{{ $env['MAIL_PORT'] ?? '' }}" placeholder="465">
                        </div>
                        <div class="col-md-3">
                            <label for="mail_encryption" class="form-label fw-semibold">Criptografia</label>
                            <select class="form-select" id="mail_encryption" name="mail_encryption">
                                <option value="" {{ ($env['MAIL_ENCRYPTION'] ?? '') == '' ? 'selected' : '' }}>Nenhuma</option>
                                <option value="tls" {{ ($env['MAIL_ENCRYPTION'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($env['MAIL_ENCRYPTION'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mail_username" class="form-label fw-semibold">Usuário SMTP</label>
                            <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ $env['MAIL_USERNAME'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="mail_password" class="form-label fw-semibold">Senha SMTP</label>
                            <input type="password" class="form-control" id="mail_password" name="mail_password" placeholder="(Deixe em branco para não alterar)">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="mail_from_address" class="form-label fw-semibold">E-mail Remetente</label>
                            <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" value="{{ $env['MAIL_FROM_ADDRESS'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="mail_from_name" class="form-label fw-semibold">Nome Remetente</label>
                            <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" value="{{ trim($env['MAIL_FROM_NAME'] ?? '', '"') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold px-5">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
