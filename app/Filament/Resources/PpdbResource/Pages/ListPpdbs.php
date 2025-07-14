<?php

namespace App\Filament\Resources\PpdbResource\Pages;

use App\Filament\Resources\PpdbResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPpdbs extends ListRecords
{
    protected static string $resource = PpdbResource::class;
    protected static ?string $title = 'PPDB';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
