const BASE_URL = "https://rafaelmaciel.net/sistemas/barberflow/backend/index.php";

const api = {
  baseURL: BASE_URL,

  // 🔥 AUTH
  login: (data) =>
    fetch(`${BASE_URL}/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(data)
    }),

  checkAuth: () =>
    fetch(`${BASE_URL}/auth`, {
      credentials: "include"
    }),

  logout: () =>
    fetch(`${BASE_URL}/logout`, {
      method: "POST",
      credentials: "include"
    }),

  // 📋 APPOINTMENTS
  getTimeSlots: (date) =>
    fetch(`${BASE_URL}/time-slots?date=${date}`),

  getAppointments: (date) =>
    fetch(`${BASE_URL}/appointments?date=${date}`),

  createAppointment: (data) =>
    fetch(`${BASE_URL}/appointments`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    }),

  cancelAppointment: (id) =>
    fetch(`${BASE_URL}/appointments`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    }),

  // 📊 DASHBOARD
  getDashboard: (date) =>
    fetch(`${BASE_URL}/dashboard?date=${date}`)
};

export default api;
