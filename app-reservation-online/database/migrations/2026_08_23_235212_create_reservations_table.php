<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->dateTime('date_heure_debut');
            $table->dateTime('date_heure_fin');

            $table->unsignedInteger('nombre_personnes');

            $table->enum('status', [
                'en_attente',
                'confirmee',
                'rejetee',
                'terminee',
                'annulee',
            ])->default('en_attente');

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('salle_id')
                ->constrained('salles')
                ->cascadeOnDelete();

            $table->timestamp('terminee_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
