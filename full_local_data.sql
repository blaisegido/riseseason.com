-- Full Data Sync from Local MySQL to LWS
SET NAMES 'utf8mb4';
SET FOREIGN_KEY_CHECKS = 0;

-- Data for table: users
INSERT IGNORE INTO users (id,email,password_hash,first_name,last_name,username,country,role,subscription_status,subscription_expires_at,seeds,performance_score,title,daily_rate,languages,availability,website,linkedin,github,bio,skills,profile_photo,verified_email,created_at,level,last_seen) VALUES ('1','blaisegido@gmail.com','$2y$12$Kc.uCtG/D/.4k29Xztoum.EP00y9RaEDnyfSwxbXSfovDwLPtEVwq','Gildas','AHOSSI','gildas.ahossi','Bénin','freelancer','premium','2028-03-14 14:18:42','28000','0.00',NULL,NULL,NULL,'available',NULL,NULL,NULL,NULL,NULL,NULL,'0','2026-02-20 23:49:30','nouveau','2026-03-17 22:31:20');
INSERT IGNORE INTO users (id,email,password_hash,first_name,last_name,username,country,role,subscription_status,subscription_expires_at,seeds,performance_score,title,daily_rate,languages,availability,website,linkedin,github,bio,skills,profile_photo,verified_email,created_at,level,last_seen) VALUES ('2','oriolwa229@gmail.com','$2y$12$Lmt1JgN0db.VuZKDbLcLH./W9EYGIBkr3Hu9GN53cJAjjIAzhRZ0y','Admin','RiseSeason','admin','Benin','admin','free',NULL,'0','0.00',NULL,NULL,NULL,'available',NULL,NULL,NULL,NULL,NULL,NULL,'0','2026-02-22 11:31:53','nouveau','2026-03-18 08:21:51');
INSERT IGNORE INTO users (id,email,password_hash,first_name,last_name,username,country,role,subscription_status,subscription_expires_at,seeds,performance_score,title,daily_rate,languages,availability,website,linkedin,github,bio,skills,profile_photo,verified_email,created_at,level,last_seen) VALUES ('3','depifa6119@kaoing.com','$2y$12$169kiYohZ0MRmUad47.jrOg7ECalW9dQ54G01lOm/K69ajJ2PFHv2','Charbel','DJOMAKI','charbel.djomaki','Sénégal','employeur','free',NULL,'0','0.00',NULL,NULL,NULL,'available',NULL,NULL,NULL,'','',NULL,'0','2026-02-24 12:42:25','nouveau','2026-03-14 13:44:07');

-- Data for table: gigs
INSERT IGNORE INTO gigs (id,user_id,title,slug,category_id,description,price_base,delivery_days,price_eur,category,status,is_sponsored,sponsorship_expires_at,created_at,updated_at,main_image,gallery,faq,extras,is_express,timezone_africa,rejection_reason,rejection_feedback,moderated_by,moderated_at,deleted_at) VALUES ('1','1','Je vais vous créer le site vitrine de vos rêves','je-vais-vous-cr-er-le-site-vitrine-de-vos-r-ves','9','Si tu me connais un peu, tu sais.
Déjà, il faut savoir que ma mère et mon père viennent du même village.
Même si je n’ai pas grandi là-bas, j’ai eu la chance d’avoir des parents qui ne faisaient pas une fixette sur l’apprentissage du français dès le bas âge.
Pour eux, c’était simple :
tant que tu vas à l’école, tu apprendras le français.
À la maison, la priorité, c’était notre langue maternelle. Donc oui, je parle aisément l’aìxɔ.
Avec nos nombreux déménagements, j’ai toujours grandi en famille, dans une communauté où l’on parlait couramment ma langue. Je baignais dedans.
Tous les jours.
Je n’entendais que ça.
Sauf peut-être les moments où mes frères et moi devions réciter une leçon, apprendre nos textes ou nous entraîner pour un travail à présenter à l’école.
Je n’ai connu que ça.
Et j’en suis tellement fière.
En grandissant, j’ai entendu beaucoup de calomnies sur mon village.
Qu’on est modestes.
Peu instruits.
Indigènes. Et j’en passe.
Mais ça ne m’a jamais freinée.
J’ai une habitude qui ne m’a jamais quittée, et ça ne changera pas :
partout où je passe, je parle ma langue maternelle.
Dans une discussion, que ce soit en français ou même en fɔn, je glisse inconsciemment des mots ou des expressions en aìxɔ.
Ça vient naturellement.
Sans effort.
Sans calcul.
Peu importe ce que mes amis, mes connaissances ou les inconnus présents à l’instant T pensent ou ont pu penser, je n’en ai jamais eu honte.
Je suis heureuse de ne pas avoir à chercher mes mots pour parler ma langue.
Je suis fière de tout ce que mes parents nous ont transmis, à mes frères et moi, à travers elle.
Je suis fière de la beauté de ma langue maternelle, peu importe ce qu’on en dit.
Aujourd''hui, en ce jour particulier, je nous souhaite d’être fiers.ères de nos langues maternelles et nous exprimer avec. 
Bonne célébration de la Journée internationale des langues maternelles.
📸: Je suis peut-être hors sujet, je sais.
Mais je suis toujours fascinée par le pouvoir de ce verset chaque fois que je mets un pied au village. 
Mais moi et ma maison, nous servirons l’Éternel, comme le dit cette pancarte fièrement implantée par mon oncle A.G. dans la cour familiale, à Sô-Tchanhoué, dans notre village natal.','150.00','12','0.00','','approved','1','2026-03-19 14:26:47','2026-02-22 10:19:23','2026-03-14 14:26:47','uploads/gigs/1/main_1_10d4ad1f20cfca0f57ee.png','["uploads/gigs/1/gallery_1_7c083d89f6ff663ae482.png", "uploads/gigs/1/gallery_1_db7a2e252c659d3e4e57.png", "uploads/gigs/1/gallery_1_bb0a81099d62dc1f3ca9.png", "uploads/gigs/1/gallery_1_359754d5a14c3cb7e360.png", "uploads/gigs/1/gallery_1_2c1c074095ea8b2cb935.png"]','[{"q": "Tu vas développer avec WordPress ?", "r": "Oui, exclusivement"}, {"q": "Tu vas Elementor ?", "r": "Pas nécessairement. Mais pour certaines spécificité, oui"}, {"q": "Des formulaires de contact ?", "r": "Oui je vais en intégrer."}]','[{"desc": "C''est une landing page", "name": "Landing Page", "price": 50}]','0','1',NULL,NULL,'2','2026-02-22 15:00:53',NULL);
INSERT IGNORE INTO gigs (id,user_id,title,slug,category_id,description,price_base,delivery_days,price_eur,category,status,is_sponsored,sponsorship_expires_at,created_at,updated_at,main_image,gallery,faq,extras,is_express,timezone_africa,rejection_reason,rejection_feedback,moderated_by,moderated_at,deleted_at) VALUES ('2','1','Votre tunnel de vente et de conversion','votre-tunnel-de-vente-et-de-conversion','31','TON ORDINATEUR N''EST PAS UNE TÉLÉ, C''EST UN BUREAU.
Arrête de scroller et commence à encaisser.
Découvre notre formation OFFERTE pour lancer ta carrière de Freelance !
Accès immédiat ici : https://training.exploitsmotivation.com/optin-potentiel-d...
LA RÉALITÉ DU FREELANCING :
Ce n''est pas de la magie, c''est une compétence.
Partout dans le monde, des chefs d''entreprise cherchent des prestataires sérieux pour déléguer leurs tâches, et ils paient en Devises Fortes.
Imagine ta vie si tu pouvais :
• Vendre tes services depuis ton salon (Rédaction, Assistance Virtuelle, Community Management...).
• Travailler en direct avec des clients en Europe ou aux USA.
• Toucher des revenus en Euros ou Dollars tout en dépensant en CFA.
TU AS DÉJÀ LES OUTILS ENTRE LES MAINS.
Si tu as un PC pour travailler (ou même un Smartphone pour commencer à te former et gérer ta connexion), tu es déjà prêt.
Pas besoin de diplôme compliqué. Il te faut juste la méthode pour trouver des clients qui ont besoin de toi.
POURQUOI NOUS FAIRE CONFIANCE ?
Nous sommes les pionniers du Freelancing en Afrique Francophone, présents depuis 2018.
Notre méthode a fait ses preuves : certains de nos étudiants ont franchi la barre des 200 000 $ (Dollars) de revenus cumulés sur des plateformes comme Upwork.
Ce n''est pas de la théorie, ce sont des résultats concrets obtenus depuis l''Afrique.
Ne laisse pas passer ta chance de rejoindre une communauté qui gagne vraiment.
Clique ici pour commencer maintenant :
https://training.exploitsmotivation.com/optin-potentiel-d... Donc c''est ça','250.00','3','0.00','','approved','0',NULL,'2026-02-22 14:25:00','2026-02-22 15:13:19','uploads/gigs/2/main_2_acf68c511065ef0555ee.png','["uploads/gigs/2/gallery_2_787341cab647e6f2d2a0.png", "uploads/gigs/2/gallery_2_37a4ac12465e61322c46.jpg"]','[{"q": "Tu vas développer avec WordPress ?", "r": "Oui"}, {"q": "Tu vas développer avec Elementor ?", "r": "Oui et avec d''autres aussi"}, {"q": "Tu vas faire des ventes", "r": "Oui évidemment."}]','[{"desc": "Un upsell pour augmenter le panier moyen", "name": "Upsell", "price": 50}]','1','1',NULL,NULL,'2','2026-02-22 15:13:19',NULL);
INSERT IGNORE INTO gigs (id,user_id,title,slug,category_id,description,price_base,delivery_days,price_eur,category,status,is_sponsored,sponsorship_expires_at,created_at,updated_at,main_image,gallery,faq,extras,is_express,timezone_africa,rejection_reason,rejection_feedback,moderated_by,moderated_at,deleted_at) VALUES ('3','1','Je cree votre site web professionnel et responsive','je-cree-votre-site-web-professionnel-et-responsive','76','Je suis un developpeur web expert avec plus de 5 ans d d''experience dans la creation de sites web professionnels. Je vous propose une solution complete pour votre presence en ligne. Mon service comprend la conception graphique sur mesure, le developpement front-end et back-end, l l''optimisation pour les moteurs de recherche et la mise en production sur votre hebergeur. Chaque projet est unique et je m m''adapte a vos besoins specifiques et a votre budget. Je travaille avec les technologies les plus recentes pour garantir un site rapide, securise et facile a maintenir. Vous beneficiez d d''un support apres livraison pour repondre a vos questions. Mon objectif est votre satisfaction totale et le succes de votre projet en ligne. Contactez-moi pour discuter de votre projet et obtenir un devis personnalise rapidement.
Chaque projet est unique et je m m''adapte a vos besoins specifiques et a votre budget. Je travaille avec les technologies les plus recentes pour garantir un site rapide, securise et facile a maintenir. Vous beneficiez d d''un support apres livraison pour repondre a vos questions. Mon objectif est votre satisfaction totale et le succes de votre projet en ligne. Contactez-moi pour discuter de votre projet et obtenir un devis personnalise rapidement.','45.73','5','0.00','','approved','1','2026-03-19 14:27:01','2026-02-27 13:53:02','2026-03-14 14:27:01','uploads/gigs/3/main_3_51ea41d7b0ee764f4557.png','[]','[{"q": "Livrez-vous avec les fichiers sources ?", "r": "Oui absolument"}, {"q": "La base de données aussi ?", "r": "Oui bien sûr"}, {"q": "Vous êtes disponible pour un accompagnement ?", "r": "Absolument"}]','[]','1','1',NULL,NULL,'2','2026-02-27 14:13:10',NULL);

-- Data for table: jobs
INSERT IGNORE INTO jobs (id,user_id,title,description,budget_eur,deadline_text,category,hero_image,status,created_at) VALUES ('1','3','Création de sites WooCommerce en 10 jours','Contexte du projet
Nous lançons / développons [une nouvelle boutique en ligne / la version e-commerce de notre activité existante] dans le secteur [précise : mode, beauté, alimentaire, high-tech, services, etc.].
L’objectif est de créer une boutique en ligne moderne, rapide, sécurisée et optimisée pour convertir les visiteurs en clients.
Technologie attendue

WordPress + WooCommerce (version la plus récente)
Thème premium compatible WooCommerce et page builder (Elementor Pro, Divi, Oxygen, Bricks, etc.) fortement recommandé
Pas de Shopify / PrestaShop / autre solution fermée pour ce projet

Livrables principaux attendus

Installation & configuration technique de base
Hébergement + domaine (ou migration si site existant)
WordPress + WooCommerce frais installés
SSL + optimisation performance de base (cache, compression, etc.)
Sauvegardes automatiques + environnement de staging

Design & identité visuelle e-commerce
Charte graphique respectée (si logo + couleurs déjà existants) OU proposition de 2-3 directions créatives
Design 100 % responsive (mobile first obligatoire)
Pages principales stylées :
Page d’accueil (hero + catégories + produits phares + témoignages / Instagram feed / etc.)
Pages catégories & sous-catégories
Fiche produit complète & moderne (variations, upsell, cross-sell, onglets personnalisés, avis, etc.)
Panier & tunnel de commande optimisé (express checkout si possible)
Pages statiques : À propos, Contact, CGV, Mentions légales, Politique de confidentialité, Livraison & Retours


Fonctionnalités e-commerce essentielles
Gestion des produits (simple, variable, groupé)
Gestion des stocks + ruptures
Filtres & tri produits (prix, popularité, nouveautés, etc.)
Recherche avancée (Ajax si possible)
Système d’avis clients + étoiles
Codes promo / réductions
Wishlist (optionnel mais apprécié)
Paiements : Stripe + PayPal + [autres moyens locaux si besoin : Payplug, Mollie, etc.]
Modes de livraison : Colissimo, Mondial Relay, Chronopost, point relais, livraison gratuite selon montant, etc.

SEO & performances de base
Installation et configuration Yoast SEO ou Rank Math
Balises meta title + description sur pages principales
Schema.org produits
Site rapide (objectif < 3 s sur mobile avec PageSpeed Insights)
Images optimisées (WebP + lazy loading)

Contenu
Option A – Vous fournissez tous les textes + photos
Option B – Le prestataire rédige les textes de base (accueil, à propos, CGV types, 5-10 fiches produits types) → tarif à préciser

Nombre de produits au lancement
≈ [indique : 20 / 50 / 100 / 300 / +1000 produits]','1200.00','Fin Avril','E-commerce (Shopify/WooCommerce)',NULL,'approved','2026-02-24 12:52:19');
INSERT IGNORE INTO jobs (id,user_id,title,description,budget_eur,deadline_text,category,hero_image,status,created_at) VALUES ('2','3','Création de Landing Page','Design & identité visuelle e-commerce
Charte graphique respectée (si logo + couleurs déjà existants) OU proposition de 2-3 directions créatives
Design 100 % responsive (mobile first obligatoire)
Pages principales stylées :
Page d’accueil (hero + catégories + produits phares + témoignages / Instagram feed / etc.)
Pages catégories & sous-catégories
Fiche produit complète & moderne (variations, upsell, cross-sell, onglets personnalisés, avis, etc.)
Panier & tunnel de commande optimisé (express checkout si possible)
Pages statiques : À propos, Contact, CGV, Mentions légales, Politique de confidentialité, Livraison & Retours


Fonctionnalités e-commerce essentielles
Gestion des produits (simple, variable, groupé)
Gestion des stocks + ruptures
Filtres & tri produits (prix, popularité, nouveautés, etc.)
Recherche avancée (Ajax si possible)
Système d’avis clients + étoiles
Codes promo / réductions
Wishlist (optionnel mais apprécié)
Paiements : Stripe + PayPal + [autres moyens locaux si besoin : Payplug, Mollie, etc.]
Modes de livraison : Colissimo, Mondial Relay, Chronopost, point relais, livraison gratuite selon montant, etc.

SEO & performances de base
Installation et configuration Yoast SEO ou Rank Math
Balises meta title + description sur pages principales
Schema.org produits
Site rapide (objectif < 3 s sur mobile avec PageSpeed Insights)
Images optimisées (WebP + lazy loading)','530.00','3 jours','Landing page conversion','uploads/jobs/2/hero_2_234f6dba9ba154ff.png','approved','2026-02-24 15:13:05');

-- Data for table: user_wallets
INSERT IGNORE INTO user_wallets (user_id,balance,pending_balance,currency,updated_at) VALUES ('1','0.00','0.00','EUR','2026-02-24 13:26:42');
INSERT IGNORE INTO user_wallets (user_id,balance,pending_balance,currency,updated_at) VALUES ('2','0.00','0.00','EUR','2026-02-24 13:26:42');
INSERT IGNORE INTO user_wallets (user_id,balance,pending_balance,currency,updated_at) VALUES ('3','0.00','0.00','EUR','2026-02-24 13:26:42');

-- Data for table: transactions
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('1','1',NULL,'seeds_purchase','5000.00','5000','completed',NULL,'2026-03-14 14:21:03');
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('2','1',NULL,'subscription','15000.00','0','completed',NULL,'2026-03-14 14:21:47');
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('3','1',NULL,'seeds_purchase','12000.00','12000','completed',NULL,'2026-03-14 14:23:32');
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('4','1',NULL,'sponsorship_spend','0.00','1000','completed',NULL,'2026-03-14 14:26:47');
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('5','1',NULL,'sponsorship_spend','0.00','1000','completed',NULL,'2026-03-14 14:27:01');
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('6','1',NULL,'seeds_purchase','1000.00','1000','completed',NULL,'2026-03-15 10:36:04');
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('7','1',NULL,'seeds_purchase','5000.00','5000','completed',NULL,'2026-03-15 10:36:13');
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('8','1',NULL,'seeds_purchase','700.00','700','completed',NULL,'2026-03-15 10:38:52');
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('9','1',NULL,'seeds_purchase','700.00','700','completed',NULL,'2026-03-15 10:38:59');
INSERT IGNORE INTO transactions (id,user_id,order_id,type,amount,seeds,status,description,created_at) VALUES ('10','1',NULL,'seeds_purchase','600.00','600','completed',NULL,'2026-03-15 10:39:06');

-- Data for table: saved_gigs
INSERT IGNORE INTO saved_gigs (user_id,gig_id,created_at) VALUES ('1','1','2026-02-23 00:18:48');
INSERT IGNORE INTO saved_gigs (user_id,gig_id,created_at) VALUES ('1','2','2026-02-22 16:19:14');
INSERT IGNORE INTO saved_gigs (user_id,gig_id,created_at) VALUES ('3','1','2026-02-24 20:03:06');
INSERT IGNORE INTO saved_gigs (user_id,gig_id,created_at) VALUES ('3','2','2026-02-24 20:03:04');

SET FOREIGN_KEY_CHECKS = 1;
