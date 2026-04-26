-- =============================================
-- Student Record Management System
-- Database Setup Script
-- Run this in phpMyAdmin or MySQL CLI
-- =============================================

CREATE DATABASE IF NOT EXISTS student_records_db;
USE student_records_db;

-- =============================================
-- USERS TABLE (for login: teacher/admin)
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher') NOT NULL DEFAULT 'teacher',
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- SECTIONS TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(50) NOT NULL,
    teacher_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- STUDENTS TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    section_id INT,
    grade DECIMAL(5,2) DEFAULT 0.00,
    attendance INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE SET NULL
);

-- =============================================
-- DUMMY DATA: USERS
-- Passwords are plain text for simplicity
-- In real apps, always hash passwords!
-- =============================================
INSERT INTO users (username, password, role, full_name) VALUES
('admin', 'admin123', 'admin', 'Administrator'),
('teacher1', 'teacher123', 'teacher', 'Ms. Maria Santos'),
('teacher2', 'teacher123', 'teacher', 'Mr. Jose Reyes');

-- =============================================
-- DUMMY DATA: SECTIONS
-- =============================================
INSERT INTO sections (section_name, teacher_id) VALUES
('BSIT 1-A', 2),
('BSIT 1-B', 3),
('BSIT 2-A', 2);


-- =============================================
-- DUMMY DATA: STUDENTS
-- =============================================
INSERT INTO students (student_id, first_name, last_name, email, section_id, grade, attendance) VALUES
('2024-0001', 'Juan', 'Dela Cruz', 'juan@email.com', 1, 88.50, 95),
('2024-0002', 'Maria', 'Garcia', 'maria@email.com', 1, 92.00, 98),
('2024-0003', 'Pedro', 'Reyes', 'pedro@email.com', 1, 75.00, 80),
('2024-0004', 'Ana', 'Torres', 'ana@email.com', 2, 85.50, 90),
('2024-0005', 'Carlos', 'Mendoza', 'carlos@email.com', 2, 91.00, 97),
('2024-0006', 'Liza', 'Santos', 'liza@email.com', 2, 78.50, 85),
('2024-0007', 'Mark', 'Villanueva', 'mark@email.com', 3, 95.00, 99),
('2024-0008', 'Rose', 'Aquino', 'rose@email.com', 3, 82.00, 88),
('2024-0009', 'Paolo', 'Cruz', 'paolo@email.com', 3, 70.00, 75),
('2024-0010', 'Jenny', 'Bautista', 'jenny@email.com', 1, 88.00, 93);