CREATE DATABASE IF NOT EXISTS noteapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE noteapp;

DROP TABLE IF EXISTS note_share;
DROP TABLE IF EXISTS note_image;
DROP TABLE IF EXISTS note_label;
DROP TABLE IF EXISTS label;
DROP TABLE IF EXISTS note;
DROP TABLE IF EXISTS user;

-- User table
CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Note table (content fields in Vietnamese)
CREATE TABLE note (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    tieu_de VARCHAR(255) NOT NULL,
    noi_dung TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

-- Label table
CREATE TABLE label (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

-- Note-Label relationship table
CREATE TABLE note_label (
    note_id INT NOT NULL,
    label_id INT NOT NULL,
    PRIMARY KEY (note_id, label_id),
    FOREIGN KEY (note_id) REFERENCES note(id) ON DELETE CASCADE,
    FOREIGN KEY (label_id) REFERENCES label(id) ON DELETE CASCADE
);

-- Note images table
CREATE TABLE note_image (
    id INT PRIMARY KEY AUTO_INCREMENT,
    note_id INT NOT NULL,
    path VARCHAR(255) NOT NULL,
    FOREIGN KEY (note_id) REFERENCES note(id) ON DELETE CASCADE
);

-- Note sharing table
CREATE TABLE note_share (
    id INT PRIMARY KEY AUTO_INCREMENT,
    note_id INT NOT NULL,
    shared_with_email VARCHAR(255) NOT NULL,
    can_edit TINYINT(1) DEFAULT 0,
    FOREIGN KEY (note_id) REFERENCES note(id) ON DELETE CASCADE
);