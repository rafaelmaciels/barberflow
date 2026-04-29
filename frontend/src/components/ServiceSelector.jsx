import React, { useState, useEffect } from 'react';
import api from '../services/api';

function ServiceSelector({ onSelect, value }) {
    const [servicos, setServicos] = useState([]);

    useEffect(() => {
        const fetchServicos = async () => {
            try {
                const response = await api.getServices();
                const data = await response.json();
                setServicos(data);
            } catch (error) {
                console.error("Erro ao buscar serviços:", error);
            }
        };

        fetchServicos();
    }, []);

    return (
        <select
            name="id_servico"
            value={value}
            onChange={(e) => onSelect(e.target.value)}
            required
        >
            <option value="">Selecione um serviço</option>

            {servicos.map((servico) => (
                <option key={servico.id} value={servico.id}>
                    {servico.name} - R$ {servico.price}
                </option>
            ))}
        </select>
    );
}

export default ServiceSelector;