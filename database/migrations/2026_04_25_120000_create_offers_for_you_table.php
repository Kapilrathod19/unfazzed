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
        Schema::create('offers_for_you', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('short_description_1')->nullable();
            $table->string('short_description_2')->nullable();
            $table->string('background_color')->nullable();
            $table->enum('type', ['large', 'small'])->default('small');
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers_for_you');
    }
};
