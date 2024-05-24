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
        Schema::create('pricings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ticketTypeId')->unsigned();
            $table->bigInteger('demoCategoryId')->unsigned();
            $table->boolean('is_local');
            $table->decimal('price');
            $table->timestamps();
            $table->foreign('ticketTypeId')->references('id')->on('ticket_types')->onDelete('cascade');
            $table->foreign('demoCategoryId')->references('id')->on('demo_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricings');
    }
};
