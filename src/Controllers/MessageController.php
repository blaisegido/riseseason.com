<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use App\Models\Message;
use App\Models\User;
use Flight;

final class MessageController
{
    public static function index(): void
    {
        Auth::requireLogin();
        $me = Auth::user();
        $partnerId = (int)(Flight::request()->query->user ?? 0);

        $conversations = Message::conversations((int)$me['id']);
        $messages = [];
        $partner = null;

        if ($partnerId > 0) {
            $partner = User::findById($partnerId);
            if ($partner) {
                $messages = Message::between((int)$me['id'], $partnerId);
            }
        }

        Flight::renderView('messages', [
            'conversations' => $conversations,
            'messages' => $messages,
            'partner' => $partner,
            'me' => $me,
        ], 'admin');
    }

    public static function send(): void
    {
        Auth::requireLogin();
        $me = Auth::user();
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $receiverId = (int)$r->receiver_id;
        $content = trim((string)$r->content);

        if ($receiverId > 0 && $content !== '') {
            Message::send((int)$me['id'], $receiverId, $content);
        }

        Flight::redirect('/messages?user=' . $receiverId);
    }
}
