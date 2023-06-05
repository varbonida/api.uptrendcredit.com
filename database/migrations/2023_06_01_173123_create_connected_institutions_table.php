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
			Schema::create('connected_institutions', function (Blueprint $table) {
				$table->id();
				$table->string('user_id');
				$table->string('access_token')->unique();
				$table->string('item_id');
				$table->string('institution_id');
				$table->string('institution_name');
				$table->timestamps();
			});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connected_institutions');
    }
};
