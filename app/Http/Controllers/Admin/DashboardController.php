<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageStatus;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Certification;
use App\Models\Message;
use App\Models\PageView;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Day labels for weeklyVisitors, Monday first.
     */
    private const DAY_LABELS = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

    public function __invoke(): JsonResponse
    {
        $counts = [
            'projects' => Project::query()->count(),
            'certifications' => Certification::query()->count(),
            'activities' => Activity::query()->count(),
            'posts' => Post::query()->count(),
            'messages' => Message::query()->count(),
            'unreadMessages' => Message::query()->where('status', MessageStatus::New)->count(),
        ];

        return response()->json([
            'data' => [
                'counts' => $counts,
                'latestMessages' => $this->latestMessages(),
                'latestContent' => $this->latestContent(),
                'profileCompleteness' => ['checks' => $this->completenessChecks($counts)],
                'weeklyVisitors' => $this->weeklyVisitors(),
            ],
        ]);
    }

    /**
     * @return Collection<int, array{id: int, name: string, subject: string, status: string, date: string}>
     */
    private function latestMessages(): Collection
    {
        return Message::query()->latest()->latest('id')->limit(4)->get()
            ->map(fn (Message $message): array => [
                'id' => $message->id,
                'name' => $message->name,
                'subject' => $message->subject,
                'status' => $message->status->value,
                'date' => $message->created_at->toDateString(),
            ]);
    }

    /**
     * The five most recently created items across all content types.
     *
     * @return Collection<int, array{type: string, title: string, year: ?string}>
     */
    private function latestContent(): Collection
    {
        $sources = [
            'project' => Project::query()->select('id', 'title', 'year', 'created_at'),
            'certification' => Certification::query()->select('id', 'title', 'year', 'created_at'),
            'activity' => Activity::query()->select('id', 'title', 'year', 'created_at'),
            'post' => Post::query()->select('id', 'title', 'date', 'created_at'),
        ];

        return collect($sources)
            ->flatMap(fn ($query, string $type) => $query->latest()->latest('id')->limit(5)->get()
                ->map(fn ($item): array => [
                    'type' => $type,
                    'title' => $item->title,
                    'year' => $type === 'post' ? $item->date?->format('Y') : $item->year,
                    'createdAt' => $item->created_at,
                ]))
            ->sortByDesc('createdAt')
            ->take(5)
            ->map(fn (array $item): array => collect($item)->except('createdAt')->all())
            ->values();
    }

    /**
     * @param  array<string, int>  $counts
     * @return list<array{label: string, done: bool}>
     */
    private function completenessChecks(array $counts): array
    {
        return [
            ['label' => 'Proyek', 'done' => $counts['projects'] > 0],
            ['label' => 'Sertifikasi', 'done' => $counts['certifications'] > 0],
            ['label' => 'Kegiatan', 'done' => $counts['activities'] > 0],
            ['label' => 'CV', 'done' => filled(Setting::current()->cv_path)],
            ['label' => 'Artikel terbit', 'done' => Post::query()->published()->exists()],
        ];
    }

    /**
     * Unique visitors per day for the current week (Monday - Sunday).
     *
     * @return list<array{day: string, value: int, future: bool}>
     */
    private function weeklyVisitors(): array
    {
        $start = Carbon::today()->startOfWeek(Carbon::MONDAY);

        $visitors = PageView::query()
            ->whereBetween('visited_on', [$start->toDateString(), $start->copy()->addDays(6)->toDateString()])
            ->selectRaw('visited_on, COUNT(DISTINCT visitor_hash) as total')
            ->groupBy('visited_on')
            ->pluck('total', 'visited_on')
            ->mapWithKeys(fn (mixed $total, string $date): array => [Carbon::parse($date)->toDateString() => (int) $total]);

        return collect(self::DAY_LABELS)
            ->map(function (string $label, int $offset) use ($start, $visitors): array {
                $day = $start->copy()->addDays($offset);

                return [
                    'day' => $label,
                    'value' => $visitors[$day->toDateString()] ?? 0,
                    'future' => $day->isFuture(),
                ];
            })
            ->all();
    }
}
