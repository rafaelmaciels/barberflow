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

function generateRangeSlots($startHour, $startMinute, $endHour, $endMinute, $stepMinutes = 30) {
    $start = ($startHour * 60) + $startMinute;
    $end = ($endHour * 60) + $endMinute;
    $slots = [];

    for ($current = $start; $current <= $end; $current += $stepMinutes) {
        $slots[] = formatSlotTime($current);
    }

    return $slots;
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

function ensureTimeSlotsExist($conn, $expectedSlots) {
    $resultExisting = $conn->query("
        SELECT TIME_FORMAT(time, '%H:%i') AS time
        FROM time_slots
    ");

    $existingMap = [];
    while ($row = $resultExisting->fetch_assoc()) {
        $existingMap[$row['time']] = true;
    }

    $stmtInsert = $conn->prepare("
        INSERT INTO time_slots (time)
        VALUES (?)
    ");

    foreach ($expectedSlots as $slotTime) {
        if (isset($existingMap[$slotTime])) {
            continue;
        }

        $stmtInsert->bind_param('s', $slotTime);
        $stmtInsert->execute();
    }
}

function getTimeSlots() {
    $conn = getConnection();
    $date = $_GET['date'] ?? date('Y-m-d');

    runDailyResetIfNeeded($conn);

    if (isSundayScheduleDate($date)) {
        echo json_encode([]);
        return;
    }

    $morningSlots = generateRangeSlots(8, 0, 12, 0);
    $afternoonSlots = generateRangeSlots(13, 30, 18, 0);
    $expectedSlots = array_merge($morningSlots, $afternoonSlots);

    ensureTimeSlotsExist($conn, $expectedSlots);

    $querySlots = "
        SELECT id, TIME_FORMAT(time, '%H:%i') AS time
        FROM time_slots
    ";

    $stmtSlots = $conn->prepare($querySlots);
    $stmtSlots->execute();
    $resultSlots = $stmtSlots->get_result();

    $slotMapByTime = [];
    while ($row = $resultSlots->fetch_assoc()) {
        $slotMapByTime[$row['time']] = [
            'id' => (int) $row['id'],
            'time' => $row['time']
        ];
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
    foreach ($expectedSlots as $slotTime) {
        if (!isset($slotMapByTime[$slotTime])) {
            continue;
        }

        $slot = $slotMapByTime[$slotTime];
        $slots[] = [
            'id' => $slot['id'],
            'time' => $slot['time'],
            'available' => !isset($busySlotIds[$slot['id']])
        ];
    }

    echo json_encode($slots);
}
