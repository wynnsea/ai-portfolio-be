<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRequest;
use App\Support\PublicFile;
use Illuminate\Http\JsonResponse;

class UploadController extends Controller
{
    /**
     * Store a file on the public disk under a random name.
     */
    public function store(UploadRequest $request): JsonResponse
    {
        $path = $request->file('file')->store($request->validated('folder'), 'public');

        return response()->json([
            'url' => PublicFile::url($path),
            'path' => $path,
        ], 201);
    }
}
