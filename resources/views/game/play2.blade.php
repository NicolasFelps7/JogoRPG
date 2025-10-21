<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Batalha Sombria | {{ $character->name }}</title>
<link rel="icon" href="{{ asset('img/logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

<style>
    /* Estilos gerais, intro, etc. */
    :root { --bg-dark: #1a1c2c; --ui-main: #5a3a2b; --ui-border-light: #a18c7c; --ui-border-dark: #3f2a1f; --text-light: #ffffff; --text-highlight: #94e5ff; --hp-color: #70d870; --mp-color: #1e88e5; --xp-color: #fdd835; --dialog-bg: #e0e0e0; --dialog-border: #606060; --dialog-text: #303030; }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Press Start 2P', cursive; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("https://img.itch.zone/aW1hZ2UvMzc0Mjk0OS8yMjI3NzU0Ni5naWY=/original/JDKJBv.gif") no-repeat center center fixed; background-size: cover; background-blend-mode: multiply; min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 10px; color: var(--text-light); image-rendering: pixelated; overflow: hidden; position: relative; }
    #story-intro { position: fixed; inset: 0; background-color: #000; color: #fff; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 200; padding: 20px; opacity: 1; transition: none; overflow: hidden; }
    #story-intro.hide-intro-animation { animation: hide-intro-up 1.5s forwards ease-in-out; }
    @keyframes hide-intro-up { 0% { transform: translateY(0); opacity: 1; } 50% { opacity: 0.5; } 100% { transform: translateY(-100%); opacity: 0; visibility: hidden; } }
    #story-text { font-size: 1.5rem; line-height: 1.8; max-width: 800px; text-align: center; text-shadow: 2px 2px #000; z-index: 10; background: rgba(0,0,0,0.4); padding: 10px 20px; border-radius: 5px; }
    #stage-title { font-size: 3rem; color: var(--text-highlight); margin-top: 40px; opacity: 0; transform: scale(0.5); text-shadow: 4px 4px #000; z-index: 10; }
    #stage-title.visible { animation: stage-intro 1.5s forwards; }
    .battle-screen { visibility: hidden; opacity: 0; width: 100%; max-width: 1024px; height: 768px; position: relative; transition: opacity 1s; z-index: 2; background-color: transparent; border: none; overflow: hidden; }
    .battle-screen.visible { visibility: visible; opacity: 1; }
    .battle-arena { position: absolute; top: 0; left: 0; width: 100%; height: calc(100% - 200px); }
    .battle-monster-sprite { width: 250px; height: 250px; object-fit: contain; }
    #player-area { position: absolute; bottom: 40px; left: 10%; display: flex; flex-direction: column; align-items: center; gap: 5px; }
    #enemy-area { position: absolute; top: 80px; right: 10%; display: flex; flex-direction: column; align-items: center; gap: 5px; }
    .status-box-pokemon { background: var(--dialog-bg); border: 2px solid var(--dialog-border); box-shadow: 2px 2px 0px rgba(0,0,0,0.3); padding: 8px 12px; color: var(--dialog-text); font-size: 0.8rem; z-index: 10; min-width: 220px; text-align: left; }
    .status-box-pokemon h2 { font-size: 0.9rem; margin-bottom: 3px; color: var(--dialog-text); text-shadow: none; }
    .hp-bar-container { width: 100%; height: 10px; background-color: #505050; border: 1px solid var(--dialog-border); position: relative; margin-top: 3px; margin-bottom: 3px; }
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
    .pokemon-card { background-color: #f8f8f8; border: 4px solid #9ca4a5; border-radius: 8px; padding: 8px; display: flex; align-items: center; color: #333; position: relative; cursor: pointer; transition: transform 0.1s ease; }
    .pokemon-card:hover { transform: scale(1.02); }
    .pokemon-card.selected { border-color: #e56a11; background: linear-gradient(to right, #fdd5b6, #f8f8f8 30%); cursor: default; }
    .pokemon-card.selected:hover { transform: none; }
    .pokemon-card.fainted { background-color: #c0c0c0; color: #888; cursor: not-allowed; }
    .pokemon-card.fainted:hover { transform: none; }
    .pokemon-card.active { border-color: #0d6efd; box-shadow: 0 0 8px #0d6efd; }
    .sprite-large { width: 80px; height: 80px; margin-right: 10px; }
    .sprite-small { width: 50px; height: 50px; margin-right: 10px; }
    .team-info { flex-grow: 1; }
    .team-name-level { display: flex; justify-content: space-between; margin-bottom: 4px; }
    .team-hp-container { display: flex; align-items: center; margin-bottom: 2px; }
    .team-hp-label { background-color: #daac20; color: white; font-size: 0.7em; padding: 1px 3px; border-radius: 4px; margin-right: 4px; }
    .team-hp-bar-background { flex-grow: 1; background-color: #555; border-radius: 5px; height: 8px; padding: 2px; border: 1px solid #333; }
    .team-hp-bar-foreground { background-color: #30a850; height: 100%; border-radius: 3px; }
    .team-hp-values { text-align: right; font-size: 0.9em; }
    .team-dialog-box { background-color: #f8f8f8; border: 4px solid #9ca4a5; border-radius: 8px; padding: 12px; margin-top: 12px; color: #333; position: relative; min-height: 50px; display: flex; align-items: center; justify-content: space-between; }
    #team-dialog-buttons { display: flex; gap: 10px; }
    .team-action-btn { font-family: 'Press Start 2P', cursive; background-color: #dcdcdc; border: 2px solid #aaa; border-radius: 10px; padding: 5px 10px; font-size: 0.8em; cursor: pointer; }
    .team-action-btn.desistir { background-color: #e57373; border-color: #d32f2f; color: white; }
    .back-button { font-family: 'Press Start 2P', cursive; background-color: #dcdcdc; border: 2px solid #aaa; border-radius: 10px; padding: 5px 10px; font-size: 0.8em; cursor: pointer; }
</style>
</head>
<body>

<div id="story-intro">
    <p id="story-text"></p>
    <h1 id="stage-title">FASE 2</h1>
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

<script>
const Intro = {
    storyContainer: document.getElementById('story-intro'), storyTextEl: document.getElementById('story-text'), stageTitleEl: document.getElementById('stage-title'), battleScreenEl: document.querySelector('.battle-screen'), typewriter(text, i = 0) { if (i < text.length) { this.storyTextEl.innerHTML += text.charAt(i); setTimeout(() => this.typewriter(text, i + 1), 50); } else { setTimeout(() => this.showStageTitle(), 2000); } }, showStageTitle() { this.stageTitleEl.classList.add('visible'); setTimeout(() => this.hideIntro(), 2500); }, hideIntro() { this.storyContainer.classList.add('hide-intro-animation'); this.storyContainer.addEventListener('animationend', () => { this.storyContainer.remove(); this.showBattleScreen(); }, { once: true }); }, showBattleScreen() { this.battleScreenEl.classList.add('visible'); Game.init(); }, start() { this.typewriter(`APÓS A VITÓRIA, {{ $character->name }} CONTINUA SUA TRAJETÓRIA PARA DENTRO DA CAVERNA SOMBRIA...`); }
};

const Game = {
    state: {
        playerTeam: [
            { id: 1, name: "{{ $character->name }}", hp: parseInt("{{ $character->hp }}"), maxHp: parseInt("{{ $character->max_hp }}"), mp: parseInt("{{ $character->mp }}"), maxMp: parseInt("{{ $character->max_mp }}"), attack: parseInt("{{ $character->attack }}"), defense: parseInt("{{ $character->defense }}"), sp_attack: parseInt("{{ $character->special_attack }}"), sp_defense: parseInt("{{ $character->special_defense }}"), speed: parseInt("{{ $character->speed }}"), level: parseInt("{{ $character->level }}"), xp: parseInt("{{ $character->exp ?? 0 }}"), gold: parseInt("{{ $character->gold ?? 0 }}"), xpToNextLevel: 150, isFainted: false, sprite: "{{ asset($character->avatar) }}" },
            { id: 2, name: "RAICHU", hp: 200, maxHp: 250, mp: 100, maxMp: 200, attack: 200, defense: 20, sp_attack: 8, sp_defense: 8, speed: 10, level: 20, xp: 10, xpToNextLevel: 80, isFainted: false, sprite: 'https://pa1.aminoapps.com/6744/4f8b0d5940eda27c49a30dfa903c3c1321ac798d_hq.gif' },
            { id: 3, name: "PIKACHU", hp: 200, maxHp: 250, mp: 100, maxMp: 200, attack: 200, defense: 9, sp_attack: 10, sp_defense: 9, speed: 18, level: 20, xp: 10, xpToNextLevel: 70, isFainted: false, sprite: 'https://i.pinimg.com/originals/9f/b1/25/9fb125f1fedc8cc62ab5b20699ebd87d.gif' }
        ],
        inventory: {
            potion: parseInt("{{ $character->potions ?? 0 }}", 10), pokeball: parseInt("{{ $character->pokeballs ?? 0 }}", 10), greatball: parseInt("{{ $character->greatballs ?? 0 }}", 10), thunderstone: parseInt("{{ $character->thunderstones ?? 0 }}", 10)
        },
        enemy: {},
        enemies: [
            { name:"Rattata", hp: 80, attack: 30, defense: 30, xp: 150, gold: 75, level: 6, img:"{{ asset('img/miguel.gif') }}" },
            { name:"Onix", hp: 90, attack: 30, defense: 50, xp: 220, gold: 100, level: 7, img:"{{ asset('img/oni.gif') }}" },
            { name:"Snorlax", hp: 100, attack: 30, defense: 35, xp: 500, gold: 250, level: 8, img:"{{ asset('img/sno.gif') }}" }
        ],
        currentEnemyIndex: 0, gameState: 'PLAYER_TURN', menuState: 'main', activeBagItemIndex: 0,
        // <<< MODIFICAÇÃO 1: Adicionando o novo estado para a lista >>>
        activeTeamListIndex: 0, 
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
        thunderstone: { name: "PEDRA DO TROVÃO", description: "Não pode ser usada em batalha.", actionKey: 'none' }
    },
    init() { this.sanitizeStats(); this.loadEnemy(); this.renderMenu(); this.updateUI(); this.setupKeyboardNavigation(); this.logMessage(`UMA SOMBRA EMERGE... ${this.state.enemy.name.toUpperCase()} APARECE!`, 'log-system'); },
    renderMenu() { const grid = this.elements.player.actions; grid.innerHTML = ''; const menu = this.menus[this.state.menuState]; for (const key in menu) { const item = menu[key]; const el = document.createElement('div'); el.className = 'action-item'; el.textContent = item.name; if(key === 'bag') { el.textContent += ` (${Object.values(this.state.inventory).reduce((a, b) => a + b, 0)})`; } el.onclick = () => { if (item.type === 'submenu') { this.state.menuState = item.target; this.renderMenu(); } else if (item.type === 'open_bag') this.openBag(); else if (item.type === 'open_team_screen') this.openTeamScreen(); else if (item.type === 'back') { this.state.menuState = 'main'; this.renderMenu(); } else this.executeTurn(item.actionKey); }; grid.appendChild(el); } },
    
    // --- LÓGICA DA BOLSA (sem alterações) ---
    openBag() { if(this.state.gameState !== 'PLAYER_TURN') return; this.setGameState('BAG_OPEN'); this.elements.bag.screen.style.display = 'block'; this.renderBag(); },
    closeBag() { this.elements.bag.screen.style.display = 'none'; this.setGameState('PLAYER_TURN'); this.renderMenu(); },
    renderBag() { const listEl = this.elements.bag.list; listEl.innerHTML = ''; const availableItems = Object.keys(this.state.inventory).filter(k => this.state.inventory[k] > 0); availableItems.forEach(key => { const item = this.items[key]; if(!item) return; const itemEl = document.createElement('div'); itemEl.className = 'bag-item'; itemEl.dataset.itemKey = key; itemEl.innerHTML = `<span>${item.name}</span><span>x${this.state.inventory[key]}</span>`; itemEl.onclick = () => this.useItem(key); listEl.appendChild(itemEl); }); const cancelBtn = document.createElement('div'); cancelBtn.id = 'bag-cancel-button'; cancelBtn.className = 'bag-item'; cancelBtn.textContent = 'CANCELAR'; cancelBtn.onclick = () => this.closeBag(); listEl.appendChild(cancelBtn); },
    useItem(itemKey) { const itemData = this.items[itemKey]; if(itemData.actionKey === 'none') { this.logMessage('Não pode ser usado agora.'); return; } this.closeBag(); this.executeTurn(itemData.actionKey, itemKey); },
    
    // <<< MODIFICAÇÃO 2: LÓGICA DA EQUIPE TOTALMENTE SUBSTITUÍDA >>>
    openTeamScreen() {
        if (this.state.gameState !== 'PLAYER_TURN' && this.state.gameState !== 'MUST_SWITCH') return;
        this.elements.team.screen.style.display = 'block';
        this.state.activeTeamListIndex = 0; 
        this.renderTeamScreen();
        if (this.state.gameState !== 'MUST_SWITCH') {
            this.setGameState('TEAM_SCREEN_OPEN');
        }
    },
    closeTeamScreen() {
        // A tela só pode ser fechada pelo botão se NÃO for uma troca obrigatória
        if (this.state.gameState === 'MUST_SWITCH') return;
        this.elements.team.screen.style.display = 'none';
        this.setGameState('PLAYER_TURN');
        this.renderMenu();
    },
    renderTeamScreen() {
        const { selectedPanel, listPanel, dialogText, dialogButtons } = this.elements.team;
        selectedPanel.innerHTML = '';
        listPanel.innerHTML = '';
        dialogButtons.innerHTML = '';

        const battlingPokemon = this.state.playerTeam[0];
        selectedPanel.innerHTML = this.createPokemonCard(battlingPokemon, true, false);

        let currentListIndex = 0;
        this.state.playerTeam.forEach((pokemon, index) => {
            if (index > 0) {
                const isActive = (currentListIndex === this.state.activeTeamListIndex);
                listPanel.insertAdjacentHTML('beforeend', this.createPokemonCard(pokemon, false, isActive));
                currentListIndex++;
            }
        });

        // Lógica para os textos e botões
        if (this.state.gameState === 'MUST_SWITCH') {
            dialogText.textContent = 'Escolha o próximo Pokémon!';
            // O botão de troca será acionado pelo clique no card ou Enter
        } else {
            dialogText.textContent = 'Escolha um Pokémon.';
            const backBtn = document.createElement('button');
            backBtn.className = 'back-button';
            backBtn.textContent = 'VOLTAR';
            backBtn.onclick = () => this.closeTeamScreen();
            dialogButtons.appendChild(backBtn);
        }

        this.addTeamClickListeners();
        this.updateTeamSelection();
    },
    createPokemonCard(pokemon, isSelected, isActive) {
        const hpPercentage = pokemon.isFainted ? 0 : (pokemon.hp / pokemon.maxHp) * 100;
        const activeClass = isActive ? 'active' : '';
        return `
            <div class="pokemon-card ${isSelected ? 'selected' : ''} ${pokemon.isFainted ? 'fainted' : ''} ${activeClass}" data-index="${this.state.playerTeam.indexOf(pokemon)}">
                <img src="${pokemon.sprite}" alt="${pokemon.name}" class="${isSelected ? 'sprite-large' : 'sprite-small'}">
                <div class="team-info">
                    <div class="team-name-level"><span class="team-name">${pokemon.name.toUpperCase()}</span><span class="team-level">Lv${pokemon.level}</span></div>
                    <div class="team-hp-container"><span class="team-hp-label">HP</span><div class="team-hp-bar-background"><div class="team-hp-bar-foreground" style="width: ${hpPercentage}%;"></div></div></div>
                    <div class="team-hp-values"><span>${Math.ceil(pokemon.hp)}/${pokemon.maxHp}</span></div>
                </div>
            </div>
        `;
    },
    updateTeamSelection() {
        const allTeamCards = this.elements.team.listPanel.querySelectorAll('.pokemon-card');
        allTeamCards.forEach((card, index) => {
            if (index === this.state.activeTeamListIndex) {
                card.classList.add('active');
                card.scrollIntoView({ block: 'nearest' });
            } else {
                card.classList.remove('active');
            }
        });
    },
    addTeamClickListeners() {
        this.elements.team.listPanel.querySelectorAll('.pokemon-card').forEach((card, listIndex) => {
            card.onclick = () => {
                const teamIndex = parseInt(card.dataset.index, 10);
                this.swapPokemon(teamIndex);
            };
            card.onmouseenter = () => {
                this.state.activeTeamListIndex = listIndex;
                this.updateTeamSelection();
            };
        });
    },
    swapPokemon(newIndex) {
        const wasForced = this.state.gameState === 'MUST_SWITCH';
        if (newIndex === 0) {
            this.elements.team.dialogText.textContent = 'Este Pokémon já está em batalha!';
            return;
        }
        const targetPokemon = this.state.playerTeam[newIndex];
        if (targetPokemon.isFainted) {
            this.elements.team.dialogText.textContent = `${targetPokemon.name} não pode batalhar!`;
            return;
        }
        
        this.elements.team.screen.style.display = 'none';
        if (!wasForced) {
            this.logMessage(`Volte, ${this.activePokemon.name}!`);
        }
        
        setTimeout(() => {
            // Lógica de troca simplificada e mais segura
            const temp = this.state.playerTeam[0];
            this.state.playerTeam[0] = this.state.playerTeam[newIndex];
            this.state.playerTeam[newIndex] = temp;
            
            this.updateUI();
            this.logMessage(`Vá, ${this.activePokemon.name}!`);
            
            if (!wasForced) {
                setTimeout(() => this.enemyTurn(), 1500);
            } else {
                this.setGameState('PLAYER_TURN');
                this.renderMenu();
            }
        }, 1000);
    },
    
    // --- LÓGICA DE BATALHA ---
    executeTurn(actionKey, itemKey = null) { if (this.state.gameState !== 'PLAYER_TURN' && this.state.gameState !== 'BAG_OPEN') return; this.setGameState('PROCESSING'); const action = this.actions[actionKey]; const itemName = itemKey ? this.items[itemKey].name : (actionKey === 'attack' ? 'Ataque' : 'Magia'); this.logMessage(`${this.activePokemon.name} usa ${itemName}!`); if (itemKey) this.state.inventory[itemKey]--; if (action.cost) this.activePokemon.mp -= action.cost; if (action.stat === 'heal') { this.activePokemon.hp = Math.min(this.activePokemon.maxHp, this.activePokemon.hp + 50); setTimeout(() => this.enemyTurn(), 1500); } else if (action.stat === 'run') { this.gameOver(false); return; } else if (action.stat === 'catch') { setTimeout(() => { this.logMessage('Oh, não! O Pokémon escapou!'); this.enemyTurn(); }, 1500); } else { let damage = this.calculateDamage(this.activePokemon[action.stat], this.state.enemy.defense); this.state.enemy.hp -= damage; if (this.state.enemy.hp <= 0) { this.logMessage(`${this.state.enemy.name.toUpperCase()} DERROTADO!`); this.gainXP(this.state.enemies[this.state.currentEnemyIndex].xp); setTimeout(() => this.nextEnemy(), 2000); return; } setTimeout(() => this.enemyTurn(), 1500); } this.updateUI(); },
    
    // <<< MODIFICAÇÃO 3: Atualizando enemyTurn para usar o novo estado >>>
    enemyTurn() {
        this.setGameState('ENEMY_TURN');
        const enemy = this.state.enemy;
        this.logMessage(`${enemy.name.toUpperCase()} ATACA!`);
        let damage = this.calculateDamage(enemy.attack, this.activePokemon.defense);
        this.activePokemon.hp -= damage;
        this.updateUI();
        if (this.activePokemon.hp <= 0) {
            this.activePokemon.hp = 0;
            this.activePokemon.isFainted = true;
            this.logMessage(`${this.activePokemon.name} foi derrotado!`);
            const availablePokemon = this.state.playerTeam.filter(p => !p.isFainted);
            if (availablePokemon.length > 0) {
                // Usa o novo estado para forçar a troca
                this.setGameState('MUST_SWITCH');
                setTimeout(() => this.openTeamScreen(), 1500);
            } else {
                this.gameOver(false);
            }
            return;
        }
        setTimeout(() => {
            this.setGameState('PLAYER_TURN');
            this.renderMenu();
        }, 1500);
    },
    
    // --- FUNÇÕES UTILITÁRIAS ---
    setGameState(newState) { this.state.gameState = newState; const menuContainer = this.elements.player.actions.parentElement; menuContainer.style.pointerEvents = (newState === 'PLAYER_TURN' || newState === 'TEAM_SCREEN_OPEN') ? 'auto' : 'none'; menuContainer.style.opacity = (newState === 'PLAYER_TURN' || newState === 'TEAM_SCREEN_OPEN') ? '1' : '0.7'; },
    updateUI() { const player = this.activePokemon; const { player: pEl, enemy: eEl } = this.elements; pEl.monsterSprite.src = player.sprite; pEl.name.innerHTML = `${player.name} <span style="font-size:0.7em;">LV ${player.level}</span>`; pEl.hpBar.style.width = `${Math.max(0, player.hp / player.maxHp * 100)}%`; pEl.hpText.textContent = `${Math.ceil(player.hp)}/${player.maxHp}`; pEl.mpBar.style.width = `${Math.max(0, player.mp / player.maxMp * 100)}%`; pEl.mpText.textContent = `${Math.ceil(player.mp)}/${player.maxMp}`; pEl.xpBar.style.width = `${Math.max(0, player.xp / player.xpToNextLevel * 100)}%`; pEl.xpText.textContent = `${player.xp}/${player.xpToNextLevel}`; if (this.state.enemy.name) { eEl.name.innerHTML = `${this.state.enemy.name.toUpperCase()} <span style="font-size:0.7em;">LV ${this.state.enemy.level || 1}</span>`; eEl.monsterSprite.src = this.state.enemy.img; eEl.hpBar.style.width = `${Math.max(0, this.state.enemy.hp / this.state.enemy.maxHp * 100)}%`; eEl.hpText.textContent = `${Math.ceil(this.state.enemy.hp)}/${this.state.enemy.maxHp}`; } },
    sanitizeStats() { this.state.playerTeam.forEach(p => { p.hp = Math.min(p.hp, p.maxHp); if (p.hp <= 0) { p.isFainted = true; p.hp = 0;} else {p.isFainted = false;} }); Object.keys(this.state.inventory).forEach(key => this.state.inventory[key] = this.state.inventory[key] || 0); },
    calculateDamage(power, defense) { return Math.floor(Math.max(1, power - (defense * 0.5))); },
    gainXP(amount) { this.activePokemon.xp += amount; if (this.activePokemon.xp >= this.activePokemon.xpToNextLevel) { this.activePokemon.level++; this.activePokemon.xp = 0; this.logMessage(`LEVEL UP! NÍVEL ${this.activePokemon.level}!`); } },
    nextEnemy() { this.state.currentEnemyIndex++; if (this.state.currentEnemyIndex >= this.state.enemies.length) { this.gameOver(true); return; } this.loadEnemy(); this.updateUI(); this.setGameState('PLAYER_TURN'); },
    loadEnemy() { this.state.enemy = { ...this.state.enemies[this.state.currentEnemyIndex] }; this.state.enemy.maxHp = this.state.enemy.hp; },
    gameOver(isVictory) { this.setGameState('GAME_OVER'); const saveData = { ...this.activePokemon, gold: this.state.playerTeam[0].gold, ...this.state.inventory }; if (isVictory) { this.logMessage('FASE CONCLUÍDA!'); this.saveProgress(saveData); setTimeout(() => { window.location.href = "{{ route('character.shop', ['character' => $character->id, 'next_stage' => 'play3']) }}"; }, 1500); } else { const modal = this.elements.modal.container; modal.innerHTML = `<div class="modal-box"><h2>FIM DE JOGO</h2><p>Sua jornada termina aqui...</p><a href="{{ route('home') }}" class="btn">REINICIAR</a></div>`; modal.classList.add('is-visible'); } },
    async saveProgress(data) { try { await fetch("{{ route('character.saveProgress', $character->id) }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: JSON.stringify(data) }); } catch (error) { console.error('Erro:', error); } },
    logMessage(message) { this.elements.log.querySelector('p').innerHTML = message; },
    
    // <<< MODIFICAÇÃO 4: Adicionando a navegação por teclado para a equipe >>>
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            if (this.state.gameState === 'BAG_OPEN') {
                // ... (código da bolsa sem alteração)
            } else if (this.state.gameState === 'TEAM_SCREEN_OPEN' || this.state.gameState === 'MUST_SWITCH') {
                const teamList = this.elements.team.listPanel.querySelectorAll('.pokemon-card');
                if (teamList.length === 0) return;
                let newIndex = this.state.activeTeamListIndex;
                if (e.key === 'ArrowDown') { e.preventDefault(); newIndex = (newIndex + 1) % teamList.length; }
                else if (e.key === 'ArrowUp') { e.preventDefault(); newIndex = (newIndex - 1 + teamList.length) % teamList.length; }
                else if (e.key === 'Enter') { 
                    e.preventDefault();
                    const selectedCard = teamList[this.state.activeTeamListIndex];
                    if (selectedCard) selectedCard.click();
                    return; 
                }
                else if (e.key === 'Escape') { 
                    e.preventDefault(); 
                    // Só permite fechar com ESC se não for obrigatório
                    if (this.state.gameState !== 'MUST_SWITCH') {
                       this.closeTeamScreen();
                    }
                    return; 
                }
                if (newIndex !== this.state.activeTeamListIndex) {
                    this.state.activeTeamListIndex = newIndex;
                    this.updateTeamSelection();
                }
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => Intro.start());
</script>
</body>
</html>