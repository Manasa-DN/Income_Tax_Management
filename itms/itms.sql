CREATE DATABASE IF NOT EXISTS income;

USE income;

CREATE TABLE IF NOT EXISTS User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('taxpayer', 'taxprofessional', 'taxauthority') NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS Taxpayer (
    TaxpayerID INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE,
    Name VARCHAR(100),
    TIN VARCHAR(15) UNIQUE,
    Address TEXT,
    Email VARCHAR(100) UNIQUE,
    Phone VARCHAR(15),
    RegistrationDate DATE,
    Password VARCHAR(255),
    tax_professional_id INT NULL,
    FOREIGN KEY (user_id) REFERENCES User(id),
    FOREIGN KEY (tax_professional_id) REFERENCES User(id)
);

CREATE TABLE IF NOT EXISTS TaxProfessional (
    ProfessionalID INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE,
    Name VARCHAR(100),
    TIN VARCHAR(15),
    Certification_ID VARCHAR(20),
    Email VARCHAR(100) UNIQUE,
    Phone VARCHAR(15),
    RegistrationDate DATE,
    Password VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES User(id)
);

CREATE TABLE IF NOT EXISTS TaxAuthority (
    AuthorityID INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE,
    Name VARCHAR(100),
    Email VARCHAR(100) UNIQUE,
    Phone VARCHAR(15),
    Designation VARCHAR(50),
    Department VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES User(id)
);

CREATE TABLE IF NOT EXISTS Notification (
    NotificationID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT,
    Message TEXT,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Type ENUM('comment', 'tax_adjustment', 'refund', 'additional_tax', 'payment_reminder', 'general') DEFAULT 'general',
    Status ENUM('Unread', 'Read') DEFAULT 'Unread',
    FOREIGN KEY (UserID) REFERENCES Taxpayer(TaxpayerID)
);

CREATE TABLE IF NOT EXISTS tax_revenues (
    revenue_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    amount DECIMAL(10, 2),
    tax_professional_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Taxpayer(user_id)
);

CREATE TABLE IF NOT EXISTS Documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_path VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(id)
);

-- Trigger: delete related user when TaxProfessional row deleted
DELIMITER //
CREATE TRIGGER delete_user_on_taxprofessional
AFTER DELETE ON TaxProfessional
FOR EACH ROW
BEGIN
    DELETE FROM User WHERE id = OLD.user_id;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS AddTaxProfessional;

-- Stored procedure: AddTaxProfessional
DELIMITER //

CREATE PROCEDURE AddTaxProfessional(
    IN professional_name VARCHAR(255),
    IN professional_email VARCHAR(255),
    IN professional_phone VARCHAR(15),
    IN professional_tin VARCHAR(15),
    IN professional_password VARCHAR(255),
    IN certification_id VARCHAR(20)
)
BEGIN
    DECLARE new_user_id INT;
    INSERT INTO User (name, email, phone, password, role)
    VALUES (professional_name, professional_email, professional_phone, professional_password, 'taxprofessional');
    SET new_user_id = LAST_INSERT_ID();
    INSERT INTO TaxProfessional (user_id, name, email, phone, TIN, RegistrationDate, Certification_ID, Password)
    VALUES (new_user_id, professional_name, professional_email, professional_phone, professional_tin, NOW(), certification_id, professional_password);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS DeleteTaxProfessional;

-- Stored procedure: DeleteTaxProfessional
DELIMITER //
CREATE PROCEDURE DeleteTaxProfessional(IN prof_user_id INT)
BEGIN
    DELETE FROM TaxProfessional WHERE user_id = prof_user_id;
    DELETE FROM User WHERE id = prof_user_id;
END //
DELIMITER ;

CREATE TABLE IF NOT EXISTS estimated_tax (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    estimated_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    professional_adjustment DECIMAL(15,2) DEFAULT NULL,
    adjustment_reason TEXT DEFAULT NULL,
    paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Taxpayer(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tax_calculation_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    calculation_type ENUM('estimated', 'final', 'adjustment') NOT NULL,
    total_income DECIMAL(15,2) NOT NULL,
    calculated_tax DECIMAL(15,2) NOT NULL,
    tax_slabs JSON DEFAULT NULL,
    calculated_by INT DEFAULT NULL,
    calculation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES Taxpayer(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quarterly_estimates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    quarter ENUM('Q1', 'Q2', 'Q3', 'Q4') NOT NULL,
    year YEAR NOT NULL,
    estimated_income DECIMAL(15,2) NOT NULL,
    estimated_tax DECIMAL(15,2) NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    due_date DATE NOT NULL,
    status ENUM('pending', 'paid', 'overdue') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Taxpayer(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_quarter_year (user_id, quarter, year)
);