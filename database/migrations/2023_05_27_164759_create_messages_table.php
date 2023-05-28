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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id');
            $table->foreignId('user_id')->comment('The user who send de message');
            $table->text('message')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->string('attachment_name', 255)->nullable();
            $table->string('attachment_size', 255)->nullable();
            $table->string('attachment_extension', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
