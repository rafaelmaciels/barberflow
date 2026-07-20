<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Public\PublicBookingController;
use App\Http\Controllers\Public\QueueController;
use App\Http\Controllers\Settings\SettingController;
use Illuminate\Support\Facades\Route;

// Rota Pública (Módulo 5: Agendamento Público)
Route::get('/', [PublicBookingController::class, 'index'])->name('public.booking');
Route::post('/agendar', [PublicBookingController::class, 'store'])->name('public.booking.store');
Route::get('/api/available-times', [PublicBookingController::class, 'getAvailableTimes'])->name('api.available-times');

// Rota Pública/TV (Módulo 6: Fila de Atendimento)
Route::get('/fila', [QueueController::class, 'index'])->name('queue.index');
Route::get('/fila/dados', [QueueController::class, 'data'])->name('queue.data');

// Rotas Autenticadas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
