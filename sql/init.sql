CREATE DATABASE IF NOT EXISTS riseseason CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE riseseason;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  country VARCHAR(80) NOT NULL,
  role ENUM('freelancer','employeur','admin','contributeur') NOT NULL DEFAULT 'freelancer',
  bio TEXT NULL,
  skills TEXT NULL,
  profile_photo VARCHAR(255) NULL,
  verified_email TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE
);

INSERT INTO categories (name) VALUES
('Développement web'),
('Marketing digital'),
('Design graphique'),
('Rédaction & Traduction'),
('Montage vidéo'),
('Community management'),
('Assistance virtuelle'),
('Autre');

CREATE TABLE gigs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  price_eur DECIMAL(10,2) NOT NULL,
  category VARCHAR(120) NOT NULL,
  status ENUM('pending','approved') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE jobs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  budget_eur DECIMAL(10,2) NOT NULL,
  deadline_text VARCHAR(120) NOT NULL,
  category VARCHAR(120) NOT NULL,
  status ENUM('pending','approved') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_id INT UNSIGNED NOT NULL,
  receiver_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE portfolio_files (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
