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
        Schema::create('camions', function (Blueprint $table) {
            $table->id();
            $table->string('designation')->unique();
            $table->string('immatriculation')->unique();
            $table->string('categorie');
            $table->string('marque');
            $table->date('date_mise_en_service')->nullable();
            $table->string('status')->default("disponible");
            $table->double('cout')->default(0);
            $table->longText('observation')->nullable();
            $table->foreign('categorie')->references('designation')->on('categorie_camions')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camions');
    }
};
