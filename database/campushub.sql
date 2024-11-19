CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    phone_number VARCHAR(20) NOT NULL,
    img_profile VARCHAR(255) DEFAULT 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR4g_2Qj3LsNR-iqUAFm6ut2EQVcaou4u2YXw&s',
    birth_date DATE NOT NULL
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    time_end TIME NOT NULL,
    capacity INT NOT NULL,
    image_url VARCHAR(255),
    location_id INT NOT NULL,
    tag_id INT,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE SET NULL,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
);

CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    inscription_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Inserting admin user
INSERT INTO users (name, email, password, role, phone_number, birth_date)
VALUES ('Admin', 'admin@admin.com', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'admin', '123456789', '1984-11-23');

INSERT INTO tags (name) VALUES
('Tecnología'),
('Educación'),
('Arte'),
('Deporte'),
('Música');

INSERT INTO users (name, password, email, role, phone_number, birth_date) VALUES
('Juan Pérez', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'juan.perez@utp.ac.pa', 'user', '8-123-4567', '1990-05-15'),
('María Soto', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'maria.soto@utp.ac.pa', 'admin', '8-987-6543', '1985-03-22'),
('Carlos Gómez', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'carlos.gomez@utp.ac.pa', 'user', '4-567-8901', '1992-11-08'),
('Ana Torres', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'ana.torres@utp.ac.pa', 'user', '4-321-7654', '1995-07-10'),
('Luis Martínez', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'luis.martinez@utp.ac.pa', 'user', '3-987-6543', '1988-09-17'),
('Gabriela López', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'gabriela.lopez@utp.ac.pa', 'user', '9-876-5432', '1997-12-05'),
('Pedro Ramírez', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'pedro.ramirez@utp.ac.pa', 'user', '3-210-9876', '1993-04-25'),
('Sofía Sánchez', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'sofia.sanchez@utp.ac.pa', 'user', '8-654-3210', '1998-01-30'),
('Miguel Herrera', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'miguel.herrera@utp.ac.pa', 'user', '6-789-1234', '1991-06-18'),
('Laura Vega', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'laura.vega@utp.ac.pa', 'user', '5-432-8765', '1990-10-02');


INSERT INTO locations (name, capacity) VALUES
('Auditorio UTP Panamá', 500),
('Lobby de Sistemas', 1000),
('Lobby del Edificio 3', 300),
('Estacionamientos', 800),
('Area Social', 1500);

INSERT INTO events (title, description, date, time, time_end, capacity, image_url, location_id, tag_id) VALUES
('Feria Tecnológica', 'Exposición de innovaciones tecnológicas.', '2024-12-01', '10:00:00', '16:00:00', 200, 'default.png', 1, 1),
('Conferencia de Ingeniería', 'Conferencias impartidas por expertos en ingeniería.', '2024-12-05', '09:00:00', '15:00:00', 300, 'default.png', 2, 2),
('Taller de Programación', 'Curso intensivo de desarrollo de software.', '2024-12-10', '13:00:00', '17:00:00', 50, 'default.png', 3, 1),
('Concierto de Fin de Año', 'Celebración musical para despedir el año.', '2024-12-31', '20:00:00', '23:59:59', 1000, 'default.png', 4, 5),
('Maratón de Innovación', 'Evento de 24 horas para desarrollar proyectos innovadores.', '2024-11-25', '08:00:00', '08:00:00', 150, 'default.png', 1, 1),
('Festival de Cine', 'Proyección de películas de todo el mundo.', '2024-12-20', '18:00:00', '22:00:00', 500, 'default.png', 2, 3),
('Exposición de Arte', 'Exhibición de obras de artistas locales.', '2024-11-30', '10:00:00', '18:00:00', 200, 'default.png', 3, 3),
('Charla de Liderazgo', 'Aprende habilidades clave de liderazgo.', '2024-12-12', '14:00:00', '17:00:00', 100, 'default.png', 4, 2),
('Encuentro de Robótica', 'Competencia de robots autónomos.', '2024-12-15', '09:00:00', '16:00:00', 300, 'default.png', 5, 1),
('Torneo de Videojuegos', 'Competición de eSports con premios en efectivo.', '2024-12-18', '10:00:00', '22:00:00', 400, 'default.png', 5, 4);

INSERT INTO inscriptions (user_id, event_id, inscription_date) VALUES
(1, 1, '2024-11-01 10:00:00'),
(2, 1, '2024-11-02 14:30:00'),
(3, 2, '2024-11-03 09:15:00'),
(4, 2, '2024-11-04 11:45:00'),
(5, 3, '2024-11-05 16:00:00'),
(6, 3, '2024-11-06 13:20:00'),
(7, 4, '2024-11-07 18:40:00'),
(8, 4, '2024-11-08 20:00:00'),
(9, 5, '2024-11-09 08:30:00'),
(10, 5, '2024-11-10 12:00:00'),
(1, 6, '2024-11-11 10:10:00'),
(2, 6, '2024-11-12 14:00:00'),
(3, 7, '2024-11-13 09:50:00'),
(4, 7, '2024-11-14 11:30:00'),
(5, 8, '2024-11-15 15:15:00'),
(6, 8, '2024-11-16 12:45:00'),
(7, 9, '2024-11-17 17:20:00'),
(8, 9, '2024-11-18 19:10:00'),
(9, 10, '2024-11-19 09:00:00'),
(10, 10, '2024-11-20 10:30:00');
