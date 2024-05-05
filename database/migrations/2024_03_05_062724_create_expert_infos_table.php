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
        Schema::create('expert_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('mobile')->unique()->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->integer('rating')->default(0);
            $table->string('description')->nullable();
            $table->string('certificate')->nullable();
            $table->string('working_hours')->default('3 hours');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_infos');
    }
};
