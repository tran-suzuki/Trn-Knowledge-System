<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dt_operation_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();

            $table->foreignId('fk_user_id')->nullable()->constrained('mt_users');

            $table->string('action', 100);
            $table->string('target_type', 100);
            $table->bigInteger('target_id')->nullable();

            $table->json('details')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_operation_logs');
    }
};
