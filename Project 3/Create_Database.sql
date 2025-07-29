-- Create the schema 
CREATE DATABASE project_three;
-- Use the schema
USE project_three;
-- Create the table users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME,
    login_count INT DEFAULT 0,
    failed_attempts INT DEFAULT 0,
    is_locked TINYINT(1) DEFAULT 0,
    security_question VARCHAR(255) NOT NULL,
    security_answer VARCHAR(255) NOT NULL
);

CREATE TABLE watchlist (
    itemID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    stock_ticker VARCHAR(50) NOT NULL,
    stock_name VARCHAR(255) NOT NULL,
    added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES users(id) ON DELETE CASCADE
);