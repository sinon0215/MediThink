CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notification_time TIME DEFAULT NULL,
    morning_time TIME DEFAULT NULL,
    lunch_time TIME DEFAULT NULL,
    evening_time TIME DEFAULT NULL,
    bedtime_time TIME DEFAULT NULL,
    secret_question VARCHAR(255) DEFAULT NULL,
    secret_answer VARCHAR(255) DEFAULT NULL,
    is_first_login TINYINT(1) NOT NULL DEFAULT 1
);
