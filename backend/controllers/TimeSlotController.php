<?php

require_once __DIR__ . '/../config/database.php';

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

function getTimeSlots() {
    $conn = getConnection();
    $date = $_GET['date'] ?? date('Y-m-d');

    $morningSlots = generateRangeSlots(8, 0, 12, 0);
    $afternoonSlots = generateRangeSlots(13, 30, 18, 0);
    $expectedSlots = array_merge($morningSlots, $afternoonSlots);

    $timePlaceholders = implode(',', array_fill(0, count($expectedSlots), '?'));

    $querySlots = "
        SELECT id, TIME_FORMAT(time, '%H:%i') AS time
        FROM time_slots
        WHERE TIME_FORMAT(time, '%H:%i') IN ($timePlaceholders)
    ";

    $stmtSlots = $conn->prepare($querySlots);
    $types = str_repeat('s', count($expectedSlots));
    $stmtSlots->bind_param($types, ...$expectedSlots);
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
