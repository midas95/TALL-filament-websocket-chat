<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Resources\Pages\Page;

class booking extends Page
{
    protected static string $resource = BookingResource::class;

    protected static string $view = 'filament.booking';
    protected static ?string $title = '';
}
