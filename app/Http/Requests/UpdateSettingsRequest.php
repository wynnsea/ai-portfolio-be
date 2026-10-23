<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateSettingsRequest extends ContentRequest
{
    /**
     * @var array<string, string>
     */
    protected array $fileFields = ['photoUrl' => 'photo_path', 'cvUrl' => 'cv_path'];

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand' => ['sometimes', 'nullable', 'string', 'max:80'],
            'name' => ['sometimes', 'nullable', 'string', 'max:80'],
            'nameJp' => ['sometimes', 'nullable', 'string', 'max:50'],
            'role' => ['sometimes', 'nullable', 'string', 'max:80'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'location' => ['sometimes', 'nullable', 'string', 'max:80'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'photoUrl' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'cvUrl' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'availability' => ['sometimes', 'array:open,label,detail'],
            'availability.open' => ['sometimes', 'boolean'],
            'availability.label' => ['sometimes', 'nullable', 'string', 'max:80'],
            'availability.detail' => ['sometimes', 'nullable', 'string', 'max:120'],
            'socials' => ['sometimes', 'nullable', 'array'],
            'socials.*.label' => ['required', 'string', 'max:50'],
            'socials.*.href' => ['required', 'url', 'max:255'],
            'features' => ['sometimes', 'array:chatbot,preloader'],
            'features.chatbot' => ['sometimes', 'boolean'],
            'features.preloader' => ['sometimes', 'boolean'],
        ];
    }
}
