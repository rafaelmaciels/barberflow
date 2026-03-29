<?php

// =========================
// DEBUG (remova depois)
// =========================
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =========================
// HEADERS (CORS + JSON)
// =========================
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
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

// Remove barra final
$request = rtrim($request, '/');

// 🔥 Se vier vazio, define rota padrão
if ($request === '' || $request === null) {
    $request = '/services';
}

// Garante que começa com "/"
if ($request[0] !== '/') {
    $request = '/' . $request;
}

// =========================
// IMPORTS
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

elseif ($request === '/appointments' && $method === 'PUT') {
    cancelAppointment();
}

elseif ($request === '/settings' && $method === 'GET') {
    getSettings();
}

elseif ($request === '/settings' && $method === 'PUT') {
    updateSettings();
}

elseif ($request === '/dashboard' && $method === 'GET') {
    getDashboard();
}

elseif ($request === '/login' && $method === 'POST') {
    login();
}

elseif ($request === '/auth' && $method === 'GET') {
    checkAuth();
}

elseif ($request === '/logout' && $method === 'POST') {
    logout();
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