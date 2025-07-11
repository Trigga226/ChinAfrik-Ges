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
        Schema::create('pointage_machines', function (Blueprint $table) {
            $table->id();
            $table->string('machine');
            $table->unsignedBigInteger('location');
            $table->unsignedBigInteger('chauffeur')->nullable();
            $table->date('date');
            $table->time('heure_sortie')->nullable();
            $table->time('heure_retour')->nullable();
            $table->boolean('ravitailler')->default(false);
            $table->double('qte_ravitailler')->nullable();
            $table->boolean('a_travailler')->default(false);
            $table->longText('observation')->nullable();
            $table->foreign('machine')->references('designation')->on('machines')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('location')->references('id')->on('location_machines')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('chauffeur')->references('id')->on('chauffeurs')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointage_machines');
    }
};
