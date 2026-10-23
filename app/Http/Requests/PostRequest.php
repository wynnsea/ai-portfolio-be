<?php

namespace App\Http\Requests;

use App\Enums\ContentStatus;
use App\Enums\PostCategory;
use App\Rules\PostBlock;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class PostRequest extends ContentRequest
{
    /**
     * The admin form may send the content as plain text: every paragraph
     * (separated by a blank line) becomes a "p" block.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('content'))) {
            $paragraphs = preg_split('/\R\s*\R/', trim($this->input('content'))) ?: [];

            $this->merge([
                'content' => collect($paragraphs)
                    ->map(fn (string $paragraph): string => trim($paragraph))
                    ->filter()
                    ->map(fn (string $paragraph): array => ['type' => 'p', 'text' => $paragraph])
                    ->values()
                    ->all(),
            ]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($this->route('post'))],
            'title' => [...$this->requiredOnCreate(), 'string', 'max:255'],
            'category' => [...$this->requiredOnCreate(), Rule::enum(PostCategory::class)],
            'status' => ['sometimes', 'required', Rule::enum(ContentStatus::class)],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'readTime' => ['nullable', 'string', 'max:30'],
            'excerpt' => [...$this->requiredOnCreate(), 'string', 'max:300'],
            'content' => ['nullable', 'array'],
            'content.*' => [new PostBlock],
            ...$this->kanjiCoverRules(),
            ...$this->coverImageRules(),
        ];
    }
}
