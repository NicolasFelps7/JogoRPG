<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar as rotas web para sua aplicação.
| Essas rotas são carregadas pelo RouteServiceProvider e todas elas
| serão atribuídas ao grupo de middleware "web".
|
*/

// ROTA PRINCIPAL - Tela de seleção de personagem ou tela inicial
Route::get('/', [HomeController::class, 'index'])->name('home');


// --- GRUPO DE ROTAS DO JOGO ---
// Todas as rotas aqui dentro terão o prefixo de URL /game e o prefixo de nome 'character.'
Route::prefix('game')->name('character.')->group(function () {

    // 🧙‍♂️ Criação do personagem (ex: /game/create)
    Route::get('/create', [GameController::class, 'create'])->name('create');
    Route::post('/store', [GameController::class, 'store'])->name('store');

    // 🎓 Tutorial e Alocação de Pontos (ex: /game/tutorial/1)
    Route::get('/tutorial/{character}', [GameController::class, 'tutorial'])->name('tutorial');
    Route::get('/allocate/{character}', [GameController::class, 'allocate'])->name('allocate');
    Route::post('/allocate/{character}', [GameController::class, 'allocateStore'])->name('allocate.store');

    // ⚔️ Fases da Batalha (ex: /game/play/1)
    Route::get('/play/{character}', [GameController::class, 'play'])->name('play');
    Route::get('/play2/{character}', [GameController::class, 'play2'])->name('play2');
    Route::get('/play3/{character}', [GameController::class, 'play3'])->name('play3');

    // 🏪 Loja e Sistema de Compras
    Route::get('/shop/{character}', [GameController::class, 'shop'])->name('shop');
    
    // ✅ ROTA ADICIONADA PARA A LOJA FUNCIONAR
    // Esta é a rota que o JavaScript da loja (shop.blade.php) chama para salvar os dados.
    // O nome 'updateStats' corresponde ao que está no fetch() do JavaScript.
    Route::post('/{character}/update-stats', [GameController::class, 'updateStats'])->name('updateStats');
    
    // ❌ ROTA ANTIGA REMOVIDA PARA EVITAR CONFUSÃO
    // A rota 'shop.buy' foi removida, pois a nova rota 'updateStats' faz o mesmo trabalho.
    // Route::post('/{character}/shop/buy', [GameController::class, 'buyItem'])->name('shop.buy');
    
    // 💾 Ações do Jogo (Salvar, Editar, Deletar)
    Route::post('/save-progress/{character}', [GameController::class, 'saveProgress'])->name('saveProgress');
    Route::post('/update/{character}', [GameController::class, 'update'])->name('update');
    Route::delete('/delete/{character}', [GameController::class, 'destroy'])->name('delete');

});