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
        Schema::create('book_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('service_id')
                  ->constrained('services')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->string('description')->nullable();
            $table->dateTime('delivery_time');
            $table->enum('status',['pending','process','rejected','finished'])->default('pending');
            $table->unique(['customer_id','service_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_services');
    }
};
