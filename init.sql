CREATE DATABASE IF NOT EXISTS rasxodi;
USE rasxodi;

CREATE TABLE IF NOT EXISTS cards (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    initial_balance DECIMAL(15, 2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS card_transactions (
    id VARCHAR(50) PRIMARY KEY,
    card_id VARCHAR(50),
    type ENUM('in', 'out') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    description TEXT,
    FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cash_transactions (
    id VARCHAR(50) PRIMARY KEY,
    type ENUM('in', 'out') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    recipient VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
