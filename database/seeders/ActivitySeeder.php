<?php

namespace Database\Seeders;

use App\Models\Activity;
use Database\Seeders\Concerns\CopiesSeedFiles;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    use CopiesSeedFiles;

    /**
     * Activities from the frontend's lib/activities.ts.
     */
    public function run(): void
    {
        if (Activity::query()->exists()) {
            return;
        }

        $activities = [
            [
                'title' => 'Nusantara Data Center Academy',
                'role' => 'Peserta',
                'organizer' => 'Nusantara Data Center Academy × Microsoft',
                'description' => 'Lulus dari Industrial Vocational Class Program Batch 2, program vokasi industri data center yang didukung Microsoft dan mitra industri.',
                'title_jp' => 'データセンター',
                'kanji' => '卒',
                'tone' => 'dark',
                'category' => 'Program',
                'photos' => ['activities/nusantara-data-center-academy.jpg'],
            ],
            [
                'title' => 'Bunkasai',
                'role' => 'Fasilitator',
                'organizer' => 'Politeknik Takumi',
                'description' => 'Mendampingi peserta selama kegiatan festival budaya Jepang, menjelaskan alur acara, dan membantu mereka menyelesaikan tantangan.',
                'title_jp' => '文化祭',
                'kanji' => '祭',
                'tone' => 'mid',
                'category' => 'Facilitator',
                'photos' => ['activities/fasilitator-bunkasai.jpg'],
            ],
            [
                'title' => 'Learning Factory PT Minori',
                'role' => 'Peserta',
                'organizer' => 'PT Minori',
                'description' => 'Mengikuti program Learning Factory untuk mengenal langsung lingkungan dan budaya kerja industri.',
                'title_jp' => 'ラーニングファクトリー',
                'kanji' => '工',
                'tone' => 'dark',
                'category' => 'Industry',
                'photos' => ['activities/learning-factory-minori.jpg'],
            ],
            [
                'title' => 'HI Tech Berbagi',
                'role' => 'Panitia',
                'organizer' => 'Politeknik Takumi',
                'description' => 'Kegiatan berbagi sembako dan bingkisan Idul Fitri bersama teman-teman kampus.',
                'title_jp' => '分かち合い',
                'kanji' => '分',
                'tone' => 'mid',
                'category' => 'Social',
                'photos' => ['activities/hi-tech-berbagi.jpg'],
                'focus' => '50% 78%',
            ],
            [
                'title' => 'Malam Keakraban HIMA TI',
                'role' => 'Peserta',
                'organizer' => 'HIMA Teknologi Informasi',
                'description' => 'Malam keakraban himpunan mahasiswa Teknologi Informasi untuk mempererat hubungan antar angkatan dan dosen.',
                'title_jp' => '親睦会',
                'kanji' => '絆',
                'tone' => 'dark',
                'category' => 'Community',
                'photos' => ['activities/makrab-hima-ti.jpg'],
            ],
        ];

        foreach ($activities as $position => $activity) {
            Activity::query()->create([
                ...$activity,
                'photos' => array_map($this->seedFile(...), $activity['photos']),
                'sort_order' => $position,
            ]);
        }
    }
}
