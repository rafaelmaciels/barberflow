<?php

require_once __DIR__ . '/../config/database.php';

function isSundayAppointmentDate($date) {
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    return $dateObj && $dateObj->format('w') === '0';
}

function ensureAppointmentPhoneColumn($conn) {
    $result = $conn->query("SHOW COLUMNS FROM appointments LIKE 'client_phone'");

    if ($result && $result->num_rows > 0) {
        return;
    }

    $conn->query("
        ALTER TABLE appointments
        ADD COLUMN client_phone VARCHAR(20) NULL AFTER client_name
    ");
}

function normalizeClientPhone($phone) {
    $digits = preg_replace('/\D+/', '', (string) $phone);

    if (strlen($digits) < 10 || strlen($digits) > 13) {
        return null;
    }

    return $digits;
}

function createAppointment() {
    $conn = getConnection();
    ensureAppointmentPhoneColumn($conn);
    ensureBlockedDatesTable($conn);

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["error" => "JSON inválido"]);
        return;
    }

    $name = trim((string) ($data['nome_cliente'] ?? ''));
    $phone = normalizeClientPhone($data['telefone_cliente'] ?? null);
    $service_id = $data['id_servico'] ?? null;
    $time_slot_id = $data['id_horario'] ?? null;
    $date = $data['data_agendamento'] ?? null;

    if (!$name || !$phone || !$service_id || !$time_slot_id || !$date) {
        http_response_code(400);
        echo json_encode([
            "error" => "Dados incompletos",
            "debug" => $data
        ]);
        return;
    }

    if (!isValidScheduleDate($date) || $date < date('Y-m-d')) {
        http_response_code(422);
        echo json_encode(["error" => "A data do agendamento é inválida"]);
        return;
    }

    if (isSundayAppointmentDate($date)) {
        http_response_code(403);
        echo json_encode(["error" => "FECHADO!"]);
        return;
    }

    if (isBlockedScheduleDate($conn, $date)) {
        http_response_code(403);
        echo json_encode(["error" => "Esta data não está disponível para agendamento"]);
        return;
    }

    $settings = $conn->query("SELECT barbershop_open FROM settings LIMIT 1");

    if ($settings && $settings->num_rows > 0) {
        $row = $settings->fetch_assoc();

        if (!$row['barbershop_open']) {
            http_response_code(403);
            echo json_encode(["error" => "Barbearia fechada"]);
            return;
        }
    }

    $stmt = $conn->prepare("
        SELECT id FROM appointments
        WHERE time_slot_id = ?
        AND appointment_date = ?
        AND status = 'agendado'
    ");

    $stmt->bind_param("is", $time_slot_id, $date);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Horário já ocupado"]);
        return;
    }

    $stmtSlot = $conn->prepare("
        SELECT TIME_FORMAT(time, '%H:%i') AS time
        FROM time_slots
        WHERE id = ?
        LIMIT 1
    ");
    $stmtSlot->bind_param("i", $time_slot_id);
    $stmtSlot->execute();
    $slotResult = $stmtSlot->get_result();
    $slotRow = $slotResult ? $slotResult->fetch_assoc() : null;

    if (!$slotRow) {
        http_response_code(404);
        echo json_encode(["error" => "Horário não encontrado"]);
        return;
    }

    if ($date === date('Y-m-d') && $slotRow['time'] <= date('H:i')) {
        http_response_code(409);
        echo json_encode(["error" => "Esse horário já passou"]);
        return;
    }

    $stmt = $conn->prepare("
        INSERT INTO appointments
        (client_name, client_phone, service_id, time_slot_id, appointment_date, status)
        VALUES (?, ?, ?, ?, ?, 'agendado')
    ");

    $stmt->bind_param("ssiis", $name, $phone, $service_id, $time_slot_id, $date);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Agendamento realizado com sucesso"]);
    } else {
        http_response_code(500);
        echo json_encode([
            "error" => "Erro ao salvar",
            "details" => $stmt->error
        ]);
    }
}

function getAppointments() {
    $conn = getConnection();
    ensureAppointmentPhoneColumn($conn);

    $date = $_GET['date'] ?? date('Y-m-d');

    $stmt = $conn->prepare("
        SELECT
            a.id,
            a.client_name,
            a.client_phone,
            a.appointment_date,
            s.name AS service,
            s.price,
            t.time
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        JOIN time_slots t ON a.time_slot_id = t.id
        WHERE a.appointment_date = ?
        AND a.status = 'agendado'
        ORDER BY t.time
    ");

    $stmt->bind_param("s", $date);
    $stmt->execute();

    $result = $stmt->get_result();
    $appointments = [];

    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }

    echo json_encode($appointments);
}

function getSettings() {
    $conn = getConnection();
    $result = $conn->query("SELECT barbershop_open FROM settings LIMIT 1");
    $data = $result->fetch_assoc();
    echo json_encode($data);
}

function updateSettings() {
    $conn = getConnection();
    $data = json_decode(file_get_contents("php://input"), true);
    $open = !empty($data['barbershop_open']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE settings SET barbershop_open = ?");
    $stmt->bind_param("i", $open);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Configurações atualizadas"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar"]);
    }
}

function cancelAppointment() {
    $conn = getConnection();
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "ID obrigatório"]);
        return;
    }

    $stmt = $conn->prepare("
        UPDATE appointments
        SET status = 'cancelado'
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Agendamento cancelado"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao cancelar"]);
    }
}

function getDashboard() {
    $conn = getConnection();
    $date = $_GET['date'] ?? date('Y-m-d');

    $stmt = $conn->prepare("
        SELECT
            COUNT(a.id) AS total,
            SUM(s.price) AS faturamento
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        WHERE a.appointment_date = ?
        AND a.status = 'agendado'
    ");

    $stmt->bind_param("s", $date);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode([
        "total" => intval($data['total']),
        "faturamento" => floatval($data['faturamento'] ?? 0)
    ]);
}
