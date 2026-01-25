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
        Schema::create('source_entity_counts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('source_id');
            $table->unsignedTinyInteger('entity_type');
            $table->unsignedBigInteger('count')->default(0);
            $table->timestamps();

            $table->unique(['source_id', 'entity_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_entity_counts');
    }
};
