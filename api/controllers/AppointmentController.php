<?php
// Inclui os arquivos necessários de configuração e modelo
include_once '../config/database.php';
include_once '../models/Appointment.php';

class AppointmentController {
    // Função principal para processar a requisição de criação
    public function store() {
        // Instancia a classe Database
        $database = new Database();
        // Obtém a conexão chamando o método getConnection
        $db = $database->getConnection();
        
        // Instancia o model Appointment passando a conexão
        $appointment = new Appointment($db);
        
        // Lê os dados JSON enviados no corpo da requisição pelo frontend
        $data = json_decode(file_get_contents("php://input"));

        // Verifica se todos os campos obrigatórios foram enviados
        if(!empty($data->nome_cliente) && !empty($data->id_servico) && !empty($data->id_horario) && !empty($data->data_agendamento)) {
            
            // Chama a função create do model passando os dados recebidos
            if($appointment->create($data->nome_cliente, $data->id_servico, $data->id_horario, $data->data_agendamento)) {
                // Se der certo, define o status HTTP como 201 (Criado)
                http_response_code(201);
                // Retorna um JSON confirmando o sucesso
                echo json_encode(["mensagem" => "Agendamento criado com sucesso."]);
            } else {
                // Se falhar no banco, define status 503 (Serviço Indisponível)
                http_response_code(503);
                // Retorna a mensagem de erro
                echo json_encode(["mensagem" => "Não foi possível criar o agendamento."]);
            }
        } else {
            // Se faltarem dados, define status 400 (Requisição Ruim)
            http_response_code(400);
            // Avisa o cliente sobre os dados incompletos
            echo json_encode(["mensagem" => "Dados incompletos."]);
        }
    }
}
?>