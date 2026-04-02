import { useEffect, useState } from "react";
import api from "../services/api";
import { getBrazilISODate } from "../utils/dateTime";

export default function Admin() {
  const [appointments, setAppointments] = useState([]);
  const [dashboard, setDashboard] = useState({
    total: 0,
    faturamento: 0
  });

  const [date, setDate] = useState(getBrazilISODate());

  // 🔥 Carrega dados
  useEffect(() => {
    loadAppointments();
    loadDashboard();
  }, [date]);

  async function handleLogout() {
  await api.logout();
  window.location.reload();
  }

  // 📋 Buscar agendamentos
  async function loadAppointments() {
    const res = await fetch(`${api.baseURL}/appointments?date=${date}`);
    const data = await res.json();
    setAppointments(data);
  }

  // 💰 Buscar dashboard
  async function loadDashboard() {
    const res = await fetch(`${api.baseURL}/dashboard?date=${date}`);
    const data = await res.json();
    setDashboard(data);
  }

  // ❌ Cancelar agendamento
  async function cancelAppointment(id) {
    const confirmCancel = window.confirm("Cancelar agendamento?");
    if (!confirmCancel) return;

    await fetch(`${api.baseURL}/appointments`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
    });

    loadAppointments();
    loadDashboard();
  }

  return (
    <section className="py-5">
      <div className="container">
        <div className="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <div>
            <h1 className="h3 fw-bold mb-1">Painel Admin</h1>
            <p className="text-secondary mb-0">Visão diária de agendamentos e faturamento</p>
          </div>
          <button onClick={handleLogout} className="btn btn-outline-dark">
            Sair
          </button>
        </div>

        <div className="row g-3 mb-4">
          <div className="col-md-6">
            <div className="card border-0 shadow-sm modern-card h-100">
              <div className="card-body">
                <p className="text-uppercase small text-secondary mb-2">Atendimentos</p>
                <h3 className="mb-0 fw-bold">{dashboard.total}</h3>
              </div>
            </div>
          </div>

          <div className="col-md-6">
            <div className="card border-0 shadow-sm modern-card h-100">
              <div className="card-body">
                <p className="text-uppercase small text-secondary mb-2">Faturamento</p>
                <h3 className="mb-0 fw-bold">R$ {dashboard.faturamento}</h3>
              </div>
            </div>
          </div>
        </div>

        <div className="card border-0 shadow-sm modern-card">
          <div className="card-body">
            <div className="row align-items-end mb-3">
              <div className="col-sm-6 col-md-4 col-lg-3">
                <label className="form-label fw-semibold">Filtrar por data</label>
                <input
                  type="date"
                  value={date}
                  onChange={(e) => setDate(e.target.value)}
                  className="form-control"
                />
              </div>
            </div>

            <div className="table-responsive">
              <table className="table align-middle mb-0">
                <thead className="table-light">
                  <tr>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Serviço</th>
                    <th>Valor</th>
                    <th className="text-end">Ação</th>
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

                        <td className="text-end">
                          <button
                            onClick={() => cancelAppointment(a.id)}
                            className="btn btn-sm btn-outline-danger"
                          >
                            Cancelar
                          </button>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan="5" className="text-center text-secondary py-4">
                        Nenhum agendamento
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
