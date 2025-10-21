<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;

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
     * Armazena um novo personagem no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:30',
            'avatar' => 'required',
        ]);

        $character = Character::create([
            'name' => $request->name,
            'avatar' => $request->avatar,
            'hp' => 100, 'max_hp' => 100,
            'mp' => 50, 'max_mp' => 50,
            'attack' => 10, 'defense' => 10,
            'speed' => 10, 'special_attack' => 10, 'special_defense' => 10,
            'level' => 1, 'exp' => 0, 'gold' => 500,
            'potions' => 3,
            'pokeballs' => 0, // Valor inicial para novos itens
            'greatballs' => 0,
            'thunderstones' => 0,
        ]);

        return redirect()->route('character.tutorial', $character->id);
    }

    /**
     * Mostra a tela de tutorial.
     */
    public function tutorial(Character $character)
    {
        return view('game.tutorial', compact('character'));
    }

    /**
     * Mostra a tela de alocação de pontos.
     */
    public function allocate(Character $character)
    {
        return view('game.allocate', compact('character'));
    }

    /**
     * Armazena os atributos distribuídos.
     */
    public function allocateStore(Request $request, Character $character)
    {
        $validated = $request->validate([
            'hp' => 'required|integer|min:0',
            'mp' => 'required|integer|min:0',
            'attack' => 'required|integer|min:0',
            'defense' => 'required|integer|min:0',
            'speed' => 'required|integer|min:0',
            'special_attack' => 'required|integer|min:0',
            'special_defense' => 'required|integer|min:0',
        ]);

        $character->hp += $validated['hp'];
        $character->mp += $validated['mp'];
        $character->attack += $validated['attack'];
        $character->defense += $validated['defense'];
        $character->speed += $validated['speed'];
        $character->special_attack += $validated['special_attack'];
        $character->special_defense += $validated['special_defense'];
        
        $character->max_hp = $character->hp;
        $character->max_mp = $character->mp;
        
        $character->save();
        
        return redirect()->route('character.play', $character->id);
    }

    // --- Fases do Jogo ---
    public function play(Character $character)
    {
        return view('game.play', compact('character'));
    }

    public function play2(Character $character)
    {
        return view('game.play2', compact('character'));
    }

    public function play3(Character $character)
    {
        return view('game.play3', compact('character'));
    }

    /**
     * Salva o progresso do personagem vindo da batalha.
     */
    public function saveProgress(Request $request, Character $character)
    {
        // Valida os dados recebidos da batalha
        $validatedData = $request->validate([
            'hp' => 'integer', 'mp' => 'integer', 'attack' => 'integer', 
            'defense' => 'integer', 'special_attack' => 'integer', 
            'special_defense' => 'integer', 'speed' => 'integer', 'level' => 'integer', 
            'exp' => 'integer', 'gold' => 'integer', 'potions' => 'integer',
            'pokeballs' => 'integer', 'greatballs' => 'integer', 'thunderstones' => 'integer'
        ]);
        
        // Atualiza o personagem com os dados validados
        $character->update($validatedData);

        return response()->json(['success' => true]);
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
     * ATUALIZADO: Processa a compra de um item da loja.
     * Este é o método que a rota 'character.updateStats' chama.
     */
    public function updateStats(Request $request, Character $character)
    {
        // Valida se os dados recebidos são números inteiros.
        $validatedData = $request->validate([
            'gold' => 'sometimes|integer',
            'potions' => 'sometimes|integer',
            'pokeballs' => 'sometimes|integer',
            'greatballs' => 'sometimes|integer',
            'thunderstones' => 'sometimes|integer',
        ]);

        try {
            // O método update() usa o array $fillable do seu Model
            // para salvar apenas os campos permitidos de forma segura.
            $character->update($validatedData);

            // Retorna uma resposta de sucesso.
            return response()->json([
                'success' => true,
                'message' => 'Progresso salvo com sucesso!',
                'newStats' => $character->fresh() // Retorna os dados atualizados do personagem
            ]);

        } catch (\Exception $e) {
            // Se algo der errado, retorna um erro 500.
            // Verifique o arquivo `storage/logs/laravel.log` para detalhes.
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro no servidor ao salvar os dados.'
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
