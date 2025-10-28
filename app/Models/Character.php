<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'hp',
        'max_hp',
        'mp',
        'max_mp',
        // ... outros campos que você tenha ...
        'potions',
        'pokeballs',
        'greatballs',
        'thunderstones',
    ];

    // ▼▼▼ COPIE E COLE A FUNÇÃO ABAIXO AQUI DENTRO ▼▼▼
    
    /**
     * Define a relação de que um Personagem (Character) pode ter muitos Pokémons.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pokemons()
    {
        return $this->hasMany(Pokemon::class);
    }
}