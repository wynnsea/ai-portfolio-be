<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class MessageController extends Controller
{
    /**
     * Contact form submission. A filled "website" field is a bot hitting the
     * honeypot: it gets the same 201 response but nothing is stored.
     */
    public function store(Request $request): JsonResponse
    {
        $response = response()->json(['message' => 'Pesan terkirim. Terima kasih!'], 201);

        if ($request->filled('website')) {
            return $response;
        }

        $validated = app(StoreMessageRequest::class)->validated();
        $message = Message::query()->create($validated);

        if (filled($adminEmail = config('portfolio.admin.email'))) {
            Notification::route('mail', $adminEmail)->notify(new NewMessageReceived($message));
        }

        return $response;
    }
}
