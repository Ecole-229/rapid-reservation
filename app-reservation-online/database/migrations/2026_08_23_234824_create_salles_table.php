<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salles', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->unsignedBigInteger('capacite');
            $table->longText('description')->nullable();
            $table->enum('status' , ['disponible' , 'indisponible'])->default('disponible');
            $table->string('localisation');
            $table->decimal('prix' , 10 , 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salles');
    }
};
