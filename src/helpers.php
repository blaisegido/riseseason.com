<?php

declare(strict_types=1);

if (!function_exists('get_eur_to_xof_rate')) {
    function get_eur_to_xof_rate(): float
    {
        // Taux fixe officiel du FCFA par rapport à l'Euro
        $fixedRate = 655.957;
        
        // Fichier de cache pour ne pas faire de requêtes à chaque page
        $uploadDir = __DIR__ . '/../public/uploads';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        $cacheFile = $uploadDir . '/eur_xof_rate.json';
        
        // On garde le cache pendant 12 heures
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 12 * 3600) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && isset($data['rate'])) {
                return (float) $data['rate'];
            }
        }
        
        // Appel d'une API publique gratuite pour les taux en temps réel
        try {
            $ctx = stream_context_create(['http' => ['timeout' => 2]]);
            $response = @file_get_contents('https://open.er-api.com/v6/latest/EUR', false, $ctx);
            if ($response !== false) {
                $data = json_decode($response, true);
                if (isset($data['rates']['XOF'])) {
                    $rate = (float) $data['rates']['XOF'];
                    @file_put_contents($cacheFile, json_encode(['rate' => $rate, 'time' => time()]));
                    return $rate;
                }
            }
        } catch (\Throwable $e) {
            // Ignorer les erreurs réseau, fallback au taux fixe
        }
        
        return $fixedRate;
    }
}

if (!function_exists('format_price')) {
    /**
     * Formate un montant Euro avec double affichage FCFA / EUR.
     * Les deux versions sont rendues dans des <span> avec des classes CSS :
     *   - .price-xof : visible quand la devise choisie est FCFA (par défaut)
     *   - .price-eur : visible quand la devise choisie est EUR
     * Le JavaScript côté client gère le toggle via localStorage.
     */
    function format_price(float $amountInEur, bool $showCents = false): string
    {
        $rate = get_eur_to_xof_rate();
        $amountInXof = $amountInEur * $rate;
        
        $roundedXof = round($amountInXof / 10) * 10;
        $formattedXof = number_format($roundedXof, 0, ',', ' ');
        
        $decimalsEur = $showCents || fmod($amountInEur, 1) !== 0.0 ? 2 : 0;
        $formattedEur = number_format($amountInEur, $decimalsEur, ',', ' ');
        
        // Mode FCFA : montant FCFA principal + EUR en petit entre parenthèses
        $xofHtml = sprintf(
            '<span class="price-xof">%s FCFA <span class="text-[0.6em] font-bold text-gray-400 opacity-80 whitespace-nowrap ml-0.5">(%s €)</span></span>',
            $formattedXof,
            $formattedEur
        );
        
        // Mode EUR : montant EUR principal + FCFA en petit entre parenthèses
        $eurHtml = sprintf(
            '<span class="price-eur" style="display:none">%s € <span class="text-[0.6em] font-bold text-gray-400 opacity-80 whitespace-nowrap ml-0.5">(%s FCFA)</span></span>',
            $formattedEur,
            $formattedXof
        );
        
        return $xofHtml . $eurHtml;
    }
}

if (!function_exists('format_price_raw')) {
    /**
     * Version brute sans balises HTML
     */
    function format_price_raw(float $amountInEur, bool $showCents = false): string
    {
        $rate = get_eur_to_xof_rate();
        $roundedXof = round(($amountInEur * $rate) / 10) * 10;
        $formattedXof = number_format($roundedXof, 0, ',', ' ');
        
        $decimalsEur = $showCents || fmod($amountInEur, 1) !== 0.0 ? 2 : 0;
        $formattedEur = number_format($amountInEur, $decimalsEur, ',', ' ');
        
        return sprintf('%s FCFA (%s €)', $formattedXof, $formattedEur);
    }
}

if (!function_exists('format_date_fr')) {
    /**
     * Formate une date en français (traduction basique des mois anglais)
     */
    function format_date_fr(string $dateString, string $format = 'd M Y'): string
    {
        $timestamp = strtotime($dateString);
        if (!$timestamp) return '';
        
        $months = [
            'Jan' => 'Jan', 'Feb' => 'Fév', 'Mar' => 'Mar', 'Apr' => 'Avr',
            'May' => 'Mai', 'Jun' => 'Juin', 'Jul' => 'Juil', 'Aug' => 'Août',
            'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Déc',
            'January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars',
            'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin', 'July' => 'Juillet',
            'August' => 'Août', 'September' => 'Septembre', 'October' => 'Octobre',
            'November' => 'Novembre', 'December' => 'Décembre'
        ];
        
        $enDate = date($format, $timestamp);
        return strtr($enDate, $months);
    }
}

if (!function_exists('format_status_fr')) {
    /**
     * Traduit les statuts anglais du système en français
     */
    function format_status_fr(string $status): string
    {
        $map = [
            'pending'   => 'En attente',
            'published' => 'Publié',
            'approved'  => 'Approuvé',
            'rejected'  => 'Rejeté',
            'draft'     => 'Brouillon',
            'paid'      => 'Payé',
            'in_progress' => 'En cours',
            'delivered' => 'Livré',
            'completed' => 'Terminé',
            'canceled'  => 'Annulé',
            'archived'  => 'Archivé',
            'open'      => 'Ouvert',
            'closed'    => 'Fermé',
        ];
        return $map[$status] ?? ucfirst($status);
    }
}
