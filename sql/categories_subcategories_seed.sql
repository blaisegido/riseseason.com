-- RiseSeason - Seed massif de sous-categories
-- A importer apres la migration gig_system_upgrade_compat.sql

-- DEVELOPPEMENT WEB
INSERT INTO categories (name, parent_id)
SELECT 'Site vitrine', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Site vitrine');
INSERT INTO categories (name, parent_id)
SELECT 'E-commerce (Shopify/WooCommerce)', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='E-commerce (Shopify/WooCommerce)');
INSERT INTO categories (name, parent_id)
SELECT 'WordPress sur mesure', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='WordPress sur mesure');
INSERT INTO categories (name, parent_id)
SELECT 'Landing page conversion', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Landing page conversion');
INSERT INTO categories (name, parent_id)
SELECT 'Débogage / Correction bugs', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Débogage / Correction bugs');
INSERT INTO categories (name, parent_id)
SELECT 'Maintenance technique', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Maintenance technique');
INSERT INTO categories (name, parent_id)
SELECT 'Intégration HTML/CSS', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Intégration HTML/CSS');
INSERT INTO categories (name, parent_id)
SELECT 'API / Backend (PHP, Node, etc.)', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='API / Backend (PHP, Node, etc.)');
INSERT INTO categories (name, parent_id)
SELECT 'No-code (Webflow, Bubble)', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='No-code (Webflow, Bubble)');
INSERT INTO categories (name, parent_id)
SELECT 'Optimisation performance site', p.id FROM categories p WHERE p.name='Développement web'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Optimisation performance site');

-- MARKETING DIGITAL
INSERT INTO categories (name, parent_id)
SELECT 'SEO On-page', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='SEO On-page');
INSERT INTO categories (name, parent_id)
SELECT 'SEO Technique', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='SEO Technique');
INSERT INTO categories (name, parent_id)
SELECT 'Google Ads', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Google Ads');
INSERT INTO categories (name, parent_id)
SELECT 'Meta Ads (Facebook/Instagram)', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Meta Ads (Facebook/Instagram)');
INSERT INTO categories (name, parent_id)
SELECT 'Email marketing', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Email marketing');
INSERT INTO categories (name, parent_id)
SELECT 'Tunnel de vente', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Tunnel de vente');
INSERT INTO categories (name, parent_id)
SELECT 'Copywriting publicitaire', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Copywriting publicitaire');
INSERT INTO categories (name, parent_id)
SELECT 'Étude de marché', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Étude de marché');
INSERT INTO categories (name, parent_id)
SELECT 'Stratégie de marque', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Stratégie de marque');
INSERT INTO categories (name, parent_id)
SELECT 'Analytics / Tracking', p.id FROM categories p WHERE p.name='Marketing digital'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Analytics / Tracking');

-- DESIGN GRAPHIQUE
INSERT INTO categories (name, parent_id)
SELECT 'Logo', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Logo');
INSERT INTO categories (name, parent_id)
SELECT 'Identité visuelle complète', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Identité visuelle complète');
INSERT INTO categories (name, parent_id)
SELECT 'Bannière réseaux sociaux', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Bannière réseaux sociaux');
INSERT INTO categories (name, parent_id)
SELECT 'Miniature YouTube', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Miniature YouTube');
INSERT INTO categories (name, parent_id)
SELECT 'Affiche / Flyer', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Affiche / Flyer');
INSERT INTO categories (name, parent_id)
SELECT 'Packaging produit', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Packaging produit');
INSERT INTO categories (name, parent_id)
SELECT 'UI Design (web/mobile)', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='UI Design (web/mobile)');
INSERT INTO categories (name, parent_id)
SELECT 'Présentation PowerPoint', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Présentation PowerPoint');
INSERT INTO categories (name, parent_id)
SELECT 'Retouche photo', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Retouche photo');
INSERT INTO categories (name, parent_id)
SELECT 'Motion design', p.id FROM categories p WHERE p.name='Design graphique'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Motion design');

-- REDACTION & TRADUCTION
INSERT INTO categories (name, parent_id)
SELECT 'Article de blog SEO', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Article de blog SEO');
INSERT INTO categories (name, parent_id)
SELECT 'Page de vente', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Page de vente');
INSERT INTO categories (name, parent_id)
SELECT 'Fiche produit e-commerce', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Fiche produit e-commerce');
INSERT INTO categories (name, parent_id)
SELECT 'Correction / Relecture', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Correction / Relecture');
INSERT INTO categories (name, parent_id)
SELECT 'Traduction FR-EN', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Traduction FR-EN');
INSERT INTO categories (name, parent_id)
SELECT 'Traduction EN-FR', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Traduction EN-FR');
INSERT INTO categories (name, parent_id)
SELECT 'Script vidéo / podcast', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Script vidéo / podcast');
INSERT INTO categories (name, parent_id)
SELECT 'Rédaction administrative', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Rédaction administrative');
INSERT INTO categories (name, parent_id)
SELECT 'CV / Lettre de motivation', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='CV / Lettre de motivation');
INSERT INTO categories (name, parent_id)
SELECT 'Ghostwriting', p.id FROM categories p WHERE p.name='Rédaction & Traduction'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Ghostwriting');

-- MONTAGE VIDEO
INSERT INTO categories (name, parent_id)
SELECT 'Montage Reels/TikTok/Shorts', p.id FROM categories p WHERE p.name='Montage vidéo'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Montage Reels/TikTok/Shorts');
INSERT INTO categories (name, parent_id)
SELECT 'Montage YouTube long format', p.id FROM categories p WHERE p.name='Montage vidéo'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Montage YouTube long format');
INSERT INTO categories (name, parent_id)
SELECT 'Sous-titrage vidéo', p.id FROM categories p WHERE p.name='Montage vidéo'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Sous-titrage vidéo');
INSERT INTO categories (name, parent_id)
SELECT 'Habillage / Intro-Outro', p.id FROM categories p WHERE p.name='Montage vidéo'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Habillage / Intro-Outro');
INSERT INTO categories (name, parent_id)
SELECT 'Color grading', p.id FROM categories p WHERE p.name='Montage vidéo'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Color grading');
INSERT INTO categories (name, parent_id)
SELECT 'Clip musical', p.id FROM categories p WHERE p.name='Montage vidéo'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Clip musical');
INSERT INTO categories (name, parent_id)
SELECT 'Publicité vidéo', p.id FROM categories p WHERE p.name='Montage vidéo'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Publicité vidéo');
INSERT INTO categories (name, parent_id)
SELECT 'Podcast vidéo', p.id FROM categories p WHERE p.name='Montage vidéo'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Podcast vidéo');

-- COMMUNITY MANAGEMENT
INSERT INTO categories (name, parent_id)
SELECT 'Gestion Instagram', p.id FROM categories p WHERE p.name='Community management'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Gestion Instagram');
INSERT INTO categories (name, parent_id)
SELECT 'Gestion Facebook', p.id FROM categories p WHERE p.name='Community management'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Gestion Facebook');
INSERT INTO categories (name, parent_id)
SELECT 'Gestion LinkedIn', p.id FROM categories p WHERE p.name='Community management'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Gestion LinkedIn');
INSERT INTO categories (name, parent_id)
SELECT 'Gestion TikTok', p.id FROM categories p WHERE p.name='Community management'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Gestion TikTok');
INSERT INTO categories (name, parent_id)
SELECT 'Calendrier éditorial', p.id FROM categories p WHERE p.name='Community management'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Calendrier éditorial');
INSERT INTO categories (name, parent_id)
SELECT 'Création de contenus sociaux', p.id FROM categories p WHERE p.name='Community management'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Création de contenus sociaux');
INSERT INTO categories (name, parent_id)
SELECT 'Modération de communauté', p.id FROM categories p WHERE p.name='Community management'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Modération de communauté');
INSERT INTO categories (name, parent_id)
SELECT 'Veille et reporting social media', p.id FROM categories p WHERE p.name='Community management'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Veille et reporting social media');

-- ASSISTANCE VIRTUELLE
INSERT INTO categories (name, parent_id)
SELECT 'Support client (email/chat)', p.id FROM categories p WHERE p.name='Assistance virtuelle'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Support client (email/chat)');
INSERT INTO categories (name, parent_id)
SELECT 'Saisie de données', p.id FROM categories p WHERE p.name='Assistance virtuelle'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Saisie de données');
INSERT INTO categories (name, parent_id)
SELECT 'Recherche web', p.id FROM categories p WHERE p.name='Assistance virtuelle'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Recherche web');
INSERT INTO categories (name, parent_id)
SELECT 'Gestion d’agenda', p.id FROM categories p WHERE p.name='Assistance virtuelle'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Gestion d’agenda');
INSERT INTO categories (name, parent_id)
SELECT 'Gestion inbox', p.id FROM categories p WHERE p.name='Assistance virtuelle'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Gestion inbox');
INSERT INTO categories (name, parent_id)
SELECT 'CRM / Prospection', p.id FROM categories p WHERE p.name='Assistance virtuelle'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='CRM / Prospection');
INSERT INTO categories (name, parent_id)
SELECT 'Transcription audio', p.id FROM categories p WHERE p.name='Assistance virtuelle'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Transcription audio');
INSERT INTO categories (name, parent_id)
SELECT 'Gestion administrative', p.id FROM categories p WHERE p.name='Assistance virtuelle'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Gestion administrative');

-- AUTRE
INSERT INTO categories (name, parent_id)
SELECT 'Coaching professionnel', p.id FROM categories p WHERE p.name='Autre'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Coaching professionnel');
INSERT INTO categories (name, parent_id)
SELECT 'Formation en ligne', p.id FROM categories p WHERE p.name='Autre'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Formation en ligne');
INSERT INTO categories (name, parent_id)
SELECT 'Services audio / voix off', p.id FROM categories p WHERE p.name='Autre'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Services audio / voix off');
INSERT INTO categories (name, parent_id)
SELECT 'Analyse data / Excel', p.id FROM categories p WHERE p.name='Autre'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Analyse data / Excel');
INSERT INTO categories (name, parent_id)
SELECT 'Automatisation (Zapier/Make)', p.id FROM categories p WHERE p.name='Autre'
AND NOT EXISTS (SELECT 1 FROM categories c WHERE c.name='Automatisation (Zapier/Make)');
