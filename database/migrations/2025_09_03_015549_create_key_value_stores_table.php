<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('key_value_stores', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->json('value');
            $table->timestamp('stored_at');
            $table->timestamps();

            $table->index(['key', 'stored_at']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_value_stores');
    }
};
