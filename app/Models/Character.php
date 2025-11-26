<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'gold',
        'potions',
        'pokeballs',
        'greatballs',
        'thunderstones'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function pokemons()
    {
        return $this->hasMany(Pokemon::class);
    }
}
