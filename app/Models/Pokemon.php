<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

    protected $table = 'pokemons';

    protected $fillable = [
        'character_id', 'name', 'level', 'hp', 'maxHp', 'mp', 'maxMp',
        'attack', 'defense', 'sp_attack', 'sp_defense', 'speed',
        'xp', 'xpToNextLevel', 'sprite', 'isFainted',
    ];

    // CONSTRUTOR
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
