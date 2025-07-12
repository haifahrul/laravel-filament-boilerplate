<?php

namespace App\Traits;

use Filament\Notifications\Notification;
use Illuminate\Support\Str;

trait RedirectsToIndexAfterSave
{
    protected function afterCreate(): void
    {
        Notification::make()
            ->title(Str::ucfirst(static::$resource::getModelLabel()) . ' berhasil disimpan')
            ->success()
            ->send();
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title(Str::ucfirst(static::$resource::getModelLabel()) . ' berhasil diperbarui')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null; // 🔕 Matikan notif default (akan diganti di trait)
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;  // 🔕 Matikan notif default (akan diganti di trait)
    }
}
