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
        Schema::create('dt_documents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('fk_created_by')->nullable()->constrained('mt_users');
            $table->foreignId('fk_updated_by')->nullable()->constrained('mt_users');

            $table->integer('lock_version')->default(1);
            $table->string('display_id', 8)->unique();

            $table->foreignId('fk_parent_id')->nullable()->constrained('dt_documents');
            $table->foreignId('fk_user_id')->nullable()->constrained('mt_users');
            $table->foreignId('fk_group_id')->nullable()->constrained('mt_groups');

            $table->string('type', 10); // file/folder
            $table->string('name', 255);
            $table->string('path', 255);
            $table->integer('size')->nullable();
            $table->string('mime_type', 100)->nullable();

            $table->unique(['fk_parent_id', 'fk_group_id', 'name'], 'uk_group_file');
            $table->unique(['fk_parent_id', 'fk_user_id',  'name'], 'uk_user_file');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_documents');
    }
};
