<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Resources\BookingResource\Pages;


class BookingResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

//    protected static ?string $navigationGroup = 'Admin Management';

    // protected static bool $shouldRegisterNavigation = false;



    public static function getPages(): array
    {
        return [
            'index' => Pages\Booking::route('/'),
        ];
    }
}
