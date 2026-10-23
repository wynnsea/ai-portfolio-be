<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Certification;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_admin_from_config_and_frontend_content(): void
    {
        Storage::fake('public');
        config(['portfolio.admin.email' => 'admin@example.com', 'portfolio.admin.password' => 'rahasia123']);

        $this->seed();

        $admin = User::query()->sole();
        $this->assertSame('admin@example.com', $admin->email);
        $this->assertTrue(Hash::check('rahasia123', $admin->password));
        $this->assertSame(1, Project::query()->published()->count());
        $this->assertSame(4, Certification::query()->count());
        $this->assertSame(5, Activity::query()->count());
        $this->assertSame('Wynnsea', Setting::current()->brand);
        Storage::disk('public')->assertExists(['certifications/jlpt-n4.jpg', 'activities/hi-tech-berbagi.jpg', 'profile/wyn.jpg']);
    }

    public function test_running_again_keeps_existing_data(): void
    {
        Storage::fake('public');
        config(['portfolio.admin.email' => 'admin@example.com', 'portfolio.admin.password' => 'rahasia123']);
        $this->seed();
        User::query()->sole()->update(['password' => 'diganti123']);
        Project::query()->sole()->update(['title' => 'Diubah admin']);

        $this->seed();

        $this->assertTrue(Hash::check('diganti123', User::query()->sole()->password));
        $this->assertSame('Diubah admin', Project::query()->sole()->title);
        $this->assertSame(4, Certification::query()->count());
    }
}
