<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

    /**
     * Adicione esta linha para garantir que o Laravel use o nome de tabela correto.
     */
    protected $table = 'pokemons';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'character_id', 'name', 'level', 'hp', 'maxHp', 'mp', 'maxMp',
        'attack', 'defense', 'sp_attack', 'sp_defense', 'speed',
        'xp', 'xpToNextLevel', 'sprite', 'isFainted',
    ];

    /**
     * Get the character that owns the pokémon.
     */
    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}