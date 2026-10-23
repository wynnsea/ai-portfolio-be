<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageViewRequest;
use App\Models\PageView;
use Illuminate\Http\Response;

class PageViewController extends Controller
{
    /**
     * Record a visit. Visitors are identified by an anonymous daily hash,
     * so the same visitor is only counted once per path per day.
     */
    public function store(StorePageViewRequest $request): Response
    {
        $today = today()->toDateString();

        PageView::query()->insertOrIgnore([
            'path' => $request->validated('path'),
            'visited_on' => $today,
            'visitor_hash' => hash_hmac('sha256', $request->ip().'|'.$request->userAgent().'|'.$today, config('app.key')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->noContent();
    }
}
