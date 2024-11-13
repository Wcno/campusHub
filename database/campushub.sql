CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    phone_number VARCHAR(20) NOT NULL,
    img_profile VARCHAR(255) DEFAULT 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR4g_2Qj3LsNR-iqUAFm6ut2EQVcaou4u2YXw&s',
    age INT NOT NULL
);
CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    time_end TIME NOT NULL,
    lugar VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    image_url VARCHAR(255)
);
CREATE TABLE inscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES eventos(id) ON DELETE CASCADE
);
-- Inserting admin user
INSERT INTO users (username, email, password, role, phone_number, age)
VALUES ('admin', 'admin@admin.com', '$2y$10$7ITGd5n2N1noA2DL8VWuZ.njh3qZMJjD1Rxxm4KuHHD/GHmQkjYFq', 'admin', '123456789', 40);
