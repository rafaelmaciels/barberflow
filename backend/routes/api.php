<?php

// =========================
// HEADERS (CORS + JSON)
// =========================
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// =========================
// PRE-FLIGHT (CORS)
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =========================
// CAPTURA REQUEST
// =========================
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string (?date=...)
$request = parse_url($request, PHP_URL_PATH);

// Remove /backend se existir
$request = str_replace('/backend', '', $request);

// Remove index.php
$request = str_replace('/index.php', '', $request);

// =========================
// IMPORTS CORRETOS
// =========================
require_once __DIR__ . '/../controllers/AppointmentController.php';
require_once __DIR__ . '/../controllers/ServiceController.php';
require_once __DIR__ . '/../controllers/TimeSlotController.php';
require_once __DIR__ . '/../controllers/AdminController.php';

// =========================
// ROTAS
// =========================

if ($request === '/services' && $method === 'GET') {
    getServices();
}

elseif ($request === '/time-slots' && $method === 'GET') {
    getTimeSlots();
}

elseif ($request === '/appointments' && $method === 'POST') {
    createAppointment();
}

elseif ($request === '/appointments' && $method === 'GET') {
    getAppointments();
}

// 🔥 CANCELAR AGENDAMENTO (colocado no lugar correto)
elseif ($request === '/appointments' && $method === 'PUT') {
    cancelAppointment();
}

elseif ($request === '/settings' && $method === 'GET') {
    getSettings();
}

elseif ($request === '/settings' && $method === 'PUT') {
    updateSettings();
}

else {
    http_response_code(404);
    echo json_encode([
        "error" => "Rota não encontrada",
        "debug" => [
            "request" => $request,
            "method" => $method
        ]
    ]);
}