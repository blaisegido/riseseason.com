-- RiseSeason Full DB Export for LWS Deployment - VERSION 4 (Exact Local Match)
-- This version is a perfect mirror of the local MySQL schema to ensure data import compatibility.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1) CATEGORIES
CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  parent_id INT UNSIGNED NULL,
  INDEX idx_categories_parent (parent_id),
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) USERS
CREATE TABLE IF NOT EXISTS users (
  id int unsigned NOT NULL AUTO_INCREMENT,
  email varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  password_hash varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  first_name varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  last_name varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  username varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  country varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  role enum('freelancer','employeur','admin','contributeur') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'freelancer',
  subscription_status enum('free','premium') COLLATE utf8mb4_unicode_ci DEFAULT 'free',
  subscription_expires_at datetime DEFAULT NULL,
  seeds int DEFAULT '0',
  performance_score decimal(5,2) DEFAULT '0.00',
  title varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  daily_rate decimal(10,2) DEFAULT NULL,
  languages varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  availability enum('available','soon','unavailable') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  website varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  linkedin varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  github varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  bio text COLLATE utf8mb4_unicode_ci,
  skills text COLLATE utf8mb4_unicode_ci,
  profile_photo varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  verified_email tinyint(1) NOT NULL DEFAULT '0',
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  level enum('nouveau','confirmé','expert','elite') COLLATE utf8mb4_unicode_ci DEFAULT 'nouveau',
  last_seen timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  UNIQUE KEY username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) GIGS
CREATE TABLE IF NOT EXISTS gigs (
  id int unsigned NOT NULL AUTO_INCREMENT,
  user_id int unsigned NOT NULL,
  title varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  slug varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  category_id int unsigned DEFAULT NULL,
  description text COLLATE utf8mb4_unicode_ci NOT NULL,
  price_base decimal(10,2) DEFAULT NULL,
  delivery_days int DEFAULT NULL,
  price_eur decimal(10,2) NOT NULL,
  category varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  status enum('pending','approved','rejected','paused') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  is_sponsored tinyint(1) DEFAULT '0',
  sponsorship_expires_at datetime DEFAULT NULL,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  main_image varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  gallery json DEFAULT NULL,
  faq json DEFAULT NULL,
  extras json DEFAULT NULL,
  is_express tinyint(1) NOT NULL DEFAULT '0',
  timezone_africa tinyint(1) NOT NULL DEFAULT '0',
  rejection_reason varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  rejection_feedback text COLLATE utf8mb4_unicode_ci,
  moderated_by int unsigned DEFAULT NULL,
  moderated_at timestamp NULL DEFAULT NULL,
  deleted_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_gigs_slug (slug),
  KEY idx_gigs_status (status),
  KEY idx_gigs_user (user_id),
  KEY idx_gigs_category (category_id),
  KEY idx_gigs_moderated_by (moderated_by),
  KEY idx_gigs_moderated_at (moderated_at),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4) JOBS
CREATE TABLE IF NOT EXISTS jobs (
  id int unsigned NOT NULL AUTO_INCREMENT,
  user_id int unsigned NOT NULL,
  title varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  description text COLLATE utf8mb4_unicode_ci NOT NULL,
  budget_eur decimal(10,2) NOT NULL,
  deadline_text varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  category varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  hero_image varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  status enum('pending','approved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5) MESSAGES
CREATE TABLE IF NOT EXISTS messages (
  id int unsigned NOT NULL AUTO_INCREMENT,
  sender_id int unsigned NOT NULL,
  receiver_id int unsigned NOT NULL,
  content text COLLATE utf8mb4_unicode_ci NOT NULL,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY sender_id (sender_id),
  KEY receiver_id (receiver_id),
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6) PORTFOLIO
CREATE TABLE IF NOT EXISTS portfolio_files (
  id int unsigned NOT NULL AUTO_INCREMENT,
  user_id int unsigned NOT NULL,
  title varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  description text COLLATE utf8mb4_unicode_ci,
  file_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  original_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7) ECONOMY
CREATE TABLE IF NOT EXISTS user_wallets (
  user_id int unsigned NOT NULL,
  balance decimal(10,2) DEFAULT '0.00',
  pending_balance decimal(10,2) DEFAULT '0.00',
  currency varchar(3) DEFAULT 'EUR',
  updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_wallet_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id int unsigned NOT NULL AUTO_INCREMENT,
  buyer_id int unsigned NOT NULL,
  seller_id int unsigned NOT NULL,
  gig_id int unsigned NOT NULL,
  amount decimal(10,2) NOT NULL,
  commission decimal(10,2) DEFAULT '1.00',
  net_to_seller decimal(10,2) NOT NULL,
  status enum('pending','paid','in_progress','delivered','completed','disputed','cancelled') DEFAULT 'pending',
  stripe_payment_intent_id varchar(255) DEFAULT NULL,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (buyer_id) REFERENCES users(id),
  FOREIGN KEY (seller_id) REFERENCES users(id),
  FOREIGN KEY (gig_id) REFERENCES gigs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transactions (
  id int unsigned NOT NULL AUTO_INCREMENT,
  user_id int unsigned NOT NULL,
  order_id int unsigned DEFAULT NULL,
  type enum('credit','debit','withdrawal','subscription','seeds_purchase','sponsorship_spend') NOT NULL,
  amount decimal(10,2) NOT NULL,
  seeds int DEFAULT '0',
  status enum('pending','completed','failed') DEFAULT 'pending',
  description text,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8) BLOG
CREATE TABLE IF NOT EXISTS posts (
  id int unsigned NOT NULL AUTO_INCREMENT,
  user_id int unsigned NOT NULL,
  title varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  slug varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  content text COLLATE utf8mb4_unicode_ci NOT NULL,
  excerpt text COLLATE utf8mb4_unicode_ci,
  featured_image varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  status enum('draft','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY slug (slug),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9) REVIEWS
CREATE TABLE IF NOT EXISTS reviews (
  id int unsigned NOT NULL AUTO_INCREMENT,
  order_id int unsigned NOT NULL,
  gig_id int unsigned NOT NULL,
  buyer_id int unsigned NOT NULL,
  freelancer_id int unsigned NOT NULL,
  rating tinyint unsigned NOT NULL,
  comment text COLLATE utf8mb4_unicode_ci,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_review_order (order_id),
  FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (freelancer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (gig_id) REFERENCES gigs(id) ON DELETE CASCADE,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT chk_rating CHECK ((rating between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10) SAVED GIGS
CREATE TABLE IF NOT EXISTS saved_gigs (
  user_id int unsigned NOT NULL,
  gig_id int unsigned NOT NULL,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id,gig_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (gig_id) REFERENCES gigs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEEDS Categories
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

SET FOREIGN_KEY_CHECKS = 1;
