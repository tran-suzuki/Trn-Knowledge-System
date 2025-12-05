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
        Schema::create('dt_group_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('fk_created_by')->constrained('mt_users');
            $table->foreignId('fk_user_id')->constrained('mt_users');
            $table->foreignId('fk_group_id')->constrained('mt_groups');
            $table->string('role', 20);
            $table->unique(['fk_user_id', 'fk_group_id'], 'uk_user_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_group_user');
    }
};
