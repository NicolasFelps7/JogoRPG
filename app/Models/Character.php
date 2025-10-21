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
        'level',
        'hp',
        'max_hp',
        'mp',
        'max_mp',
        'attack',
        'defense',
        'special_attack',
        'special_defense',
        'speed',
        'exp',
        'gold',
        'potions',      // <-- Necessário
        'pokeballs',    // <-- Necessário
        'greatballs',   // <-- Necessário
        'thunderstones' // <-- Necessário
    ];
}