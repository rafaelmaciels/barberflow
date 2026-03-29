import { useState } from "react";

export default function Home() {
  const [name, setName] = useState("");
  const [service, setService] = useState("");
  const [time, setTime] = useState("");

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

        <button style={styles.button}>
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