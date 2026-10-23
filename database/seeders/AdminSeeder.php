<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Create the single admin from ADMIN_EMAIL / ADMIN_PASSWORD.
     * An existing admin is left untouched so a changed password is kept.
     */
    public function run(): void
    {
        $email = config('portfolio.admin.email');
        $password = config('portfolio.admin.password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD belum diisi, admin tidak dibuat.');

            return;
        }

        User::query()->firstOrCreate(['email' => $email], [
            'name' => config('portfolio.admin.name'),
            'password' => $password,
        ]);
    }
}
