<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pokemons', function (Blueprint $table) {
            $table->id();
            
            // Chave estrangeira para conectar o Pokémon ao seu personagem/treinador.
            // Se o personagem for deletado, todos os seus pokémons também serão.
            $table->foreignId('character_id')->constrained()->onDelete('cascade');
            
            // Atributos básicos do Pokémon
            $table->string('name');
            $table->string('sprite');
            $table->integer('level')->default(5);

            // Status de batalha
            $table->integer('hp');
            $table->integer('maxHp');
            $table->integer('mp');
            $table->integer('maxMp');
            $table->integer('attack');
            $table->integer('defense');
            $table->integer('sp_attack');
            $table->integer('sp_defense');
            $table->integer('speed');

            // Progressão
            $table->integer('xp')->default(0);
            $table->integer('xpToNextLevel');
            
            // Estado
            $table->boolean('isFainted')->default(false);

            $table->timestamps(); // Cria as colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pokemons');
    }
};