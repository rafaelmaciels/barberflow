<?php

function getConnection() {
    $host = 'localhost';
    $db   = 'rafaelm1_barberflow';
    $user = 'rafaelm1_barberflow';
    $pass = 'zYVdmNrVcTGCAJUZGJP4';
    $port = 3306;

    $conn = new mysqli($host, $user, $pass, $db, $port);
    $conn->set_charset('utf8mb4');

    if ($conn->connect_error) {
        die(json_encode([
            "error" => "Erro na conexão",
            "details" => $conn->connect_error
        ]));
    }

    return $conn;
}
