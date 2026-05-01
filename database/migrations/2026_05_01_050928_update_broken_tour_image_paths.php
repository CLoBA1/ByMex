<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('tours')
            ->whereNotNull('image')
            ->where('image', 'like', 'storage/%')
            ->update([
                'image' => DB::raw("REPLACE(image, 'storage/', 'tours/')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tours')
            ->whereNotNull('image')
            ->where('image', 'like', 'tours/%')
            ->update([
                'image' => DB::raw("REPLACE(image, 'tours/', 'storage/')")
            ]);
    }
};
