<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Resources\ChatResource\Pages;


class ChatResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

//    protected static ?string $navigationGroup = 'Admin Management';

    // protected static bool $shouldRegisterNavigation = false;



    public static function getPages(): array
    {
        return [
            'index' => Pages\Chat::route('/'),
        ];
    }
}
