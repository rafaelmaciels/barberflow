import React, { useState } from "react";
import api from "../services/api";
import ServiceSelector from "./ServiceSelector";
import TimeSlots from "./TimeSlots";

function ScheduleForm() {
  const [formData, setFormData] = useState({
    nome_cliente: "",
    id_servico: "",
    id_horario: "",
    data_agendamento: ""
  });

  // Atualiza inputs comuns
  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  // Serviço
  const handleServiceSelect = (serviceId) => {
    setFormData({ ...formData, id_servico: serviceId });
  };

  // Horário
  const handleTimeSelect = (timeId) => {
    setFormData({ ...formData, id_horario: timeId });
  };

  // Submit
  const handleSubmit = async (e) => {
    e.preventDefault();

    console.log("📦 Enviando dados:", formData); // DEBUG

    // Validação
    if (
      !formData.nome_cliente ||
      !formData.id_servico ||
      !formData.id_horario ||
      !formData.data_agendamento
    ) {
      alert("Preencha todos os campos!");
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

      console.log("📥 Resposta:", data); // DEBUG

      if (data.success) {
        alert("Agendamento realizado com sucesso!");

        // Reset
        setFormData({
          nome_cliente: "",
          id_servico: "",
          id_horario: "",
          data_agendamento: ""
        });
      } else {
        alert(data.error || "Erro ao agendar");
      }
    } catch (error) {
      console.error("Erro:", error);
      alert("Erro ao conectar com o servidor");
    }
  };

  return (
    <form onSubmit={handleSubmit} className="schedule-form">
      <h2>Novo Agendamento</h2>

      {/* Nome */}
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

      {/* Serviço */}
      <div className="form-group">
        <label>Serviço:</label>
        <ServiceSelector
          onSelect={handleServiceSelect}
          value={formData.id_servico}
        />
      </div>

      {/* Data */}
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

      {/* Horário */}
      <div className="form-group">
        <label>Horário:</label>
        {formData.data_agendamento ? (
          <TimeSlots
            date={formData.data_agendamento}
            onSelect={handleTimeSelect}
            value={formData.id_horario}
          />
        ) : (
          <p className="hint">Escolha uma data primeiro</p>
        )}
      </div>

      {/* Botão */}
      <button type="submit" className="btn-submit">
        Confirmar Agendamento
      </button>
    </form>
  );
}

export default ScheduleForm;