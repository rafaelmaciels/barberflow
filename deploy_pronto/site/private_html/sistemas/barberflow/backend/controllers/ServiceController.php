<?php

require_once __DIR__ . '/../config/database.php';

function getServices() {

    //  CORREÇÃO: criar conexão corretamente
    $conn = getConnection();

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao conectar com banco"]);
        return;
    }

    $result = $conn->query("SELECT * FROM services");

    if (!$result) {
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