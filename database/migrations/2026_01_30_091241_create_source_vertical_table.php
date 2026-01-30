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
        Schema::create('source_vertical', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('source_id');
            $table->ulid('vertical_id');
            $table->decimal('relevance', 3, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['source_id', 'vertical_id']);
            $table->index(['vertical_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_vertical');
    }
};
