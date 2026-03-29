<?php

function getConnection() {
    $host = '127.0.0.1'; // 🔥 SEMPRE isso
    $db   = 'barberflow';
    $user = 'root'; // ← do Beekeeper
    $pass = 'pass';   // ← do Beekeeper
    $port = 3306;

    $conn = new mysqli($host, $user, $pass, $db, $port);

    if ($conn->connect_error) {
        die(json_encode([
            "error" => "Erro na conexão",
            "details" => $conn->connect_error
        ]));
    }

    return $conn;
}