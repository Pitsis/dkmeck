<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TicketStatus: string implements HasColor, HasLabel
{
    case Open = 'open';
    case Pending = 'pending';
    case Answered = 'answered';
    case Closed = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Open => 'Ανοιχτό',
            self::Pending => 'Σε εκκρεμότητα',
            self::Answered => 'Απαντήθηκε',
            self::Closed => 'Κλειστό',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Open => 'info',
            self::Pending => 'warning',
            self::Answered => 'success',
            self::Closed => 'gray',
        };
    }
}
