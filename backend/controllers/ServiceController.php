<?php

require_once __DIR__ . '/../config/database.php';

function getServices() {
    global $conn;

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "Conexão com banco não inicializada"]);
        return;
    }

    $result = $conn->query("SELECT * FROM services");

    $services = [];

    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }

    echo json_encode($services);
}