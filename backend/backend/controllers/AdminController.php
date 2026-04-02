<?php

require_once __DIR__ . '/../config/database.php';

// =========================
// LOGIN
// =========================
function login() {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $conn = getConnection();

    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'] ?? '';
    $password = md5($data['password'] ?? '');

    $stmt = $conn->prepare("
        SELECT id FROM users 
        WHERE username = ? AND password = ?
    ");

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = true;

        echo json_encode(["success" => true]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Credenciais inválidas"]);
    }
}

// =========================
// CHECK AUTH
// =========================
function checkAuth() {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['admin'])) {
        http_response_code(401);
        echo json_encode(["authenticated" => false]);
        return;
    }

    echo json_encode(["authenticated" => true]);
}

// =========================
// LOGOUT
// =========================
function logout() {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION = [];
    session_destroy();

    echo json_encode(["success" => true]);
}