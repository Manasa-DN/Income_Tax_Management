USE income;

-- Optional: dedicated admin table (not strictly required with hardcoded login)
CREATE TABLE IF NOT EXISTS AdminUser (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed an admin user record for reference (password: admin@123)
INSERT IGNORE INTO AdminUser (username, password_hash)
VALUES ('admin', '$2y$10$z7S8hM1b0w0y9Zy1y7D9E.OF7k3kqvW2xw1D4qzq5x0m7m7h9r1QK');


