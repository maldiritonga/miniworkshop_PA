<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('nama_produk');
        });

        // Backfill existing products with a slug
        $produks = DB::table('produk')->get();
        foreach ($produks as $produk) {
            $baseSlug = Str::slug($produk->nama_produk);
            $slug = $baseSlug;
            
            // Check for uniqueness
            $counter = 1;
            while (DB::table('produk')->where('slug', $slug)->where('id_produk', '!=', $produk->id_produk)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            DB::table('produk')->where('id_produk', $produk->id_produk)->update(['slug' => $slug]);
        }

        // Now make it unique and not nullable (optional, we can just keep it nullable but unique)
        Schema::table('produk', function (Blueprint $table) {
            $table->string('slug')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
