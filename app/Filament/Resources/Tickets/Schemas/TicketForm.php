<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Enums\TicketStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Στοιχεία αιτήματος')
                    ->columns(2)
                    ->components([
                        TextInput::make('subject')
                            ->label('Θέμα')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('user_id')
                            ->label('Πελάτης')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label('Κατάσταση')
                            ->options(TicketStatus::class)
                            ->default(TicketStatus::Open->value)
                            ->required(),

                        Select::make('assignees')
                            ->label('Ανάθεση σε')
                            ->relationship(
                                name: 'assignees',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas(
                                    'roles',
                                    fn (Builder $q) => $q->whereIn('name', ['admin', 'agent']),
                                ),
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
