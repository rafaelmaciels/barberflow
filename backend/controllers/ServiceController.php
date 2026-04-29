<?php

require_once __DIR__ . '/../config/database.php';

function requireAdminSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['admin'])) {
        http_response_code(401);
        echo json_encode(["error" => "Não autorizado"]);
        return false;
    }

    return true;
}

function getServices() {
    $conn = getConnection();

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao conectar com banco"]);
        return;
    }

    $result = $conn->query("SELECT * FROM services ORDER BY name ASC");

    if (!$result) {
        http_response_code(500);
        echo json_encode([
            "error" => "Erro na query",
            "details" => $conn->error
        ]);
        return;
    }

    $services = [];

    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }

    echo json_encode($services);
}

function validateServicePayload($data) {
    $name = trim((string) ($data['name'] ?? ''));
    $price = $data['price'] ?? null;
    $duration = $data['duration'] ?? null;

    if ($name === '') {
        http_response_code(422);
        echo json_encode(["error" => "Informe o nome do serviço"]);
        return null;
    }

    if (!is_numeric($price) || (float) $price <= 0) {
        http_response_code(422);
        echo json_encode(["error" => "Informe um preço válido"]);
        return null;
    }

    if (filter_var($duration, FILTER_VALIDATE_INT) === false || (int) $duration <= 0) {
        http_response_code(422);
        echo json_encode(["error" => "Informe uma duração válida em minutos"]);
        return null;
    }

    return [
        "name" => $name,
        "price" => (float) $price,
        "duration" => (int) $duration
    ];
}

function findExistingServiceByName($conn, $name, $ignoredId = null) {
    $query = "SELECT id FROM services WHERE LOWER(name) = LOWER(?)";

    if ($ignoredId !== null) {
        $query .= " AND id <> ?";
    }

    $query .= " LIMIT 1";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            "error" => "Erro ao validar serviço",
            "details" => $conn->error
        ]);
        return null;
    }

    if ($ignoredId !== null) {
        $stmt->bind_param("si", $name, $ignoredId);
    } else {
        $stmt->bind_param("s", $name);
    }

    $stmt->execute();
    return $stmt->get_result();
}

function createService() {
    if (!requireAdminSession()) {
        return;
    }

    $conn = getConnection();

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao conectar com banco"]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $payload = validateServicePayload($data);

    if (!$payload) {
        return;
    }

    $existing = findExistingServiceByName($conn, $payload['name']);

    if ($existing && $existing->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Já existe um serviço com esse nome"]);
        return;
    }

    $stmt = $conn->prepare("
        INSERT INTO services (name, price, duration)
        VALUES (?, ?, ?)
    ");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            "error" => "Erro ao preparar cadastro do serviço",
            "details" => $conn->error
        ]);
        return;
    }

    $stmt->bind_param("sdi", $payload['name'], $payload['price'], $payload['duration']);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode([
            "error" => "Erro ao salvar serviço",
            "details" => $stmt->error
        ]);
        return;
    }

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "service" => [
            "id" => $stmt->insert_id,
            "name" => $payload['name'],
            "price" => number_format($payload['price'], 2, '.', ''),
            "duration" => $payload['duration']
        ]
    ]);
}

function updateService() {
    if (!requireAdminSession()) {
        return;
    }

    $conn = getConnection();

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao conectar com banco"]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = isset($data['id']) ? (int) $data['id'] : 0;

    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(["error" => "Informe um serviço válido"]);
        return;
    }

    $payload = validateServicePayload($data);

    if (!$payload) {
        return;
    }

    $existsStmt = $conn->prepare("SELECT id FROM services WHERE id = ? LIMIT 1");

    if (!$existsStmt) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao validar serviço"]);
        return;
    }

    $existsStmt->bind_param("i", $id);
    $existsStmt->execute();
    $existsResult = $existsStmt->get_result();

    if (!$existsResult || $existsResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Serviço não encontrado"]);
        return;
    }

    $existing = findExistingServiceByName($conn, $payload['name'], $id);

    if ($existing && $existing->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Já existe um serviço com esse nome"]);
        return;
    }

    $stmt = $conn->prepare("
        UPDATE services
        SET name = ?, price = ?, duration = ?
        WHERE id = ?
    ");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            "error" => "Erro ao preparar atualização do serviço",
            "details" => $conn->error
        ]);
        return;
    }

    $stmt->bind_param("sdii", $payload['name'], $payload['price'], $payload['duration'], $id);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode([
            "error" => "Erro ao atualizar serviço",
            "details" => $stmt->error
        ]);
        return;
    }

    echo json_encode([
        "success" => true,
        "service" => [
            "id" => $id,
            "name" => $payload['name'],
            "price" => number_format($payload['price'], 2, '.', ''),
            "duration" => $payload['duration']
        ]
    ]);
}

function deleteService() {
    if (!requireAdminSession()) {
        return;
    }

    $conn = getConnection();

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao conectar com banco"]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = isset($data['id']) ? (int) $data['id'] : 0;

    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(["error" => "Informe um serviço válido"]);
        return;
    }

    $usageStmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM appointments
        WHERE service_id = ?
    ");

    if (!$usageStmt) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao validar exclusão do serviço"]);
        return;
    }

    $usageStmt->bind_param("i", $id);
    $usageStmt->execute();
    $usageResult = $usageStmt->get_result();
    $usageRow = $usageResult ? $usageResult->fetch_assoc() : null;

    if ((int) ($usageRow['total'] ?? 0) > 0) {
        http_response_code(409);
        echo json_encode([
            "error" => "Não é possível excluir um serviço com agendamentos vinculados"
        ]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            "error" => "Erro ao preparar exclusão do serviço",
            "details" => $conn->error
        ]);
        return;
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode([
            "error" => "Erro ao excluir serviço",
            "details" => $stmt->error
        ]);
        return;
    }

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Serviço não encontrado"]);
        return;
    }

    echo json_encode([
        "success" => true,
        "id" => $id
    ]);
}
