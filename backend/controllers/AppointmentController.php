<?php

require_once "config/database.php";

function createAppointment() {
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);

    $name = $conn->real_escape_string($data['client_name'] ?? '');
    $service_id = intval($data['service_id'] ?? 0);
    $time_slot_id = intval($data['time_slot_id'] ?? 0);
    $date = $data['date'] ?? '';

    // Validação básica
    if (!$name || !$service_id || !$time_slot_id || !$date) {
        http_response_code(400);
        echo json_encode(["error" => "Dados inválidos"]);
        return;
    }

    // Verifica se barbearia está aberta
    $settings = $conn->query("SELECT barbershop_open FROM settings LIMIT 1")->fetch_assoc();

    if (!$settings['barbershop_open']) {
        http_response_code(403);
        echo json_encode(["error" => "Barbearia fechada"]);
        return;
    }

    // Verifica se horário já está ocupado
    $check = $conn->query("
        SELECT id FROM appointments
        WHERE time_slot_id = $time_slot_id
        AND appointment_date = '$date'
        AND status = 'agendado'
    ");

    if ($check->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Horário já ocupado"]);
        return;
    }

    // Inserir agendamento
    $insert = $conn->query("
        INSERT INTO appointments (client_name, service_id, time_slot_id, appointment_date)
        VALUES ('$name', $service_id, $time_slot_id, '$date')
    ");

    if ($insert) {
        echo json_encode(["success" => "Agendamento realizado"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao salvar"]);
    }
}

function getAppointments() {
    global $conn;

    $date = $_GET['date'] ?? date('Y-m-d');

    $query = "
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
        WHERE a.appointment_date = '$date'
        AND a.status = 'agendado'
        ORDER BY t.time
    ";

    $result = $conn->query($query);

    $appointments = [];

    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }

    echo json_encode($appointments);
}

function getSettings() {
    global $conn;

    $result = $conn->query("SELECT barbershop_open FROM settings LIMIT 1");
    $data = $result->fetch_assoc();

    echo json_encode($data);
}

function updateSettings() {
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);
    $open = $data['barbershop_open'] ? 1 : 0;

    $update = $conn->query("UPDATE settings SET barbershop_open = $open");

    if ($update) {
        echo json_encode(["success" => "Configurações atualizadas"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar"]);
    }
}

// Função para cancelar um agendamento existente
function cancelAppointment() {
    global $conn;

    // Captura o corpo da requisição (JSON enviado pelo React)
    $data = json_decode(file_get_contents("php://input"), true);

    // Pega o ID do agendamento que será cancelado
    $id = $data['id'] ?? null;

    // Valida se o ID foi enviado
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "ID do agendamento é obrigatório"]);
        return;
    }

    // Atualiza o status do agendamento para "cancelado"
    $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelado' WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Executa a query
    if ($stmt->execute()) {
        echo json_encode(["success" => "Agendamento cancelado com sucesso"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao cancelar agendamento"]);
    }
}

