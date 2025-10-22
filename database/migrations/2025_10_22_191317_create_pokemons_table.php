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
        Schema::create('pokemons', function (Blueprint $table) {
            $table->id();

            // Chave para ligar o Pokémon ao seu dono (Character)
            $table->foreignId('character_id')->constrained()->onDelete('cascade');

            $table->string('name');
            $table->integer('level')->default(5);
            $table->integer('hp');
            $table->integer('max_hp');
            $table->integer('mp')->default(30);
            $table->integer('max_mp')->default(30);
            $table->integer('attack');
            $table->integer('defense');
            $table->integer('sp_attack')->default(50);
            $table->integer('sp_defense')->default(50);
            $table->integer('speed')->default(45);
            $table->integer('xp')->default(0);
            $table->integer('xp_to_next_level')->default(100);
            $table->string('sprite_url', 2048); // 2048 para URLs longas

            $table->timestamps(); // Cria as colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokemons');
    }
};