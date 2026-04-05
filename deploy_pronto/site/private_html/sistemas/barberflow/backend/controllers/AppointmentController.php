<?php

require_once __DIR__ . '/../config/database.php';

function isSundayAppointmentDate($date) {
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    return $dateObj && $dateObj->format('w') === '0';
}

// =========================
// CRIAR AGENDAMENTO
// =========================
function createAppointment() {

    $conn = getConnection();

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["error" => "JSON inválido"]);
        return;
    }

    // 🔥 mantém padrão frontend (PT-BR)
    $name = $data['nome_cliente'] ?? null;
    $service_id = $data['id_servico'] ?? null;
    $time_slot_id = $data['id_horario'] ?? null;
    $date = $data['data_agendamento'] ?? null;

    if (!$name || !$service_id || !$time_slot_id || !$date) {
        http_response_code(400);
        echo json_encode([
            "error" => "Dados incompletos",
            "debug" => $data
        ]);
        return;
    }

    if (isSundayAppointmentDate($date)) {
        http_response_code(403);
        echo json_encode(["error" => "FECHADO!"]);
        return;
    }

    // 🔥 verifica se está aberto
    $settings = $conn->query("SELECT barbershop_open FROM settings LIMIT 1");

    if ($settings && $settings->num_rows > 0) {
        $row = $settings->fetch_assoc();

        if (!$row['barbershop_open']) {
            http_response_code(403);
            echo json_encode(["error" => "Barbearia fechada"]);
            return;
        }
    }

    // 🔥 verifica conflito de horário (NOMES EM INGLÊS)
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

    // 🔥 INSERT CORRETO (INGLÊS)
    $stmt = $conn->prepare("
        INSERT INTO appointments 
        (client_name, service_id, time_slot_id, appointment_date, status)
        VALUES (?, ?, ?, ?, 'agendado')
    ");

    $stmt->bind_param("siis", $name, $service_id, $time_slot_id, $date);

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

// =========================
// LISTAR AGENDAMENTOS
// =========================
function getAppointments() {

    $conn = getConnection();

    $date = $_GET['date'] ?? date('Y-m-d');

    // 🔥 SELECT CORRIGIDO (INGLÊS)
    $stmt = $conn->prepare("
        SELECT 
            a.id,
            a.client_name,
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

// =========================
// CONFIGURAÇÕES
// =========================
function getSettings() {

    $conn = getConnection();

    $result = $conn->query("SELECT barbershop_open FROM settings LIMIT 1");

    $data = $result->fetch_assoc();

    echo json_encode($data);
}

function updateSettings() {

    $conn = getConnection();

    $data = json_decode(file_get_contents("php://input"), true);

    $open = $data['barbershop_open'] ? 1 : 0;

    $stmt = $conn->prepare("UPDATE settings SET barbershop_open = ?");
    $stmt->bind_param("i", $open);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Configurações atualizadas"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar"]);
    }
}

// =========================
// CANCELAR AGENDAMENTO
// =========================
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

// =========================
// DASHBOARD
// =========================
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
