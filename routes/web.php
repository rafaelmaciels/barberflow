<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Public\PublicBookingController;
use App\Http\Controllers\Public\QueueController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Installation\InstallationController;
use Illuminate\Support\Facades\Route;

// Rotas de Instalação (Livres de autenticação e de bloqueio, exceto se já instalado)
Route::prefix('install')->name('installation.')->group(function () {
    Route::get('/', [InstallationController::class, 'index'])->name('index');
    Route::get('/database', [InstallationController::class, 'database'])->name('database');
    Route::post('/database', [InstallationController::class, 'setupDatabase'])->name('setupDatabase');
    Route::get('/migrations', [InstallationController::class, 'migrations'])->name('migrations');
    Route::get('/smtp', [InstallationController::class, 'smtp'])->name('smtp');
    Route::post('/smtp', [InstallationController::class, 'setupSmtp'])->name('setupSmtp');
    Route::get('/company', [InstallationController::class, 'company'])->name('company');
    Route::post('/company', [InstallationController::class, 'setupCompany'])->name('setupCompany');
    Route::get('/admin', [InstallationController::class, 'admin'])->name('admin');
    Route::post('/admin', [InstallationController::class, 'setupAdmin'])->name('setupAdmin');
    Route::get('/complete', [InstallationController::class, 'complete'])->name('complete');
});

// Rota Pública (Módulo 5: Agendamento Público)
Route::get('/', [PublicBookingController::class, 'index'])->name('public.booking');
Route::post('/agendar', [PublicBookingController::class, 'store'])->name('public.booking.store');
Route::get('/api/available-times', [PublicBookingController::class, 'getAvailableTimes'])->name('api.available-times');

// Rota Pública/TV (Módulo 6: Fila de Atendimento)
Route::get('/fila', [QueueController::class, 'index'])->name('queue.index');
Route::get('/fila/dados', [QueueController::class, 'data'])->name('queue.data');

// Rotas Autenticadas
Route::middleware(['auth'])->group(function () {
    // Dashboard (Módulo 3: Métricas)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/youtube', [DashboardController::class, 'saveYoutubeLink'])->name('dashboard.youtube');
    
    // Módulo 1: Configurações
    Route::get('/settings', [\App\Http\Controllers\Settings\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Settings\SettingsController::class, 'update'])->name('settings.update');

    // Módulo 2: Barbeiros
    Route::resource('barbers', \App\Http\Controllers\Barbers\BarberController::class);

    // Módulo 3: Serviços
    Route::resource('services', \App\Http\Controllers\Services\ServiceController::class);

    // Módulo 4: Agenda
    Route::get('appointments/api', [\App\Http\Controllers\Appointments\AppointmentController::class, 'apiEvents'])->name('appointments.api');
    Route::resource('appointments', \App\Http\Controllers\Appointments\AppointmentController::class);

    // Módulo 2: Profissionais (Barbeiros)
    Route::get('/barbers', [\App\Http\Controllers\Dashboard\BarberController::class, 'index'])->name('barbers.index');
    Route::post('/barbers', [\App\Http\Controllers\Dashboard\BarberController::class, 'store'])->name('barbers.store');
    Route::put('/barbers/{id}/toggle', [\App\Http\Controllers\Dashboard\BarberController::class, 'toggleStatus'])->name('barbers.toggle');
    Route::put('/barbers/{id}', [\App\Http\Controllers\Dashboard\BarberController::class, 'update'])->name('barbers.update');

    // Módulo de Bloqueios de Agenda
    Route::get('/blocked-times', [\App\Http\Controllers\Dashboard\BlockedTimeController::class, 'index'])->name('blocked-times.index');
    Route::post('/blocked-times', [\App\Http\Controllers\Dashboard\BlockedTimeController::class, 'store'])->name('blocked-times.store');
    Route::delete('/blocked-times/{id}', [\App\Http\Controllers\Dashboard\BlockedTimeController::class, 'destroy'])->name('blocked-times.destroy');

    // Módulo 7: Financeiro
    Route::resource('finance', \App\Http\Controllers\Finance\FinancialController::class)->except(['edit', 'update', 'show']);

    // Módulo 8: Relatórios
    Route::get('/reports', [\App\Http\Controllers\Reports\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [\App\Http\Controllers\Reports\ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/excel', [\App\Http\Controllers\Reports\ReportController::class, 'exportExcel'])->name('reports.excel');
});

// Inclui as rotas de autenticação
require __DIR__.'/auth.php';

// ROTA TEMPORÁRIA PARA TESTAR O BANCO DE DADOS (FASE 3)
Route::get('/test-db', function () {
    // 1. Cria um barbeiro
    $barber = \App\Models\Barber::create([
        'nome' => 'João Teste',
        'email' => 'joao' . rand(1, 9999) . '@teste.com',
        'telefone' => '11999999999'
    ]);

    // 2. Cria um serviço
    $service = \App\Models\Service::create([
        'nome' => 'Corte de Cabelo Teste',
        'duracao' => 30,
        'valor' => 45.00
    ]);

    // 3. Cria um agendamento vinculando os dois
    $appointment = \App\Models\Appointment::create([
        'cliente_nome' => 'Cliente Feliz',
        'cliente_whatsapp' => '11988888888',
        'barber_id' => $barber->id,
        'service_id' => $service->id,
        'data' => date('Y-m-d'),
        'hora' => '14:00:00'
    ]);

    // 4. Retorna os dados para provar que os relacionamentos estão funcionando
    return response()->json([
        'mensagem' => 'Tudo funcionando perfeitamente!',
        'agendamento' => $appointment->load('barber', 'service')
    ]);
});
