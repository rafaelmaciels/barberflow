<?php
class Appointment {
    // Armazena a conexão com o banco
    private $conn;
    // Define o nome da tabela no banco de dados
    private $table_name = "agendamentos";

    // O construtor recebe a conexão e a atribui à propriedade da classe
    public function __construct($db) {
        $this->conn = $db;
    }

    // Função para criar um novo agendamento
    public function create($nome_cliente, $id_servico, $id_horario, $data_agendamento) {
        // Cria a string da query SQL de inserção com parâmetros nomeados (segurança contra SQL Injection)
        $query = "INSERT INTO " . $this->table_name . " (nome_cliente, id_servico, id_horario, data_agendamento) VALUES (:nome, :servico, :horario, :data)";
        
        // Prepara a query no banco de dados
        $stmt = $this->conn->prepare($query);

        // Faz o bind (vínculo) do valor recebido na variável com o parâmetro da query
        $stmt->bindParam(":nome", $nome_cliente);
        $stmt->bindParam(":servico", $id_servico);
        $stmt->bindParam(":horario", $id_horario);
        $stmt->bindParam(":data", $data_agendamento);

        // Executa a query e verifica se foi bem sucedida
        if($stmt->execute()) {
            // Retorna verdadeiro se inseriu com sucesso
            return true;
        }
        // Retorna falso se houve algum erro na inserção
        return false;
    }
}
?>