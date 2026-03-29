import { useEffect, useState } from "react";
import api from "../services/api";

export default function Admin() {
  const [appointments, setAppointments] = useState([]);
  const [dashboard, setDashboard] = useState({
    total: 0,
    faturamento: 0
  });

  const [date, setDate] = useState(
    new Date().toISOString().split("T")[0]
  );

  // 🔄 Carrega dados ao abrir ou mudar data
  useEffect(() => {
    loadAppointments();
    loadDashboard();
  }, [date]);

  // 📦 Buscar agendamentos (PROTEGIDO)
  async function loadAppointments() {
    try {
      const res = await fetch(`${api.baseURL}/appointments?date=${date}`, {
        credentials: "include" // 🔥 ESSENCIAL
      });

      // Se não autorizado → logout automático
      if (res.status === 401) {
        window.location.reload();
        return;
      }

      const data = await res.json();
      setAppointments(data);

    } catch (error) {
      console.error("Erro ao carregar agendamentos");
    }
  }

  // 📊 Buscar dashboard (PROTEGIDO)
  async function loadDashboard() {
    try {
      const res = await fetch(`${api.baseURL}/dashboard?date=${date}`, {
        credentials: "include" // 🔥 ESSENCIAL
      });

      if (res.status === 401) {
        window.location.reload();
        return;
      }

      const data = await res.json();
      setDashboard(data);

    } catch (error) {
      console.error("Erro ao carregar dashboard");
    }
  }

  // 🚪 Logout
  async function handleLogout() {
    await fetch(`${api.baseURL}/logout`, {
      method: "POST",
      credentials: "include"
    });

    window.location.reload();
  }

  // ❌ Cancelar agendamento
  async function cancelAppointment(id) {
    const confirmCancel = window.confirm("Deseja cancelar este agendamento?");
    if (!confirmCancel) return;

    try {
      await fetch(`${api.baseURL}/appointments`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json"
        },
        credentials: "include", // 🔥 ESSENCIAL
        body: JSON.stringify({ id })
      });

      // Atualiza dados
      loadAppointments();
      loadDashboard();

    } catch (error) {
      alert("Erro ao cancelar agendamento");
    }
  }

  return (
    <div style={styles.container}>
      <h1>💈 Painel Admin</h1>

      {/* 🔥 BOTÃO LOGOUT */}
      <button onClick={handleLogout} style={styles.logoutBtn}>
        Sair
      </button>

      {/* 📊 DASHBOARD */}
      <div style={styles.dashboard}>
        <div style={styles.card}>
          <h3>Atendimentos</h3>
          <p>{dashboard.total}</p>
        </div>

        <div style={styles.card}>
          <h3>Faturamento</h3>
          <p>R$ {dashboard.faturamento}</p>
        </div>
      </div>

      {/* 📅 Filtro */}
      <input
        type="date"
        value={date}
        onChange={(e) => setDate(e.target.value)}
      />

      {/* 📋 Tabela */}
      <table style={styles.table}>
        <thead>
          <tr>
            <th>Hora</th>
            <th>Cliente</th>
            <th>Serviço</th>
            <th>Valor</th>
            <th>Ação</th>
          </tr>
        </thead>

        <tbody>
          {appointments.length > 0 ? (
            appointments.map((a) => (
              <tr key={a.id}>
                <td>{a.time}</td>
                <td>{a.client_name}</td>
                <td>{a.service}</td>
                <td>R$ {a.price}</td>
                <td>
                  <button
                    onClick={() => cancelAppointment(a.id)}
                    style={styles.cancelBtn}
                  >
                    Cancelar
                  </button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5">Nenhum agendamento</td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}

// 🎨 Estilos
const styles = {
  container: {
    padding: "20px",
    background: "#111",
    color: "#fff",
    minHeight: "100vh"
  },
  dashboard: {
    display: "flex",
    gap: "10px",
    margin: "20px 0"
  },
  card: {
    background: "#1c1c1c",
    padding: "15px",
    borderRadius: "8px"
  },
  table: {
    width: "100%",
    marginTop: "20px",
    borderCollapse: "collapse"
  },
  cancelBtn: {
    background: "#d32f2f",
    color: "#fff",
    border: "none",
    padding: "6px 10px",
    cursor: "pointer",
    borderRadius: "4px"
  },
  logoutBtn: {
    marginBottom: "10px",
    background: "#555",
    color: "#fff",
    border: "none",
    padding: "6px 10px",
    cursor: "pointer",
    borderRadius: "4px"
  }
};