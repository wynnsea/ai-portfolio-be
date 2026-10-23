<?php

namespace Database\Seeders;

use App\Models\Setting;
use Database\Seeders\Concerns\CopiesSeedFiles;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    use CopiesSeedFiles;

    /**
     * Profile data from the frontend's lib/site.ts.
     */
    public function run(): void
    {
        if (Setting::query()->exists()) {
            return;
        }

        Setting::query()->create([
            'brand' => 'Wynnsea',
            'name' => 'Wayan',
            'name_jp' => 'ワヤン',
            'role' => 'Junior AI Engineer',
            'description' => 'Lulusan Politeknik Takumi yang suka ngulik integrasi AI. Saya nyambungin LLM, API, dan data jadi aplikasi web yang beneran kepake.',
            'location' => 'Indonesia',
            'email' => 'wayaandeva123@gmail.com',
            'photo_path' => $this->seedFile('profile/wyn.jpg'),
            'cv_path' => null,
            'availability' => ['open' => true, 'label' => 'Siap bekerja', 'detail' => 'Full-time & freelance'],
            'socials' => [
                ['label' => 'GitHub', 'href' => 'https://github.com/wynnsea'],
                ['label' => 'LinkedIn', 'href' => 'https://www.linkedin.com/in/wayan-deva/'],
            ],
            'features' => ['chatbot' => true, 'preloader' => true],
        ]);
    }
}
