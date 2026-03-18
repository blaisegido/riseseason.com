<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use App\Models\Gig;
use App\Models\User;
use Flight;

final class GigController
{
    public static function create(): void
    {
        Auth::requireRole('freelancer');
        $user = Auth::user();

        if (strtoupper(Flight::request()->method) === 'GET') {
            // Check quota for free members
            if (!User::isPremium((int)$user['id'])) {
                $count = Gig::countByUser((int)$user['id']);
                if ($count >= 3) {
                    Flight::renderView('gig-create', [
                        'error' => 'Vous avez atteint la limite de 3 services gratuits. Passez à l\'abonnement Premium pour en créer plus !',
                        'quotaExceeded' => true
                    ], 'admin');
                    return;
                }
            }
            self::renderCreateForm();
            return;
        }

        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $title = trim((string) ($r->title ?? ''));
        $description = trim((string) ($r->description ?? ''));
        $categoryId = (int) ($r->category_id ?? 0);
        $priceBaseXof = (float) ($r->price_base_xof ?? 0);
        $priceBase = $priceBaseXof / get_eur_to_xof_rate();
        $deliveryDays = (int) ($r->delivery_days ?? 0);
        $isExpress = !empty($r->is_express) ? 1 : 0;
        $timezoneAfrica = !empty($r->timezone_africa) ? 1 : 0;
        if ($deliveryDays <= 2) {
            // Impossible d'accelerer un service deja <= 48h
            $isExpress = 0;
        }

        $faq = self::parseFaq($_POST['faq_q'] ?? [], $_POST['faq_r'] ?? []);
        $extrasXofPrices = $_POST['extra_price_xof'] ?? [];
        $extrasPricesEur = array_map(function($p) { return (float)$p / get_eur_to_xof_rate(); }, $extrasXofPrices);
        $extras = self::parseExtras($_POST['extra_name'] ?? [], $extrasPricesEur, $_POST['extra_desc'] ?? []);

        $errors = self::validateInput(
            $title,
            $description,
            $categoryId,
            $priceBase,
            $deliveryDays,
            $faq,
            $_FILES['main_image'] ?? null,
            $_FILES['gallery'] ?? null
        );

        if (!empty($errors)) {
            self::renderCreateForm([
                'error' => implode(' ', $errors),
                'old' => [
                    'title' => $title,
                    'description' => $description,
                    'category_id' => $categoryId,
                    'price_base' => $priceBase,
                    'delivery_days' => $deliveryDays,
                    'is_express' => $isExpress,
                    'timezone_africa' => $timezoneAfrica,
                ],
                'oldFaq' => $faq,
                'oldExtras' => $extras,
            ]);
            return;
        }

        $db = Flight::get('db');
        $gigId = 0;

        try {
            $db->beginTransaction();

            $gigId = Gig::create([
                'user_id' => (int) $user['id'],
                'title' => $title,
                'category_id' => $categoryId,
                'description' => $description,
                'price_base' => $priceBase,
                'delivery_days' => $deliveryDays,
                'faq' => $faq,
                'extras' => $extras,
                'is_express' => $isExpress,
                'timezone_africa' => $timezoneAfrica,
            ]);

            $targetDir = __DIR__ . '/../../public/uploads/gigs/' . $gigId . '/';
            if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new \RuntimeException('Impossible de creer le dossier des uploads.');
            }

            $mainImagePath = self::storeImage($_FILES['main_image'], $targetDir, true, $gigId);
            $galleryPaths = self::storeGallery($_FILES['gallery'] ?? null, $targetDir, $gigId);

            Gig::updateMedia($gigId, $mainImagePath, $galleryPaths);

            $db->commit();

            // TODO: envoyer un email reel a l'admin avec PHPMailer.
            unset($_SESSION['gig_draft']);
            $_SESSION['success'] = 'Gig cree avec succes. Statut: pending (en attente de validation admin).';
            Flight::redirect('/profil');
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            if ($gigId > 0) {
                self::cleanupGigUploadFolder($gigId);
            }

            self::renderCreateForm([
                'error' => 'Erreur lors de la creation du gig. Reessaie.',
                'old' => [
                    'title' => $title,
                    'description' => $description,
                    'category_id' => $categoryId,
                    'price_base' => $priceBase,
                    'delivery_days' => $deliveryDays,
                    'is_express' => $isExpress,
                    'timezone_africa' => $timezoneAfrica,
                ],
                'oldFaq' => $faq,
                'oldExtras' => $extras,
            ]);
        }
    }

    public static function edit(int $id): void
    {
        Auth::requireRole('freelancer');
        $user = Auth::user();

        $gig = Gig::findById($id);
        if (!$gig || $gig['user_id'] !== (int)$user['id']) {
            Flight::halt(404, 'Gig introuvable ou acces refuse.');
        }

        if ($gig['status'] === 'rejected') {
            // On autorise la modification pour corriger avant resoumission
            // (status restera rejected jusqu'à la soumission)
        }

        // DEBUG: Log pour vérifier que le gig est bien récupéré
        // error_log('DEBUG edit(): gig = ' . json_encode($gig));

        if (strtoupper(Flight::request()->method) === 'GET') {
            self::renderEditForm($gig);
            return;
        }

        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $title = trim((string) ($r->title ?? ''));
        $description = trim((string) ($r->description ?? ''));
        $categoryId = (int) ($r->category_id ?? 0);
        $priceBaseXof = (float) ($r->price_base_xof ?? 0);
        $priceBase = $priceBaseXof / get_eur_to_xof_rate();
        $deliveryDays = (int) ($r->delivery_days ?? 0);
        $isExpress = !empty($r->is_express) ? 1 : 0;
        $timezoneAfrica = !empty($r->timezone_africa) ? 1 : 0;
        if ($deliveryDays <= 2) {
            // Impossible d'accelerer un service deja <= 48h
            $isExpress = 0;
        }

        $faq = self::parseFaq($_POST['faq_q'] ?? [], $_POST['faq_r'] ?? []);
        $extrasXofPrices = $_POST['extra_price_xof'] ?? [];
        $extrasPricesEur = array_map(function($p) { return (float)$p / get_eur_to_xof_rate(); }, $extrasXofPrices);
        $extras = self::parseExtras($_POST['extra_name'] ?? [], $extrasPricesEur, $_POST['extra_desc'] ?? []);

        $errors = self::validateInput(
            $title,
            $description,
            $categoryId,
            $priceBase,
            $deliveryDays,
            $faq,
            $_FILES['main_image'] ?? null,
            $_FILES['gallery'] ?? null,
            true  // isEditing
        );

        if (!empty($errors)) {
            self::renderEditForm($gig, [
                'error' => implode(' ', $errors),
                'old' => [
                    'title' => $title,
                    'description' => $description,
                    'category_id' => $categoryId,
                    'price_base' => $priceBase,
                    'delivery_days' => $deliveryDays,
                    'is_express' => $isExpress,
                    'timezone_africa' => $timezoneAfrica,
                ],
                'oldFaq' => $faq,
                'oldExtras' => $extras,
            ]);
            return;
        }

        try {
            Gig::update($id, [
                'title' => $title,
                'category_id' => $categoryId,
                'description' => $description,
                'price_base' => $priceBase,
                'delivery_days' => $deliveryDays,
                'faq' => $faq,
                'extras' => $extras,
                'is_express' => $isExpress,
                'timezone_africa' => $timezoneAfrica,
            ]);

            // Handle image updates if provided
            if (!empty($_FILES['main_image']['tmp_name'])) {
                $targetDir = __DIR__ . '/../../public/uploads/gigs/' . $id . '/';
                if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                    throw new \RuntimeException('Impossible de creer le dossier des uploads.');
                }

                $mainImagePath = self::storeImage($_FILES['main_image'], $targetDir, true, $id);
                $galleryPaths = self::storeGallery($_FILES['gallery'] ?? null, $targetDir, $id);

                Gig::updateMedia($id, $mainImagePath, $galleryPaths);
            }

            // Si le gig était approuvé ou en pause, on le repasse en pending pour re-validation
            if ($gig['status'] === 'approved' || $gig['status'] === 'paused') {
                Flight::get('db')->prepare('UPDATE gigs SET status = "pending", updated_at = CURRENT_TIMESTAMP WHERE id = :id')
                    ->execute(['id' => $id]);
            }

            Flight::redirect('/profil?message=Gig modifie avec succes.');
        } catch (\Throwable $e) {
            error_log('Erreur lors de la modification du gig: ' . $e->getMessage());
            self::renderEditForm($gig, [
                'error' => 'Erreur lors de la modification du gig. Reessaie.',
                'old' => [
                    'title' => $title,
                    'description' => $description,
                    'category_id' => $categoryId,
                    'price_base' => $priceBase,
                    'delivery_days' => $deliveryDays,
                    'is_express' => $isExpress,
                    'timezone_africa' => $timezoneAfrica,
                ],
                'oldFaq' => $faq,
                'oldExtras' => $extras,
            ]);
        }
    }

    public static function saveDraft(): void
    {
        Auth::requireRole('freelancer');
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::json(['ok' => false, 'message' => 'CSRF invalide.'], 419);
            return;
        }

        $old = [
            'title' => mb_substr(trim((string) ($r->title ?? '')), 0, 80),
            'description' => trim((string) ($r->description ?? '')),
            'category_id' => (int) ($r->category_id ?? 0),
            'price_base' => (float) ($r->price_base_xof ?? 0), // Keeping XOF for draft temporarily if needed, though variable is named price_base. Let's just store XOF value directly in draft array as base.
            'delivery_days' => (int) ($r->delivery_days ?? 5),
            'is_express' => !empty($r->is_express) ? 1 : 0,
            'timezone_africa' => !empty($r->timezone_africa) ? 1 : 0,
        ];
        if ($old['delivery_days'] <= 2) {
            $old['is_express'] = 0;
        }

        $oldFaq = self::parseFaq($_POST['faq_q'] ?? [], $_POST['faq_r'] ?? []);
        $extrasXofPrices = $_POST['extra_price_xof'] ?? [];
        $extrasPricesEur = array_map(function($p) { return (float)$p; }, $extrasXofPrices); // Also keep XOF in draft for extras
        $oldExtras = self::parseExtras($_POST['extra_name'] ?? [], $extrasPricesEur, $_POST['extra_desc'] ?? []);

        $_SESSION['gig_draft'] = [
            'old' => $old,
            'oldFaq' => $oldFaq,
            'oldExtras' => $oldExtras,
            'saved_at' => date('Y-m-d H:i:s'),
        ];

        Flight::json([
            'ok' => true,
            'saved_at' => $_SESSION['gig_draft']['saved_at'],
        ]);
    }

    public static function show(string $slug): void
    {
        $gig = Gig::findApprovedBySlug($slug);
        if (!$gig) {
            Flight::halt(404, 'Gig introuvable ou non publie.');
        }

        Flight::renderView('gig-single', ['gig' => $gig]);
    }

    public static function status(string $slug): void
    {
        Auth::requireRole('freelancer');
        $user = Auth::user();

        // Find the gig by slug regardless of status
        $stmt = Flight::get('db')->prepare('SELECT * FROM gigs WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $gig = $stmt->fetch();

        if (!$gig) {
            Flight::halt(404, 'Service introuvable.');
        }

        // Only the owner can access this page
        if ((int)$gig['user_id'] !== (int)$user['id']) {
            Flight::halt(403, 'Accès refusé.');
        }

        // If approved, redirect to public page
        if ($gig['status'] === 'approved') {
            Flight::redirect('/gig/' . $gig['slug']);
            return;
        }

        Flight::renderView('gig-status', ['gig' => $gig]);
    }

    public static function adminList(): void
    {
        Auth::requireRole('admin');
        Flight::renderView('admin-gigs', [
            'pendingGigs' => Gig::pendingForAdmin(),
        ], 'admin');
    }

    public static function adminReview(int $id): void
    {
        Auth::requireRole('admin');

        $gig = Gig::findById($id);
        if (!$gig || $gig['status'] !== 'pending') {
            Flight::halt(404, 'Gig introuvable ou déjà modéré.');
        }

        Flight::renderView('admin-gig-review', [
            'gig' => $gig,
        ], 'admin');
    }

    public static function adminApprove(int $id): void
    {
        Auth::requireRole('admin');
        $user = Auth::user();

        if (!check_csrf(Flight::request()->data->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        Gig::approve($id, (int)$user['id']);
        Flight::redirect('/admin/gigs');
    }

    public static function adminReject(int $id): void
    {
        Auth::requireRole('admin');
        $user = Auth::user();
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $reason = trim((string) ($r->rejection_reason ?? ''));
        $feedback = trim((string) ($r->rejection_feedback ?? ''));

        if (empty($reason)) {
            Flight::halt(400, 'Une raison de rejet est requise.');
        }

        Gig::reject($id, $reason, $feedback, (int)$user['id']);
        Flight::redirect('/admin/gigs');
    }

    private static function renderCreateForm(array $params = []): void
    {
        $categoryTree = Gig::categoryTree();
        $draft = $_SESSION['gig_draft'] ?? [];

        $defaults = [
            'error' => null,
            'old' => [
                'title' => '',
                'description' => '',
                'category_id' => 0,
                'price_base' => '',
                'delivery_days' => 5,
                'is_express' => 0,
                'timezone_africa' => 0,
            ],
            'oldFaq' => [
                ['q' => '', 'r' => ''],
                ['q' => '', 'r' => ''],
                ['q' => '', 'r' => ''],
            ],
            'oldExtras' => [],
            'categoryTree' => $categoryTree,
            'suggestions' => self::suggestionsByCategory($categoryTree),
            'editing' => false,
            'mainImage' => null,
            'gallery' => [],
        ];

        $payload = array_replace_recursive($defaults, $draft, $params);
        $payload['draftSavedAt'] = $draft['saved_at'] ?? null;

        Flight::renderView('gig-create', $payload, 'admin');
    }

    private static function renderEditForm(array $gig, array $params = []): void
    {
        $categoryTree = Gig::categoryTree();

        $defaults = [
            'error' => null,
            'old' => [
                'title' => $gig['title'],
                'description' => $gig['description'],
                'category_id' => $gig['category_id'],
                'price_base' => round($gig['price_base'] * get_eur_to_xof_rate()),
                'delivery_days' => $gig['delivery_days'],
                'is_express' => $gig['is_express'],
                'timezone_africa' => $gig['timezone_africa'],
            ],
            'oldFaq' => $gig['faq'],
            'oldExtras' => array_map(function($extra) {
                $extra['price'] = round($extra['price'] * get_eur_to_xof_rate());
                return $extra;
            }, $gig['extras']),
            'categoryTree' => $categoryTree,
            'suggestions' => self::suggestionsByCategory($categoryTree),
            'editing' => true,
            'gig' => $gig,
            'mainImage' => $gig['main_image'],
            'gallery' => $gig['gallery'],
        ];

        $payload = array_replace_recursive($defaults, $params);

        Flight::renderView('gig-create', $payload, 'admin');
    }

    private static function validateInput(
        string $title,
        string $description,
        int $categoryId,
        float $priceBase,
        int $deliveryDays,
        array $faq,
        ?array $mainImage,
        ?array $gallery,
        bool $isEditing = false
    ): array {
        $errors = [];

        $titleLength = mb_strlen($title);
        if ($titleLength < 10 || $titleLength > 80) {
            $errors[] = 'Le titre doit contenir entre 10 et 80 caracteres.';
        }

        if (self::wordCount($description) < 150) {
            $errors[] = 'La description doit contenir au moins 150 mots (200 recommandes).';
        }

        if ($categoryId <= 0) {
            $errors[] = 'Selectionne une categorie/sous-categorie valide.';
        }

        // $priceBase est censé être en EUR à ce stade, correspond à 3000 FCFA min (environ 4.5 EUR). On garde la validation mais avec une valeur plus basse pour accommoder le taux de change
        if ($priceBase < 4.0) {
            $errors[] = 'Le prix de base minimum est de 3000 FCFA.';
        }

        if ($deliveryDays < 1) {
            $errors[] = 'Le delai de livraison doit etre superieur ou egal a 1 jour.';
        }

        if (count($faq) < 3 || count($faq) > 5) {
            $errors[] = 'La FAQ doit contenir entre 3 et 5 questions/reponses.';
        }

        // En édition, l'image principale est optionnelle (on peut la garder)
        // En création, elle est obligatoire
        if (!$isEditing && (!$mainImage || ($mainImage['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK)) {
            $errors[] = 'La vignette principale est obligatoire.';
        } elseif ($mainImage && ($mainImage['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            // Si une image a été uploadée (même en édition), on la valide
            if (!self::isAllowedImage($mainImage, true)) {
                $errors[] = 'La vignette doit etre une image jpg/png/webp de 2 Mo max.';
            }
        }

        $galleryCount = self::countGalleryFiles($gallery);
        if ($galleryCount > 0 && ($galleryCount < 2 || $galleryCount > 5)) {
            $errors[] = 'La galerie doit contenir entre 2 et 5 images (ou rester vide).';
        }

        if ($gallery && $galleryCount > 0) {
            foreach ($gallery['error'] as $i => $error) {
                if ($error !== UPLOAD_ERR_OK) {
                    continue;
                }
                $file = [
                    'name' => $gallery['name'][$i],
                    'type' => $gallery['type'][$i],
                    'tmp_name' => $gallery['tmp_name'][$i],
                    'error' => $gallery['error'][$i],
                    'size' => $gallery['size'][$i],
                ];
                if (!self::isAllowedImage($file, true)) {
                    $errors[] = 'Chaque image de galerie doit etre jpg/png/webp et <= 2 Mo.';
                    break;
                }
            }
        }

        return $errors;
    }

    private static function parseFaq(array $questions, array $answers): array
    {
        $faq = [];
        $max = min(count($questions), count($answers));

        for ($i = 0; $i < $max; $i++) {
            $q = trim((string) $questions[$i]);
            $r = trim((string) $answers[$i]);
            if ($q === '' || $r === '') {
                continue;
            }
            $faq[] = ['q' => $q, 'r' => $r];
        }

        return array_slice($faq, 0, 5);
    }

    private static function parseExtras(array $names, array $prices, array $descs): array
    {
        $extras = [];
        $max = min(count($names), count($prices), count($descs));

        for ($i = 0; $i < $max; $i++) {
            $name = trim((string) $names[$i]);
            $price = (float) $prices[$i];
            $desc = trim((string) $descs[$i]);

            if ($name === '' || $price <= 0) {
                continue;
            }

            $extras[] = [
                'name' => $name,
                'price' => $price,
                'desc' => $desc,
            ];
        }

        return array_slice($extras, 0, 5);
    }

    private static function storeImage(array $file, string $targetDir, bool $required, int $gigId): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            if ($required) {
                throw new \RuntimeException('Image principale manquante.');
            }
            return '';
        }

        $mime = self::detectMime($file['tmp_name']);
        $ext = self::extensionByMime($mime);
        if ($ext === null) {
            throw new \RuntimeException('Format image invalide.');
        }

        $safeName = 'main_' . $gigId . '_' . bin2hex(random_bytes(10)) . '.' . $ext;
        $absolute = $targetDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $absolute)) {
            throw new \RuntimeException('Upload image principale echoue.');
        }

        return 'uploads/gigs/' . $gigId . '/' . $safeName;
    }

    private static function storeGallery(?array $gallery, string $targetDir, int $gigId): array
    {
        if (!$gallery || !isset($gallery['name']) || !is_array($gallery['name'])) {
            return [];
        }

        $paths = [];

        foreach ($gallery['error'] as $i => $error) {
            if ($error !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmp = $gallery['tmp_name'][$i] ?? '';
            if ($tmp === '') {
                continue;
            }

            $mime = self::detectMime($tmp);
            $ext = self::extensionByMime($mime);
            if ($ext === null) {
                continue;
            }

            $safeName = 'gallery_' . $gigId . '_' . bin2hex(random_bytes(10)) . '.' . $ext;
            $absolute = $targetDir . $safeName;

            if (move_uploaded_file($tmp, $absolute)) {
                $paths[] = 'uploads/gigs/' . $gigId . '/' . $safeName;
            }
        }

        return array_slice($paths, 0, 5);
    }

    private static function isAllowedImage(array $file, bool $checkSize): bool
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($checkSize && (int) ($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return false;
        }

        $mime = self::detectMime((string) ($file['tmp_name'] ?? ''));
        return in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true);
    }

    private static function detectMime(string $tmpPath): string
    {
        if (!is_file($tmpPath)) {
            return '';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return '';
        }

        $mime = finfo_file($finfo, $tmpPath) ?: '';
        finfo_close($finfo);

        return $mime;
    }

    private static function extensionByMime(string $mime): ?string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
    }

    private static function countGalleryFiles(?array $gallery): int
    {
        if (!$gallery || !isset($gallery['name']) || !is_array($gallery['name'])) {
            return 0;
        }

        $count = 0;
        foreach ($gallery['error'] as $error) {
            if ($error === UPLOAD_ERR_OK) {
                $count++;
            }
        }

        return $count;
    }

    private static function wordCount(string $text): int
    {
        preg_match_all('/\p{L}+/u', $text, $matches);
        return count($matches[0]);
    }

    private static function cleanupGigUploadFolder(int $gigId): void
    {
        $dir = __DIR__ . '/../../public/uploads/gigs/' . $gigId;
        if (!is_dir($dir)) {
            return;
        }

        foreach (glob($dir . '/*') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        @rmdir($dir);
    }

    private static function suggestionsByCategory(array $categoryTree): array
    {
        $suggestions = [];

        foreach ($categoryTree as $parent) {
            $name = self::normalizeCategoryName((string) ($parent['name'] ?? ''));
            $id = (int) ($parent['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            if (str_contains($name, 'develop')) {
                $suggestions[$id] = ['title' => 'Je cree votre site vitrine moderne et rapide', 'desc' => 'Attirez plus de clients avec un site responsive, clair et optimise conversion.'];
            } elseif (str_contains($name, 'marketing')) {
                $suggestions[$id] = ['title' => 'Je lance votre strategie marketing digitale en 7 jours', 'desc' => 'Structure, canaux, calendrier editorial et actions concretes orientees resultats.'];
            } elseif (str_contains($name, 'design')) {
                $suggestions[$id] = ['title' => 'Je concois votre identite visuelle professionnelle', 'desc' => 'Logo, palette et univers graphique coherents pour renforcer votre image de marque.'];
            } elseif (str_contains($name, 'redaction') || str_contains($name, 'traduction')) {
                $suggestions[$id] = ['title' => 'Je redige vos contenus persuasifs et optimises SEO', 'desc' => 'Des textes clairs, credibles et orientes conversion pour votre audience cible.'];
            } elseif (str_contains($name, 'montage') || str_contains($name, 'video')) {
                $suggestions[$id] = ['title' => 'Je monte vos videos dynamiques pour reseaux sociaux', 'desc' => 'Montage rythme, sous-titres et habillage pro pour maximiser la retention.'];
            } elseif (str_contains($name, 'community')) {
                $suggestions[$id] = ['title' => 'Je gere vos reseaux sociaux et engage votre communaute', 'desc' => 'Plan editorial, visuels et suivi hebdomadaire pour faire grandir votre presence.'];
            } elseif (str_contains($name, 'assistance')) {
                $suggestions[$id] = ['title' => 'Je vous assiste au quotidien sur vos taches administratives', 'desc' => 'Support fiable: emails, planning, suivi clients et organisation operationnelle.'];
            } else {
                $suggestions[$id] = ['title' => 'Je propose un service sur mesure pour votre activite', 'desc' => 'Expliquez le besoin, je fournis une prestation claire avec livrables definis.'];
            }
        }

        return $suggestions;
    }

    private static function normalizeCategoryName(string $value): string
    {
        $trimmed = trim($value);
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $trimmed);
        $base = $ascii !== false ? $ascii : $trimmed;
        return strtolower($base);
    }


    public static function togglePause(int $id): void
    {
        Auth::requireRole('freelancer');
        $user = Auth::user();
        
        if (!check_csrf(Flight::request()->data->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $gig = Gig::findById($id);
        if (!$gig || $gig['user_id'] !== (int)$user['id']) {
            Flight::halt(404, 'Service introuvable.');
        }

        $newStatus = null;
        if ($gig['status'] === 'approved') {
            $newStatus = 'paused';
        } elseif ($gig['status'] === 'paused') {
            $newStatus = 'approved';
        }

        if ($newStatus) {
            Flight::get('db')->prepare('UPDATE gigs SET status = :status WHERE id = :id')
                ->execute(['status' => $newStatus, 'id' => $id]);
        }

        Flight::redirect('/mes-services');
    }

    public static function delete(int $id): void
    {
        Auth::requireRole('freelancer');
        $user = Auth::user();
        
        if (!check_csrf(Flight::request()->data->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $gig = Gig::findById($id);
        if (!$gig || $gig['user_id'] !== (int)$user['id']) {
            Flight::halt(404, 'Service introuvable.');
        }

        Flight::get('db')->prepare('UPDATE gigs SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id')
            ->execute(['id' => $id]);

        Flight::redirect('/mes-services');
    }
    public static function myServices(): void
    {
        Auth::requireRole('freelancer');
        $user = Auth::user();

        $gigs = Gig::byUser((int)$user['id']);
        
        // Stats for the dashboard
        $stats = [
            'total' => count($gigs),
            'approved' => count(array_filter($gigs, fn($g) => $g['status'] === 'approved')),
            'pending' => count(array_filter($gigs, fn($g) => $g['status'] === 'pending')),
            'paused' => count(array_filter($gigs, fn($g) => $g['status'] === 'paused')),
            'rejected' => count(array_filter($gigs, fn($g) => $g['status'] === 'rejected')),
        ];

        Flight::renderView('my-services', [
            'gigs' => $gigs,
            'stats' => $stats,
            'user' => $user
        ], 'admin');
    }
}
