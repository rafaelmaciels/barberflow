-- BarberFlow Database Schema
-- Execute este script no phpMyAdmin para criar as tabelas

-- Tabela de usuários (admin)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Inserir usuário admin padrão (senha: admin123)
INSERT INTO users (username, password) VALUES ('admin', MD5('admin123'));

-- Tabela de serviços
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL -- em minutos
);

-- Inserir serviços de exemplo
INSERT INTO services (name, price, duration) VALUES
('Corte Masculino', 25.00, 30),
('Barba', 15.00, 20),
('Corte + Barba', 35.00, 45),
('Sobrancelha', 10.00, 15);

-- Tabela de horários disponíveis
CREATE TABLE IF NOT EXISTS time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    time TIME NOT NULL,
    available TINYINT(1) NOT NULL DEFAULT 1
);

-- Inserir horários de exemplo (8:00 às 18:00, a cada 30 min)
INSERT INTO time_slots (time) VALUES
('08:00:00'), ('08:30:00'), ('09:00:00'), ('09:30:00'),
('10:00:00'), ('10:30:00'), ('11:00:00'), ('11:30:00'),
('12:00:00'), ('12:30:00'), ('13:00:00'), ('13:30:00'),
('14:00:00'), ('14:30:00'), ('15:00:00'), ('15:30:00'),
('16:00:00'), ('16:30:00'), ('17:00:00'), ('17:30:00'),
('18:00:00');

-- Tabela de agendamentos
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    service_id INT NOT NULL,
    time_slot_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    status ENUM('agendado', 'cancelado', 'concluido') DEFAULT 'agendado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (time_slot_id) REFERENCES time_slots(id)
);

-- Tabela de configurações
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barbershop_open BOOLEAN DEFAULT TRUE
);

-- Inserir configuração padrão
INSERT INTO settings (barbershop_open) VALUES (TRUE);
