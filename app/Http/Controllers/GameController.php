<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;
use App\Models\Pokemon; // Adicionado para usar o Model Pokemon

class GameController extends Controller
{
    /**
     * Mostra a tela de criação de personagem.
     */
    public function create()
    {
        return view('game.index');
    }

    /**
     * Armazena um novo personagem e seu Pokémon inicial no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:30',
            'avatar' => 'required',
            'pokemon_choice' => 'required|string|in:squirtle,bulbasaur,pikachu',
        ]);

        $character = Character::create([
            'name' => $request->name,
            'avatar' => $request->avatar,
            'gold' => 50, // Ouro inicial
            'potions' => 3,
            'pokeballs' => 5,
            'greatballs' => 0,
            'thunderstones' => 0,
        ]);
        
        // Chama a função para criar o pokémon inicial escolhido
        $this->createInitialPokemon($character, $request->pokemon_choice);

        return redirect()->route('character.tutorial', $character->id);
    }

    /**
     * Método privado para criar o Pokémon inicial com base na escolha.
     */
    private function createInitialPokemon(Character $character, string $choice)
    {
        $starters = [
            'squirtle' => [
                'name' => 'Squirtle', 'hp' => 44, 'maxHp' => 44, 'mp' => 50, 'maxMp' => 50,
                'attack' => 48, 'defense' => 65, 'sp_attack' => 50, 'sp_defense' => 64, 'speed' => 43,
                'level' => 5, 'xp' => 0, 'xpToNextLevel' => 40,
                'sprite' => 'https://i.gifer.com/origin/d8/d83e9951f28fc811c1166b16dcaec930_w200.gif'
            ],
            'bulbasaur' => [
                'name' => 'Bulbasaur', 'hp' => 45, 'maxHp' => 45, 'mp' => 50, 'maxMp' => 50,
                'attack' => 49, 'defense' => 49, 'sp_attack' => 65, 'sp_defense' => 65, 'speed' => 45,
                'level' => 5, 'xp' => 0, 'xpToNextLevel' => 40,
                'sprite' => 'https://i.gifer.com/origin/fe/fe4ebd8a9c0547e94000a9c759acf591_w200.gif'
            ],
            'pikachu' => [
                'name' => 'Pikachu', 'hp' => 35, 'maxHp' => 35, 'mp' => 50, 'maxMp' => 50,
                'attack' => 55, 'defense' => 40, 'sp_attack' => 50, 'sp_defense' => 50, 'speed' => 90,
                'level' => 5, 'xp' => 0, 'xpToNextLevel' => 40,
                'sprite' => 'https://i.pinimg.com/originals/9f/b1/25/9fb125f1fedc8cc62ab5b20699ebd87d.gif'
            ]
        ];

        if (isset($starters[$choice])) {
            $pokemonData = $starters[$choice];
            // Associa o pokémon ao personagem
            $pokemonData['character_id'] = $character->id;
            $pokemonData['isFainted'] = false;

            Pokemon::create($pokemonData);
        }
    }

    /**
     * Mostra a tela de tutorial.
     */
    public function tutorial(Character $character)
    {
        return view('game.tutorial', compact('character'));
    }

    /**
     * Método privado para carregar a view de batalha com os dados corretos.
     */
    private function showPlayView(Character $character, string $viewName)
    {
        $character->load('pokemons');
        $playerTeamJson = $character->pokemons->toJson();
        return view($viewName, [
            'character' => $character,
            'playerTeamJson' => $playerTeamJson
        ]);
    }
    
    // --- Fases do Jogo ---
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

    /**
     * Salva o progresso do personagem vindo da batalha.
     * ▼▼▼ MÉTODO TOTALMENTE CORRIGIDO ▼▼▼
     */
    public function saveProgress(Request $request, Character $character)
    {
        // Validação completa dos dados que o JavaScript envia
        $validatedData = $request->validate([
            'level' => 'required|integer',
            'hp' => 'required|numeric',
            'mp' => 'required|numeric',
            'xp' => 'required|integer',
            'maxHp' => 'required|integer', // Recebe como maxHp (camelCase) do JS
            'maxMp' => 'required|integer', // Recebe como maxMp (camelCase) do JS
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

        // 1. ATUALIZA DADOS DO PERSONAGEM (TRAINER)
        $character->gold = $validatedData['gold'];
        $character->potions = $validatedData['inventory']['potion'];
        $character->pokeballs = $validatedData['inventory']['pokeball'];
        $character->greatballs = $validatedData['inventory']['greatball'];
        $character->thunderstones = $validatedData['inventory']['thunderstone'];
        $character->save(); // Salva as alterações do personagem

        // 2. ATUALIZA DADOS DO POKÉMON ATIVO
        $mainPokemon = $character->pokemons()->first(); // Pega o primeiro pokémon da equipe (o que batalhou)
        
        if ($mainPokemon) {
            $mainPokemon->level = $validatedData['level'];
            $mainPokemon->hp = $validatedData['hp'];
            $mainPokemon->mp = $validatedData['mp'];
            $mainPokemon->xp = $validatedData['xp'];
            $mainPokemon->max_hp = $validatedData['maxHp']; // Mapeia de 'maxHp' para 'max_hp'
            $mainPokemon->max_mp = $validatedData['maxMp']; // Mapeia de 'maxMp' para 'max_mp'
            $mainPokemon->attack = $validatedData['attack'];
            $mainPokemon->defense = $validatedData['defense'];
            $mainPokemon->sp_attack = $validatedData['sp_attack'];
            $mainPokemon->sp_defense = $validatedData['sp_defense'];
            $mainPokemon->speed = $validatedData['speed'];
            $mainPokemon->save(); // Salva as alterações do pokémon
        }

        return response()->json(['success' => true, 'message' => 'Progresso salvo com sucesso!']);
    }

    /**
     * Mostra a tela da loja.
     */
    public function shop(Character $character, Request $request)
    {
        $next_stage = $request->query('next_stage', 'play');
        return view('game.shop', compact('character', 'next_stage'));
    }

    /**
     * Processa a compra de um item da loja.
     */
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
                'newStats' => $character->fresh() // Retorna os dados atualizados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a compra.'
            ], 500);
        }
    }
    
    /**
     * Atualiza o nome do personagem.
     */
    public function update(Request $request, Character $character)
    {
        $request->validate(['name' => 'required|string|max:50']);
        $character->name = $request->name;
        $character->save();
        return response()->json(['success' => true]);
    }

    /**
     * Deleta um personagem.
     */
    public function destroy(Character $character)
    {
        $character->delete();
        return response()->json(['success' => true]);
    }
}