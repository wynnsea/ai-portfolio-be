<?php

namespace App\Models;

use App\Enums\MessageStatus;
use App\Models\Concerns\HasAdminListing;
use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'subject', 'message', 'status'])]
class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasAdminListing, HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'Baru',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MessageStatus::class,
        ];
    }

    public function searchableColumns(): array
    {
        return ['name', 'email', 'subject'];
    }

    public function filterableColumns(): array
    {
        return ['status' => 'status'];
    }

    public function markAsRead(): void
    {
        if ($this->status === MessageStatus::New) {
            $this->update(['status' => MessageStatus::Read]);
        }
    }
}
