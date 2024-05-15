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
		Schema::create('payments', function (Blueprint $table) {
			$table->id();
			$table->foreignId('book_service_id')
				->constrained('book_services')
				->cascadeOnDelete()
				->cascadeOnUpdate();
			$table->foreignId('payment_expert_id')
				->constrained('users')
				->cascadeOnDelete()
				->cascadeOnUpdate();
			$table->string('operation_number')->unique();
            $table->enum('status',['pandding','success','failed'])->default('pandding');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('payments');
	}
};
