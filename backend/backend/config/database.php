<?php

function getConnection() {
    $host = 'localhost';
    $db   = 'rafaelm1_barberflow';
    $user = 'rafaelm1_barberflow';
    $pass = 'wmMCG5qpZMtDdqDX8rvE';
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