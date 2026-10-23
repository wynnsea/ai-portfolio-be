<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Validates one article content block:
 * { type: "p" | "h2" | "quote", text } | { type: "list", items } | { type: "code", lang, code }.
 */
class PostBlock implements ValidationRule
{
    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = is_array($value) ? ($value['type'] ?? null) : null;

        $valid = match ($type) {
            'p', 'h2', 'quote' => $this->filledString($value['text'] ?? null),
            'list' => $this->stringList($value['items'] ?? null),
            'code' => is_string($value['lang'] ?? null) && $this->filledString($value['code'] ?? null),
            default => null,
        };

        if ($valid === null) {
            $fail(':Attribute harus punya type p, h2, quote, list, atau code.');
        } elseif (! $valid) {
            $fail(match ($type) {
                'list' => ':Attribute bertipe list wajib punya items berupa daftar teks.',
                'code' => ':Attribute bertipe code wajib punya lang dan code.',
                default => ":Attribute bertipe {$type} wajib punya text.",
            });
        }
    }

    private function filledString(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '';
    }

    private function stringList(mixed $value): bool
    {
        return is_array($value) && array_is_list($value) && $value !== []
            && collect($value)->every(fn (mixed $item): bool => $this->filledString($item));
    }
}
