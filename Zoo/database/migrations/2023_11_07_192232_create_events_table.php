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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->integer('parkId')->unsigned();
            $table->string('eventName');
            $table->string('schedule');
            $table->date('startDate');
            $table->date('endDate')->nullable();
            $table->string('startTime');
            $table->string('endTime');
            $table->text('description');
            $table->string('img_dir');
            $table->timestamps();
            $table->foreign('parkId')->references('id')->on('parks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
