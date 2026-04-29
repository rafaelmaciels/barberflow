<?php

require_once __DIR__ . '/../config/database.php';

function ensureBlockedDatesTable($conn) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS blocked_dates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            blocked_date DATE NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function isValidScheduleDate($date) {
    $dateObj = DateTime::createFromFormat('Y-m-d', (string) $date);
    return $dateObj && $dateObj->format('Y-m-d') === $date;
}

function validateBlockedDatePayload($date) {
    $normalizedDate = trim((string) $date);

    if (!isValidScheduleDate($normalizedDate)) {
        http_response_code(422);
        echo json_encode(["error" => "Informe uma data válida"]);
        return null;
    }

    return $normalizedDate;
}

function isBlockedScheduleDate($conn, $date) {
    ensureBlockedDatesTable($conn);

    $stmt = $conn->prepare("
        SELECT id
        FROM blocked_dates
        WHERE blocked_date = ?
        LIMIT 1
    ");
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result && $result->num_rows > 0;
}

function getBlockedDates() {
    $conn = getConnection();
    ensureBlockedDatesTable($conn);

    $from = $_GET['from'] ?? date('Y-m-d');
    $to = $_GET['to'] ?? date('Y-m-d', strtotime('+90 days'));

    if (!isValidScheduleDate($from) || !isValidScheduleDate($to)) {
        http_response_code(422);
        echo json_encode(["error" => "Período inválido"]);
        return;
    }

    $stmt = $conn->prepare("
        SELECT id, blocked_date
        FROM blocked_dates
        WHERE blocked_date BETWEEN ? AND ?
        ORDER BY blocked_date
    ");
    $stmt->bind_param('ss', $from, $to);
    $stmt->execute();

    $result = $stmt->get_result();
    $dates = [];

    while ($row = $result->fetch_assoc()) {
        $dates[] = [
            "id" => (int) $row['id'],
            "date" => $row['blocked_date']
        ];
    }

    echo json_encode($dates);
}

function blockScheduleDate() {
    if (!requireAdminSession()) {
        return;
    }

    $conn = getConnection();
    ensureBlockedDatesTable($conn);

    $data = json_decode(file_get_contents("php://input"), true);
    $date = validateBlockedDatePayload($data['date'] ?? '');

    if (!$date) {
        return;
    }

    $existingStmt = $conn->prepare("
        SELECT id
        FROM blocked_dates
        WHERE blocked_date = ?
        LIMIT 1
    ");
    $existingStmt->bind_param('s', $date);
    $existingStmt->execute();
    $existing = $existingStmt->get_result();

    if ($existing && $existing->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Essa data já está bloqueada"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO blocked_dates (blocked_date) VALUES (?)");
    $stmt->bind_param('s', $date);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao bloquear data"]);
        return;
    }

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "date" => $date
    ]);
}

function unblockScheduleDate() {
    if (!requireAdminSession()) {
        return;
    }

    $conn = getConnection();
    ensureBlockedDatesTable($conn);

    $data = json_decode(file_get_contents("php://input"), true);
    $date = validateBlockedDatePayload($data['date'] ?? '');

    if (!$date) {
        return;
    }

    $stmt = $conn->prepare("DELETE FROM blocked_dates WHERE blocked_date = ?");
    $stmt->bind_param('s', $date);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao liberar data"]);
        return;
    }

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Data não encontrada na lista de bloqueios"]);
        return;
    }

    echo json_encode([
        "success" => true,
        "date" => $date
    ]);
}
