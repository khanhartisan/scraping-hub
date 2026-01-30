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
        Schema::create('entity_counts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('countable_type');
            $table->ulid('countable_id');
            $table->unsignedTinyInteger('entity_type');
            $table->unsignedTinyInteger('scraping_status')->default(\App\Enums\ScrapingStatus::PENDING->value);
            $table->unsignedBigInteger('count')->default(0);
            $table->timestamps();

            $table->unique(['countable_type', 'countable_id', 'entity_type', 'scraping_status'], 'countable_entity_scraping_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_counts');
    }
};
