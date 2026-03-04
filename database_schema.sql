-- ExpressionWebsite Database Schema
-- This SQL file creates all necessary tables for the ExEC (Expression Evaluation and Conversion) application

-- Create the database (if not exists)
CREATE DATABASE IF NOT EXISTS expression;
USE expression;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conversion History table
CREATE TABLE IF NOT EXISTS conversion_history (
    conversion_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    source_type ENUM('infix', 'prefix', 'postfix') NOT NULL,
    target_type ENUM('infix', 'prefix', 'postfix') NOT NULL,
    original_expression VARCHAR(500) NOT NULL,
    converted_expression VARCHAR(500) NOT NULL,
    conversion_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_conversion_timestamp (conversion_timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Evaluation History table
CREATE TABLE IF NOT EXISTS evaluation_history (
    evaluation_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    expression_type ENUM('infix', 'prefix', 'postfix') NOT NULL,
    expression VARCHAR(500) NOT NULL,
    variables JSON NOT NULL,
    result DECIMAL(20, 10) NOT NULL,
    evaluation_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_evaluation_timestamp (evaluation_timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Additional indexes for better query performance
CREATE INDEX idx_conversion_history_user_date ON conversion_history(user_id, conversion_timestamp DESC);
CREATE INDEX idx_evaluation_history_user_date ON evaluation_history(user_id, evaluation_timestamp DESC);
