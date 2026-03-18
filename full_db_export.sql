-- RiseSeason Full DB Export for LWS Deployment
-- Combined Schema & Seeds

-- 1) BASE TABLES
CREATE TABLE IF NOT EXISTS users (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  parent_id INT UNSIGNED NULL,
  INDEX idx_categories_parent (parent_id),
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  rejection_reason VARCHAR(255) NULL,
  rejection_feedback TEXT NULL,
  moderated_by INT UNSIGNED NULL,
  moderated_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  main_image VARCHAR(255) NULL,
  gallery JSON NULL,
  faq JSON NULL,
  extras JSON NULL,
  is_express TINYINT(1) NOT NULL DEFAULT 0,
  timezone_africa TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_gigs_status (status),
  INDEX idx_gigs_user (user_id),
  INDEX idx_gigs_category (category_id),
  INDEX idx_gigs_moderated_by (moderated_by),
  INDEX idx_gigs_moderated_at (moderated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_id INT UNSIGNED NOT NULL,
  receiver_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS portfolio_files (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) ECONOMIC TABLES
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
    type ENUM('credit', 'debit', 'withdrawal', 'subscription') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS subscriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    plan_name ENUM('basic', 'premium') DEFAULT 'basic',
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    expires_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) BLOG TABLES
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

-- 4) BASE SEEDS
INSERT IGNORE INTO categories (name) VALUES
('Développement web'), ('Marketing digital'), ('Design graphique'),
('Rédaction & Traduction'), ('Montage vidéo'), ('Community management'),
('Assistance virtuelle'), ('Autre');

-- 5) MASSIVE SUB-CATEGORIES SEED
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
SELECT 'No-code (Webflow, Bubble)', id FROM categories WHERE name='Développement web';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Optimisation performance site', id FROM categories WHERE name='Développement web';

INSERT IGNORE INTO categories (name, parent_id)
SELECT 'SEO On-page', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'SEO Technique', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Google Ads', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Meta Ads (Facebook/Instagram)', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Email marketing', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Tunnel de vente', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Copywriting publicitaire', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Étude de marché', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Stratégie de marque', id FROM categories WHERE name='Marketing digital';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Analytics / Tracking', id FROM categories WHERE name='Marketing digital';

INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Logo', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Identité visuelle complète', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Bannière réseaux sociaux', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Miniature YouTube', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Affiche / Flyer', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Packaging produit', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'UI Design (web/mobile)', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Présentation PowerPoint', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Retouche photo', id FROM categories WHERE name='Design graphique';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Motion design', id FROM categories WHERE name='Design graphique';

INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Article de blog SEO', id FROM categories WHERE name='Rédaction & Traduction';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Page de vente', id FROM categories WHERE name='Rédaction & Traduction';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Fiche produit e-commerce', id FROM categories WHERE name='Rédaction & Traduction';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Correction / Relecture', id FROM categories WHERE name='Rédaction & Traduction';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Traduction FR-EN', id FROM categories WHERE name='Rédaction & Traduction';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Traduction EN-FR', id FROM categories WHERE name='Rédaction & Traduction';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Script vidéo / podcast', id FROM categories WHERE name='Rédaction & Traduction';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Rédaction administrative', id FROM categories WHERE name='Rédaction & Traduction';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'CV / Lettre de motivation', id FROM categories WHERE name='Rédaction & Traduction';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Ghostwriting', id FROM categories WHERE name='Rédaction & Traduction';

INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Montage Reels/TikTok/Shorts', id FROM categories WHERE name='Montage vidéo';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Montage YouTube long format', id FROM categories WHERE name='Montage vidéo';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Sous-titrage vidéo', id FROM categories WHERE name='Montage vidéo';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Habillage / Intro-Outro', id FROM categories WHERE name='Montage vidéo';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Color grading', id FROM categories WHERE name='Montage vidéo';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Clip musical', id FROM categories WHERE name='Montage vidéo';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Publicité vidéo', id FROM categories WHERE name='Montage vidéo';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Podcast vidéo', id FROM categories WHERE name='Montage vidéo';

INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Gestion Instagram', id FROM categories WHERE name='Community management';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Gestion Facebook', id FROM categories WHERE name='Community management';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Gestion LinkedIn', id FROM categories WHERE name='Community management';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Gestion TikTok', id FROM categories WHERE name='Community management';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Calendrier éditorial', id FROM categories WHERE name='Community management';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Création de contenus sociaux', id FROM categories WHERE name='Community management';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Modération de communauté', id FROM categories WHERE name='Community management';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Veille et reporting social media', id FROM categories WHERE name='Community management';

INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Support client (email/chat)', id FROM categories WHERE name='Assistance virtuelle';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Saisie de données', id FROM categories WHERE name='Assistance virtuelle';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Recherche web', id FROM categories WHERE name='Assistance virtuelle';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Gestion d’agenda', id FROM categories WHERE name='Assistance virtuelle';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Gestion inbox', id FROM categories WHERE name='Assistance virtuelle';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'CRM / Prospection', id FROM categories WHERE name='Assistance virtuelle';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Transcription audio', id FROM categories WHERE name='Assistance virtuelle';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Gestion administrative', id FROM categories WHERE name='Assistance virtuelle';

INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Coaching professionnel', id FROM categories WHERE name='Autre';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Formation en ligne', id FROM categories WHERE name='Autre';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Services audio / voix off', id FROM categories WHERE name='Autre';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Analyse data / Excel', id FROM categories WHERE name='Autre';
INSERT IGNORE INTO categories (name, parent_id)
SELECT 'Automatisation (Zapier/Make)', id FROM categories WHERE name='Autre';
