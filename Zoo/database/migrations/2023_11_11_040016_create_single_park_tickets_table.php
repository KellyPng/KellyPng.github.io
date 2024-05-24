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
        Schema::create('single_park_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('parkId')->unsigned();
            $table->integer('remaining_quantity')->default(0);
            $table->integer('capacity')->default(0);
            $table->date('validfrom')->nullable();
            $table->date('validtill')->nullable();
            $table->foreign('parkId')->references('id')->on('parks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('single_park_tickets');
    }
};
