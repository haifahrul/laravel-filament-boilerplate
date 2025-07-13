<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('ppdbs')) {
            Schema::create('ppdbs', function (Blueprint $table) {
                $table->id();
                $table->string('full_name', 255)->index();
                $table->string('place_of_birth');
                $table->date('date_of_birth');
                $table->text('address');
                $table->string('city');
                $table->string('phone_number', 30);
                $table->string('email');
                $table->string('origin_school');
                $table->string('current_class', 50)->comment('⁠Saat ini Kelas');
                $table->string('school_year', 12)->comment('Tahun ajaran');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppdbs');
    }
};
