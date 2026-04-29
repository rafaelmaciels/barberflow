<?php

require_once __DIR__ . '/../config/database.php';

function upgradeLegacyAdminPasswordIfNeeded($conn, $userId, $plainPassword, $storedPassword) {
    $isLegacyMd5 = preg_match('/^[a-f0-9]{32}$/i', (string) $storedPassword) === 1;

    if ($isLegacyMd5 && md5($plainPassword) === $storedPassword) {
        $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
        $stmtUpdate = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");

        if ($stmtUpdate) {
            $stmtUpdate->bind_param("si", $newHash, $userId);
            $stmtUpdate->execute();
        }

        return true;
    }

    return false;
}

function passwordMatchesAdmin($conn, $userId, $plainPassword, $storedPassword) {
    if (password_verify($plainPassword, $storedPassword)) {
        if (password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
            $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
            $stmtUpdate = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");

            if ($stmtUpdate) {
                $stmtUpdate->bind_param("si", $newHash, $userId);
                $stmtUpdate->execute();
            }
        }

        return true;
    }

    return upgradeLegacyAdminPasswordIfNeeded($conn, $userId, $plainPassword, $storedPassword);
}

function login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $conn = getConnection();
    $data = json_decode(file_get_contents("php://input"), true);

    $username = trim((string) ($data['username'] ?? ''));
    $password = (string) ($data['password'] ?? '');

    $stmt = $conn->prepare("
        SELECT id, password FROM users
        WHERE username = ?
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (passwordMatchesAdmin($conn, (int) $row['id'], $password, (string) $row['password'])) {
            $_SESSION['admin'] = true;
            echo json_encode(["success" => true]);
            return;
        }
    }

    http_response_code(401);
    echo json_encode(["error" => "Credenciais inválidas"]);
}

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

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION = [];
    session_destroy();

    echo json_encode(["success" => true]);
}
