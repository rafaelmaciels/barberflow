import { useEffect, useMemo, useState } from "react";
import api from "../services/api";
import {
  addDaysToISODate,
  formatBrazilWeekdayAndDate,
  getBrazilISODate,
  isBrazilSunday
} from "../utils/dateTime";

function getOptionLabelByValue(options, value) {
  const option = options.find((item) => String(item.value) === String(value));
  return option ? option.label : value;
}

function buildAppointmentConfirmationMessage({ clientName, serviceName, selectedTime, selectedDate }) {
  return `Olá, ${clientName}!\n\nSeu agendamento foi confirmado para ${selectedDate} às ${selectedTime}, serviço: ${serviceName}.\n\nSe precisar de ajustes, estamos à disposição.`;
}

export default function Home() {
  const [todayDate, setTodayDate] = useState(() => getBrazilISODate());
  const [selectedDate, setSelectedDate] = useState(() => getBrazilISODate());
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [service, setService] = useState("");
  const [time, setTime] = useState("");
  const [message, setMessage] = useState("");
  const [services, setServices] = useState([]);
  const [blockedDates, setBlockedDates] = useState([]);
  const [loadingServices, setLoadingServices] = useState(true);
  const [timeSlots, setTimeSlots] = useState([]);
  const [loadingSlots, setLoadingSlots] = useState(true);
  const [loadingBlockedDates, setLoadingBlockedDates] = useState(true);

  const maxDate = useMemo(() => {
    return addDaysToISODate(todayDate, 60);
  }, [todayDate]);

  const currentDateLabel = useMemo(() => {
    return formatBrazilWeekdayAndDate(selectedDate);
  }, [selectedDate]);

  const blockedDateSet = useMemo(() => {
    return new Set(blockedDates.map((item) => item.date));
  }, [blockedDates]);

  const isSunday = useMemo(() => {
    return isBrazilSunday(selectedDate);
  }, [selectedDate]);

  const isBlockedDate = useMemo(() => {
    return blockedDateSet.has(selectedDate);
  }, [blockedDateSet, selectedDate]);

  const isPastDate = useMemo(() => {
    return selectedDate < todayDate;
  }, [selectedDate, todayDate]);

  const isUnavailableDate = isPastDate || isSunday || isBlockedDate;

  const serviceOptions = useMemo(() => {
    return services.map((item) => ({
      value: String(item.id),
      label: item.name
    }));
  }, [services]);

  const availableTimeSlots = useMemo(() => {
    return timeSlots.filter((slot) => slot.available);
  }, [timeSlots]);

  useEffect(() => {
    loadServices();
  }, []);

  useEffect(() => {
    loadBlockedDates(todayDate, maxDate);
  }, [todayDate, maxDate]);

  useEffect(() => {
    if (selectedDate < todayDate) {
      setSelectedDate(todayDate);
    }
  }, [selectedDate, todayDate]);

  useEffect(() => {
    setTime("");
    setMessage("");

    if (isUnavailableDate) {
      setLoadingSlots(false);
      setTimeSlots([]);
      return;
    }

    loadTimeSlots();
  }, [selectedDate, isUnavailableDate]);

  useEffect(() => {
    const intervalId = setInterval(() => {
      const nowDate = getBrazilISODate();
      setTodayDate((previousDate) => {
        if (previousDate !== nowDate) {
          return nowDate;
        }
        return previousDate;
      });
    }, 30000);

    return () => clearInterval(intervalId);
  }, []);

  async function loadServices() {
    setLoadingServices(true);

    try {
      const res = await api.getServices();
      const data = await res.json();
      setServices(Array.isArray(data) ? data : []);
    } catch (error) {
      console.log("Erro ao buscar serviços:", error);
      setServices([]);
    } finally {
      setLoadingServices(false);
    }
  }

  async function loadBlockedDates(from, to) {
    setLoadingBlockedDates(true);

    try {
      const res = await api.getBlockedDates({ from, to });
      const data = await res.json();
      setBlockedDates(Array.isArray(data) ? data : []);
    } catch (error) {
      console.log("Erro ao buscar datas bloqueadas:", error);
      setBlockedDates([]);
    } finally {
      setLoadingBlockedDates(false);
    }
  }

  async function loadTimeSlots() {
    setLoadingSlots(true);

    try {
      const res = await api.getTimeSlots(selectedDate);
      const data = await res.json();
      setTimeSlots(Array.isArray(data) ? data : []);
    } catch (error) {
      console.log("Erro ao buscar horários:", error);
      setTimeSlots([]);
    } finally {
      setLoadingSlots(false);
    }
  }

  function getDateUnavailableMessage() {
    if (isPastDate) {
      return "Datas passadas não estão disponíveis.";
    }

    if (isSunday) {
      return "Domingos não recebem agendamentos.";
    }

    if (isBlockedDate) {
      return "Esta data está indisponível para agendamento.";
    }

    return "";
  }

  async function handleSubmit() {
    if (isUnavailableDate) {
      setMessage(getDateUnavailableMessage());
      return;
    }

    if (!name || !phone || !service || !time) {
      setMessage("Preencha nome, WhatsApp, serviço e horário.");
      return;
    }

    const selectedServiceLabel = getOptionLabelByValue(serviceOptions, service);
    const selectedSlot = timeSlots.find((slot) => String(slot.id) === time);
    const selectedTimeLabel = selectedSlot ? selectedSlot.time : time;
    const clientName = name;

    if (!selectedSlot || !selectedSlot.available) {
      setMessage("Este horário não está mais disponível. Selecione outro horário.");
      loadTimeSlots();
      return;
    }

    try {
      const response = await api.createAppointment({
        nome_cliente: clientName,
        telefone_cliente: phone,
        id_servico: service,
        id_horario: time,
        data_agendamento: selectedDate
      });

      const data = await response.json();

      if (data.success) {
        setMessage(
          buildAppointmentConfirmationMessage({
            clientName,
            serviceName: selectedServiceLabel,
            selectedTime: selectedTimeLabel,
            selectedDate
          })
        );
        loadTimeSlots();
      } else {
        setMessage(data.error || "⚠️ Funcionando localmente (erro no backend)");
        loadTimeSlots();
      }
    } catch (error) {
      console.log("Erro:", error);
      setMessage("⚠️ Funcionando localmente (sem backend)");
    }

    setName("");
    setPhone("");
    setService("");
    setTime("");
  }

  return (
    <section className="py-5 hero-modern">
      <div className="container">
        <div className="row align-items-center g-4">
          <div className="col-lg-6">
            <h1 className="display-5 fw-bold mb-3">💈 BarberFlow</h1>
            <p className="lead text-secondary mb-4">
              Agende seu horário com rapidez e escolha datas futuras disponíveis em poucos cliques.
            </p>
            <div className="d-flex flex-wrap gap-2">
              <span className="badge text-bg-dark">Atendimento Rápido</span>
              <span className="badge text-bg-primary">Agenda por Data</span>
              <span className="badge text-bg-secondary">Confirmação Fácil</span>
            </div>
          </div>

          <div className="col-lg-6">
            <div className="card border-0 shadow modern-card">
              <div className="card-body p-4 p-md-5">
                <h5 className="fw-semibold mb-3">Agende seu horário</h5>

                <div className="mb-3">
                  <label className="form-label">Nome</label>
                  <input
                    type="text"
                    placeholder="Seu nome"
                    value={name}
                    onChange={(event) => setName(event.target.value)}
                    className="form-control"
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label">WhatsApp</label>
                  <input
                    type="tel"
                    placeholder="Ex.: 85999998888"
                    value={phone}
                    onChange={(event) => setPhone(event.target.value)}
                    className="form-control"
                    autoComplete="tel"
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label">Data</label>
                  <input
                    type="date"
                    value={selectedDate}
                    min={todayDate}
                    max={maxDate}
                    onChange={(event) => setSelectedDate(event.target.value)}
                    className="form-control"
                  />
                  <p className="mt-2 mb-0 small text-secondary">
                    {loadingBlockedDates ? "Carregando calendário..." : currentDateLabel}
                  </p>
                  {isUnavailableDate && (
                    <p className="mt-2 mb-0 small fw-semibold text-danger">
                      {getDateUnavailableMessage()}
                    </p>
                  )}
                </div>

                <div className="mb-3">
                  <label className="form-label">Serviço</label>
                  <select
                    value={service}
                    onChange={(event) => setService(event.target.value)}
                    className="form-select"
                    disabled={loadingServices}
                  >
                    <option value="">Selecione o serviço</option>
                    {serviceOptions.map((option) => (
                      <option key={option.value} value={option.value}>{option.label}</option>
                    ))}
                  </select>
                </div>

                <div className="mb-3">
                  <label className="form-label">Horário</label>
                  <select
                    value={time}
                    onChange={(event) => setTime(event.target.value)}
                    className="form-select"
                    disabled={loadingSlots || isUnavailableDate}
                  >
                    <option value="">
                      {isUnavailableDate
                        ? "Data indisponível"
                        : loadingSlots
                          ? "Carregando horários..."
                          : availableTimeSlots.length === 0
                            ? "Nenhum horário disponível"
                            : "Selecione o horário"}
                    </option>
                    {availableTimeSlots.map((slot) => (
                      <option
                        key={slot.id}
                        value={String(slot.id)}
                      >
                        {slot.time}
                      </option>
                    ))}
                  </select>
                  {!isUnavailableDate && !loadingSlots && availableTimeSlots.length > 0 && (
                    <p className="mt-2 mb-0 small text-secondary">
                      Disponíveis: {availableTimeSlots.map((slot) => slot.time).join(", ")}
                    </p>
                  )}
                </div>

                <button
                  className="btn btn-primary w-100"
                  onClick={handleSubmit}
                  disabled={isUnavailableDate}
                >
                  {isUnavailableDate ? "Data indisponível" : "Agendar"}
                </button>

                {message && (
                  <p className="mt-3 mb-0 small fw-semibold text-secondary confirmation-message">
                    {message}
                  </p>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
