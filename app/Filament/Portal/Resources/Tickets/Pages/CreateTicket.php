<?php

namespace App\Filament\Portal\Resources\Tickets\Pages;

use App\Enums\TicketStatus;
use App\Filament\Portal\Resources\Tickets\TicketResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = TicketStatus::Open->value;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Ανάθεση σε όλους τους admins ώστε το αίτημα να είναι ορατό εξαρχής
        // και να σταλεί email όταν δημιουργηθεί το πρώτο μήνυμα.
        $this->record->assignees()->syncWithoutDetaching(
            User::role('admin')->pluck('id')->all(),
        );

        $this->record->messages()->create([
            'user_id' => auth()->id(),
            'body' => $this->data['message'],
        ]);
    }
}
