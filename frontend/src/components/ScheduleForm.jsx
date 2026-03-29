import React, { useState } from 'react';
import api from '../services/api';
import ServiceSelector from './ServiceSelector';
import TimeSlots from './TimeSlots';

function ScheduleForm() {
    const [formData, setFormData] = useState({
        client_name: '',
        service_id: '',
        time_slot_id: '',
        date: ''
    });

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleServiceSelect = (serviceId) => {
        setFormData({ ...formData, service_id: serviceId });
    };

    const handleTimeSelect = (timeId) => {
        setFormData({ ...formData, time_slot_id: timeId });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!formData.service_id || !formData.time_slot_id) {
            alert("Selecione um serviço e um horário.");
            return;
        }

        try {
            const response = await fetch(`${api.baseURL}/appointments`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            alert(data.success || data.error);

            // Atualiza horários depois do agendamento
            setFormData({
                client_name: '',
                service_id: '',
                time_slot_id: '',
                date: ''
            });

        } catch (error) {
            alert("Erro ao agendar. Verifique o servidor.");
        }
    };

    return (
        <form onSubmit={handleSubmit} className="schedule-form">
            <h2>Novo Agendamento</h2>

            <div className="form-group">
                <label>Nome Completo:</label>
                <input
                    type="text"
                    name="client_name"
                    value={formData.client_name}
                    onChange={handleChange}
                    required
                />
            </div>

            <div className="form-group">
                <label>Serviço:</label>
                <ServiceSelector
                    onSelect={handleServiceSelect}
                    value={formData.service_id}
                />
            </div>

            <div className="form-group">
                <label>Data:</label>
                <input
                    type="date"
                    name="date"
                    value={formData.date}
                    onChange={handleChange}
                    required
                />
            </div>

            <div className="form-group">
                <label>Horário:</label>
                {formData.date ? (
                    <TimeSlots
                        date={formData.date}
                        onSelect={handleTimeSelect}
                        value={formData.time_slot_id}
                    />
                ) : (
                    <p className="hint">Escolha uma data primeiro</p>
                )}
            </div>

            <button type="submit" className="btn-submit">
                Confirmar Agendamento
            </button>
        </form>
    );
}

export default ScheduleForm;