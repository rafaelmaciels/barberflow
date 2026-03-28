// Importa o React e os hooks necessários
import React, { useState, useEffect } from 'react';
// Importa a configuração da API
import api from '../services/api';

// Declara o componente recebendo a data escolhida e a função para capturar o clique no horário
function TimeSlots({ dataSelecionada, onSelect }) {
    // Cria um estado para guardar os horários que vieram do banco
    const [horarios, setHorarios] = useState([]);

    // O useEffect vai rodar sempre que a variável 'dataSelecionada' mudar de valor
    useEffect(() => {
        // Se o usuário ainda não escolheu uma data, interrompe a função e não busca nada
        if (!dataSelecionada) return;

        // Cria a função para buscar os horários disponíveis
        const fetchHorarios = async () => {
            try {
                // Envia uma requisição GET passando a data escolhida na URL para filtrar no backend
                const response = await fetch(`${api.baseURL}/routes/api.php?action=get_timeslots&date=${dataSelecionada}`);
                // Converte a resposta para JSON
                const data = await response.json();
                // Salva os horários disponíveis no estado do componente
                setHorarios(data);
            } catch (error) {
                // Mostra um erro no console caso o servidor não responda
                console.error("Erro ao buscar horários:", error);
            }
        };

        // Executa a função de busca
        fetchHorarios();
    // A dependência [dataSelecionada] avisa ao React para re-executar o useEffect se a data for alterada
    }, [dataSelecionada]);

    // Retorna a visualização dos botões
    return (
        // Cria uma div para agrupar os botões de horário
        <div className="time-slots-container">
            {/* Faz uma verificação condicional: se houver horários, mostra os botões. Se não, mostra um aviso. */}
            {horarios.length > 0 ? (
                // Percorre a lista de horários disponíveis
                horarios.map((horario) => (
                    // Cria um botão para cada horário
                    <button 
                        key={horario.id} 
                        type="button" 
                        // Ao clicar no botão, chama a função onSelect passando o ID daquele horário
                        onClick={() => onSelect(horario.id)}
                    >
                        {/* Exibe a hora formatada no botão (ex: 14:00) */}
                        {horario.hora}
                    </button>
                ))
            ) : (
                // Parágrafo exibido se o array de horários vier vazio
                <p>Nenhum horário disponível para esta data.</p>
            )}
        </div>
    );
}

// Exporta o componente
export default TimeSlots;