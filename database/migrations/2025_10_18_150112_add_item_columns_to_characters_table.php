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
        Schema::table('characters', function (Blueprint $table) {
            // Adiciona colunas para HP/MP máximos, se ainda não existirem
            $table->integer('max_hp')->default(100)->after('hp');
            $table->integer('max_mp')->default(50)->after('mp');

            // Adiciona colunas para os itens da loja
            $table->integer('pokeballs')->default(0)->after('potions');
            $table->integer('greatballs')->default(0)->after('pokeballs');
            $table->integer('special_items')->default(0)->after('greatballs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            // Isso permite reverter a migration, se necessário
            $table->dropColumn(['max_hp', 'max_mp', 'pokeballs', 'greatballs', 'special_items']);
        });
    }
};