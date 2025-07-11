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
        Schema::create('location_machines', function (Blueprint $table) {
            $table->id();
            $table->string('client');
            $table->date('date');
            $table->double('remise');
            $table->double('total_a_percevoir');
            $table->string('statut');
            $table->json('details');
            $table->foreign('client')->references('designation')->on('clients')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_machines');
    }
};
