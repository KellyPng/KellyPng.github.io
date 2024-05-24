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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bookID')->unsigned();
            $table->bigInteger('demoCategoryID')->unsigned();
            $table->boolean('is_local');
            $table->integer('quantity');
            $table->timestamps();
            $table->foreign('bookID')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('demoCategoryID')->references('id')->on('demo_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
