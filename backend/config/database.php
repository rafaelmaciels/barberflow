<?php

function getConnection() {
    $host = 'localhost'; // 🔥 ALTERE PARA O HOST DO SEU SERVIDOR MYSQL
    $db   = 'barberflow'; // 🔥 ALTERE PARA O NOME DO BANCO CRIADO
    $user = 'usuario_mysql'; // 🔥 ALTERE PARA SEU USUÁRIO MYSQL
    $pass = 'senha_mysql';   // 🔥 ALTERE PARA SUA SENHA MYSQL
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