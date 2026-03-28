<?php
class Database {
    // Define as credenciais do banco de dados como propriedades privadas
    private $host = "localhost";
    private $db_name = "barbearia_db";
    private $username = "root";
    private $password = "";
    // Propriedade para armazenar a instância da conexão
    public $conn;

    // Função responsável por estabelecer e retornar a conexão
    public function getConnection() {
        // Inicializa a conexão como nula
        $this->conn = null;
        try {
            // Instancia um novo objeto PDO com os dados de acesso
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Configura o PDO para lançar exceções em caso de erro (ajuda no debug)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Em caso de falha, exibe a mensagem de erro
            echo "Erro de conexão: " . $exception->getMessage();
        }
        // Retorna a conexão ativa para ser usada em outros arquivos
        return $this->conn;
    }
}
?>