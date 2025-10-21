<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Batalha Final | {{ $character->name }}</title>
<link rel="icon" href="{{ asset('img/logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

<style>
    /* Estilos gerais, intro, etc. */
    :root { --bg-dark: #1a1c2c; --ui-main: #5a3a2b; --ui-border-light: #a18c7c; --ui-border-dark: #3f2a1f; --text-light: #ffffff; --text-highlight: #94e5ff; --hp-color: #70d870; --mp-color: #1e88e5; --xp-color: #fdd835; --dialog-bg: #e0e0e0; --dialog-border: #606060; --dialog-text: #303030; }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Press Start 2P', cursive; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("https://i.pinimg.com/originals/70/59/28/705928d396944cff05417cfe7ea07f2f.gif") no-repeat center center fixed; background-size: cover; background-blend-mode: multiply; min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 10px; color: var(--text-light); image-rendering: pixelated; overflow: hidden; position: relative; }
    #story-intro { position: fixed; inset: 0; background-color: #000; color: #fff; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 200; padding: 20px; opacity: 1; transition: none; overflow: hidden; }
    #story-intro.hide-intro-animation { animation: hide-intro-up 1.5s forwards ease-in-out; }
    @keyframes hide-intro-up { 0% { transform: translateY(0); opacity: 1; } 50% { opacity: 0.5; } 100% { transform: translateY(-100%); opacity: 0; visibility: hidden; } }
    #story-text { font-size: 1.5rem; line-height: 1.8; max-width: 800px; text-align: center; text-shadow: 2px 2px #000; z-index: 10; background: rgba(0,0,0,0.4); padding: 10px 20px; border-radius: 5px; }
    #stage-title { font-size: 3rem; color: var(--text-highlight); margin-top: 40px; opacity: 0; transform: scale(0.5); text-shadow: 4px 4px #000; z-index: 10; }
    #stage-title.visible { animation: stage-intro 1.5s forwards; }
    @keyframes stage-intro { from { opacity: 0; transform: scale(0.5); } to { opacity: 1; transform: scale(1); } }
    .battle-screen { visibility: hidden; opacity: 0; width: 100%; max-width: 1024px; height: 768px; position: relative; transition: opacity 1s; z-index: 2; background-color: transparent; border: none; overflow: hidden; }
    .battle-screen.visible { visibility: visible; opacity: 1; }
    .battle-arena { position: absolute; top: 0; left: 0; width: 100%; height: calc(100% - 200px); }
    .battle-monster-sprite { width: 250px; height: 250px; object-fit: contain; }
    #player-area { position: absolute; bottom: 40px; left: 10%; display: flex; flex-direction: column; align-items: center; gap: 5px; z-index: 5; }
    #enemy-area { position: absolute; top: 80px; right: 10%; display: flex; flex-direction: column; align-items: center; gap: 5px; z-index: 5; }
    .status-box-pokemon { background: var(--dialog-bg); border: 2px solid var(--dialog-border); box-shadow: 2px 2px 0px rgba(0,0,0,0.3); padding: 8px 12px; color: var(--dialog-text); font-size: 0.8rem; z-index: 10; min-width: 220px; text-align: left; }
    .status-box-pokemon h2 { font-size: 0.9rem; margin-bottom: 3px; color: var(--dialog-text); text-shadow: none; }
    .hp-bar-container { width: 100%; height: 10px; background-color: #505050; border: 1px solid var(--dialog-border); position: relative; margin-top: 3px; margin-bottom: 3px; border-radius: 5px; overflow: hidden; }
    .hp-bar-fill { height: 100%; background: var(--hp-color); transition: width 0.5s ease-out; }
    .hp-bar-text { position: absolute; inset: 0; font-size: 0.6rem; color: white; text-shadow: 1px 1px #000; line-height: 8px; text-align: center; }
    .hp-bar-fill.mp { background: var(--mp-color); }
    .hp-bar-fill.xp { background: var(--xp-color); }
    .dialog-box { position: absolute; bottom: 10px; left: 10px; width: calc(100% - 20px); height: 180px; display: flex; background: transparent; border: none; padding: 0; }
    .battle-log { flex: 1; height: 100%; background-color: #2E4C7A; color: white; padding: 20px; font-size: 1.1rem; line-height: 1.5; border: 4px solid #5A6A83; box-shadow: inset 0 0 0 4px #A5AFC1; border-radius: 12px; margin-right: -10px; z-index: 2; }
    .actions-menu { width: 300px; height: 100%; background-color: #F8F8F8; border: 4px solid #5A6A83; box-shadow: inset 0 0 0 4px #A5AFC1; border-radius: 12px; padding: 20px; z-index: 1; }
    .actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px 25px; height: 100%; width: 100%; align-content: center; color: #333; }
    .action-item { font-size: 1.1rem; cursor: pointer; position: relative; }
    .action-item:hover { color: #000; }
    .action-item.active::before { content: '►'; font-size: 1rem; color: #333; position: absolute; left: -20px; top: 2px; }
    .modal-overlay { position: fixed; inset: 0; z-index: 102; display: flex; justify-content: center; align-items: center; background: rgba(0,0,0,0.8); opacity: 0; visibility: hidden; transition: all 0.3s ease; }
    .modal-overlay.is-visible { opacity: 1; visibility: visible; }
    .modal-box { background: var(--ui-main); border: 4px solid var(--ui-border-dark); box-shadow: inset 0 0 0 4px var(--ui-border-light); padding: 30px; max-width: 600px; width: 90%; color: var(--text-light); transform: scale(0.9); transition: transform 0.3s ease; text-align: center;}
    .modal-overlay.is-visible .modal-box { transform: scale(1); }
    .modal-box .btn { display: inline-block; padding: 10px 20px; background: var(--ui-border-dark); color: var(--text-light); text-decoration: none; margin-top: 20px; border: 2px solid var(--ui-border-light); font-family: 'Press Start 2P', cursive; }
    .hidden { opacity: 0; visibility: hidden; transition: opacity 0.5s ease, visibility 0s linear 0.5s; } /* Delay visibility change */
    .visible { opacity: 1; visibility: visible; transition: opacity 0.5s ease; }

    /* Estilos da Bolsa (Bag) */
    #bag-screen { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 700px; height: 480px; background: none; display: none; z-index: 100; pointer-events: all; }
    .bag-container { display: flex; width: 100%; height: 100%; }
    .bag-left-panel { flex-basis: 35%; height: 100%; background-color: #E0E0E0; border: 2px solid #606060; border-right: none; border-bottom: none; display: flex; flex-direction: column; justify-content: center; align-items: center; border-radius: 8px 0 0 0; }
    .bag-left-panel img { width: 180px; height: auto; image-rendering: pixelated; }
    .bag-right-panel { flex-basis: 65%; height: 100%; display: flex; flex-direction: column; }
    .bag-item-list-container { height: 65%; background-color: #E0E0E0; border: 2px solid #606060; border-left: none; border-bottom: none; border-radius: 0 8px 0 0; padding: 10px; overflow-y: auto; color: #333; font-size: 1rem; display: flex; flex-direction: column; }
    .bag-item { display: flex; justify-content: space-between; padding: 5px 8px; cursor: pointer; position: relative; }
    .bag-item:hover, .bag-item.active { background-color: #A0A0A0; }
    .bag-item.active::before { content: '►'; font-size: 0.8rem; position: absolute; left: -15px; top: 50%; transform: translateY(-50%); }
    #bag-cancel-button { padding: 5px 8px; cursor: pointer; margin-top: auto; }
    #bag-cancel-button:hover, #bag-cancel-button.active { background-color: #A0A0A0; }
    #bag-cancel-button.active::before { content: '►'; font-size: 0.8rem; position: absolute; left: -15px; top: 50%; transform: translateY(-50%); }
    .bag-item-description-container { height: 35%; background-color: #2E4C7A; color: white; padding: 15px; font-size: 0.9rem; line-height: 1.4; border: 2px solid #606060; border-left: none; border-top: none; border-radius: 0 0 8px 0; }
    
    /* Estilos da Tela de Equipe */
    #team-screen { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 640px; display: none; z-index: 100; pointer-events: all; background-color: #55775a; padding: 12px; border: 4px solid #334435; border-radius: 8px; }
    .team-layout { display: flex; gap: 12px; }
    .team-selected-pokemon-panel { width: 45%; }
    .team-list-panel { width: 55%; display: flex; flex-direction: column; gap: 8px; }
    .pokemon-card { background-color: #f8f8f8; border: 4px solid #9ca4a5; border-radius: 8px; padding: 8px; display: flex; align-items: center; color: #333; position: relative; cursor: pointer; }
    .pokemon-card.selected { border-color: #e56a11; background: linear-gradient(to right, #fdd5b6, #f8f8f8 30%); }
    .pokemon-card.fainted { background-color: #c0c0c0; color: #888; cursor: not-allowed; }
    .sprite-large { width: 80px; height: 80px; margin-right: 10px; }
    .sprite-small { width: 50px; height: 50px; margin-right: 10px; }
    .team-info { flex-grow: 1; }
    .team-name-level { display: flex; justify-content: space-between; margin-bottom: 4px; }
    .team-hp-container { display: flex; align-items: center; margin-bottom: 2px; }
    .team-hp-label { background-color: #daac20; color: white; font-size: 0.7em; padding: 1px 3px; border-radius: 4px; margin-right: 4px; }
    .team-hp-bar-background { flex-grow: 1; background-color: #555; border-radius: 5px; height: 8px; padding: 2px; border: 1px solid #333; overflow: hidden; }
    .team-hp-bar-foreground { background-color: #30a850; height: 100%; border-radius: 3px; }
    .team-hp-values { text-align: right; font-size: 0.9em; }
    .team-dialog-box { background-color: #f8f8f8; border: 4px solid #9ca4a5; border-radius: 8px; padding: 12px; margin-top: 12px; color: #333; position: relative; min-height: 50px; display: flex; align-items: center; justify-content: space-between; }
    #team-dialog-buttons { display: flex; gap: 10px; }
    .team-action-btn { font-family: 'Press Start 2P', cursive; background-color: #dcdcdc; border: 2px solid #aaa; border-radius: 10px; padding: 5px 10px; font-size: 0.8em; cursor: pointer; }
    .team-action-btn.desistir { background-color: #e57373; border-color: #d32f2f; color: white; }
    .back-button { font-family: 'Press Start 2P', cursive; background-color: #dcdcdc; border: 2px solid #aaa; border-radius: 10px; padding: 5px 10px; font-size: 0.8em; cursor: pointer; }
    
    .attack-effect { position: absolute; z-index: 100; pointer-events: none; width: 150px; height: 150px; animation: fadeOutEffect 0.5s forwards; }
    @keyframes fadeOutEffect { from { opacity: 1; transform: scale(1); } to { opacity: 0; transform: scale(1.2); } }

    /* ===== ANIMAÇÃO DE DANO (Estilo Clássico Aprimorado) ===== */
    .battle-monster-sprite.take-damage {
        animation: take-hit-classic 0.4s steps(2, end) infinite;
    }
    @keyframes take-hit-classic {
      0%, 100% { opacity: 1; transform: translateX(0); }
      50% { opacity: 0.2; transform: translateX(6px); }
    }

    /* ===== VENTO DE BATALHA ===== */
    .wind-particle {
        position: absolute;
        background-color: rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        opacity: 0;
        animation: wind-flow linear infinite;
        z-index: 1;
        pointer-events: none;
    }
    @keyframes wind-flow {
        0% { transform: translateX(-100px) translateY(0px) rotate(0deg); opacity: 0; }
        20%, 80% { opacity: 0.7; }
        100% { transform: translateX(1124px) translateY(40px) rotate(720deg); opacity: 0; }
    }

    /* ===== ESTILOS PARA VITÓRIA E CRÉDITOS (REVISÃO FINAL) ===== */
    #victory-sequence .victory-content { text-align: center; }
    #victory-chest { width: 256px; height: 256px; }
    #victory-reward-text { color: white; font-size: 1.5rem; margin-top: 20px; text-shadow: 2px 2px #000; }
    
    #credits-sequence { 
        display: block; /* Garante que não use flex */
        align-items: initial;
        justify-content: initial;
        background-color: #000; 
        color: #fff; 
        text-align: center; 
        overflow: hidden; 
    }
    .credits-content {
        position: absolute;
        width: 90%; /* Ajustado para melhor responsividade */
        max-width: 800px;
        left: 50%;
        transform: translateX(-50%); /* Apenas centraliza horizontalmente */
        /* Animação controla a posição vertical */
        animation: scroll-credits 50s linear forwards;
    }

    @keyframes scroll-credits {
        from { top: 100vh; } /* Começa abaixo da tela */
        to { top: -250vh; } /* Termina bem acima (ajuste se necessário) */
    }
    .credits-content h1 { font-size: 2.5rem; margin-bottom: 40px; color: var(--xp-color); }
    .credits-content h2 { font-size: 1.5rem; margin-top: 30px; margin-bottom: 10px; color: var(--text-highlight); }
    .credits-content p { font-size: 1.2rem; line-height: 1.6; }
    
    #end-game-menu {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        transition: opacity 1s ease 0.5s; /* Adiciona delay para garantir que apareça depois */
    }
    #end-game-menu h1 {
        font-size: 3rem;
        color: var(--xp-color);
        margin-bottom: 20px;
    }

</style>
</head>
<body>

<div id="story-intro">
    <p id="story-text"></p>
    <h1 id="stage-title">FASE FINAL</h1>
</div>

<div class="battle-screen">
    <div class="battle-arena">
        <div id="player-area"><img src="{{ asset($character->avatar) }}" id="player-battle-monster-sprite" class="battle-monster-sprite" alt="Sprite do Monstro do Jogador"><div id="player-battle-status" class="status-box-pokemon"><h2 id="playerName"></h2><div class="hp-bar-container"><div class="hp-bar-fill" id="playerHpBar"></div><div class="hp-bar-text" id="playerHpText"></div></div><div class="hp-bar-container"><div class="hp-bar-fill mp" id="playerMpBar"></div><div class="hp-bar-text" id="playerMpText"></div></div><div class="hp-bar-container"><div class="hp-bar-fill xp" id="playerXpBar"></div><div class="hp-bar-text" id="playerXpText"></div></div></div></div>
        <div id="enemy-area"><img src="" id="enemy-monster-sprite" class="battle-monster-sprite" alt="Sprite do Monstro Inimigo"><div id="enemy-battle-status" class="status-box-pokemon"><h2 id="enemyName"></h2><div class="hp-bar-container"><div class="hp-bar-fill" id="enemyHpBar"></div><div class="hp-bar-text" id="enemyHpText"></div></div></div></div>
    </div>
    
    <div id="bag-screen">
        <div class="bag-container">
            <div class="bag-left-panel"><img src="{{ asset('img/nicolas.png') }}" alt="Bolsa"></div>
            <div class="bag-right-panel">
                <div class="bag-item-list-container" id="bag-item-list"></div>
                <div class="bag-item-description-container" id="bag-item-description"><p>Selecione um item.</p></div>
            </div>
        </div>
    </div>
    
    <div id="team-screen">
        <div class="team-layout">
            <div id="team-selected-pokemon-panel" class="team-selected-pokemon-panel"></div>
            <div id="team-list-panel" class="team-list-panel"></div>
        </div>
        <div class="team-dialog-box">
            <p id="team-dialog-text">Escolha um Pokémon.</p>
            <div id="team-dialog-buttons"></div>
        </div>
    </div>

    <div class="dialog-box">
        <div class="battle-log" id="battleLog"><p></p></div>
        <div class="actions-menu"><div class="actions-grid" id="actionsGrid"></div></div>
    </div>
</div>
<div class="modal-overlay" id="endgameModal"></div>

<div id="victory-sequence" class="modal-overlay">
    <div class="victory-content">
        <img id="victory-chest" src="https://i.gifer.com/7H6T.gif" alt="Baú do Tesouro Abrindo">
        <p id="victory-reward-text" class="hidden">Você ganhou 2x Pedra do Trovão!</p>
    </div>
</div>

<div id="credits-sequence" class="modal-overlay">
    <div class="credits-content">
        <h1>JOGO RPG</h1>
        <h2>- A JORNADA FINAL -</h2>
        <div style="margin-top: 50px;">
            <h2>Designer</h2>
            <p>nicolas</p>
        </div>
        <div>
            <h2>Desenvolvedores</h2>
            <p>nicolas</p>
            <p>victor</p>
            <p>pietro</p>
        </div>
        <div>
            <h2>Colaboradores</h2>
            <p>Neil</p>
        </div>
        <div>
            <h2>Agradecimentos Especiais</h2>
            <p>Miguel</p>
            <p>O Professor</p>
            <p>ChatGPT</p>
            <p>Gemini</p>
        </div>
        <div>
            <h2>Assets e Inspiração</h2>
            <p>Pokémon (Nintendo)</p>
            <p>Final Fantasy (Square Enix)</p>
        </div>
        <div style="margin-top: 80px;">
            <p>Obrigado por jogar!</p>
        </div>
    </div>
    <div id="end-game-menu" class="hidden">
        <h1>FIM</h1>
        <a href="{{ route('home') }}" class="btn">VOLTAR AO INÍCIO</a>
   </div>
</div>


<script>
const Intro = {
    storyContainer: document.getElementById('story-intro'), storyTextEl: document.getElementById('story-text'), stageTitleEl: document.getElementById('stage-title'), battleScreenEl: document.querySelector('.battle-screen'), typewriter(text, i = 0) { if (i < text.length) { this.storyTextEl.innerHTML += text.charAt(i); setTimeout(() => this.typewriter(text, i + 1), 50); } else { setTimeout(() => this.showStageTitle(), 2000); } }, showStageTitle() { this.stageTitleEl.classList.add('visible'); setTimeout(() => this.hideIntro(), 2500); }, hideIntro() { this.storyContainer.classList.add('hide-intro-animation'); this.storyContainer.addEventListener('animationend', () => { this.storyContainer.remove(); this.showBattleScreen(); }, { once: true }); }, showBattleScreen() { this.battleScreenEl.classList.add('visible'); Game.init(); }, start() { this.typewriter(`APÓS UMA BATALHA ÉPICA, {{ $character->name }} DESCE MAIS AFUNDO NA CAVERNA SOMBRIA. E CHEGA NA PARTE DAS LAVAS, E NOVOS DESAFIOS O AGUARDAM...`); }
};

const Game = {
    state: {
        playerTeam: [
            { id: 1, name: "{{ $character->name }}", hp: parseInt("{{ $character->hp }}"), maxHp: parseInt("{{ $character->max_hp }}"), mp: parseInt("{{ $character->mp }}"), maxMp: parseInt("{{ $character->max_mp }}"), attack: parseInt("{{ $character->attack }}"), defense: parseInt("{{ $character->defense }}"), sp_attack: parseInt("{{ $character->special_attack }}"), sp_defense: parseInt("{{ $character->special_defense }}"), speed: parseInt("{{ $character->speed }}"), level: parseInt("{{ $character->level }}"), xp: parseInt("{{ $character->exp ?? 0 }}"), gold: parseInt("{{ $character->gold ?? 0 }}"), xpToNextLevel: 150, isFainted: false, sprite: "{{ asset($character->avatar) }}" },
            { id: 2, name: "GEODUDE", hp: 40, maxHp: 40, mp: 20, maxMp: 20, attack: 15, defense: 20, sp_attack: 8, sp_defense: 8, speed: 10, level: 8, xp: 0, xpToNextLevel: 80, isFainted: false, sprite: 'https://play.pokemonshowdown.com/sprites/gen3/geodude.gif' },
            { id: 3, name: "ZUBAT", hp: 40, maxHp: 40, mp: 15, maxMp: 15, attack: 12, defense: 9, sp_attack: 10, sp_defense: 9, speed: 18, level: 7, xp: 0, xpToNextLevel: 70, isFainted: false, sprite: 'https://play.pokemonshowdown.com/sprites/gen3/zubat.gif' }
        ],
        inventory: {
            potion: parseInt("{{ $character->potions ?? 0 }}", 10), pokeball: parseInt("{{ $character->pokeballs ?? 0 }}", 10), greatball: parseInt("{{ $character->greatballs ?? 0 }}", 10), thunderstone: parseInt("{{ $character->thunderstones ?? 0 }}", 10)
        },
        enemy: {},
        enemies: [
            { name:"Charizard", level: 10, hp: 200, attack: 45, defense: 30, xp: 150, gold: 75, img:"{{ asset('img/cha.gif') }}" },
            { name:"Solgaleo", level: 12, hp: 140, attack: 30, defense: 18, xp: 220, gold: 100, img:"{{ asset('img/sol.png') }}" },
            { name:"Lorde Demônio", level: 15, hp: 300, attack: 60, defense: 35, xp: 500, gold: 250, img:"{{ asset('img/lorde-demonio.png') }}" }
        ],
        currentEnemyIndex: 0, gameState: 'PLAYER_TURN', menuState: 'main', activeBagItemIndex: 0, activeTeamMemberIndex: 0,
        isForcedSwap: false,
    },
    get activePokemon() { return this.state.playerTeam[0]; },
    elements: {
        player: { monsterSprite: document.getElementById('player-battle-monster-sprite'), name: document.getElementById('playerName'), hpBar: document.getElementById('playerHpBar'), hpText: document.getElementById('playerHpText'), mpBar: document.getElementById('playerMpBar'), mpText: document.getElementById('playerMpText'), xpBar: document.getElementById('playerXpBar'), xpText: document.getElementById('playerXpText'), actions: document.getElementById('actionsGrid') }, enemy: { monsterSprite: document.getElementById('enemy-monster-sprite'), name: document.getElementById('enemyName'), hpBar: document.getElementById('enemyHpBar'), hpText: document.getElementById('enemyHpText') }, log: document.getElementById('battleLog'), modal: { container: document.getElementById('endgameModal') },
        bag: { screen: document.getElementById('bag-screen'), list: document.getElementById('bag-item-list'), description: document.getElementById('bag-item-description'), },
        team: { screen: document.getElementById('team-screen'), selectedPanel: document.getElementById('team-selected-pokemon-panel'), listPanel: document.getElementById('team-list-panel'), dialogText: document.getElementById('team-dialog-text'), dialogButtons: document.getElementById('team-dialog-buttons') }
    },
    menus: { main: { fight: { name: 'Lutar', type: 'submenu', target: 'fight' }, bag: { name: 'Bolsa', type: 'open_bag' }, pokemon: { name: 'Pokémon', type: 'open_team_screen' }, run: { name: 'Sair', type: 'action', actionKey: 'run' } }, fight: { tackle: { name: 'Ataque', type: 'action', actionKey: 'attack' }, skill: { name: 'Magia', type: 'action', actionKey: 'skill' }, defend: { name: 'Defender', type: 'action', actionKey: 'defend' }, back: { name: 'Voltar', type: 'back' } }, },
    actions: { attack: { cost: 0, stat: 'attack' }, skill: { cost: 10, stat: 'sp_attack' }, defend: { stat: 'defend_stance' }, potion: { stat: 'heal' }, 'catch': { stat: 'catch' }, run: { stat: 'run' } },
    items: { 
        potion: { name: "POÇÃO", description: "Restaura 50 de HP.", actionKey: 'potion' },
        pokeball: { name: "POKÉ BALL", description: "Tenta capturar um Pokémon.", actionKey: 'catch' },
        greatball: { name: "GREAT BALL", description: "Uma Poké Ball melhorada.", actionKey: 'catch' },
        thunderstone: { name: "PEDRA DO TROVÃO", description: "Uma pedra peculiar que faz certos Pokémon evoluírem.", actionKey: 'none' }
    },
    init() { this.sanitizeStats(); this.loadEnemy(); this.renderMenu(); this.updateUI(); this.setupKeyboardNavigation(); this.createWindEffect(); this.logMessage(`UM PODER ESMAGADOR... ${this.state.enemy.name.toUpperCase()} APARECE!`, 'log-system'); },
    renderMenu() { const grid = this.elements.player.actions; grid.innerHTML = ''; const menu = this.menus[this.state.menuState]; for (const key in menu) { const item = menu[key]; const el = document.createElement('div'); el.className = 'action-item'; el.textContent = item.name; if(key === 'bag') { el.textContent += ` (${Object.values(this.state.inventory).reduce((a, b) => a + b, 0)})`; } el.onclick = () => { if (item.type === 'submenu') { this.state.menuState = item.target; this.renderMenu(); } else if (item.type === 'open_bag') this.openBag(); else if (item.type === 'open_team_screen') this.openTeamScreen(); else if (item.type === 'back') { this.state.menuState = 'main'; this.renderMenu(); } else this.executeTurn(item.actionKey); }; grid.appendChild(el); } },
    openBag() { if(this.state.gameState !== 'PLAYER_TURN') return; this.setGameState('BAG_OPEN'); this.elements.bag.screen.style.display = 'block'; this.renderBag(); },
    closeBag() { this.elements.bag.screen.style.display = 'none'; this.setGameState('PLAYER_TURN'); this.renderMenu(); },
    renderBag() { const listEl = this.elements.bag.list; listEl.innerHTML = ''; const availableItems = Object.keys(this.state.inventory).filter(k => this.state.inventory[k] > 0); availableItems.forEach(key => { const item = this.items[key]; if(!item) return; const itemEl = document.createElement('div'); itemEl.className = 'bag-item'; itemEl.dataset.itemKey = key; itemEl.innerHTML = `<span>${item.name}</span><span>x${this.state.inventory[key]}</span>`; itemEl.onclick = () => this.useItem(key); listEl.appendChild(itemEl); }); const cancelBtn = document.createElement('div'); cancelBtn.id = 'bag-cancel-button'; cancelBtn.className = 'bag-item'; cancelBtn.textContent = 'CANCELAR'; cancelBtn.onclick = () => this.closeBag(); listEl.appendChild(cancelBtn); },
    useItem(itemKey) { const itemData = this.items[key]; if(itemData.actionKey === 'none') { this.logMessage('Não pode ser usado agora.'); return; } this.closeBag(); this.executeTurn(itemData.actionKey, itemKey); },
    openTeamScreen(forceOpen = false) { if (this.state.gameState !== 'PLAYER_TURN' && !forceOpen) return; this.state.isForcedSwap = forceOpen; this.elements.team.screen.style.display = 'block'; const firstAvailable = this.state.playerTeam.findIndex(p => !p.isFainted); this.state.activeTeamMemberIndex = firstAvailable >= 0 ? firstAvailable : 0; this.renderTeamScreen(); this.setGameState('TEAM_SCREEN_OPEN'); },
    closeTeamScreen() { if (this.state.isForcedSwap) return; this.elements.team.screen.style.display = 'none'; this.setGameState('PLAYER_TURN'); this.renderMenu(); },
    renderTeamScreen() { const { selectedPanel, listPanel, dialogText, dialogButtons } = this.elements.team; selectedPanel.innerHTML = ''; listPanel.innerHTML = ''; dialogButtons.innerHTML = ''; const selectedPokemon = this.state.playerTeam[this.state.activeTeamMemberIndex]; selectedPanel.innerHTML = this.createPokemonCard(selectedPokemon, true); this.state.playerTeam.forEach((pokemon, index) => { if (index !== this.state.activeTeamMemberIndex) { listPanel.innerHTML += this.createPokemonCard(pokemon, false); } }); if (this.state.isForcedSwap) { dialogText.textContent = 'Escolha o próximo Pokémon!'; const swapBtn = document.createElement('button'); swapBtn.className = 'team-action-btn'; swapBtn.textContent = 'TROCAR'; swapBtn.onclick = () => this.swapPokemon(this.state.activeTeamMemberIndex); dialogButtons.appendChild(swapBtn); const giveUpBtn = document.createElement('button'); giveUpBtn.className = 'team-action-btn desistir'; giveUpBtn.textContent = 'DESISTIR'; giveUpBtn.onclick = () => this.gameOver(false); dialogButtons.appendChild(giveUpBtn); } else { dialogText.textContent = 'Escolha um Pokémon.'; const backBtn = document.createElement('button'); backBtn.className = 'back-button'; backBtn.textContent = 'VOLTAR'; backBtn.onclick = () => this.closeTeamScreen(); dialogButtons.appendChild(backBtn); } this.addTeamClickListeners(); },
    createPokemonCard(pokemon, isSelected) { const hpPercentage = pokemon.isFainted ? 0 : (pokemon.hp / pokemon.maxHp) * 100; return ` <div class="pokemon-card ${isSelected ? 'selected' : ''} ${pokemon.isFainted ? 'fainted' : ''}" data-index="${this.state.playerTeam.indexOf(pokemon)}"> <img src="${pokemon.sprite}" alt="${pokemon.name}" class="${isSelected ? 'sprite-large' : 'sprite-small'}"> <div class="team-info"> <div class="team-name-level"><span class="team-name">${pokemon.name.toUpperCase()}</span><span class="team-level">Lv${pokemon.level}</span></div> <div class="team-hp-container"><span class="team-hp-label">HP</span><div class="team-hp-bar-background"><div class="team-hp-bar-foreground" style="width: ${hpPercentage}%;"></div></div></div> <div class="team-hp-values"><span>${Math.ceil(pokemon.hp)}/${pokemon.maxHp}</span></div> </div> </div> `; },
    addTeamClickListeners() { document.querySelectorAll('#team-screen .pokemon-card').forEach(card => { const cardIndex = parseInt(card.dataset.index); card.onmouseenter = () => { if (this.state.activeTeamMemberIndex !== cardIndex) { this.state.activeTeamMemberIndex = cardIndex; this.renderTeamScreen(); } }; card.onclick = () => { if (!this.state.isForcedSwap) { this.swapPokemon(cardIndex); } }; }); },
    swapPokemon(newIndex) { const wasForced = this.state.isForcedSwap; const currentActiveIndex = this.state.playerTeam.findIndex(p => p.id === this.activePokemon.id); if (newIndex === currentActiveIndex && !wasForced) { this.elements.team.dialogText.textContent = 'Este Pokémon já está em batalha!'; return; } const targetPokemon = this.state.playerTeam[newIndex]; if (targetPokemon.isFainted) { this.elements.team.dialogText.textContent = `${targetPokemon.name} não pode batalhar!`; return; } this.state.isForcedSwap = false; this.elements.team.screen.style.display = 'none'; if (!wasForced) { this.logMessage(`Volte, ${this.activePokemon.name}!`); } setTimeout(() => { const newActivePokemon = this.state.playerTeam.splice(newIndex, 1)[0]; this.state.playerTeam.unshift(newActivePokemon); this.updateUI(); this.logMessage(`Vá, ${this.activePokemon.name}!`); if (!wasForced) { setTimeout(() => this.enemyTurn(), 1500); } else { this.setGameState('PLAYER_TURN'); this.renderMenu(); } }, 1000); },
    executeTurn(actionKey, itemKey = null) { if (this.state.gameState !== 'PLAYER_TURN') return; this.setGameState('PROCESSING'); const action = this.actions[actionKey]; const itemName = itemKey ? this.items[itemKey].name : (actionKey === 'attack' ? 'Ataque' : 'Magia'); this.logMessage(`${this.activePokemon.name} usa ${itemName}!`); if (itemKey) this.state.inventory[itemKey]--; if (action.cost) this.activePokemon.mp -= action.cost; if (action.stat === 'heal') { this.activePokemon.hp = Math.min(this.activePokemon.maxHp, this.activePokemon.hp + 50); setTimeout(() => this.enemyTurn(), 1500); } else if (action.stat === 'run') { this.gameOver(false); return; } else if (action.stat === 'catch') { setTimeout(() => { this.logMessage('Oh, não! O Pokémon escapou!'); this.enemyTurn(); }, 1500); } else { this.showAttackEffect(this.elements.enemy.monsterSprite); this.applyDamageEffect(this.elements.enemy.monsterSprite); let damage = this.calculateDamage(this.activePokemon[action.stat], this.state.enemy.defense); this.state.enemy.hp -= damage; if (this.state.enemy.hp <= 0) { this.logMessage(`${this.state.enemy.name.toUpperCase()} foi derrotado!`); this.gainXP(this.state.enemies[this.state.currentEnemyIndex].xp); this.state.playerTeam[0].gold += this.state.enemies[this.state.currentEnemyIndex].gold; setTimeout(() => this.nextEnemy(), 2000); return; } setTimeout(() => this.enemyTurn(), 1500); } this.updateUI(); },
    enemyTurn() { this.setGameState('ENEMY_TURN'); const enemy = this.state.enemy; this.logMessage(`${enemy.name.toUpperCase()} ATACA!`); this.showAttackEffect(this.elements.player.monsterSprite); this.applyDamageEffect(this.elements.player.monsterSprite); let damage = this.calculateDamage(enemy.attack, this.activePokemon.defense); this.activePokemon.hp -= damage; this.updateUI(); if (this.activePokemon.hp <= 0) { this.activePokemon.hp = 0; this.activePokemon.isFainted = true; this.logMessage(`${this.activePokemon.name} foi derrotado!`); const availablePokemon = this.state.playerTeam.filter(p => !p.isFainted); if (availablePokemon.length > 0) { setTimeout(() => this.openTeamScreen(true), 1500); } else { this.gameOver(false); } return; } setTimeout(() => { this.setGameState('PLAYER_TURN'); this.renderMenu(); }, 1500); },
    setGameState(newState) { this.state.gameState = newState; const menuContainer = this.elements.player.actions.parentElement; menuContainer.style.pointerEvents = (newState === 'PLAYER_TURN' || newState === 'TEAM_SCREEN_OPEN') ? 'auto' : 'none'; menuContainer.style.opacity = (newState === 'PLAYER_TURN' || newState === 'TEAM_SCREEN_OPEN') ? '1' : '0.7'; },
    updateUI() { const player = this.activePokemon; const { player: pEl, enemy: eEl } = this.elements; pEl.monsterSprite.src = player.sprite; pEl.name.innerHTML = `${player.name} <span style="font-size:0.7em;">LV ${player.level}</span>`; pEl.hpBar.style.width = `${Math.max(0, player.hp / player.maxHp * 100)}%`; pEl.hpText.textContent = `${Math.ceil(player.hp)}/${player.maxHp}`; pEl.mpBar.style.width = `${Math.max(0, player.mp / player.maxMp * 100)}%`; pEl.mpText.textContent = `${Math.ceil(player.mp)}/${player.maxMp}`; pEl.xpBar.style.width = `${Math.max(0, player.xp / player.xpToNextLevel * 100)}%`; pEl.xpText.textContent = `${player.xp}/${player.xpToNextLevel}`; if (this.state.enemy.name) { eEl.name.innerHTML = `${this.state.enemy.name.toUpperCase()} <span style="font-size:0.7em;">LV ${this.state.enemy.level || 1}</span>`; eEl.monsterSprite.src = this.state.enemy.img; eEl.hpBar.style.width = `${Math.max(0, this.state.enemy.hp / this.state.enemy.maxHp * 100)}%`; eEl.hpText.textContent = `${Math.ceil(this.state.enemy.hp)}/${this.state.enemy.maxHp}`; } },
    sanitizeStats() { this.state.playerTeam.forEach(p => { p.hp = Math.min(p.hp, p.maxHp); if (p.hp <= 0) { p.isFainted = true; p.hp = 0;} else {p.isFainted = false;} }); Object.keys(this.state.inventory).forEach(key => this.state.inventory[key] = this.state.inventory[key] || 0); },
    calculateDamage(power, defense) { return Math.floor(Math.max(1, power - (defense * 0.5))); },
    gainXP(amount) { this.activePokemon.xp += amount; if (this.activePokemon.xp >= this.activePokemon.xpToNextLevel) { this.activePokemon.level++; this.activePokemon.xp = 0; this.logMessage(`LEVEL UP! NÍVEL ${this.activePokemon.level}!`); } },
    nextEnemy() { this.state.currentEnemyIndex++; if (this.state.currentEnemyIndex >= this.state.enemies.length) { this.gameOver(true); return; } this.loadEnemy(); this.updateUI(); this.setGameState('PLAYER_TURN'); },
    loadEnemy() { this.state.enemy = { ...this.state.enemies[this.state.currentEnemyIndex] }; this.state.enemy.maxHp = this.state.enemy.hp; },
    showAttackEffect(targetElement) { const effect = document.createElement('img'); effect.src = 'https://i.gifer.com/origin/d7/d7ac4f38b77abe73165c2b9691907245_w200.gif'; effect.className = 'attack-effect'; const targetRect = targetElement.getBoundingClientRect(); const effectSize = 150; effect.style.left = `${targetRect.left + (targetRect.width / 2) - (effectSize / 2)}px`; effect.style.top = `${targetRect.top + (targetRect.height / 2) - (effectSize / 2)}px`; document.body.appendChild(effect); setTimeout(() => { effect.remove(); }, 500); },
    
    applyDamageEffect(targetSprite) {
        targetSprite.classList.add('take-damage');
        setTimeout(() => {
            targetSprite.classList.remove('take-damage');
        }, 400);
    },

    createWindEffect() {
        const arena = document.querySelector('.battle-arena');
        if (!arena) return;
        for (let i = 0; i < 15; i++) {
            const particle = document.createElement('div');
            particle.className = 'wind-particle';
            const size = Math.random() * 3 + 1;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${Math.random() * -200}px`;
            particle.style.top = `${Math.random() * 100}%`;
            const duration = Math.random() * 2 + 1.5;
            const delay = Math.random() * 3;
            particle.style.animationDuration = `${duration}s`;
            particle.style.animationDelay = `${delay}s`;
            arena.appendChild(particle);
        }
    },

    gameOver(isVictory) {
        this.setGameState('GAME_OVER');
        const loseModal = this.elements.modal.container;

        if (isVictory) {
            this.logMessage('TODOS OS INIMIGOS FORAM DERROTADOS!');
            const newStoneCount = (this.state.inventory.thunderstone || 0) + 2;
            this.state.inventory.thunderstone = newStoneCount;
            this.saveProgress({ 
                final_win: true,
                thunderstones: newStoneCount
            });
            
            document.querySelector('.battle-screen').style.display = 'none';

            const victoryOverlay = document.getElementById('victory-sequence');
            const rewardText = document.getElementById('victory-reward-text');
            victoryOverlay.classList.add('is-visible');

            setTimeout(() => {
                rewardText.classList.remove('hidden');
                rewardText.classList.add('visible');
            }, 2000);

            setTimeout(() => {
                victoryOverlay.classList.remove('is-visible');
                const creditsOverlay = document.getElementById('credits-sequence');
                creditsOverlay.classList.add('is-visible');

                const creditsContent = document.querySelector('.credits-content');
                creditsContent.addEventListener('animationend', () => {
                    // Importante: Apenas esconda o conteúdo que rolou, não o overlay todo
                    creditsContent.style.visibility = 'hidden'; 
                    document.getElementById('end-game-menu').classList.replace('hidden', 'visible');
                }, { once: true });

            }, 5000); 

        } else {
            loseModal.innerHTML = `<div class="modal-box"><h2>FIM DE JOGO</h2><p>Sua jornada termina aqui.</p><a href="{{ route('home') }}" class="btn">TELA INICIAL</a></div>`;
            loseModal.classList.add('is-visible');
        }
    },
    async saveProgress(data) { try { await fetch("{{ route('character.saveProgress', $character->id) }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: JSON.stringify(data) }); } catch (error) { console.error('Erro:', error); } },
    logMessage(message) { this.elements.log.querySelector('p').innerHTML = message; },
    setupKeyboardNavigation() { /* ... */ }
};

document.addEventListener('DOMContentLoaded', () => Intro.start());
</script>
</body>
</html>