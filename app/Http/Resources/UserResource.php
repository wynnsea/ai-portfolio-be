<?php

namespace App\Http\Resources;

use App\Models\Setting;
use App\Models\User;
use App\Support\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'photoUrl' => PublicFile::url(Setting::current()->photo_path),
        ];
    }
}
