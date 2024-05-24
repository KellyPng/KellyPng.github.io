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
        Schema::create('ticket_availabilities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ticketTypeId')->unsigned();
            $table->date('date');
            $table->integer('available_quantity');
            $table->timestamps();
            $table->foreign('ticketTypeId')->references('id')->on('ticket_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_availabilities');
    }
};

