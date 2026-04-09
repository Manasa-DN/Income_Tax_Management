USE income;

-- Payments table (optional, using existing tax_revenues as payments source)
-- CREATE TABLE payments (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT NOT NULL,
--     amount DECIMAL(10,2) NOT NULL,
--     status ENUM('pending','paid','failed') DEFAULT 'paid',
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (user_id) REFERENCES Taxpayer(user_id)
-- );

-- Error logs for simple app-level tracking
CREATE TABLE IF NOT EXISTS error_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    role ENUM('taxpayer','taxprofessional','taxauthority') NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tax refunds table
CREATE TABLE IF NOT EXISTS tax_refunds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('requested','approved','rejected','processed') DEFAULT 'requested',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Taxpayer(user_id)
);


