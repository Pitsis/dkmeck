<?php

namespace App\Filament\Portal\Resources\Tickets\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Νέο αίτημα')
                    ->components([
                        TextInput::make('subject')
                            ->label('Θέμα')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('message')
                            ->label('Περιγραφή')
                            ->required()
                            ->rows(6)
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
