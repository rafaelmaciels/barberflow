<?php
// Define os cabeçalhos CORS para permitir que o React se comunique com o PHP
header("Access-Control-Allow-Origin: *");
// Define que o conteúdo retornado sempre será no formato JSON
header("Content-Type: application/json; charset=UTF-8");
// Permite requisições dos tipos POST e GET
header("Access-Control-Allow-Methods: POST, GET");

// Inclui o arquivo do controlador de agendamentos que criamos anteriormente
include_once '../controllers/AppointmentController.php';

// Instancia o controlador para podermos usar suas funções
$appointmentController = new AppointmentController();

// Verifica se existe um parâmetro 'action' na URL (ex: api.php?action=create_appointment)
// Se não existir, define 'none' como valor padrão para evitar erros
$action = isset($_GET['action']) ? $_GET['action'] : 'none';

// Usa a estrutura switch para decidir qual rota executar
switch($action) {
    // Caso a ação solicitada seja criar um agendamento
    case 'create_appointment':
        // Chama a função store() do AppointmentController
        $appointmentController->store();
        // Encerra a execução deste bloco switch
        break;
        
    // Caso a ação não seja reconhecida pelas opções acima
    default:
        // Retorna um código HTTP 404 (Não Encontrado)
        http_response_code(404);
        // Devolve um JSON informando que a rota não existe
        echo json_encode(["mensagem" => "Rota não encontrada."]);
        // Encerra a execução
        break;
}
?>