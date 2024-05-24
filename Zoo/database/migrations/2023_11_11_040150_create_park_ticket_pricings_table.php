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
        Schema::create('park_ticket_pricings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('demoCategoryId')->unsigned();
            $table->bigInteger('parkTicketId')->unsigned();
            $table->boolean('is_local');
            $table->decimal('price');
            $table->foreign('demoCategoryId')->references('id')->on('demo_categories')->onDelete('cascade');
            $table->foreign('parkTicketId')->references('id')->on('single_park_tickets')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_ticket_pricings');
    }
};
