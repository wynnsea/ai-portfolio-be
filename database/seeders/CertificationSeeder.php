<?php

namespace Database\Seeders;

use App\Models\Certification;
use Database\Seeders\Concerns\CopiesSeedFiles;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    use CopiesSeedFiles;

    /**
     * Certifications from the frontend's lib/certifications.ts.
     * The images are the censored versions (date of birth hidden).
     */
    public function run(): void
    {
        if (Certification::query()->exists()) {
            return;
        }

        $certifications = [
            [
                'title' => 'JLPT N4',
                'title_jp' => '日本語能力試験',
                'kanji' => '語',
                'tone' => 'dark',
                'category' => 'Japanese',
                'issuer' => 'Japan Foundation & JEES',
                'year' => 'Des 2024',
                'note' => 'Japanese-Language Proficiency Test level N4',
                'cover_path' => 'certifications/jlpt-n4.jpg',
            ],
            [
                'title' => 'Junior Web Developer',
                'title_jp' => 'ウェブ開発',
                'kanji' => '網',
                'tone' => 'mid',
                'category' => 'Web Dev',
                'issuer' => 'BNSP · LSP Teknologi Digital',
                'year' => '2026',
                'note' => 'Sertifikat kompetensi Pengembang Web Pratama, berlaku 3 tahun',
                'cover_path' => 'certifications/junior-web-developer.jpg',
            ],
            [
                'title' => 'TOEFL Prediction',
                'title_jp' => '英語能力',
                'kanji' => '英',
                'tone' => 'dark',
                'category' => 'English',
                'issuer' => 'Central Course, Pare',
                'year' => '2025',
                'score' => '477',
                'note' => 'Listening 47 · Structure 50 · Reading 46',
                'cover_path' => 'certifications/toefl.jpg',
            ],
            [
                'title' => 'iTEP Essential',
                'title_jp' => '英語試験',
                'kanji' => '試',
                'tone' => 'mid',
                'category' => 'English',
                'issuer' => 'iTEP International',
                'year' => '2025',
                'score' => 'B1',
                'note' => 'Intermediate level (CEFR B1)',
                'cover_path' => 'certifications/itep.jpg',
            ],
        ];

        foreach ($certifications as $position => $certification) {
            Certification::query()->create([
                ...$certification,
                'cover_path' => $this->seedFile($certification['cover_path']),
                'sort_order' => $position,
            ]);
        }
    }
}
