<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();

            $table->string('nom_client')->nullable()->after('user_id');
            $table->string('telephone_client')->nullable()->after('nom_client');

            $table->foreignId('cree_par_id')
                ->nullable()
                ->after('telephone_client')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['cree_par_id']);
            $table->dropColumn(['cree_par_id', 'nom_client', 'telephone_client']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
