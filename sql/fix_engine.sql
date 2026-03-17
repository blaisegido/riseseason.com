USE riseseason;

-- Conversion des tables vers InnoDB pour supporter les clés étrangères et les transactions (crucial pour le modèle ComeUp)
ALTER TABLE users ENGINE=InnoDB;
ALTER TABLE categories ENGINE=InnoDB;
ALTER TABLE gigs ENGINE=InnoDB;
ALTER TABLE jobs ENGINE=InnoDB;
ALTER TABLE messages ENGINE=InnoDB;
ALTER TABLE portfolio_files ENGINE=InnoDB;
ALTER TABLE posts ENGINE=InnoDB;
ALTER TABLE saved_gigs ENGINE=InnoDB;

-- Maintenant vous pouvez relancer economics.sql
