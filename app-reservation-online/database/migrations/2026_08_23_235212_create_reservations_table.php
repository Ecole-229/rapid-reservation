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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_heure_reservation_debut');
            $table->date('date_fin_reservation');
            $table->time('durer_reservation');
            $table->integer('nombre_personne');
            $table->enum('status' , ['attente' , 'confrimer' , 'rejecter' , 'terminer'])->default('attente');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('salle_id')->references('id')->on('salles')->cascadeOnDelete();
            $table->timestamp('terminer_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
