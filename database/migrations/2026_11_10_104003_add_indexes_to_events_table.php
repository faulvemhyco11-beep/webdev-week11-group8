<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->fullText('description');
        });

        // The schema builder can't create these, so we use raw SQL:
        DB::statement("CREATE INDEX events_details_theme_idx ON events ((details->>'theme'))");
        DB::statement("CREATE INDEX events_details_gin_idx ON events USING GIN (details)");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS events_details_gin_idx');
        DB::statement('DROP INDEX IF EXISTS events_details_theme_idx');

        Schema::table('events', function (Blueprint $table) {
            $table->dropFullText(['description']);
        });
    }
};