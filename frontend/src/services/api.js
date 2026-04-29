const APP_BASE_PATH = "/sistemas/barberflow";
const LOCAL_API_BASE_PATH = "/backend/index.php";

function resolveBasePath() {
  if (typeof window === "undefined") {
    return APP_BASE_PATH;
  }

  if (window.location.pathname.startsWith(APP_BASE_PATH)) {
    return APP_BASE_PATH;
  }

  return "";
}

const BASE_URL = `${resolveBasePath()}${LOCAL_API_BASE_PATH}`;

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
  getServices: () =>
    fetch(`${BASE_URL}/services`),

  createService: (data) =>
    fetch(`${BASE_URL}/services`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(data)
    }),

  updateService: (data) =>
    fetch(`${BASE_URL}/services`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(data)
    }),

  deleteService: (id) =>
    fetch(`${BASE_URL}/services`, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({ id })
    }),

  getTimeSlots: (date) =>
    fetch(`${BASE_URL}/time-slots?date=${date}`),

  getAdminTimeSlots: () =>
    fetch(`${BASE_URL}/time-slots/admin`, {
      credentials: "include"
    }),

  createTimeSlot: (data) =>
    fetch(`${BASE_URL}/time-slots`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(data)
    }),

  updateTimeSlot: (data) =>
    fetch(`${BASE_URL}/time-slots`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(data)
    }),

  deleteTimeSlot: (id) =>
    fetch(`${BASE_URL}/time-slots`, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({ id })
    }),

  getBlockedDates: ({ from, to }) =>
    fetch(`${BASE_URL}/blocked-dates?from=${from}&to=${to}`),

  blockDate: (date) =>
    fetch(`${BASE_URL}/blocked-dates`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({ date })
    }),

  unblockDate: (date) =>
    fetch(`${BASE_URL}/blocked-dates`, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({ date })
    }),

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
