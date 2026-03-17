-- Migration: Ajout des colonnes pour la modération des gigs
-- Permet aux admins de laisser des commentaires lors du rejet

-- Ajouter rejection_reason si elle n'existe pas
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gigs' AND COLUMN_NAME = 'rejection_reason') = 0,
    'ALTER TABLE gigs ADD COLUMN rejection_reason VARCHAR(255) NULL AFTER timezone_africa',
    'SELECT "Column rejection_reason already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ajouter rejection_feedback si elle n'existe pas
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gigs' AND COLUMN_NAME = 'rejection_feedback') = 0,
    'ALTER TABLE gigs ADD COLUMN rejection_feedback TEXT NULL AFTER rejection_reason',
    'SELECT "Column rejection_feedback already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ajouter moderated_by si elle n'existe pas
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gigs' AND COLUMN_NAME = 'moderated_by') = 0,
    'ALTER TABLE gigs ADD COLUMN moderated_by INT UNSIGNED NULL AFTER rejection_feedback',
    'SELECT "Column moderated_by already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ajouter moderated_at si elle n'existe pas
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gigs' AND COLUMN_NAME = 'moderated_at') = 0,
    'ALTER TABLE gigs ADD COLUMN moderated_at TIMESTAMP NULL AFTER moderated_by',
    'SELECT "Column moderated_at already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index pour les performances
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gigs' AND INDEX_NAME = 'idx_gigs_moderated_by') = 0,
    'ALTER TABLE gigs ADD INDEX idx_gigs_moderated_by (moderated_by)',
    'SELECT "Index idx_gigs_moderated_by already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gigs' AND INDEX_NAME = 'idx_gigs_moderated_at') = 0,
    'ALTER TABLE gigs ADD INDEX idx_gigs_moderated_at (moderated_at)',
    'SELECT "Index idx_gigs_moderated_at already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Contrainte de clé étrangère pour le modérateur
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gigs' AND CONSTRAINT_NAME = 'fk_gigs_moderated_by') = 0,
    'ALTER TABLE gigs ADD CONSTRAINT fk_gigs_moderated_by FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT "Foreign key fk_gigs_moderated_by already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;