<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_pivot_friends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_user');
            $table->unsignedBigInteger('friend_id');
            $table->string('status');
            $table->timestamps();

            $table->foreign('from_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('friend_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_pivot_friends');
    }
};
