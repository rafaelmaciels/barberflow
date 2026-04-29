<?php

require_once __DIR__ . '/../config/database.php';

function isSundayScheduleDate($date) {
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    return $dateObj && $dateObj->format('w') === '0';
}

function formatSlotTime($minutes) {
    $hours = str_pad((string) floor($minutes / 60), 2, '0', STR_PAD_LEFT);
    $mins = str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT);
    return $hours . ':' . $mins;
}

function getDefaultTimeSlotList() {
    $morning = [];
    $afternoon = [];

    for ($current = (8 * 60); $current <= (12 * 60); $current += 30) {
        $morning[] = formatSlotTime($current);
    }

    for ($current = ((13 * 60) + 30); $current <= (18 * 60); $current += 30) {
        $afternoon[] = formatSlotTime($current);
    }

    return array_merge($morning, $afternoon);
}

function runDailyResetIfNeeded($conn) {
    $today = date('Y-m-d');
    $resetFlagPath = sys_get_temp_dir() . '/barberflow_daily_reset_' . $today . '.flag';

    if (file_exists($resetFlagPath)) {
        return;
    }

    $stmtReset = $conn->prepare("
        UPDATE appointments
        SET status = 'cancelado'
        WHERE appointment_date < ?
        AND status = 'agendado'
    ");
    $stmtReset->bind_param('s', $today);
    $stmtReset->execute();

    file_put_contents($resetFlagPath, date('c'));
}

function ensureDefaultTimeSlotsIfEmpty($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM time_slots");
    $row = $result ? $result->fetch_assoc() : null;

    if ((int) ($row['total'] ?? 0) > 0) {
        return;
    }

    $defaultSlots = getDefaultTimeSlotList();
    $stmt = $conn->prepare("INSERT INTO time_slots (time) VALUES (?)");

    foreach ($defaultSlots as $slotTime) {
        $stmt->bind_param('s', $slotTime);
        $stmt->execute();
    }
}

function validateTimeSlotValue($value) {
    $time = trim((string) $value);

    if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time) !== 1) {
        http_response_code(422);
        echo json_encode(["error" => "Informe um horário válido no formato HH:MM"]);
        return null;
    }

    return $time;
}

function findTimeSlotByTime($conn, $time, $ignoredId = null) {
    $query = "SELECT id FROM time_slots WHERE TIME_FORMAT(time, '%H:%i') = ?";

    if ($ignoredId !== null) {
        $query .= " AND id <> ?";
    }

    $query .= " LIMIT 1";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao validar horário"]);
        return null;
    }

    if ($ignoredId !== null) {
        $stmt->bind_param('si', $time, $ignoredId);
    } else {
        $stmt->bind_param('s', $time);
    }

    $stmt->execute();
    return $stmt->get_result();
}

function getAdminTimeSlots() {
    if (!requireAdminSession()) {
        return;
    }

    $conn = getConnection();
    ensureDefaultTimeSlotsIfEmpty($conn);

    $result = $conn->query("
        SELECT id, TIME_FORMAT(time, '%H:%i') AS time
        FROM time_slots
        ORDER BY time
    ");

    $slots = [];

    while ($row = $result->fetch_assoc()) {
        $slots[] = [
            "id" => (int) $row['id'],
            "time" => $row['time']
        ];
    }

    echo json_encode($slots);
}

function createTimeSlot() {
    if (!requireAdminSession()) {
        return;
    }

    $conn = getConnection();
    ensureDefaultTimeSlotsIfEmpty($conn);

    $data = json_decode(file_get_contents("php://input"), true);
    $time = validateTimeSlotValue($data['time'] ?? '');

    if (!$time) {
        return;
    }

    $existing = findTimeSlotByTime($conn, $time);

    if ($existing && $existing->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Esse horário já existe"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO time_slots (time) VALUES (?)");
    $stmt->bind_param('s', $time);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao salvar horário"]);
        return;
    }

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "slot" => [
            "id" => $stmt->insert_id,
            "time" => $time
        ]
    ]);
}

function updateTimeSlot() {
    if (!requireAdminSession()) {
        return;
    }

    $conn = getConnection();
    ensureDefaultTimeSlotsIfEmpty($conn);

    $data = json_decode(file_get_contents("php://input"), true);
    $id = isset($data['id']) ? (int) $data['id'] : 0;
    $time = validateTimeSlotValue($data['time'] ?? '');

    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(["error" => "Informe um horário válido"]);
        return;
    }

    if (!$time) {
        return;
    }

    $existsStmt = $conn->prepare("SELECT id FROM time_slots WHERE id = ? LIMIT 1");
    $existsStmt->bind_param('i', $id);
    $existsStmt->execute();
    $existsResult = $existsStmt->get_result();

    if (!$existsResult || $existsResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Horário não encontrado"]);
        return;
    }

    $existing = findTimeSlotByTime($conn, $time, $id);

    if ($existing && $existing->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Esse horário já existe"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE time_slots SET time = ? WHERE id = ?");
    $stmt->bind_param('si', $time, $id);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar horário"]);
        return;
    }

    echo json_encode([
        "success" => true,
        "slot" => [
            "id" => $id,
            "time" => $time
        ]
    ]);
}

function deleteTimeSlot() {
    if (!requireAdminSession()) {
        return;
    }

    $conn = getConnection();
    $data = json_decode(file_get_contents("php://input"), true);
    $id = isset($data['id']) ? (int) $data['id'] : 0;

    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(["error" => "Informe um horário válido"]);
        return;
    }

    $usageStmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM appointments
        WHERE time_slot_id = ?
    ");
    $usageStmt->bind_param('i', $id);
    $usageStmt->execute();
    $usageResult = $usageStmt->get_result();
    $usageRow = $usageResult ? $usageResult->fetch_assoc() : null;

    if ((int) ($usageRow['total'] ?? 0) > 0) {
        http_response_code(409);
        echo json_encode([
            "error" => "Nao e possivel excluir um horario com agendamentos vinculados"
        ]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM time_slots WHERE id = ?");
    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao excluir horário"]);
        return;
    }

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Horário não encontrado"]);
        return;
    }

    echo json_encode([
        "success" => true,
        "id" => $id
    ]);
}

function getTimeSlots() {
    $conn = getConnection();
    $date = $_GET['date'] ?? date('Y-m-d');

    runDailyResetIfNeeded($conn);
    ensureBlockedDatesTable($conn);

    if (!isValidScheduleDate($date) || $date < date('Y-m-d')) {
        echo json_encode([]);
        return;
    }

    if (isSundayScheduleDate($date)) {
        echo json_encode([]);
        return;
    }

    if (isBlockedScheduleDate($conn, $date)) {
        echo json_encode([]);
        return;
    }

    ensureDefaultTimeSlotsIfEmpty($conn);

    $resultSlots = $conn->query("
        SELECT id, TIME_FORMAT(time, '%H:%i') AS time
        FROM time_slots
        ORDER BY time
    ");

    $slotMapById = [];
    $orderedSlotIds = [];

    while ($row = $resultSlots->fetch_assoc()) {
        $slotId = (int) $row['id'];
        $slotMapById[$slotId] = [
            'id' => $slotId,
            'time' => $row['time']
        ];
        $orderedSlotIds[] = $slotId;
    }

    $stmtBusy = $conn->prepare("
        SELECT time_slot_id
        FROM appointments
        WHERE appointment_date = ?
        AND status = 'agendado'
    ");

    $stmtBusy->bind_param('s', $date);
    $stmtBusy->execute();
    $resultBusy = $stmtBusy->get_result();

    $busySlotIds = [];
    while ($row = $resultBusy->fetch_assoc()) {
        $busySlotIds[(int) $row['time_slot_id']] = true;
    }

    $slots = [];

    foreach ($orderedSlotIds as $slotId) {
        $slot = $slotMapById[$slotId];
        $hasPassedToday = $date === date('Y-m-d') && $slot['time'] <= date('H:i');
        $slots[] = [
            'id' => $slot['id'],
            'time' => $slot['time'],
            'available' => !isset($busySlotIds[$slot['id']]) && !$hasPassedToday
        ];
    }

    echo json_encode($slots);
}
