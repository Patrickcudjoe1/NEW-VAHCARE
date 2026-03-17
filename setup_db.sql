-- Database schema for VAH Care

CREATE DATABASE IF NOT EXISTS vah_care;
USE vah_care;

-- Table for contact form submissions
CREATE TABLE IF NOT EXISTS contact_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    service_interest VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for job applications
CREATE TABLE IF NOT EXISTS job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    experience VARCHAR(50) NOT NULL,
    availability VARCHAR(50) NOT NULL,
    cover_letter TEXT NOT NULL,
    resume_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for admin users
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a default admin (password: admin123)
-- In production, the user should be prompted to change this
INSERT INTO admins (username, password_hash, email) 
VALUES ('admin', '$2y$10$vIK6S/6H8G6n.k/K98/q9.qQ9qQ9qQ9qQ9qQ9qQ9qQ9qQ9qQ9qQ9', 'info@vahcare.co.uk');
