// Importa o React e o hook useState para gerenciar os dados digitados
import React, { useState } from 'react';
// Importa a configuração da API para enviar os dados ao backend
import api from '../services/api';
// Importa o componente que busca e exibe a lista de serviços
import ServiceSelector from './ServiceSelector';
// Importa o componente que busca e exibe os horários disponíveis
import TimeSlots from './TimeSlots';

// Declara o componente principal do formulário
function ScheduleForm() {
    // Cria o estado 'formData' para armazenar todas as escolhas do cliente
    const [formData, setFormData] = useState({
        nome_cliente: '',
        id_servico: '',
        id_horario: '',
        data_agendamento: ''
    });

    // Função para atualizar o nome ou a data quando o usuário digita/escolhe no input padrão
    const handleChange = (e) => {
        // Copia os dados atuais e atualiza apenas o campo que disparou o evento
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    // Função específica para receber o ID do serviço escolhido no ServiceSelector
    const handleServiceSelect = (servicoId) => {
        // Atualiza o estado formData alterando apenas o id_servico
        setFormData({ ...formData, id_servico: servicoId });
    };

    // Função específica para receber o ID do horário escolhido no TimeSlots
    const handleTimeSelect = (horarioId) => {
        // Atualiza o estado formData alterando apenas o id_horario
        setFormData({ ...formData, id_horario: horarioId });
    };

    // Função disparada ao clicar no botão de enviar o formulário
    const handleSubmit = async (e) => {
        // Evita que a página recarregue ao enviar o formulário
        e.preventDefault();
        
        // Verifica se o usuário selecionou o serviço e o horário (validação extra no front)
        if (!formData.id_servico || !formData.id_horario) {
            alert("Por favor, selecione um serviço e um horário.");
            return; // Interrompe a função se faltar algo
        }

        try {
            // Envia o estado completo para o backend PHP via POST
            const response = await api.post('/routes/api.php?action=create_appointment', formData);
            // Exibe a mensagem de sucesso retornada pelo servidor
            alert(response.mensagem);
            
            // Opcional: Limpar o formulário após o sucesso para um novo agendamento
            // setFormData({ nome_cliente: '', id_servico: '', id_horario: '', data_agendamento: '' });
        } catch (error) {
            // Caso ocorra um erro de rede ou no servidor, avisa o usuário
            alert('Erro ao tentar agendar. Verifique sua conexão.');
        }
    };

    // Retorna a interface completa do formulário
    return (
        // Define a função handleSubmit para processar o envio
        <form onSubmit={handleSubmit} className="schedule-form">
            <h2>Novo Agendamento</h2>

            {/* Input para o nome do cliente */}
            <div className="form-group">
                <label>Nome Completo:</label>
                <input 
                    type="text" 
                    name="nome_cliente" 
                    value={formData.nome_cliente}
                    onChange={handleChange} 
                    required 
                />
            </div>
            
            {/* Renderiza o componente ServiceSelector passando a função de atualização como propriedade (prop) */}
            <div className="form-group">
                <label>Serviço:</label>
                <ServiceSelector onSelect={handleServiceSelect} />
            </div>
            
            {/* Input de data. Ao escolher uma data, o estado atualiza e engatilha a busca de horários */}
            <div className="form-group">
                <label>Data:</label>
                <input 
                    type="date" 
                    name="data_agendamento" 
                    value={formData.data_agendamento}
                    onChange={handleChange} 
                    required 
                />
            </div>
            
            {/* Renderiza o TimeSlots apenas se uma data já tiver sido selecionada no input acima */}
            <div className="form-group">
                <label>Horário:</label>
                {formData.data_agendamento ? (
                    <TimeSlots 
                        dataSelecionada={formData.data_agendamento} 
                        onSelect={handleTimeSelect} 
                    />
                ) : (
                    <p className="hint">Escolha uma data para ver os horários.</p>
                )}
            </div>
            
            {/* Botão final para submeter os dados */}
            <button type="submit" className="btn-submit">Confirmar Agendamento</button>
        </form>
    );
}

// Exporta o formulário pronto para ser colocado na sua página principal (Home.jsx)
export default ScheduleForm;