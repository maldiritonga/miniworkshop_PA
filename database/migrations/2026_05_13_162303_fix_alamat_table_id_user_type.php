<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The alamat table already has the correct BIGINT UNSIGNED foreign key from 2026_05_13_162047.
        // This migration is now a no-op since the original migration was fixed.
    }

    public function down(): void
    {
        Schema::table('alamat', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        DB::statement('ALTER TABLE `alamat` MODIFY `id_user` BIGINT UNSIGNED NOT NULL');
    }
};
