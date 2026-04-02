<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

// =========================
// HEADERS
// =========================
header("Access-Control-Allow-Origin: https://rafaelmaciel.net");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// =========================
// PRE-FLIGHT
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =========================
// REQUEST
// =========================
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$request = str_replace('/backend', '', $request);
$request = str_replace('/index.php', '', $request);
$request = rtrim($request, '/');

if ($request === '' || $request === null) {
    $request = '/services';
}

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

switch (true) {

    case $request === '/services' && $method === 'GET':
        getServices();
        break;

    case $request === '/time-slots' && $method === 'GET':
        getTimeSlots();
        break;

    case $request === '/appointments' && $method === 'POST':
        createAppointment();
        break;

    case $request === '/appointments' && $method === 'GET':
        getAppointments();
        break;

    case $request === '/appointments' && $method === 'PUT':
        cancelAppointment();
        break;

    case $request === '/settings' && $method === 'GET':
        getSettings();
        break;

    case $request === '/settings' && $method === 'PUT':
        updateSettings();
        break;

    case $request === '/dashboard' && $method === 'GET':
        getDashboard();
        break;

    case $request === '/login' && $method === 'POST':
        login();
        break;

    case $request === '/auth' && $method === 'GET':
        checkAuth();
        break;

    case $request === '/logout' && $method === 'POST':
        logout();
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "error" => "Rota não encontrada",
            "request" => $request,
            "method" => $method
        ]);
}
