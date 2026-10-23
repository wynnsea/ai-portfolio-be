<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\FormatsContent;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Certification
 */
class CertificationResource extends JsonResource
{
    use FormatsContent;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'issuer' => $this->issuer,
            'category' => $this->category->value,
            'year' => $this->year,
            'score' => $this->score,
            'note' => $this->note,
            'credentialUrl' => $this->credential_url,
            ...$this->kanjiCover(),
            ...$this->coverImage(),
            ...$this->meta(),
        ];
    }
}
