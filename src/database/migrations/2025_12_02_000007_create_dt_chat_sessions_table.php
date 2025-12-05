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
        Schema::create('dt_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('fk_created_by')->nullable()->constrained('mt_users');
            $table->foreignId('fk_updated_by')->nullable()->constrained('mt_users');

            $table->integer('lock_version')->default(1);
            $table->string('display_id', 8)->unique();

            $table->foreignId('fk_user_id')->constrained('mt_users');
            $table->foreignId('fk_company_id')->constrained('mt_companies');
            $table->foreignId('fk_group_id')->nullable()->constrained('mt_groups');

            $table->string('title', 255)->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_chat_sessions');
    }
};
