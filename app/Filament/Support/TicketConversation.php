<?php

namespace App\Filament\Support;

use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;

class TicketConversation
{
    public static function entry(): ViewEntry
    {
        return ViewEntry::make('conversation')
            ->hiddenLabel()
            ->view('filament.tickets.conversation')
            ->state(fn (Ticket $record): array => [
                'customerId' => $record->user_id,
                'messages' => $record->messages()->with('author')->get(),
            ]);
    }

    public static function replyAction(): Action
    {
        return Action::make('reply')
            ->label('Απάντηση')
            ->icon(Heroicon::ChatBubbleLeftRight)
            ->modalHeading('Νέα απάντηση')
            ->modalSubmitActionLabel('Αποστολή')
            ->schema([
                Textarea::make('body')
                    ->label('Μήνυμα')
                    ->required()
                    ->rows(5),
            ])
            ->action(function (array $data, Page $livewire): void {
                /** @var Ticket $ticket */
                $ticket = $livewire->getRecord();

                $ticket->messages()->create([
                    'user_id' => auth()->id(),
                    'body' => $data['body'],
                ]);

                Notification::make()
                    ->title('Η απάντηση στάλθηκε')
                    ->success()
                    ->send();
            });
    }
}
