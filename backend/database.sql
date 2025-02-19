-- Таблица для администраторов
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица для концертов
CREATE TABLE IF NOT EXISTS concerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_ru VARCHAR(255) NOT NULL,
    title_et VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    title_uk VARCHAR(255) NOT NULL,
    description_ru TEXT,
    description_et TEXT,
    description_en TEXT,
    description_uk TEXT,
    date DATE NOT NULL,
    time TIME NOT NULL,
    venue_ru VARCHAR(255) NOT NULL,
    venue_et VARCHAR(255) NOT NULL,
    venue_en VARCHAR(255) NOT NULL,
    venue_uk VARCHAR(255) NOT NULL,
    price VARCHAR(100) NOT NULL,
    ticket_link VARCHAR(255),
    image_path VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица для сообщений обратной связи
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 