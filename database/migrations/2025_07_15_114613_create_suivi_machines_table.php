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
        Schema::create('suivi_machines', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('machine');
            $table->unsignedBigInteger('chauffeur');
            $table->string('type_entretient');
            $table->string('piece_change')->nullable();
            $table->longText('decription_panne')->nullable();
            $table->double('kilometrage')->nullable();
            $table->string('duree_immobilisation');
            $table->string('atelier')->nullable();
            $table->longText('observation')->nullable();
            $table->string('document')->nullable();
            $table->foreign('machine')->references('immatriculation')->on('machines')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('chauffeur')->references('id')->on('chauffeurs')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suivi_machines');
    }
};
