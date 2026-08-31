<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {

            $table->foreignId('user_id')
                ->nullable()
                ->change();

            $table->foreignId('cree_par_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {

            $table->dropForeign(['cree_par_id']);
            $table->dropColumn('cree_par_id');

            $table->foreignId('user_id')
                ->nullable(false)
                ->change();
        });
    }
};