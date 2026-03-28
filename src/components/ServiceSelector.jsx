// Importa o React e os hooks useState (para guardar dados) e useEffect (para rodar código ao carregar)
import React, { useState, useEffect } from 'react';
// Importa a configuração da API para fazer a chamada ao backend
import api from '../services/api';

// Declara o componente recebendo a função 'onSelect' como propriedade do componente pai
function ServiceSelector({ onSelect }) {
    // Cria um estado chamado 'servicos' iniciando como um array vazio
    const [servicos, setServicos] = useState([]);

    // O useEffect executa o código interno logo que o componente é exibido na tela
    useEffect(() => {
        // Cria uma função assíncrona para buscar os dados no banco
        const fetchServicos = async () => {
            try {
                // Faz uma requisição GET para a rota que lista os serviços (assumindo que você crie essa rota no PHP)
                const response = await fetch(`${api.baseURL}/routes/api.php?action=get_services`);
                // Converte a resposta do PHP para um objeto JavaScript
                const data = await response.json();
                // Atualiza o estado da tela com os serviços encontrados
                setServicos(data);
            } catch (error) {
                // Se der erro de conexão, exibe o aviso no console do navegador
                console.error("Erro ao buscar serviços:", error);
            }
        };
        // Chama a função de busca imediatamente
        fetchServicos();
    // O array vazio no final garante que essa busca aconteça apenas uma vez, na primeira renderização
    }, []);

    // Retorna a estrutura visual do componente
    return (
        // Cria o campo select. Quando o usuário escolhe uma opção, chama o onSelect passando o ID do serviço
        <select name="id_servico" onChange={(e) => onSelect(e.target.value)} required>
            {/* Primeira opção padrão, desabilitada, serve apenas como instrução */}
            <option value="">Selecione um serviço</option>
            
            {/* Percorre o array de serviços e cria uma tag <option> para cada item */}
            {servicos.map((servico) => (
                // A key é obrigatória no React para listas. O value é o ID que será salvo no banco
                <option key={servico.id} value={servico.id}>
                    {/* Mostra o nome e o preço do serviço para o usuário */}
                    {servico.nome} - R$ {servico.preco}
                </option>
            ))}
        </select>
    );
}

// Libera o componente para ser importado em outros arquivos
export default ServiceSelector;