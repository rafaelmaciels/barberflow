import React, { useState, useEffect } from 'react';
import api from '../services/api';

function TimeSlots({ date, onSelect, value }) {
    const [slots, setSlots] = useState([]);

    useEffect(() => {
        if (!date) return;

        const fetchSlots = async () => {
            try {
                const response = await fetch(`${api.baseURL}/time-slots?date=${date}`);
                const data = await response.json();
                setSlots(data);
            } catch (error) {
                console.error("Erro ao buscar horários:", error);
            }
        };

        fetchSlots();
    }, [date]);

    return (
        <div className="time-slots-container">
            {slots.length > 0 ? (
                slots
                    .filter((slot) => slot.available == 1)
                    .map((slot) => (
                        <button
                            key={slot.id}
                            type="button"
                            onClick={() => onSelect(slot.id)}
                            className={value == slot.id ? "active" : ""}
                        >
                            {slot.time}
                        </button>
                    ))
            ) : (
                <p>Nenhum horário disponível.</p>
            )}
        </div>
    );
}

export default TimeSlots;