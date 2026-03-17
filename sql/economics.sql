USE riseseason;

-- Tables pour le modèle économique (Inspired by ComeUp)

-- Portefeuilles des utilisateurs
CREATE TABLE IF NOT EXISTS user_wallets (
    user_id INT UNSIGNED PRIMARY KEY,
    balance DECIMAL(10, 2) DEFAULT 0.00,
    pending_balance DECIMAL(10, 2) DEFAULT 0.00, -- Fonds en attente de déblocage (escrow)
    currency VARCHAR(3) DEFAULT 'EUR',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Commandes (Gigs achetés)
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

-- Historique des transactions
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

-- Abonnements Premium
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    plan_name ENUM('basic', 'premium') DEFAULT 'basic',
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    expires_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initialisation des portefeuilles pour les utilisateurs existants
INSERT IGNORE INTO user_wallets (user_id) SELECT id FROM users;
