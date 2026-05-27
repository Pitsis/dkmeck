<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Filament\Support\TicketConversation;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Αίτημα')
                    ->columns(2)
                    ->components([
                        TextEntry::make('subject')
                            ->label('Θέμα')
                            ->columnSpanFull(),

                        TextEntry::make('customer.name')
                            ->label('Πελάτης'),

                        TextEntry::make('status')
                            ->label('Κατάσταση')
                            ->badge(),

                        TextEntry::make('assignees.name')
                            ->label('Ανάθεση σε')
                            ->badge()
                            ->placeholder('Καμία ανάθεση'),

                        TextEntry::make('created_at')
                            ->label('Δημιουργήθηκε')
                            ->dateTime('d/m/Y H:i'),
                    ]),

                Section::make('Συζήτηση')
                    ->components([
                        TicketConversation::entry(),
                    ]),
            ]);
    }
}
