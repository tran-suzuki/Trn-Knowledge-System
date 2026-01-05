<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::table('dt_operation_logs', function (Blueprint $table) {
			Schema::table('dt_operation_logs', function (Blueprint $table): void {
				$table->string('display_id', 8)
					->unique()
					->after('id');
			});
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::table('dt_operation_logs', function (Blueprint $table) {
			Schema::table('dt_operation_logs', function (Blueprint $table): void {
				$table->dropUnique(['display_id']);
				$table->dropColumn('display_id');
			});
		});
	}
};
