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
            $table->dateTime('date_heure_debut');
            $table->dateTime('date_heure_fin');
            $table->unsignedBigInteger('nombre_personnes');
            $table->enum('status' , ['en_attente' , 'confirmee' , 'rejetee' , 'terminee'])->default('en_attente');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('salle_id')->references('id')->on('salles')->cascadeOnDelete();
            $table->timestamp('terminee_at')->nullable();
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
