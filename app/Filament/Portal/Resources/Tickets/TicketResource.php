<?php

namespace App\Filament\Portal\Resources\Tickets;

use App\Filament\Portal\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Portal\Resources\Tickets\Pages\ListTickets;
use App\Filament\Portal\Resources\Tickets\Pages\ViewTicket;
use App\Filament\Portal\Resources\Tickets\Schemas\TicketForm;
use App\Filament\Portal\Resources\Tickets\Schemas\TicketInfolist;
use App\Filament\Portal\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'subject';

    protected static ?string $navigationLabel = 'Τα αιτήματά μου';

    protected static ?string $modelLabel = 'Αίτημα';

    protected static ?string $pluralModelLabel = 'Τα αιτήματά μου';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view' => ViewTicket::route('/{record}'),
        ];
    }
}
