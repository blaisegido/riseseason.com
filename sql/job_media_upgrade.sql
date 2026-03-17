-- Upgrade jobs table for media support
ALTER TABLE jobs ADD COLUMN hero_image VARCHAR(255) NULL AFTER category;

CREATE TABLE job_attachments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_id INT UNSIGNED NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_type VARCHAR(100) NULL,
  file_size INT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
