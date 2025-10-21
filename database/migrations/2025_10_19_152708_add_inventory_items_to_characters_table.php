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
        // Verifica se a tabela 'characters' realmente existe antes de tentar alterá-la
        if (Schema::hasTable('characters')) {
            Schema::table('characters', function (Blueprint $table) {
                
                // Verifica se a coluna 'potions' NÃO existe antes de adicioná-la
                if (!Schema::hasColumn('characters', 'potions')) {
                    // Tenta adicionar depois de 'gold', se 'gold' existir
                    $afterColumn = Schema::hasColumn('characters', 'gold') ? 'gold' : 'exp';
                    $table->integer('potions')->default(0)->after($afterColumn);
                }

                // Verifica se a coluna 'pokeballs' NÃO existe antes de adicioná-la
                if (!Schema::hasColumn('characters', 'pokeballs')) {
                    $table->integer('pokeballs')->default(0)->after('potions');
                }

                // Verifica se a coluna 'greatballs' NÃO existe antes de adicioná-la
                if (!Schema::hasColumn('characters', 'greatballs')) {
                    $table->integer('greatballs')->default(0)->after('pokeballs');
                }

                // Verifica se a coluna 'thunderstones' NÃO existe antes de adicioná-la
                if (!Schema::hasColumn('characters', 'thunderstones')) {
                    $table->integer('thunderstones')->default(0)->after('greatballs');
                }

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Verifica se a tabela 'characters' existe antes de tentar reverter
        if (Schema::hasTable('characters')) {
            Schema::table('characters', function (Blueprint $table) {
                // Lista de colunas para remover
                $columns = ['potions', 'pokeballs', 'greatballs', 'thunderstones'];
                
                // Verifica cada coluna antes de tentar removê-la para evitar erros
                foreach ($columns as $column) {
                    if (Schema::hasColumn('characters', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};