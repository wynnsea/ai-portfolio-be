<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Pesan baru: {$this->message->subject}")
            ->replyTo($this->message->email, $this->message->name)
            ->greeting('Ada pesan baru dari website!')
            ->line("Dari: {$this->message->name} <{$this->message->email}>")
            ->line("Subjek: {$this->message->subject}")
            ->line($this->message->message);
    }
}
