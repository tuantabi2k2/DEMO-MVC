
CREATE DATABASE IF NOT EXISTS mvc_demo
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE mvc_demo;

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    birth_year SMALLINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO patients (name, birth_year)
VALUES
    ('Nguyễn Văn An', 1995),
    ('Trần Thị Bình', 2000),
    ('Lê Văn Cường', 1988);
