<?php

require_once "config/database.php";

function getTimeSlots() {
    global $conn;

    $date = $_GET['date'] ?? date('Y-m-d');

    $query = "
        SELECT ts.id, ts.time,
        CASE 
            WHEN a.id IS NULL THEN 1
            ELSE 0
        END AS available
        FROM time_slots ts
        LEFT JOIN appointments a
        ON ts.id = a.time_slot_id
        AND a.appointment_date = '$date'
        AND a.status = 'agendado'
    ";

    $result = $conn->query($query);

    $slots = [];

    while ($row = $result->fetch_assoc()) {
        $slots[] = $row;
    }

    echo json_encode($slots);
}