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
        Schema::table('mt_companies', function (Blueprint $table) {
            Schema::table('mt_companies', function (Blueprint $table): void {
                
                $table->foreign('fk_created_by')
                    ->references('id')
                    ->on('mt_users');   

                $table->foreign('fk_updated_by')
                    ->references('id')
                    ->on('mt_users');  
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mt_companies', function (Blueprint $table) {
            $table->dropForeign(['fk_created_by']);
            $table->dropForeign(['fk_updated_by']);
        });
    }
};
