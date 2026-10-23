<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    /**
     * Folders an upload may be stored in.
     */
    public const FOLDERS = ['projects', 'certifications', 'activities', 'posts', 'profile', 'cv'];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Images: jpg/png/webp up to 4 MB. CV: pdf up to 5 MB.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $fileRules = $this->input('folder') === 'cv'
            ? ['mimes:pdf', 'max:5120']
            : ['mimes:jpg,jpeg,png,webp', 'max:4096'];

        return [
            'folder' => ['required', 'string', 'in:'.implode(',', self::FOLDERS)],
            'file' => ['required', 'file', ...$fileRules],
        ];
    }
}
