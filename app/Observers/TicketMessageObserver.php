<?php

namespace App\Observers;

use App\Enums\TicketStatus;
use App\Filament\Portal\Resources\Tickets\TicketResource as PortalTicketResource;
use App\Filament\Resources\Tickets\TicketResource as AdminTicketResource;
use App\Models\TicketMessage;
use App\Notifications\NewTicketMessageNotification;
use Illuminate\Support\Facades\Notification;

class TicketMessageObserver
{
    public function created(TicketMessage $message): void
    {
        $ticket = $message->ticket->loadMissing(['customer', 'assignees']);
        $authorIsCustomer = $message->user_id === $ticket->user_id;

        if ($authorIsCustomer) {
            // Customer replied -> notify every assigned admin.
            $recipients = $ticket->assignees;
            $url = AdminTicketResource::getUrl('view', ['record' => $ticket], panel: 'admin');
            $ticket->update(['status' => TicketStatus::Open]);
        } else {
            // Admin replied -> notify the ticket's customer.
            $recipients = $ticket->customer ? collect([$ticket->customer]) : collect();
            $url = PortalTicketResource::getUrl('view', ['record' => $ticket], panel: 'portal');
            $ticket->update(['status' => TicketStatus::Answered]);
        }

        $recipients = $recipients->reject(fn ($user) => $user->id === $message->user_id);

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new NewTicketMessageNotification($message, $url));
        }
    }
}
