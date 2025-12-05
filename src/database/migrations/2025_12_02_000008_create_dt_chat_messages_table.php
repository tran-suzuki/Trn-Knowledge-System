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
        Schema::create('dt_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('fk_session_id')->constrained('dt_chat_sessions');

            $table->string('role', 10);
            $table->text('content');
            $table->json('metadata')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_chat_messages');
    }
};
