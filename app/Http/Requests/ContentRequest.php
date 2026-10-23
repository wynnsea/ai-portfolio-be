<?php

namespace App\Http\Requests;

use App\Enums\CoverTone;
use App\Support\PublicFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Base request for admin content (projects, certifications, activities, posts).
 *
 * Input is camelCase; attributesForModel() maps it to snake_case model attributes.
 * POST requires every mandatory field, PUT/PATCH only validates the fields that are sent.
 */
abstract class ContentRequest extends FormRequest
{
    /**
     * Input fields holding a single file URL, mapped to the column storing its path.
     *
     * @var array<string, string>
     */
    protected array $fileFields = ['coverUrl' => 'cover_path'];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return collect($this->validated())
            ->mapWithKeys(fn (mixed $value, string $key): array => isset($this->fileFields[$key])
                ? [$this->fileFields[$key] => PublicFile::path($value)]
                : [Str::snake($key) => $value])
            ->all();
    }

    /**
     * @return list<string>
     */
    protected function requiredOnCreate(): array
    {
        return $this->isMethod('POST') ? ['required'] : ['sometimes', 'required'];
    }

    /**
     * @return array<string, mixed>
     */
    protected function kanjiCoverRules(): array
    {
        return [
            'titleJp' => ['nullable', 'string', 'max:50'],
            'kanji' => ['nullable', 'string', 'max:2'],
            'tone' => ['sometimes', 'required', Rule::enum(CoverTone::class)],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function coverImageRules(): array
    {
        return ['coverUrl' => ['nullable', 'string', 'max:2048']];
    }
}
