import { useState } from "react";
import api from "../services/api";

export default function Home() {
  const [name, setName] = useState("");
  const [service, setService] = useState("");
  const [time, setTime] = useState("");

  async function handleSubmit() {
    console.log("🔥 Clique funcionando");

    if (!name || !service || !time) {
      alert("Preencha todos os campos");
      return;
    }

    try {
      const response = await fetch(`${api.baseURL}/appointments`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          nome_cliente: name,
          id_servico: service,
          id_horario: time,
          data_agendamento: new Date().toISOString().split("T")[0]
        })
      });

      const data = await response.json();

      console.log("📥 Resposta:", data);

      if (data.success) {
        alert("Agendamento realizado!");

        // limpar campos
        setName("");
        setService("");
        setTime("");
      } else {
        alert(data.error || "Erro ao agendar");
      }

    } catch (error) {
      console.error(error);
      alert("Erro ao conectar com o servidor");
    }
  }

  return (
    <div style={styles.container}>
      <div style={styles.card}>
        <h1>💈 BarberFlow</h1>
        <p>Agende seu horário</p>

        <input
          type="text"
          placeholder="Seu nome"
          value={name}
          onChange={(e) => setName(e.target.value)}
          style={styles.input}
        />

        <select
          value={service}
          onChange={(e) => setService(e.target.value)}
          style={styles.input}
        >
          <option value="">Selecione o serviço</option>
          <option value="1">Corte</option>
          <option value="2">Barba</option>
          <option value="3">Cabelo e Barba</option>
        </select>

        <select
          value={time}
          onChange={(e) => setTime(e.target.value)}
          style={styles.input}
        >
          <option value="">Selecione o horário</option>
          <option value="1">09:00</option>
          <option value="2">09:30</option>
          <option value="3">10:00</option>
        </select>

        {/* 🔥 AGORA FUNCIONA */}
        <button style={styles.button} onClick={handleSubmit}>
          Agendar
        </button>
      </div>
    </div>
  );
}
const styles = {
  container: {
    height: "100vh",
    display: "flex",
    justifyContent: "center",
    alignItems: "center",
    background: "#111",
    color: "#fff",
  },
  card: {
    background: "#1c1c1c",
    padding: "30px",
    borderRadius: "10px",
    width: "300px",
    textAlign: "center",
  },
  input: {
    width: "100%",
    padding: "10px",
    marginTop: "10px",
    borderRadius: "5px",
    border: "none",
  },
  button: {
    width: "100%",
    padding: "12px",
    marginTop: "15px",
    background: "#00c853",
    border: "none",
    borderRadius: "5px",
    color: "#fff",
    fontWeight: "bold",
    cursor: "pointer",
  },
};