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
        Schema::create('relays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_site')->unique();
            $table->tinyInteger('relay_connection')->default(0);
            $table->json('relay_condition')->nullable();
            $table->json('relay_command')->nullable();
            $table->timestamp('update_from_site')->nullable();
            $table->timestamps();
            $table->foreign('id_site')->on('sites')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relays');
    }
};
