<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Enums\TicketStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->limit(50),

                TextColumn::make('customer.name')
                    ->label('Πελάτης')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Κατάσταση')
                    ->badge(),

                TextColumn::make('assignees.name')
                    ->label('Ανατεθειμένο σε')
                    ->badge()
                    ->placeholder('—'),

                TextColumn::make('updated_at')
                    ->label('Ενημερώθηκε')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Κατάσταση')
                    ->options(TicketStatus::class),

                Filter::make('assigned_to_me')
                    ->label('Ανατεθειμένα σε εμένα')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereHas(
                        'assignees',
                        fn (Builder $q) => $q->whereKey(auth()->id()),
                    )),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
