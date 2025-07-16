<?php

namespace App\Exports;

use App\Models\Ppdb;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PpdbsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Ppdb::select([
            'full_name',
            'place_of_birth',
            'date_of_birth',
            'address',
            'city',
            'phone_number',
            'email',
            'origin_school',
            'current_class',
            'school_year',
            'created_at',
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'Kota',
            'No HP',
            'Email',
            'Asal Sekolah',
            'Kelas Saat Ini',
            'Tahun Ajaran',
            'Tanggal Daftar',
        ];
    }
}