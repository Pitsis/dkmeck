<?php

namespace App\Notifications;

use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TicketMessage $message,
        public string $url,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->message->ticket;
        $author = $this->message->author;

        return (new MailMessage)
            ->subject("Νέα απάντηση στο αίτημα #{$ticket->id}: {$ticket->subject}")
            ->greeting("Γεια σου {$notifiable->name},")
            ->line("Ο/Η {$author->name} απάντησε στο αίτημα «{$ticket->subject}».")
            ->line('---')
            ->line($this->message->body)
            ->action('Δες το αίτημα', $this->url);
    }
}
