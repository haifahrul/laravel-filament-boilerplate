<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use App\Traits\RedirectsToIndexAfterSave;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVisit extends CreateRecord
{
    use RedirectsToIndexAfterSave;
    protected static string $resource = VisitResource::class;
}
