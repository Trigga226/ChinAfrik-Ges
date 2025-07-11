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
        Schema::create('dossier_postulants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('postulant_id');
            $table->string('nom_complet');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('photo')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('numero_passeport')->nullable()->unique();
            $table->string('scann_passeport')->nullable()->unique();
            $table->date('date_delivrance_passport')->nullable();
            $table->date('date_expiration_passport')->nullable();
            $table->string('numero_cnib')->nullable()->unique();
            $table->string('scann_cnib')->nullable()->unique();
            $table->date('date_delivrance_cnib')->nullable();
            $table->date('date_expiration_cnib')->nullable();
            $table->string('pays')->nullable();
            $table->string('ville')->nullable();
            $table->string('secteur')->nullable();
            $table->json('documents')->nullable();
            $table->boolean('complet')->default(false);
            $table->json('etapes')->nullable();
            $table->string('etat')->nullable();
            $table->string('bourse')->nullable();
            $table->string('type')->nullable();
            $table->foreign('postulant_id')->references('id')->on('postulants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('bourse')->references('titre')->on('bourses')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_postulants');
    }
};
