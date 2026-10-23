<?php

namespace App\Http\Resources;

use App\Models\Setting;
use App\Support\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Setting
 */
class SettingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'brand' => $this->brand,
            'name' => $this->name,
            'nameJp' => $this->name_jp,
            'role' => $this->role,
            'description' => $this->description,
            'location' => $this->location,
            'email' => $this->email,
            'photoUrl' => PublicFile::url($this->photo_path),
            'cvUrl' => PublicFile::url($this->cv_path),
            'availability' => [
                'open' => (bool) ($this->availability['open'] ?? false),
                'label' => $this->availability['label'] ?? null,
                'detail' => $this->availability['detail'] ?? null,
            ],
            'socials' => $this->socials ?? [],
            'features' => [
                'chatbot' => (bool) ($this->features['chatbot'] ?? true),
                'preloader' => (bool) ($this->features['preloader'] ?? true),
            ],
        ];
    }
}
