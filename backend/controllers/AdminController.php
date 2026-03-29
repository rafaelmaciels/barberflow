<?php

// =========================
// IMPORTAÇÃO GLOBAL
// =========================
require_once __DIR__ . '/../config/database.php';

// =========================
// FUNÇÃO DE LOGIN
// =========================
function login() {

    // Inicia sessão apenas se necessário
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 🔥 Verifica se função existe
    if (!function_exists('getConnection')) {
        http_response_code(500);
        echo json_encode(["error" => "Função getConnection não encontrada"]);
        return;
    }

    $conn = getConnection();

    // Recebe dados do frontend
    $data = json_decode(file_get_contents("php://input"), true);

    // 🔐 Validação básica
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(["error" => "Usuário e senha são obrigatórios"]);
        return;
    }

    $password = md5($password);

    // 🔐 Prepared statement (seguro)
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND password = ?");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Erro na preparação da query"]);
        return;
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = true;

        echo json_encode(["success" => "Login realizado"]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Credenciais inválidas"]);
    }
}

// =========================
// VERIFICA AUTENTICAÇÃO
// =========================
function checkAuth() {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['admin'])) {
        http_response_code(401);
        echo json_encode(["error" => "Não autorizado"]);
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

    echo json_encode(["success" => "Logout realizado"]);
}