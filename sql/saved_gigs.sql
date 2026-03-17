-- Système de sauvegarde de gigs
-- À exécuter une seule fois sur la base riseseason

CREATE TABLE IF NOT EXISTS saved_gigs (
  user_id    INT UNSIGNED NOT NULL,
  gig_id     INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, gig_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (gig_id)  REFERENCES gigs(id)  ON DELETE CASCADE
);
