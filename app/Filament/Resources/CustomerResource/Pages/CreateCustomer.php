<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Traits\RedirectsToIndexAfterSave;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    use RedirectsToIndexAfterSave;
    protected static string $resource = CustomerResource::class;
}
