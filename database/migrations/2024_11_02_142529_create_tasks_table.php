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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('sender_name');
            $table->string('sender_contact_no');
            $table->string('sender_address');
            $table->string('sender_city');
            $table->string('sender_location_url');
            $table->string('recipient_name');
            $table->string('recipient_contact_no');
            $table->string('recipient_address');
            $table->string('recipient_city');
            $table->string('recipient_location_url');
            $table->string('image_url')->nullable();
            $table->string('remarks')->nullable();
            $table->string('status');

            $table->foreignId('user_id')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
