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
        Schema::create('book_parks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bookingID')->unsigned();
            $table->Integer('parkID')->unsigned();
            $table->timestamps();
            $table->foreign('bookingID')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('parkID')->references('id')->on('parks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_parks');
    }
};
