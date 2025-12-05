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
        Schema::create('mt_companies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('fk_created_by')->nullable();
            $table->foreignId('fk_updated_by')->nullable();

            $table->integer('lock_version')->default(1);
            $table->string('display_id', 8)->unique();

            $table->string('status',20)->default('active');

            $table->string('name', 255);
            $table->string('address', 255)->nullable();
            $table->string('phone_number', 20)->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mt_companies');
    }
};
