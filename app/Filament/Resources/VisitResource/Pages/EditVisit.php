<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use App\Traits\RedirectsToIndexAfterSave;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisit extends EditRecord
{
    use RedirectsToIndexAfterSave;
    protected static string $resource = VisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
