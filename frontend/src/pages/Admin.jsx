import { useEffect, useMemo, useState } from "react";
import api from "../services/api";
import { addDaysToISODate, formatBrazilDate, getBrazilISODate, isBrazilSunday } from "../utils/dateTime";

const initialServiceForm = {
  name: "",
  price: "",
  duration: ""
};

const initialTimeSlotForm = {
  time: ""
};

function formatCurrency(value) {
  const amount = Number(value || 0);

  return amount.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL"
  });
}

function formatPhone(value) {
  const digits = String(value || "").replace(/\D/g, "");

  if (!digits) return "Sem WhatsApp";

  if (digits.length === 13 && digits.startsWith("55")) {
    return `+${digits.slice(0, 2)} ${digits.slice(2, 4)} ${digits.slice(4, 9)}-${digits.slice(9)}`;
  }

  if (digits.length === 11) {
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
  }

  if (digits.length === 10) {
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
  }

  return value;
}

function normalizeWhatsappPhone(phone) {
  const digits = String(phone || "").replace(/\D/g, "");

  if (!digits) return "";
  if (digits.startsWith("55")) return digits;
  return `55${digits}`;
}

function buildWhatsappReminderMessage(appointment) {
  return `Olá, ${appointment.client_name}! Passando para confirmar seu agendamento de ${appointment.service} no dia ${formatBrazilDate(appointment.appointment_date)} às ${appointment.time}. Estamos te aguardando no salão.`;
}

export default function Admin() {
  const [appointments, setAppointments] = useState([]);
  const [services, setServices] = useState([]);
  const [adminTimeSlots, setAdminTimeSlots] = useState([]);
  const [blockedDates, setBlockedDates] = useState([]);
  const [dashboard, setDashboard] = useState({
    total: 0,
    faturamento: 0
  });
  const [date, setDate] = useState(getBrazilISODate());
  const [dateControl, setDateControl] = useState(getBrazilISODate());
  const [dateControlMessage, setDateControlMessage] = useState("");
  const [dateControlError, setDateControlError] = useState("");
  const [savingDateControl, setSavingDateControl] = useState(false);

  const [serviceForm, setServiceForm] = useState(initialServiceForm);
  const [serviceMessage, setServiceMessage] = useState("");
  const [serviceError, setServiceError] = useState("");
  const [savingService, setSavingService] = useState(false);
  const [editingServiceId, setEditingServiceId] = useState(null);
  const [editingServiceForm, setEditingServiceForm] = useState(initialServiceForm);
  const [updatingServiceId, setUpdatingServiceId] = useState(null);
  const [deletingServiceId, setDeletingServiceId] = useState(null);

  const [timeSlotForm, setTimeSlotForm] = useState(initialTimeSlotForm);
  const [timeSlotMessage, setTimeSlotMessage] = useState("");
  const [timeSlotError, setTimeSlotError] = useState("");
  const [savingTimeSlot, setSavingTimeSlot] = useState(false);
  const [editingTimeSlotId, setEditingTimeSlotId] = useState(null);
  const [editingTimeSlotForm, setEditingTimeSlotForm] = useState(initialTimeSlotForm);
  const [updatingTimeSlotId, setUpdatingTimeSlotId] = useState(null);
  const [deletingTimeSlotId, setDeletingTimeSlotId] = useState(null);

  const maxControlDate = useMemo(() => {
    return addDaysToISODate(getBrazilISODate(), 180);
  }, []);

  const blockedDateSet = useMemo(() => {
    return new Set(blockedDates.map((item) => item.date));
  }, [blockedDates]);

  const isSelectedDateBlocked = useMemo(() => {
    return blockedDateSet.has(dateControl);
  }, [blockedDateSet, dateControl]);

  useEffect(() => {
    loadAppointments();
    loadDashboard();
  }, [date]);

  useEffect(() => {
    loadServices();
    loadAdminTimeSlots();
    loadBlockedDates();
  }, []);

  async function handleLogout() {
    await api.logout();
    window.location.reload();
  }

  async function loadAppointments() {
    const res = await api.getAppointments(date);
    const data = await res.json();
    setAppointments(Array.isArray(data) ? data : []);
  }

  async function loadDashboard() {
    const res = await api.getDashboard(date);
    const data = await res.json();
    setDashboard(data);
  }

  async function loadServices() {
    try {
      const res = await api.getServices();
      const data = await res.json();
      setServices(Array.isArray(data) ? data : []);
    } catch (error) {
      setServices([]);
    }
  }

  async function loadAdminTimeSlots() {
    try {
      const res = await api.getAdminTimeSlots();
      const data = await res.json();
      setAdminTimeSlots(Array.isArray(data) ? data : []);
    } catch (error) {
      setAdminTimeSlots([]);
    }
  }

  async function loadBlockedDates() {
    try {
      const from = getBrazilISODate();
      const to = addDaysToISODate(from, 180);
      const res = await api.getBlockedDates({ from, to });
      const data = await res.json();
      setBlockedDates(Array.isArray(data) ? data : []);
    } catch (error) {
      setBlockedDates([]);
    }
  }

  function clearServiceFeedback() {
    setServiceMessage("");
    setServiceError("");
  }

  function clearTimeSlotFeedback() {
    setTimeSlotMessage("");
    setTimeSlotError("");
  }

  function clearDateControlFeedback() {
    setDateControlMessage("");
    setDateControlError("");
  }

  function handleServiceFieldChange(field, value) {
    setServiceForm((currentForm) => ({
      ...currentForm,
      [field]: value
    }));
  }

  function handleEditingServiceFieldChange(field, value) {
    setEditingServiceForm((currentForm) => ({
      ...currentForm,
      [field]: value
    }));
  }

  function handleTimeSlotFieldChange(field, value) {
    setTimeSlotForm((currentForm) => ({
      ...currentForm,
      [field]: value
    }));
  }

  function handleEditingTimeSlotFieldChange(field, value) {
    setEditingTimeSlotForm((currentForm) => ({
      ...currentForm,
      [field]: value
    }));
  }

  async function handleDateToggle() {
    clearDateControlFeedback();

    if (!dateControl) {
      setDateControlError("Selecione uma data.");
      return;
    }

    if (isBrazilSunday(dateControl)) {
      setDateControlError("Domingos já ficam indisponíveis automaticamente.");
      return;
    }

    if (dateControl < getBrazilISODate()) {
      setDateControlError("Escolha a data de hoje ou uma data futura.");
      return;
    }

    setSavingDateControl(true);

    try {
      const res = isSelectedDateBlocked
        ? await api.unblockDate(dateControl)
        : await api.blockDate(dateControl);
      const data = await res.json();

      if (!res.ok) {
        setDateControlError(data.error || "Não foi possível atualizar a data.");
        return;
      }

      setDateControlMessage(
        isSelectedDateBlocked
          ? `Data ${formatBrazilDate(dateControl)} liberada para agendamentos.`
          : `Data ${formatBrazilDate(dateControl)} bloqueada para agendamentos.`
      );
      loadBlockedDates();
    } catch (error) {
      setDateControlError("Erro ao atualizar a data. Tente novamente.");
    } finally {
      setSavingDateControl(false);
    }
  }

  async function handleUnblockListedDate(blockedDate) {
    clearDateControlFeedback();
    setSavingDateControl(true);

    try {
      const res = await api.unblockDate(blockedDate);
      const data = await res.json();

      if (!res.ok) {
        setDateControlError(data.error || "Não foi possível liberar a data.");
        return;
      }

      setDateControlMessage(`Data ${formatBrazilDate(blockedDate)} liberada para agendamentos.`);
      loadBlockedDates();
    } catch (error) {
      setDateControlError("Erro ao liberar a data. Tente novamente.");
    } finally {
      setSavingDateControl(false);
    }
  }

  async function handleServiceSubmit(event) {
    event.preventDefault();
    clearServiceFeedback();

    const payload = {
      name: serviceForm.name.trim(),
      price: serviceForm.price,
      duration: serviceForm.duration
    };

    if (!payload.name || !payload.price || !payload.duration) {
      setServiceError("Preencha nome, preço e duração do serviço.");
      return;
    }

    setSavingService(true);

    try {
      const res = await api.createService(payload);
      const data = await res.json();

      if (!res.ok) {
        setServiceError(data.error || "Não foi possível cadastrar o serviço.");
        return;
      }

      setServiceForm(initialServiceForm);
      setServiceMessage(`Serviço "${data.service?.name || payload.name}" cadastrado com sucesso.`);
      loadServices();
    } catch (error) {
      setServiceError("Erro ao salvar serviço. Tente novamente.");
    } finally {
      setSavingService(false);
    }
  }

  function startServiceEdit(service) {
    clearServiceFeedback();
    setEditingServiceId(service.id);
    setEditingServiceForm({
      name: service.name,
      price: String(service.price),
      duration: String(service.duration)
    });
  }

  function cancelServiceEdit() {
    setEditingServiceId(null);
    setEditingServiceForm(initialServiceForm);
  }

  async function saveServiceEdit(id) {
    clearServiceFeedback();

    const payload = {
      id,
      name: editingServiceForm.name.trim(),
      price: editingServiceForm.price,
      duration: editingServiceForm.duration
    };

    if (!payload.name || !payload.price || !payload.duration) {
      setServiceError("Preencha nome, preço e duração do serviço.");
      return;
    }

    setUpdatingServiceId(id);

    try {
      const res = await api.updateService(payload);
      const data = await res.json();

      if (!res.ok) {
        setServiceError(data.error || "Não foi possível atualizar o serviço.");
        return;
      }

      setServiceMessage(`Serviço "${data.service?.name || payload.name}" atualizado com sucesso.`);
      cancelServiceEdit();
      loadServices();
    } catch (error) {
      setServiceError("Erro ao atualizar serviço. Tente novamente.");
    } finally {
      setUpdatingServiceId(null);
    }
  }

  async function handleDeleteService(service) {
    clearServiceFeedback();

    const confirmDelete = window.confirm(`Excluir o serviço "${service.name}"?`);
    if (!confirmDelete) return;

    setDeletingServiceId(service.id);

    try {
      const res = await api.deleteService(service.id);
      const data = await res.json();

      if (!res.ok) {
        setServiceError(data.error || "Não foi possível excluir o serviço.");
        return;
      }

      if (editingServiceId === service.id) {
        cancelServiceEdit();
      }

      setServiceMessage(`Serviço "${service.name}" excluído com sucesso.`);
      loadServices();
    } catch (error) {
      setServiceError("Erro ao excluir serviço. Tente novamente.");
    } finally {
      setDeletingServiceId(null);
    }
  }

  async function handleTimeSlotSubmit(event) {
    event.preventDefault();
    clearTimeSlotFeedback();

    const payload = {
      time: timeSlotForm.time
    };

    if (!payload.time) {
      setTimeSlotError("Informe o horário.");
      return;
    }

    setSavingTimeSlot(true);

    try {
      const res = await api.createTimeSlot(payload);
      const data = await res.json();

      if (!res.ok) {
        setTimeSlotError(data.error || "Não foi possível cadastrar o horário.");
        return;
      }

      setTimeSlotForm(initialTimeSlotForm);
      setTimeSlotMessage(`Horário ${data.slot?.time || payload.time} cadastrado com sucesso.`);
      loadAdminTimeSlots();
    } catch (error) {
      setTimeSlotError("Erro ao salvar horário. Tente novamente.");
    } finally {
      setSavingTimeSlot(false);
    }
  }

  function startTimeSlotEdit(slot) {
    clearTimeSlotFeedback();
    setEditingTimeSlotId(slot.id);
    setEditingTimeSlotForm({
      time: slot.time
    });
  }

  function cancelTimeSlotEdit() {
    setEditingTimeSlotId(null);
    setEditingTimeSlotForm(initialTimeSlotForm);
  }

  async function saveTimeSlotEdit(id) {
    clearTimeSlotFeedback();

    const payload = {
      id,
      time: editingTimeSlotForm.time
    };

    if (!payload.time) {
      setTimeSlotError("Informe o horário.");
      return;
    }

    setUpdatingTimeSlotId(id);

    try {
      const res = await api.updateTimeSlot(payload);
      const data = await res.json();

      if (!res.ok) {
        setTimeSlotError(data.error || "Não foi possível atualizar o horário.");
        return;
      }

      setTimeSlotMessage(`Horário ${data.slot?.time || payload.time} atualizado com sucesso.`);
      cancelTimeSlotEdit();
      loadAdminTimeSlots();
    } catch (error) {
      setTimeSlotError("Erro ao atualizar horário. Tente novamente.");
    } finally {
      setUpdatingTimeSlotId(null);
    }
  }

  async function handleDeleteTimeSlot(slot) {
    clearTimeSlotFeedback();

    const confirmDelete = window.confirm(`Excluir o horário ${slot.time}?`);
    if (!confirmDelete) return;

    setDeletingTimeSlotId(slot.id);

    try {
      const res = await api.deleteTimeSlot(slot.id);
      const data = await res.json();

      if (!res.ok) {
        setTimeSlotError(data.error || "Não foi possível excluir o horário.");
        return;
      }

      if (editingTimeSlotId === slot.id) {
        cancelTimeSlotEdit();
      }

      setTimeSlotMessage(`Horário ${slot.time} excluído com sucesso.`);
      loadAdminTimeSlots();
    } catch (error) {
      setTimeSlotError("Erro ao excluir horário. Tente novamente.");
    } finally {
      setDeletingTimeSlotId(null);
    }
  }

  function openWhatsappReminder(appointment) {
    const phone = normalizeWhatsappPhone(appointment.client_phone);

    if (!phone) {
      window.alert("Este cliente ainda não tem WhatsApp cadastrado.");
      return;
    }

    const message = buildWhatsappReminderMessage(appointment);
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
    window.open(url, "_blank", "noopener,noreferrer");
  }

  async function cancelAppointment(id) {
    const confirmCancel = window.confirm("Cancelar agendamento?");
    if (!confirmCancel) return;

    await api.cancelAppointment(id);
    loadAppointments();
    loadDashboard();
  }

  return (
    <section className="py-5">
      <div className="container">
        <div className="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <div>
            <h1 className="h3 fw-bold mb-1">Painel Admin</h1>
            <p className="text-secondary mb-0">Gerencie serviços, horários, agenda futura e confirmações</p>
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

        <div className="row g-4 mb-4">
          <div className="col-xl-5">
            <div className="card border-0 shadow-sm modern-card h-100">
              <div className="card-body p-4">
                <h2 className="h5 fw-bold mb-1">Datas de agendamento</h2>
                <p className="text-secondary mb-3">
                  Bloqueie ou libere datas futuras para feriados e pausas. Domingos já ficam fechados.
                </p>

                <div className="mb-3">
                  <label className="form-label fw-semibold">Data</label>
                  <input
                    type="date"
                    value={dateControl}
                    min={getBrazilISODate()}
                    max={maxControlDate}
                    onChange={(event) => setDateControl(event.target.value)}
                    className="form-control"
                  />
                </div>

                {dateControlMessage ? (
                  <div className="alert alert-success mb-3" role="alert">
                    {dateControlMessage}
                  </div>
                ) : null}

                {dateControlError ? (
                  <div className="alert alert-danger mb-3" role="alert">
                    {dateControlError}
                  </div>
                ) : null}

                <button
                  type="button"
                  className={`btn ${isSelectedDateBlocked ? "btn-outline-success" : "btn-outline-danger"}`}
                  onClick={handleDateToggle}
                  disabled={savingDateControl}
                >
                  {savingDateControl
                    ? "Salvando..."
                    : isSelectedDateBlocked
                      ? "Liberar data para agendamento"
                      : "Bloquear data para agendamento"}
                </button>
              </div>
            </div>
          </div>

          <div className="col-xl-7">
            <div className="card border-0 shadow-sm modern-card h-100">
              <div className="card-body p-4">
                <div className="d-flex align-items-start justify-content-between gap-3 mb-3">
                  <div>
                    <h2 className="h5 fw-bold mb-1">Datas bloqueadas</h2>
                    <p className="text-secondary mb-0">
                      Use esta lista para gerenciar feriados e dias indisponíveis.
                    </p>
                  </div>
                  <span className="badge text-bg-dark">{blockedDates.length} datas</span>
                </div>

                <div className="table-responsive">
                  <table className="table align-middle mb-0">
                    <thead className="table-light">
                      <tr>
                        <th>Data</th>
                        <th className="text-end">Ação</th>
                      </tr>
                    </thead>
                    <tbody>
                      {blockedDates.length > 0 ? (
                        blockedDates.map((item) => (
                          <tr key={item.id}>
                            <td>{formatBrazilDate(item.date)}</td>
                            <td className="text-end">
                              <button
                                type="button"
                                className="btn btn-sm btn-outline-success"
                                onClick={() => handleUnblockListedDate(item.date)}
                                disabled={savingDateControl}
                              >
                                Liberar
                              </button>
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr>
                          <td colSpan="2" className="text-center text-secondary py-4">
                            Nenhuma data bloqueada
                          </td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="row g-4 mb-4">
          <div className="col-xl-5">
            <div className="card border-0 shadow-sm modern-card h-100">
              <div className="card-body p-4">
                <h2 className="h5 fw-bold mb-1">Cadastrar serviço</h2>
                <p className="text-secondary mb-3">
                  Defina nome, preço e duração de cada atendimento.
                </p>

                <form onSubmit={handleServiceSubmit}>
                  <div className="mb-3">
                    <label className="form-label fw-semibold">Nome do serviço</label>
                    <input
                      type="text"
                      value={serviceForm.name}
                      onChange={(event) => handleServiceFieldChange("name", event.target.value)}
                      className="form-control"
                      placeholder="Ex.: Sobrancelha"
                    />
                  </div>

                  <div className="row g-3">
                    <div className="col-sm-6">
                      <label className="form-label fw-semibold">Preço</label>
                      <input
                        type="number"
                        min="0"
                        step="0.01"
                        value={serviceForm.price}
                        onChange={(event) => handleServiceFieldChange("price", event.target.value)}
                        className="form-control"
                        placeholder="10.00"
                      />
                    </div>

                    <div className="col-sm-6">
                      <label className="form-label fw-semibold">Duração</label>
                      <div className="input-group">
                        <input
                          type="number"
                          min="1"
                          step="1"
                          value={serviceForm.duration}
                          onChange={(event) => handleServiceFieldChange("duration", event.target.value)}
                          className="form-control"
                          placeholder="15"
                        />
                        <span className="input-group-text">min</span>
                      </div>
                    </div>
                  </div>

                  {serviceMessage ? (
                    <div className="alert alert-success mt-3 mb-0" role="alert">
                      {serviceMessage}
                    </div>
                  ) : null}

                  {serviceError ? (
                    <div className="alert alert-danger mt-3 mb-0" role="alert">
                      {serviceError}
                    </div>
                  ) : null}

                  <button
                    type="submit"
                    className="btn btn-primary mt-4"
                    disabled={savingService}
                  >
                    {savingService ? "Salvando..." : "Adicionar serviço"}
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div className="col-xl-7">
            <div className="card border-0 shadow-sm modern-card h-100">
              <div className="card-body p-4">
                <div className="d-flex align-items-start justify-content-between gap-3 mb-3">
                  <div>
                    <h2 className="h5 fw-bold mb-1">Serviços cadastrados</h2>
                    <p className="text-secondary mb-0">
                      Edite preço, duração e nome sem sair do painel.
                    </p>
                  </div>
                  <span className="badge text-bg-dark">{services.length} serviços</span>
                </div>

                <div className="table-responsive">
                  <table className="table align-middle mb-0">
                    <thead className="table-light">
                      <tr>
                        <th>Serviço</th>
                        <th>Preço</th>
                        <th>Duração</th>
                        <th className="text-end">Ações</th>
                      </tr>
                    </thead>

                    <tbody>
                      {services.length > 0 ? (
                        services.map((service) => (
                          <tr key={service.id}>
                            <td>
                              {editingServiceId === service.id ? (
                                <input
                                  type="text"
                                  value={editingServiceForm.name}
                                  onChange={(event) => handleEditingServiceFieldChange("name", event.target.value)}
                                  className="form-control form-control-sm"
                                />
                              ) : (
                                service.name
                              )}
                            </td>
                            <td>
                              {editingServiceId === service.id ? (
                                <input
                                  type="number"
                                  min="0"
                                  step="0.01"
                                  value={editingServiceForm.price}
                                  onChange={(event) => handleEditingServiceFieldChange("price", event.target.value)}
                                  className="form-control form-control-sm"
                                />
                              ) : (
                                formatCurrency(service.price)
                              )}
                            </td>
                            <td>
                              {editingServiceId === service.id ? (
                                <div className="input-group input-group-sm">
                                  <input
                                    type="number"
                                    min="1"
                                    step="1"
                                    value={editingServiceForm.duration}
                                    onChange={(event) => handleEditingServiceFieldChange("duration", event.target.value)}
                                    className="form-control"
                                  />
                                  <span className="input-group-text">min</span>
                                </div>
                              ) : (
                                `${service.duration} min`
                              )}
                            </td>
                            <td className="text-end">
                              {editingServiceId === service.id ? (
                                <div className="d-flex justify-content-end gap-2">
                                  <button
                                    type="button"
                                    className="btn btn-sm btn-primary"
                                    onClick={() => saveServiceEdit(service.id)}
                                    disabled={updatingServiceId === service.id}
                                  >
                                    {updatingServiceId === service.id ? "Salvando..." : "Salvar"}
                                  </button>
                                  <button
                                    type="button"
                                    className="btn btn-sm btn-outline-secondary"
                                    onClick={cancelServiceEdit}
                                    disabled={updatingServiceId === service.id}
                                  >
                                    Cancelar
                                  </button>
                                </div>
                              ) : (
                                <div className="d-flex justify-content-end gap-2">
                                  <button
                                    type="button"
                                    className="btn btn-sm btn-outline-primary"
                                    onClick={() => startServiceEdit(service)}
                                    disabled={deletingServiceId === service.id}
                                  >
                                    Editar
                                  </button>
                                  <button
                                    type="button"
                                    className="btn btn-sm btn-outline-danger"
                                    onClick={() => handleDeleteService(service)}
                                    disabled={deletingServiceId === service.id}
                                  >
                                    {deletingServiceId === service.id ? "Excluindo..." : "Excluir"}
                                  </button>
                                </div>
                              )}
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr>
                          <td colSpan="4" className="text-center text-secondary py-4">
                            Nenhum serviço cadastrado
                          </td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="row g-4 mb-4">
          <div className="col-xl-4">
            <div className="card border-0 shadow-sm modern-card h-100">
              <div className="card-body p-4">
                <h2 className="h5 fw-bold mb-1">Cadastrar horário</h2>
                <p className="text-secondary mb-3">
                  Adicione novos horários disponíveis para agendamento.
                </p>

                <form onSubmit={handleTimeSlotSubmit}>
                  <div className="mb-3">
                    <label className="form-label fw-semibold">Horário</label>
                    <input
                      type="time"
                      value={timeSlotForm.time}
                      onChange={(event) => handleTimeSlotFieldChange("time", event.target.value)}
                      className="form-control"
                    />
                  </div>

                  {timeSlotMessage ? (
                    <div className="alert alert-success mt-3 mb-0" role="alert">
                      {timeSlotMessage}
                    </div>
                  ) : null}

                  {timeSlotError ? (
                    <div className="alert alert-danger mt-3 mb-0" role="alert">
                      {timeSlotError}
                    </div>
                  ) : null}

                  <button
                    type="submit"
                    className="btn btn-primary mt-4"
                    disabled={savingTimeSlot}
                  >
                    {savingTimeSlot ? "Salvando..." : "Adicionar horário"}
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div className="col-xl-8">
            <div className="card border-0 shadow-sm modern-card h-100">
              <div className="card-body p-4">
                <div className="d-flex align-items-start justify-content-between gap-3 mb-3">
                  <div>
                    <h2 className="h5 fw-bold mb-1">Horários disponíveis</h2>
                    <p className="text-secondary mb-0">
                      Ajuste a grade de horários usada pelo cliente no agendamento.
                    </p>
                  </div>
                  <span className="badge text-bg-dark">{adminTimeSlots.length} horários</span>
                </div>

                <div className="table-responsive">
                  <table className="table align-middle mb-0">
                    <thead className="table-light">
                      <tr>
                        <th>Horário</th>
                        <th className="text-end">Ações</th>
                      </tr>
                    </thead>

                    <tbody>
                      {adminTimeSlots.length > 0 ? (
                        adminTimeSlots.map((slot) => (
                          <tr key={slot.id}>
                            <td>
                              {editingTimeSlotId === slot.id ? (
                                <input
                                  type="time"
                                  value={editingTimeSlotForm.time}
                                  onChange={(event) => handleEditingTimeSlotFieldChange("time", event.target.value)}
                                  className="form-control form-control-sm"
                                />
                              ) : (
                                slot.time
                              )}
                            </td>
                            <td className="text-end">
                              {editingTimeSlotId === slot.id ? (
                                <div className="d-flex justify-content-end gap-2">
                                  <button
                                    type="button"
                                    className="btn btn-sm btn-primary"
                                    onClick={() => saveTimeSlotEdit(slot.id)}
                                    disabled={updatingTimeSlotId === slot.id}
                                  >
                                    {updatingTimeSlotId === slot.id ? "Salvando..." : "Salvar"}
                                  </button>
                                  <button
                                    type="button"
                                    className="btn btn-sm btn-outline-secondary"
                                    onClick={cancelTimeSlotEdit}
                                    disabled={updatingTimeSlotId === slot.id}
                                  >
                                    Cancelar
                                  </button>
                                </div>
                              ) : (
                                <div className="d-flex justify-content-end gap-2">
                                  <button
                                    type="button"
                                    className="btn btn-sm btn-outline-primary"
                                    onClick={() => startTimeSlotEdit(slot)}
                                    disabled={deletingTimeSlotId === slot.id}
                                  >
                                    Editar
                                  </button>
                                  <button
                                    type="button"
                                    className="btn btn-sm btn-outline-danger"
                                    onClick={() => handleDeleteTimeSlot(slot)}
                                    disabled={deletingTimeSlotId === slot.id}
                                  >
                                    {deletingTimeSlotId === slot.id ? "Excluindo..." : "Excluir"}
                                  </button>
                                </div>
                              )}
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr>
                          <td colSpan="2" className="text-center text-secondary py-4">
                            Nenhum horário cadastrado
                          </td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="card border-0 shadow-sm modern-card">
          <div className="card-body">
            <div className="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
              <div>
                <h2 className="h5 fw-bold mb-1">Agendamentos do dia</h2>
                <p className="text-secondary mb-0">
                  Confirme por WhatsApp, acompanhe o contato e cancele quando necessário.
                </p>
              </div>

              <div className="col-sm-6 col-md-4 col-lg-3">
                <label className="form-label fw-semibold">Filtrar por data</label>
                <input
                  type="date"
                  value={date}
                  onChange={(event) => setDate(event.target.value)}
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
                    <th>WhatsApp</th>
                    <th>Serviço</th>
                    <th>Valor</th>
                    <th className="text-end">Ações</th>
                  </tr>
                </thead>

                <tbody>
                  {appointments.length > 0 ? (
                    appointments.map((appointment) => (
                      <tr key={appointment.id}>
                        <td>{appointment.time}</td>
                        <td>{appointment.client_name}</td>
                        <td>{formatPhone(appointment.client_phone)}</td>
                        <td>{appointment.service}</td>
                        <td>{formatCurrency(appointment.price)}</td>
                        <td className="text-end">
                          <div className="d-flex justify-content-end gap-2">
                            <button
                              type="button"
                              onClick={() => openWhatsappReminder(appointment)}
                              className="btn btn-sm btn-success"
                              disabled={!appointment.client_phone}
                            >
                              Confirmar WhatsApp
                            </button>
                            <button
                              type="button"
                              onClick={() => cancelAppointment(appointment.id)}
                              className="btn btn-sm btn-outline-danger"
                            >
                              Cancelar
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan="6" className="text-center text-secondary py-4">
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
