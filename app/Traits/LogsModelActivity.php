<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsModelActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Log semua atribut
            // ->logOnly($this->logAttributes ?? [])
            ->logOnlyDirty() // Log hanya jika nilai berubah
            ->dontSubmitEmptyLogs() // Hindari log kosong
            ->useLogName($this->getTable()); // Nama log berdasarkan nama tabel
    }
}
