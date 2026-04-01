import { useState } from "react";

export default function Home() {
  const [name, setName] = useState("");
  const [service, setService] = useState("");
  const [time, setTime] = useState("");
  const [message, setMessage] = useState("");

  // 🔥 FUNÇÃO PRINCIPAL
  async function handleSubmit() {
    console.log("🔥 Clique funcionando");

    if (!name || !service || !time) {
      setMessage("Preencha todos os campos!");
      return;
    }

    try {
      const response = await fetch("http://localhost:8000/index.php/appointments", {
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

      console.log("📥 Backend:", data);

      if (data.success) {
        setMessage("✅ Agendamento salvo no banco!");
      } else {
        setMessage("⚠️ Funcionando localmente (erro no backend)");
      }

    } catch (error) {
      console.log("Erro:", error);

      // 🔥 fallback seguro
      setMessage("⚠️ Funcionando localmente (sem backend)");
    }

    // limpa campos
    setName("");
    setService("");
    setTime("");
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
          <option value="3">Combo (Cabelo + Barba)</option>
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

        <button style={styles.button} onClick={handleSubmit}>
          Agendar
        </button>

        {/* Feedback */}
        {message && <p style={styles.message}>{message}</p>}
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
  message: {
    marginTop: "10px",
    fontWeight: "bold",
  }
};