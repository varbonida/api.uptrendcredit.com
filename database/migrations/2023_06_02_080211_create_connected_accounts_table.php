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
		Schema::create('connected_accounts', function (Blueprint $table) {
			$table->id();
			$table->string('user_id');
			$table->string('connected_institution_id');
			$table->string('account_id')->unique();
			$table->string('mask');
			$table->string('name');
			$table->string('official_name')->nullable();
			$table->string('subtype');
			$table->string('type');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
			Schema::dropIfExists('connected_accounts');
	}
};
