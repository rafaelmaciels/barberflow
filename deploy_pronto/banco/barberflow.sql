-- BarberFlow Database Schema
-- Script base para importacao via phpMyAdmin

SET NAMES utf8mb4;

-- Tabela de usuarios administrativos
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario admin padrao
INSERT INTO users (username, password)
VALUES ('admin', MD5('admin123'))
ON DUPLICATE KEY UPDATE password = MD5('admin123');

-- Tabela de servicos
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO services (name, price, duration)
SELECT 'Corte Masculino', 25.00, 30
WHERE NOT EXISTS (
    SELECT 1 FROM services WHERE name = 'Corte Masculino'
);

INSERT INTO services (name, price, duration)
SELECT 'Barba', 15.00, 20
WHERE NOT EXISTS (
    SELECT 1 FROM services WHERE name = 'Barba'
);

INSERT INTO services (name, price, duration)
SELECT 'Corte + Barba', 35.00, 45
WHERE NOT EXISTS (
    SELECT 1 FROM services WHERE name = 'Corte + Barba'
);

INSERT INTO services (name, price, duration)
SELECT 'Sobrancelha', 10.00, 15
WHERE NOT EXISTS (
    SELECT 1 FROM services WHERE name = 'Sobrancelha'
);

-- Tabela de horarios disponiveis
CREATE TABLE IF NOT EXISTS time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    time TIME NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO time_slots (time) VALUES
('08:00:00'),
('08:30:00'),
('09:00:00'),
('09:30:00'),
('10:00:00'),
('10:30:00'),
('11:00:00'),
('11:30:00'),
('12:00:00'),
('13:30:00'),
('14:00:00'),
('14:30:00'),
('15:00:00'),
('15:30:00'),
('16:00:00'),
('16:30:00'),
('17:00:00'),
('17:30:00'),
('18:00:00')
ON DUPLICATE KEY UPDATE time = VALUES(time);

-- Tabela de agendamentos
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    service_id INT NOT NULL,
    time_slot_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    status ENUM('agendado', 'cancelado', 'concluido') DEFAULT 'agendado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_appointments_service
        FOREIGN KEY (service_id) REFERENCES services(id),
    CONSTRAINT fk_appointments_time_slot
        FOREIGN KEY (time_slot_id) REFERENCES time_slots(id),
    INDEX idx_appointments_date_status (appointment_date, status),
    INDEX idx_appointments_slot_date_status (time_slot_id, appointment_date, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de configuracoes
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barbershop_open BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (id, barbershop_open)
VALUES (1, TRUE)
ON DUPLICATE KEY UPDATE barbershop_open = VALUES(barbershop_open);

-- Regra operacional:
-- Domingo permanece fechado e o frontend exibe a mensagem fixa: FECHADO!
