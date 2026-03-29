<?php

function getConnection() {
    $host = '127.0.0.1'; // 🔥 melhor que localhost
    $port = 3306;        // 🔥 explícito
    $db   = 'barberflow';
    $user = 'root';
    $pass = 'pass'; // ajuste se sua senha for diferente

    $conn = new mysqli($host, $user, $pass, $db, $port);

    if ($conn->connect_error) {
        die(json_encode([
            "error" => "Erro na conexão com banco",
            "details" => $conn->connect_error
        ]));
    }

    return $conn;
}