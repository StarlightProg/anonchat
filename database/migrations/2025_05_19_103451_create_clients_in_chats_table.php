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
        Schema::create('clients_in_chats', function (Blueprint $table) {
            $table->string('group_id');
            $table->foreign('group_id')->references('group_id')->on('socket_groups');
            $table->foreignId('client_id')->references('id')->on('users');
            $table->string("name");
            $table->integer("age");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients_in_chats');
    }
};
