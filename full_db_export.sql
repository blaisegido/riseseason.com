-- RiseSeason Full DB Export for LWS Deployment - VERSION 2 (Consolidated)
-- This file includes all columns and tables from migrations and actual models usage.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1) CATEGORIES (Required for Gigs)
CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  parent_id INT UNSIGNED NULL,
  INDEX idx_categories_parent (parent_id),
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) USERS (Consolidated)
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  country VARCHAR(80) NOT NULL,
  role ENUM('freelancer','employeur','admin','contributeur') NOT NULL DEFAULT 'freelancer',
  title VARCHAR(120) NULL,
  daily_rate DECIMAL(10,2) NULL,
  languages VARCHAR(255) NULL,
  availability ENUM('available','soon','unavailable') DEFAULT 'available',
  website VARCHAR(255) NULL,
  linkedin VARCHAR(255) NULL,
  github VARCHAR(255) NULL,
  bio TEXT NULL,
  skills TEXT NULL,
  profile_photo VARCHAR(255) NULL,
  verified_email TINYINT(1) NOT NULL DEFAULT 0,
  level ENUM('nouveau','confirmé','expert','elite') NOT NULL DEFAULT 'nouveau',
  subscription_status ENUM('free', 'premium') DEFAULT 'free',
  subscription_expires_at DATETIME NULL,
  rise_seeds INT DEFAULT 0,
  seeds INT DEFAULT 0,
  performance_score DECIMAL(5,2) DEFAULT 0.00,
  last_seen TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) GIGS (Consolidated)
CREATE TABLE IF NOT EXISTS gigs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  slug VARCHAR(180) NULL UNIQUE,
  category_id INT UNSIGNED NULL,
  description TEXT NOT NULL,
  price_base DECIMAL(10,2) NULL,
  price_eur DECIMAL(10,2) NOT NULL,
  delivery_days INT NULL,
  status ENUM('pending','approved','rejected','paused') NOT NULL DEFAULT 'pending',
  is_sponsored BOOLEAN DEFAULT 0,
  sponsorship_expires_at DATETIME NULL,
  rejection_reason VARCHAR(255) NULL,
  rejection_feedback TEXT NULL,
  moderated_by INT UNSIGNED NULL,
  moderated_at TIMESTAMP NULL,
  main_image VARCHAR(255) NULL,
  gallery JSON NULL,
  faq JSON NULL,
  extras JSON NULL,
  is_express TINYINT(1) NOT NULL DEFAULT 0,
  timezone_africa TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_gigs_status (status),
  INDEX idx_gigs_user (user_id),
  INDEX idx_gigs_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) JOBS
CREATE TABLE IF NOT EXISTS jobs (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) MESSAGES
CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_id INT UNSIGNED NOT NULL,
  receiver_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6) PORTFOLIO
CREATE TABLE IF NOT EXISTS portfolio_files (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7) ECONOMY (User Wallets, Orders, Transactions)
CREATE TABLE IF NOT EXISTS user_wallets (
    user_id INT UNSIGNED PRIMARY KEY,
    balance DECIMAL(10, 2) DEFAULT 0.00,
    pending_balance DECIMAL(10, 2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'EUR',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT UNSIGNED NOT NULL,
    seller_id INT UNSIGNED NOT NULL,
    gig_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    commission DECIMAL(10, 2) DEFAULT 1.00,
    net_to_seller DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid', 'in_progress', 'delivered', 'completed', 'disputed', 'cancelled') DEFAULT 'pending',
    stripe_payment_intent_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (seller_id) REFERENCES users(id),
    FOREIGN KEY (gig_id) REFERENCES gigs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED NULL,
    type ENUM('credit', 'debit', 'withdrawal', 'subscription', 'seeds_purchase', 'sponsorship_spend') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    seeds INT DEFAULT 0,
    description TEXT,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8) BLOG / POSTS
CREATE TABLE IF NOT EXISTS posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(190) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT NULL,
    featured_image VARCHAR(255) NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9) REVIEWS
CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gig_id INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED NOT NULL UNIQUE,
    buyer_id INT UNSIGNED NOT NULL,
    freelancer_id INT UNSIGNED NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (freelancer_id) REFERENCES users(id),
    FOREIGN KEY (gig_id) REFERENCES gigs(id),
    INDEX idx_reviews_freelancer (freelancer_id),
    INDEX idx_reviews_gig (gig_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10) SAVED GIGS
CREATE TABLE IF NOT EXISTS saved_gigs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    gig_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_gig (user_id, gig_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (gig_id) REFERENCES gigs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEEDS
INSERT IGNORE INTO categories (name) VALUES
('Développement web'), ('Marketing digital'), ('Design graphique'),
('Rédaction & Traduction'), ('Montage vidéo'), ('Community management'),
('Assistance virtuelle'), ('Autre');

-- SUB-CATEGORIES
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Site vitrine', id FROM categories WHERE name='Développement web';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'E-commerce (Shopify/WooCommerce)', id FROM categories WHERE name='Développement web';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'WordPress sur mesure', id FROM categories WHERE name='Développement web';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Landing page conversion', id FROM categories WHERE name='Développement web';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Débogage / Correction bugs', id FROM categories WHERE name='Développement web';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Maintenance technique', id FROM categories WHERE name='Développement web';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Intégration HTML/CSS', id FROM categories WHERE name='Développement web';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'API / Backend (PHP, Node, etc.)', id FROM categories WHERE name='Développement web';

INSERT IGNORE INTO categories (name, parent_id)
SELECT 'SEO On-page', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Google Ads', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Email marketing', id FROM categories WHERE name='Marketing digital';

INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Logo', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'identitée visuelle complète', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Retouche photo', id FROM categories WHERE name='Design graphique';

SET FOREIGN_KEY_CHECKS = 1;
