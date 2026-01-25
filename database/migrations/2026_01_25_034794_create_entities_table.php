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
        Schema::create('entities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedTinyInteger('type')->default(\App\Enums\EntityType::UNCLASSIFIED->value);
            $table->unsignedTinyInteger('scraping_status')->default(\App\Enums\ScrapingStatus::PENDING->value);

            $table->text('url');

            $table->string('description', 1024)->nullable();

            // For entity type = page
            $table->string('page_type')->nullable();
            $table->string('content_type')->nullable();
            $table->string('temporal')->nullable();

            $table->unsignedInteger('snapshots_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
