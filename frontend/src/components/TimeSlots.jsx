import React, { useState, useEffect } from "react";
import api from "../services/api";

function TimeSlots({ date, onSelect }) {
  const [slots, setSlots] = useState([]);

  useEffect(() => {
    if (!date) return;

    async function fetchSlots() {
      try {
        const res = await api.getTimeSlots(date);
        const data = await res.json();
        setSlots(data);
      } catch (error) {
        console.error("Erro ao buscar horários:", error);
      }
    }

    fetchSlots();
  }, [date]);

  return (
    <div className="time-slots">
      {slots.length > 0 ? (
        slots.map((slot) => (
          <button
            key={slot.id}
            type="button" // 🔥 ESSENCIAL
            onClick={() => onSelect(slot.id)}
          >
            {slot.time}
          </button>
        ))
      ) : (
        <p>Sem horários disponíveis</p>
      )}
    </div>
  );
}

export default TimeSlots;