<?php

namespace App\Http\Requests;

use App\Enums\CertificationCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class CertificationRequest extends ContentRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [...$this->requiredOnCreate(), 'string', 'max:255'],
            'issuer' => [...$this->requiredOnCreate(), 'string', 'max:255'],
            'category' => [...$this->requiredOnCreate(), Rule::enum(CertificationCategory::class)],
            'year' => ['nullable', 'string', 'max:30'],
            'score' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
            'credentialUrl' => ['nullable', 'url', 'max:255'],
            ...$this->kanjiCoverRules(),
            ...$this->coverImageRules(),
        ];
    }
}
