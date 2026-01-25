<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('snapshots', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('entity_id')->index();
            $table->unsignedTinyInteger('scraping_status')
                ->default(\App\Enums\ScrapingStatus::PENDING->value);
            $table->string('file_path')->nullable()->index();
            $table->unsignedInteger('version')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snapshots');
    }
};
