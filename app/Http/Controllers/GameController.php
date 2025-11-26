<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;
use App\Models\Pokemon;

class GameController extends Controller
{
    public function create()
    {
        return view('game.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:30',
            'avatar' => 'required',
            'pokemon_choice' => 'required|string|in:squirtle,bulbasaur,pikachu',
        ]);

        
        $character = new Character([
            'name' => $request->name,
            'avatar' => $request->avatar,
            'gold' => 200,
            'potions' => 3,
            'pokeballs' => 5,
            'greatballs' => 0,
            'thunderstones' => 0,
        ]);
        $character->save();

        // CRIA O POKÉMON INICIAL
        $this->createInitialPokemon($character, $request->pokemon_choice);

        return redirect()->route('character.tutorial', $character->id);
    }

    private function createInitialPokemon(Character $character, string $choice)
    {
        $starters = [
            'squirtle' => [
                'name' => 'Squirtle', 'hp' => 44, 'maxHp' => 44, 'mp' => 50, 'maxMp' => 50,
                'attack' => 48, 'defense' => 65, 'sp_attack' => 50, 'sp_defense' => 64, 'speed' => 43,
                'level' => 5, 'xp' => 0, 'xpToNextLevel' => 1,
                'sprite' => 'https://i.gifer.com/origin/d8/d83e9951f28fc811c1166b16dcaec930_w200.gif'
            ],
            'bulbasaur' => [
                'name' => 'Bulbasaur', 'hp' => 45, 'maxHp' => 45, 'mp' => 50, 'maxMp' => 50,
                'attack' => 49, 'defense' => 49, 'sp_attack' => 65, 'sp_defense' => 65, 'speed' => 45,
                'level' => 5, 'xp' => 0, 'xpToNextLevel' => 1,
                'sprite' => 'https://i.gifer.com/origin/fe/fe4ebd8a9c0547e94000a9c759acf591_w200.gif'
            ],
            'pikachu' => [
                'name' => 'Pikachu', 'hp' => 35, 'maxHp' => 35, 'mp' => 50, 'maxMp' => 50,
                'attack' => 55, 'defense' => 40, 'sp_attack' => 50, 'sp_defense' => 50, 'speed' => 90,
                'level' => 5, 'xp' => 0, 'xpToNextLevel' => 1,
                'sprite' => 'https://i.pinimg.com/originals/9f/b1/25/9fb125f1fedc8cc62ab5b20699ebd87d.gif'
            ]
        ];

        if (!isset($starters[$choice])) return;

        $pokemonData = $starters[$choice];
        $pokemonData['character_id'] = $character->id;
        $pokemonData['isFainted'] = false;


        $pokemon = new Pokemon($pokemonData);
        $pokemon->save();
    }

    public function tutorial(Character $character)
    {
        return view('game.tutorial', compact('character'));
    }

    private function showPlayView(Character $character, string $viewName)
    {
        $character->load('pokemons');
        $playerTeamJson = $character->pokemons->toJson();

        return view($viewName, [
            'character' => $character,
            'playerTeamJson' => $playerTeamJson
        ]);
    }

    public function play(Character $character)
    {
        return $this->showPlayView($character, 'game.play');
    }

    public function play2(Character $character)
    {
        return $this->showPlayView($character, 'game.play2');
    }

    public function play3(Character $character)
    {
        return $this->showPlayView($character, 'game.play3');
    }

    public function saveProgress(Request $request, Character $character)
    {
        $validatedData = $request->validate([
            'level' => 'required|integer',
            'hp' => 'required|numeric',
            'mp' => 'required|numeric',
            'xp' => 'required|integer',
            'maxHp' => 'required|integer',
            'maxMp' => 'required|integer',
            'attack' => 'required|integer',
            'defense' => 'required|integer',
            'sp_attack' => 'required|integer',
            'sp_defense' => 'required|integer',
            'speed' => 'required|integer',
            'gold' => 'required|integer',
            'inventory' => 'required|array',
            'inventory.potion' => 'required|integer',
            'inventory.pokeball' => 'required|integer',
            'inventory.greatball' => 'required|integer',
            'inventory.thunderstone' => 'required|integer',
        ]);

        $character->gold = $validatedData['gold'];
        $character->potions = $validatedData['inventory']['potion'];
        $character->pokeballs = $validatedData['inventory']['pokeball'];
        $character->greatballs = $validatedData['inventory']['greatball'];
        $character->thunderstones = $validatedData['inventory']['thunderstone'];
        $character->save();

        $mainPokemon = $character->pokemons()->first();

        if ($mainPokemon) {
            $mainPokemon->level = $validatedData['level'];
            $mainPokemon->hp = $validatedData['hp'];
            $mainPokemon->mp = $validatedData['mp'];
            $mainPokemon->xp = $validatedData['xp'];
            $mainPokemon->max_hp = $validatedData['maxHp'];
            $mainPokemon->max_mp = $validatedData['maxMp'];
            $mainPokemon->attack = $validatedData['attack'];
            $mainPokemon->defense = $validatedData['defense'];
            $mainPokemon->sp_attack = $validatedData['sp_attack'];
            $mainPokemon->sp_defense = $validatedData['sp_defense'];
            $mainPokemon->speed = $validatedData['speed'];
            $mainPokemon->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Progresso salvo com sucesso!'
        ]);
    }

    public function shop(Character $character, Request $request)
    {
        $next_stage = $request->query('next_stage', 'play');
        return view('game.shop', compact('character', 'next_stage'));
    }

    public function updateStats(Request $request, Character $character)
    {
        $validatedData = $request->validate([
            'gold' => 'sometimes|integer',
            'potions' => 'sometimes|integer',
            'pokeballs' => 'sometimes|integer',
            'greatballs' => 'sometimes|integer',
            'thunderstones' => 'sometimes|integer',
        ]);

        try {
            $character->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Compra realizada!',
                'newStats' => $character->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a compra.'
            ], 500);
        }
    }

    public function update(Request $request, Character $character)
    {
        $request->validate(['name' => 'required|string|max:50']);
        $character->name = $request->name;
        $character->save();

        return response()->json(['success' => true]);
    }

    public function destroy(Character $character)
    {
        $character->delete();
        return response()->json(['success' => true]);
    }
}
