<?php

namespace App\Filament\Portal\Resources\Tickets\Tables;

use App\Enums\TicketStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('Θέμα')
                    ->searchable()
                    ->limit(60),

                TextColumn::make('status')
                    ->label('Κατάσταση')
                    ->badge(),

                TextColumn::make('updated_at')
                    ->label('Τελευταία ενημέρωση')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Κατάσταση')
                    ->options(TicketStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
