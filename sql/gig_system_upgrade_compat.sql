-- RiseSeason - Migration système gigs (compat MySQL/MariaDB sans IF NOT EXISTS)
-- A exécuter une seule fois sur la base riseseason.

-- 1) Categories avec hierarchie parent/sous-categorie
ALTER TABLE categories
  ADD COLUMN parent_id INT UNSIGNED NULL AFTER name,
  ADD INDEX idx_categories_parent (parent_id);

ALTER TABLE categories
  ADD CONSTRAINT fk_categories_parent
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL;

-- 2) Evolution table gigs
ALTER TABLE gigs
  ADD COLUMN slug VARCHAR(180) NULL AFTER title,
  ADD COLUMN category_id INT UNSIGNED NULL AFTER slug,
  ADD COLUMN price_base DECIMAL(10,2) NULL AFTER description,
  ADD COLUMN delivery_days INT NULL AFTER price_base,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at,
  ADD COLUMN main_image VARCHAR(255) NULL AFTER updated_at,
  ADD COLUMN gallery JSON NULL AFTER main_image,
  ADD COLUMN faq JSON NULL AFTER gallery,
  ADD COLUMN extras JSON NULL AFTER faq,
  ADD COLUMN is_express TINYINT(1) NOT NULL DEFAULT 0 AFTER extras,
  ADD COLUMN timezone_africa TINYINT(1) NOT NULL DEFAULT 0 AFTER is_express;

ALTER TABLE gigs
  MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending';

-- Sync initiale depuis anciennes colonnes
UPDATE gigs
SET price_base = COALESCE(price_base, price_eur)
WHERE price_base IS NULL;

UPDATE gigs g
JOIN categories c ON c.name = g.category
SET g.category_id = c.id
WHERE g.category_id IS NULL AND g.category IS NOT NULL;

UPDATE gigs
SET slug = CONCAT('gig-', id)
WHERE slug IS NULL OR slug = '';

ALTER TABLE gigs
  ADD UNIQUE INDEX uq_gigs_slug (slug),
  ADD INDEX idx_gigs_status (status),
  ADD INDEX idx_gigs_user (user_id),
  ADD INDEX idx_gigs_category (category_id);

ALTER TABLE gigs
  ADD CONSTRAINT fk_gigs_category
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

-- 3) Sous-categories exemples
INSERT INTO categories (name, parent_id)
SELECT 'Landing page / Site vitrine', p.id
FROM categories p
WHERE p.name = 'Développement web'
  AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name = 'Landing page / Site vitrine');

INSERT INTO categories (name, parent_id)
SELECT 'SEO / Référencement', p.id
FROM categories p
WHERE p.name = 'Marketing digital'
  AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name = 'SEO / Référencement');

INSERT INTO categories (name, parent_id)
SELECT 'Logo & Identité visuelle', p.id
FROM categories p
WHERE p.name = 'Design graphique'
  AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name = 'Logo & Identité visuelle');

INSERT INTO categories (name, parent_id)
SELECT 'Traduction FR-EN', p.id
FROM categories p
WHERE p.name = 'Rédaction & Traduction'
  AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name = 'Traduction FR-EN');

INSERT INTO categories (name, parent_id)
SELECT 'Montage Reels / Shorts', p.id
FROM categories p
WHERE p.name = 'Montage vidéo'
  AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name = 'Montage Reels / Shorts');

INSERT INTO categories (name, parent_id)
SELECT 'Gestion Facebook / Instagram', p.id
FROM categories p
WHERE p.name = 'Community management'
  AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name = 'Gestion Facebook / Instagram');

INSERT INTO categories (name, parent_id)
SELECT 'Support client / Back-office', p.id
FROM categories p
WHERE p.name = 'Assistance virtuelle'
  AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name = 'Support client / Back-office');
