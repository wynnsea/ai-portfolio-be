<?php

namespace App\Http\Requests;

use App\Support\PublicFile;
use Illuminate\Contracts\Validation\ValidationRule;

class ActivityRequest extends ContentRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [...$this->requiredOnCreate(), 'string', 'max:255'],
            'role' => [...$this->requiredOnCreate(), 'string', 'max:255'],
            'organizer' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:80'],
            'year' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['required', 'string', 'max:2048'],
            'focus' => ['nullable', 'string', 'max:30'],
            ...$this->kanjiCoverRules(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        $attributes = parent::attributesForModel();

        if (array_key_exists('photos', $attributes)) {
            $attributes['photos'] = array_map(PublicFile::path(...), $attributes['photos'] ?? []);
        }

        return $attributes;
    }
}
