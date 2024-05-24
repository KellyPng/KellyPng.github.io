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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('bookingID');
            $table->bigInteger('visitorID')->unsigned();
            $table->bigInteger('ticketTypeID')->unsigned();
            $table->date('bookingDate');
            $table->date('visitDate');
            $table->decimal('totalPrice');
            $table->boolean('bookingStatus');
            $table->timestamps();
            $table->foreign('visitorID')->references('id')->on('visitors')->onDelete('cascade');
            $table->foreign('ticketTypeID')->references('id')->on('ticket_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
