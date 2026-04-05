import { useEffect, useMemo, useState } from "react";
import api from "../services/api";
import { formatBrazilWeekdayAndDate, getBrazilISODate, isBrazilSunday } from "../utils/dateTime";

function formatMinutesToTime(totalMinutes) {
  const hours = String(Math.floor(totalMinutes / 60)).padStart(2, "0");
  const minutes = String(totalMinutes % 60).padStart(2, "0");
  return `${hours}h${minutes}`;
}

function generateTimeSlots(startHour, startMinute, endHour, endMinute, stepMinutes = 30) {
  const start = (startHour * 60) + startMinute;
  const end = (endHour * 60) + endMinute;
  const slots = [];

  for (let current = start; current <= end; current += stepMinutes) {
    slots.push(formatMinutesToTime(current));
  }

  return slots;
}

function getOptionLabelByValue(options, value) {
  const option = options.find((item) => String(item.value) === String(value));
  return option ? option.label : value;
}

function buildAppointmentConfirmationMessage({ clientName, serviceName, selectedTime, selectedDate }) {
  const morningSlots = generateTimeSlots(8, 0, 12, 0).join(", ");
  const afternoonSlots = generateTimeSlots(13, 30, 18, 0).join(", ");

  return `Olá, ${clientName}!\n\nSeu agendamento foi confirmado com sucesso para ${selectedDate} às ${selectedTime}, serviço: ${serviceName}.\n\nInformamos que novos horários estão disponíveis para atendimento em intervalos de 30 minutos:\n\nManhã (08h00 às 12h00):\n${morningSlots}.\n\nTarde (13h30 às 18h00):\n${afternoonSlots}.\n\nSe precisar de ajustes, estamos à disposição.`;
}

export default function Home() {
  const sundayClosedMessage = "FECHADO!";
  const [name, setName] = useState("");
  const [service, setService] = useState("");
  const [time, setTime] = useState("");
  const [message, setMessage] = useState("");
  const [services, setServices] = useState([]);
  const [loadingServices, setLoadingServices] = useState(true);
  const [timeSlots, setTimeSlots] = useState([]);
  const [loadingSlots, setLoadingSlots] = useState(true);
  const [currentDate, setCurrentDate] = useState(() => getBrazilISODate());

  const currentDateLabel = useMemo(() => {
    return formatBrazilWeekdayAndDate(currentDate);
  }, [currentDate]);

  const isSunday = useMemo(() => {
    return isBrazilSunday(currentDate);
  }, [currentDate]);

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
    if (isSunday) {
      setLoadingSlots(false);
      setTimeSlots([]);
      setTime("");
      return;
    }

    loadTimeSlots();
  }, [currentDate, isSunday]);

  useEffect(() => {
    const intervalId = setInterval(() => {
      const nowDate = getBrazilISODate();
      setCurrentDate((previousDate) => {
        if (previousDate !== nowDate) {
          setTime("");
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

      if (Array.isArray(data)) {
        setServices(data);
      } else {
        setServices([]);
      }
    } catch (error) {
      console.log("Erro ao buscar serviços:", error);
      setServices([]);
    } finally {
      setLoadingServices(false);
    }
  }

  async function loadTimeSlots() {
    setLoadingSlots(true);

    try {
      const res = await api.getTimeSlots(currentDate);
      const data = await res.json();

      if (Array.isArray(data)) {
        setTimeSlots(data);
      } else {
        setTimeSlots([]);
      }
    } catch (error) {
      console.log("Erro ao buscar horários:", error);
      setTimeSlots([]);
    } finally {
      setLoadingSlots(false);
    }
  }

  // 🔥 FUNÇÃO PRINCIPAL
  async function handleSubmit() {
    console.log("🔥 Clique funcionando");

    if (isSunday) {
      setMessage(sundayClosedMessage);
      return;
    }

    if (!name || !service || !time) {
      setMessage("Preencha todos os campos!");
      return;
    }

    const selectedDate = currentDate;
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
        id_servico: service,
        id_horario: time,
        data_agendamento: selectedDate
      });

      const data = await response.json();

      console.log("📥 Backend:", data);

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

      // 🔥 fallback seguro
      setMessage("⚠️ Funcionando localmente (sem backend)");
    }

    // limpa campos
    setName("");
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
              Agende seu horário com rapidez e organize seu atendimento em poucos cliques.
            </p>
            <div className="d-flex flex-wrap gap-2">
              <span className="badge text-bg-dark">Atendimento Rápido</span>
              <span className="badge text-bg-primary">Fluxo Simples</span>
              <span className="badge text-bg-secondary">Barbearia Moderna</span>
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
                    onChange={(e) => setName(e.target.value)}
                    className="form-control"
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label">Serviço</label>
                  <select
                    value={service}
                    onChange={(e) => setService(e.target.value)}
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
                  <label className="form-label">Horário - {currentDateLabel}</label>
                  <select
                    value={time}
                    onChange={(e) => setTime(e.target.value)}
                    className="form-select"
                    disabled={loadingSlots || isSunday}
                  >
                    <option value="">
                      {isSunday
                        ? sundayClosedMessage
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
                  {!isSunday && !loadingSlots && availableTimeSlots.length > 0 && (
                    <p className="mt-2 mb-0 small text-secondary">
                      Disponíveis: {availableTimeSlots.map((slot) => slot.time).join(", ")}
                    </p>
                  )}
                  {isSunday && (
                    <p className="mt-2 mb-0 small fw-semibold text-danger">
                      {sundayClosedMessage}
                    </p>
                  )}
                </div>

                <button
                  className="btn btn-primary w-100"
                  onClick={handleSubmit}
                  disabled={isSunday}
                >
                  {isSunday ? sundayClosedMessage : "Agendar"}
                </button>

                {/* Feedback */}
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
