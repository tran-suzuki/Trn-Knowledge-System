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
        Schema::create('mt_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('fk_created_by')->nullable()->constrained('mt_users');
            $table->foreignId('fk_updated_by')->nullable()->constrained('mt_users');

            $table->integer('lock_version')->default(1);
            $table->string('display_id', 8)->unique();

            $table->foreignId('fk_company_id')->constrained('mt_companies');

            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password', 255);

            $table->string('new_email', 255)->nullable()->unique();

            $table->string('email_change_token', 255)->nullable();

            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();

            $table->string('role', 20);
            $table->string('status', 20);

            $table->string('remember_token', 100)->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mt_users');
    }
};
