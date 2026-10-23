<?php

namespace App\Http\Requests;

use App\Enums\ContentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class ProjectRequest extends ContentRequest
{
    /**
     * The admin form sends the stack as a comma separated string.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('stack'))) {
            $this->merge([
                'stack' => array_values(array_filter(array_map('trim', explode(',', $this->input('stack'))))),
            ]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($this->route('project'))],
            'title' => [...$this->requiredOnCreate(), 'string', 'max:120'],
            'category' => [...$this->requiredOnCreate(), 'string', 'max:80'],
            'year' => [...$this->requiredOnCreate(), 'digits:4'],
            'role' => ['nullable', 'string', 'max:80'],
            'duration' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'required', Rule::enum(ContentStatus::class)],
            'summary' => [...$this->requiredOnCreate(), 'string'],
            'overview' => ['nullable', 'string'],
            'challenge' => ['nullable', 'string'],
            'approach' => ['nullable', 'array'],
            'approach.*' => ['required', 'string', 'max:500'],
            'results' => ['nullable', 'array'],
            'results.*.value' => ['required', 'string', 'max:50'],
            'results.*.label' => ['required', 'string', 'max:150'],
            'stack' => ['nullable', 'array'],
            'stack.*' => ['required', 'string', 'max:50'],
            'links' => ['nullable', 'array:demo,source'],
            'links.demo' => ['nullable', 'url', 'max:2048'],
            'links.source' => ['nullable', 'url', 'max:2048'],
            ...$this->kanjiCoverRules(),
            ...$this->coverImageRules(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        $attributes = parent::attributesForModel();

        if (array_key_exists('links', $attributes)) {
            $attributes['links'] = array_filter($attributes['links'] ?? [], filled(...));
        }

        return $attributes;
    }
}
