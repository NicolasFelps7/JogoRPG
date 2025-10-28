<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Batalha Épica | {{ $character->name }}</title>
<link rel="icon" href="{{ asset('img/logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

<style>
    /* Estilos gerais, intro, efeitos, etc. */
    :root { --bg-dark: #1a1c2c; --ui-main: #5a3a2b; --ui-border-light: #a18c7c; --ui-border-dark: #3f2a1f; --text-light: #ffffff; --text-highlight: #94e5ff; --hp-color: #70d870; --mp-color: #1e88e5; --xp-color: #fdd835; --dialog-bg: #e0e0e0; --dialog-border: #606060; --dialog-text: #303030; }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Press Start 2P', cursive; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://i.redd.it/enk0oh0syll51.gif') no-repeat center center; background-size: cover; background-blend-mode: multiply; min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 10px; color: var(--text-light); image-rendering: pixelated; overflow: hidden; position: relative; }
    #story-intro { position: fixed; inset: 0; background-color: #000; color: #fff; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 200; padding: 20px; opacity: 1; transition: none; overflow: hidden; }
    #story-intro.hide-intro-animation { animation: hide-intro-up 1.5s forwards ease-in-out; }
    @keyframes hide-intro-up { 0% { transform: translateY(0); opacity: 1; } 50% { opacity: 0.5; } 100% { transform: translateY(-100%); opacity: 0; visibility: hidden; } }
    #story-text { font-size: 1.5rem; line-height: 1.8; max-width: 800px; text-align: center; text-shadow: 2px 2px #000; z-index: 10; background: rgba(0,0,0,0.4); padding: 10px 20px; border-radius: 5px; }
    #stage-title { font-size: 3rem; color: var(--text-highlight); margin-top: 40px; opacity: 0; transform: scale(0.5); text-shadow: 4px 4px #000; z-index: 10; }
    #stage-title.visible { animation: stage-intro 1.5s forwards; }
    .pokeball { position: absolute; width: 60px; height: 60px; border-radius: 50%; background: radial-gradient(circle at 50% 30%, white 5%, transparent 5%) 0 0, linear-gradient(to bottom, red 50%, white 50%); background-size: 100% 100%; border: 4px solid #333; box-shadow: inset -3px -3px 0 rgba(0,0,0,0.2), inset 3px 3px 0 rgba(255,255,255,0.8); animation: pokeball-bounce 3s infinite ease-in-out, pokeball-fade 3s forwards; z-index: 5; }
    .pokeball::before { content: ''; position: absolute; width: 15px; height: 15px; background-color: white; border-radius: 50%; border: 3px solid #333; top: 50%; left: 50%; transform: translate(-50%, -50%); box-shadow: inset -2px -2px 0 rgba(0,0,0,0.2), inset 2px 2px 0 rgba(255,255,255,0.8); }
    .pokeball::after { content: ''; position: absolute; width: 100%; height: 6px; background-color: #333; top: 50%; left: 0; transform: translateY(-50%); }
    @keyframes pokeball-bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-30px); } }
    @keyframes pokeball-fade { 0% { opacity: 0; transform: scale(0.5); } 20% { opacity: 1; transform: scale(1); } 80% { opacity: 1; transform: scale(1); } 100% { opacity: 0; transform: scale(0.5); } }
    .pokeball:nth-child(1) { bottom: 10%; left: 15%; animation-delay: 0s, 0s; }
    .pokeball:nth-child(2) { top: 20%; right: 10%; animation-delay: 1s, 1s; }
    .pokeball:nth-child(3) { bottom: 5%; right: 30%; animation-delay: 2s, 2s; transform: scale(0.8); }
    .pokeball:nth-child(4) { top: 40%; left: 5%; animation-delay: 0.5s, 0.5s; transform: scale(1.2); }
    .pokeball:nth-child(5) { bottom: 25%; left: 40%; animation-delay: 1.5s, 1.5s; transform: scale(0.9); }
    .battle-screen { visibility: hidden; opacity: 0; width: 100%; max-width: 1024px; height: 768px; position: relative; transition: opacity 1s; z-index: 2; background-color: transparent; border: none; overflow: hidden; }
    .battle-screen.visible { visibility: visible; opacity: 1; }
    .battle-arena { position: absolute; top: 0; left: 0; width: 100%; height: calc(100% - 200px); }
    #wind-effect-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; overflow: hidden; z-index: 1; }
    .wind-particle { position: absolute; background-color: rgba(255, 255, 255, 0.6); width: 15px; height: 2px; border-radius: 2px; opacity: 0; animation: wind-blow linear infinite; left: -20px; }
    @keyframes wind-blow { 0% { transform: translateX(0) translateY(0); opacity: 1; } 100% { transform: translateX(1100px) translateY(40px); opacity: 0; } }
    #attack-effect-container { position: absolute; inset: 0; pointer-events: none; overflow: hidden; z-index: 15; }
    .attack-wind-slash { position: absolute; width: 100px; height: 12px; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent); border-radius: 6px; opacity: 0; }
    .attack-wind-slash.player-attack { animation: player-attack-wind 0.4s ease-out forwards; }
    @keyframes player-attack-wind { 0% { opacity: 0; bottom: 150px; left: 25%; transform: rotate(-30deg) scale(0.5); } 50% { opacity: 1; transform: rotate(-30deg) scale(1.2); } 100% { opacity: 0; bottom: 350px; left: 65%; transform: rotate(-30deg) scale(0.5); } }
    .attack-wind-slash.enemy-attack { animation: enemy-attack-wind 0.4s ease-out forwards; }
    @keyframes enemy-attack-wind { 0% { opacity: 0; top: 150px; right: 25%; transform: rotate(-30deg) scale(0.5); } 50% { opacity: 1; transform: rotate(-30deg) scale(1.2); } 100% { opacity: 0; top: 350px; right: 65%; transform: rotate(-30deg) scale(0.5); } }

    #player-area { position: absolute; bottom: 40px; left: 5%; display: flex; flex-direction: column; align-items: center; gap: 5px; }
    .player-sprites-container { display: flex; align-items: flex-end; }
    .battle-character-sprite { width: 220px; height: 220px; object-fit: contain; position: relative; z-index: 5; }
    #player-pokemon-sprite {
        width: 160px;
        height: 160px;
        transform: translateX(-40px) scaleX(-1); /* Mantém a inversão para o Pokémon do jogador */
    }
    .battle-monster-sprite { width: 180px; height: 180px; object-fit: contain; }
    #enemy-area { position: absolute; top: 80px; right: 10%; display: flex; flex-direction: column; align-items: center; gap: 5px; }
    
    /* ===== ESTILOS PARA AS POKEBOLAS DOS TREINADORES ===== */
    .trainer-pokeballs-container {
        display: flex;
        gap: 6px;
        margin-top: -35px;
        margin-bottom: 10px;
        position: relative;
        z-index: 5;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }
    
    .trainer-pokeballs-container::before {
        content: '';
        position: absolute;
        left: -40px;
        right: -40px;
        top: 50%;
        transform: translateY(-50%);
        height: 10px;
        background: #688048;
        border-top: 2px solid #90a860;
        border-bottom: 2px solid #405030;
        z-index: -1;
        border-radius: 2px;
    }
    
    .pokeball-icon {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #405030;
        background-color: rgba(0, 0, 0, 0.3); /* Cor de slot vazio */
        box-shadow: inset 1px 1px 2px rgba(0,0,0,0.4);
        position: relative;
        overflow: hidden;
    }

    .pokeball-icon.filled {
        background: linear-gradient(to bottom, #f07070 45%, #ffffff 55%); /* Vermelho em cima, branco embaixo */
        border-color: #333;
    }

    .pokeball-icon.filled::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #333;
        transform: translateY(-50%);
    }

    .pokeball-icon.filled::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 6px;
        height: 6px;
        background-color: white;
        border: 1px solid #333;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0 0 2px #555;
    }
    /* ============================================================= */

    /* ===== ESTILOS PARA A NOVA INTRODUÇÃO DE BATALHA ===== */
    .battle-arena { overflow: hidden; }
    #player-pokemon-sprite, #enemy-monster-sprite { opacity: 0; transition: opacity 0.5s ease-in-out; }
    #player-pokemon-sprite.visible, #enemy-monster-sprite.visible { opacity: 1; }
    #player-trainer-sprite { transform: translateX(-200px); }
    #enemy-trainer-sprite { transform: translateX(200px); }
    .sprite-intro-animation {
        animation-duration: 1.5s;
        animation-fill-mode: forwards;
        animation-timing-function: ease-out;
    }
    @keyframes slideInFromLeft { from { transform: translateX(-200px); } to { transform: translateX(30px); } }
    @keyframes slideInFromRight { from { transform: translateX(200px); } to { transform: translateX(0); } }
    @keyframes slideOutToLeft { from { transform: translateX(30px); } to { transform: translateX(-200px); } }
    @keyframes slideOutToRight { from { transform: translateX(0); } to { transform: translateX(200px); } }
    /* ======================================================= */

    .intro-dialog-box { position: absolute; bottom: 10px; left: 10px; width: calc(100% - 20px); height: 180px; background: url('https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png') no-repeat right 20px bottom 20px, #e0e0e0; background-size: 80px 80px, auto; border: 4px solid #606060; box-shadow: inset 0 0 0 4px #a0a0a0; border-radius: 12px; padding: 20px 180px 20px 20px; color: #303030; font-size: 1.5rem; line-height: 1.3; display: flex; align-items: center; visibility: hidden; opacity: 0; transition: opacity 0.5s ease-in-out; z-index: 100; font-weight: bold; }
    .intro-dialog-box.visible { visibility: visible; opacity: 1; }
    
    .status-box-pokemon { background: var(--dialog-bg); border: 2px solid var(--dialog-border); box-shadow: 2px 2px 0px rgba(0,0,0,0.3); padding: 8px 12px; color: var(--dialog-text); font-size: 0.8rem; z-index: 10; min-width: 220px; text-align: left; }
    .status-box-pokemon h2 { font-size: 0.9rem; margin-bottom: 3px; color: var(--dialog-text); text-shadow: none; }
    .hp-bar-container { width: 100%; height: 10px; background-color: #505050; border: 1px solid var(--dialog-border); position: relative; margin-top: 3px; margin-bottom: 3px; }
    .hp-bar-fill { height: 100%; background: var(--hp-color); transition: width 0.5s ease-out; }
    .hp-bar-text { position: absolute; inset: 0; font-size: 0.6rem; color: white; text-shadow: 1px 1px #000; line-height: 8px; text-align: center; }
    .hp-bar-fill.mp { background: var(--mp-color); }
    .hp-bar-fill.xp { background: var(--xp-color); }
    .log-player { color: #5080c0; } .log-enemy { color: #c05050; } .log-system { color: #707070; } .log-poison { color: #9c27b0; } .log-heal { color: #7cb342; } .log-crit, .log-lvlup { color: var(--text-highlight); }
    .damage-popup { position: absolute; font-size: 2rem; font-weight: bold; color: #ff4500; text-shadow: 2px 2px #000; animation: damagePopup 1s forwards; pointer-events: none; } .crit { color: var(--text-highlight); } .heal { color: #7cb342; } .shake { animation: shake 0.4s; } .flash-red { animation: flashRed 0.2s; }
    @keyframes damagePopup { 0% { transform: translate(-50%, 0); opacity: 1; } 100% { transform: translate(-50%, -80px); opacity: 0; } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } }
    @keyframes flashRed { 50% { filter: brightness(3); } }
    @keyframes stage-intro { 0% { opacity: 0; transform: scale(0.5); } 70% { opacity: 1; transform: scale(1.2); } 100% { opacity: 1; transform: scale(1); } }
    .modal-overlay { position: fixed; inset: 0; z-index: 102; display: flex; justify-content: center; align-items: center; background: rgba(0,0,0,0.8); opacity: 0; visibility: hidden; transition: all 0.3s ease; }
    .modal-overlay.is-visible { opacity: 1; visibility: visible; }
    .modal-box { background: var(--ui-main); border: 4px solid var(--ui-border-dark); box-shadow: inset 0 0 0 4px var(--ui-border-light); padding: 30px; max-width: 600px; width: 90%; color: var(--text-light); transform: scale(0.9); transition: transform 0.3s ease; text-align: center;}
    .modal-overlay.is-visible .modal-box { transform: scale(1); }

    .dialog-box { position: absolute; bottom: 10px; left: 10px; width: calc(100% - 20px); height: 180px; display: flex; background: transparent; border: none; padding: 0; visibility: hidden; }
    .dialog-box.visible { visibility: visible; }
    .battle-log { flex: 1; height: 100%; background-color: #2E4C7A; color: white; padding: 20px; font-size: 1.1rem; line-height: 1.5; border: 4px solid #5A6A83; box-shadow: inset 0 0 0 4px #A5AFC1; border-radius: 12px; margin-right: -10px; z-index: 2; }
    .actions-menu { width: 300px; height: 100%; background-color: #F8F8F8; border: 4px solid #5A6A83; box-shadow: inset 0 0 0 4px #A5AFC1; border-radius: 12px; padding: 20px; z-index: 1; }
    .actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px 25px; height: 100%; width: 100%; align-content: center; color: #333; }
    .action-item { font-size: 1.1rem; cursor: pointer; position: relative; }
    .action-item:hover { color: #000; }
    .action-item.active::before { content: '►'; font-size: 1rem; color: #333; position: absolute; left: -20px; top: 2px; }
    .action-item.disabled { color: #999; cursor: not-allowed; }
    #bag-screen { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 700px; height: 480px; background: none; display: none; z-index: 100; pointer-events: all; }
    .bag-container { display: flex; width: 100%; height: 100%; background-color: transparent; border: none; box-shadow: none; padding: 0; gap: 0; }
    .bag-left-panel { flex-basis: 35%; height: 100%; background-color: #E0E0E0; border: 2px solid #606060; border-right: none; border-bottom: none; display: flex; flex-direction: column; justify-content: center; align-items: center; padding-bottom: 0; border-radius: 8px 0 0 0; box-shadow: inset 0 0 0 2px #A0A0A0; }
    .bag-left-panel img { width: 180px; height: auto; image-rendering: pixelated; }
    .bag-right-panel { flex-basis: 65%; height: 100%; display: flex; flex-direction: column; gap: 0; }
    .bag-item-list-container { height: 65%; background-color: #E0E0E0; border: 2px solid #606060; border-left: none; border-bottom: none; border-radius: 0 8px 0 0; padding: 10px; overflow-y: auto; color: #333; font-size: 1rem; box-shadow: inset 0 0 0 2px #A0A0A0; display: flex; flex-direction: column; }
    .bag-item-list-container::-webkit-scrollbar { width: 12px; }
    .bag-item-list-container::-webkit-scrollbar-track { background: #C0C0C0; border-radius: 6px; }
    .bag-item-list-container::-webkit-scrollbar-thumb { background-color: #808080; border-radius: 6px; border: 2px solid #606060; }
    .bag-item-list-container::-webkit-scrollbar-thumb:hover { background-color: #505050; }
    .bag-item { display: flex; justify-content: space-between; padding: 5px 8px; cursor: pointer; position: relative; white-space: nowrap; align-items: center; }
    .bag-item:hover, .bag-item.active { background-color: #A0A0A0; color: #333; }
    .bag-item.active::before { content: '►'; font-size: 0.8rem; color: #333; position: absolute; left: -15px; top: 50%; transform: translateY(-50%); }
    #bag-cancel-button { padding: 5px 8px; cursor: pointer; margin-top: auto; }
    #bag-cancel-button.active::before { content: '►'; font-size: 0.8rem; color: #333; position: absolute; left: -15px; top: 50%; transform: translateY(-50%); }
    #bag-cancel-button:hover, #bag-cancel-button.active { background-color: #A0A0A0; color: #333; }
    .bag-item-description-container { height: 35%; background-color: #2E4C7A; color: white; padding: 15px; font-size: 0.9rem; line-height: 1.4; border: 2px solid #606060; border-left: none; border-top: none; border-radius: 0 0 8px 0; box-shadow: inset 0 0 0 2px #5A6A83; }
    .bag-category-title { background-color: #808080; color: white; padding: 5px 10px; border: 2px solid #606060; border-bottom: none; border-radius: 8px 8px 0 0; position: absolute; top: -27px; left: 0; font-size: 0.8rem; z-index: 101; box-shadow: inset 0 0 0 2px #A0A0A0; }
    .bag-title-wrapper { position: absolute; top: calc(50% - 240px); left: calc(50% - 350px); width: 700px; pointer-events: none; display: none; }
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
    .team-name { font-size: 1em; }
    .team-level { font-size: 0.9em; }
    .team-hp-container { display: flex; align-items: center; margin-bottom: 2px; }
    .team-hp-label { background-color: #daac20; color: white; font-size: 0.7em; padding: 1px 3px; border-radius: 4px; margin-right: 4px; font-weight: bold; }
    .team-hp-bar-background { flex-grow: 1; background-color: #555; border-radius: 5px; height: 8px; padding: 2px; border: 1px solid #333; }
    .team-hp-bar-foreground { background-color: #30a850; height: 100%; border-radius: 3px; }
    .team-hp-values { text-align: right; font-size: 0.9em; }
    .team-dialog-box { background-color: #f8f8f8; border: 4px solid #9ca4a5; border-radius: 8px; padding: 12px; margin-top: 12px; color: #333; position: relative; }
    .back-button { position: absolute; right: 10px; bottom: 10px; background-color: #dcdcdc; border: 2px solid #aaa; border-radius: 10px; padding: 5px 10px; font-size: 0.8em; cursor: pointer; }
    .back-button:hover { background-color: #c8c8c8; }
    .back-button::before { content: ' '; display: inline-block; width: 20px; height: 20px; background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAARJJREFUWEft1M8rRFEUx/HPGYlCYigbRSkL2VjYKBtLLGxsJCsbS4uFsiTbWVhZ2A8wGclKWCh+gI3hGzzn3p25u081cO7c3/M+53TOPSC5ubnZzDqe4h1nOML5D/AFLpGNJ/hHtzWs4S1eYQ8fWcUzvEc73uI/XtGP7/iN7xVjfE8xiM8L+MIMbnGPpP4bzmItPpxgPjYh7B5iFVtYwS528Yr4p2ACh/CEF+xhI/txhOAEZ1mBbfyjeDXA7xU8wxqWsz/hG/4hHq/xhW4M4R5b2MY6vmMGGxnH+H5iBC8wxq9G8A57eMI+vrGJlYxjDAcxgyUM43+K4T8Q4/gH8w/u1KxVfJIAAAAASUVORK5CYII='); background-size: contain; vertical-align: middle; margin-right: 5px; }

</style>
</head>
<body>

<div id="story-intro">
    <p id="story-text"></p>
    <h1 id="stage-title">FASE 1</h1>
    <div class="pokeball"></div><div class="pokeball"></div><div class="pokeball"></div><div class="pokeball"></div><div class="pokeball"></div>
</div>

<div class="battle-screen">
    <div class="battle-arena">
        
        <div id="player-area">
            <div class="player-sprites-container">
                <img src="{{ asset($character->avatar) }}" id="player-trainer-sprite" class="battle-character-sprite" alt="Sprite do Treinador">
                <img src="" id="player-pokemon-sprite" class="battle-monster-sprite" alt="Sprite do Pokémon do Jogador">
            </div>
            <div class="trainer-pokeballs-container">
                <div class="pokeball-icon filled"></div>
                <div class="pokeball-icon filled"></div>
                <div class="pokeball-icon"></div>
                <div class="pokeball-icon"></div>
                <div class="pokeball-icon"></div>
                <div class="pokeball-icon"></div>
            </div>
            <div id="player-battle-status" class="status-box-pokemon">
                <h2 id="playerName"></h2>
                <div class="hp-bar-container">
                    <div class="hp-bar-fill" id="playerHpBar"></div>
                    <div class="hp-bar-text" id="playerHpText"></div>
                </div>
                <div class="hp-bar-container">
                    <div class="hp-bar-fill mp" id="playerMpBar"></div>
                    <div class="hp-bar-text" id="playerMpText"></div>
                </div>
                <div class="hp-bar-container">
                    <div class="hp-bar-fill xp" id="playerXpBar"></div>
                    <div class="hp-bar-text" id="playerXpText"></div>
                </div>
            </div>
        </div>
        <div id="enemy-area">
            <img src="{{ asset('img/lt.webp') }}" id="enemy-trainer-sprite" class="battle-character-sprite" alt="Sprite do Treinador Inimigo">
             <div class="trainer-pokeballs-container">
                <div class="pokeball-icon filled"></div>
                <div class="pokeball-icon"></div>
                <div class="pokeball-icon"></div>
                <div class="pokeball-icon"></div>
                <div class="pokeball-icon"></div>
                <div class="pokeball-icon"></div>
            </div>
            <img src="" id="enemy-monster-sprite" class="battle-monster-sprite" alt="Sprite do Monstro Inimigo">
            <div id="enemy-battle-status" class="status-box-pokemon">
                <h2 id="enemyName"></h2>
                <div class="hp-bar-container">
                    <div class="hp-bar-fill" id="enemyHpBar"></div>
                    <div class="hp-bar-text" id="enemyHpText"></div>
                </div>
            </div>
        </div>
        <div id="attack-effect-container"><div class="attack-wind-slash"></div></div>
    </div>
    <div id="wind-effect-container"><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div><div class="wind-particle"></div></div>
    
    <div class="bag-title-wrapper">
        <div class="bag-category-title">ITENS</div>
    </div>

    <div id="bag-screen">
        <div class="bag-container">
            <div class="bag-left-panel">
                <img src="{{ asset('img/nicolas.png') }}" alt="Bolsa">
            </div>
            <div class="bag-right-panel">
                <div class="bag-item-list-container" id="bag-item-list">
                </div>
                <div class="bag-item-description-container" id="bag-item-description">
                    <p>Selecione um item para ver a descrição.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div id="team-screen">
        <div class="team-layout">
            <div id="team-selected-pokemon-panel" class="team-selected-pokemon-panel"></div>
            <div id="team-list-panel" class="team-list-panel"></div>
        </div>
        <div id="team-dialog-box" class="team-dialog-box">
            Escolha um Pokémon.
            <div id="team-back-button" class="back-button">VOLTAR</div>
        </div>
    </div>

    <div id="intro-dialog-box" class="intro-dialog-box">
        <p id="intro-dialog-text"></p>
    </div>

    <div class="dialog-box">
        <div class="battle-log" id="battleLog"><p></p></div>
        <div class="actions-menu"><div class="actions-grid" id="actionsGrid"></div></div>
    </div>
</div>
<div class="modal-overlay" id="endgameModal"></div>

<script>
const Intro = {
    storyContainer: document.getElementById('story-intro'), storyTextEl: document.getElementById('story-text'), stageTitleEl: document.getElementById('stage-title'), battleScreenEl: document.querySelector('.battle-screen'), typewriter(text, i = 0) { if (i < text.length) { this.storyTextEl.innerHTML += text.charAt(i); setTimeout(() => this.typewriter(text, i + 1), 50); } else { setTimeout(() => this.showStageTitle(), 2000); } }, showStageTitle() { this.stageTitleEl.classList.add('visible'); setTimeout(() => this.hideIntro(), 2500); }, hideIntro() { this.storyContainer.style.opacity = '1'; this.storyContainer.classList.add('hide-intro-animation'); this.storyContainer.addEventListener('animationend', () => { this.storyContainer.remove(); this.showBattleScreen(); }, { once: true }); }, showBattleScreen() { this.battleScreenEl.classList.add('visible'); Game.init(); }, start() { this.typewriter(`A AVENTURA DE {{ $character->name }} COMEÇA...`); }
};

const Game = {
    state: {
        trainer: { name: "{{ $character->name }}", gold: parseInt("{{ $character->gold ?? 0 }}", 10) },
        
        playerTeam: {!! $playerTeamJson !!},
        
        inventory: { potion: parseInt("{{ $character->potions ?? 0 }}", 10), pokeball: parseInt("{{ $character->pokeballs ?? 0 }}", 10), greatball: parseInt("{{ $character->greatballs ?? 0 }}", 10), thunderstone: parseInt("{{ $character->thunderstones ?? 0 }}", 10) },
        enemy: {},
        enemies: [ { name: "Bulbasaur", hp: 75, attack: 20, defense: 25, xp: 40, gold: 25, level: 1, img: "https://i.gifer.com/origin/fe/fe4ebd8a9c0547e94000a9c759acf591_w200.gif" }, { name: "Totodile", hp: 75, attack: 20, defense: 25, xp: 40, gold: 25, level: 1, img: "https://media.tenor.com/lr6evdW49pcAAAAj/totodile-pokemon.gif" }, { name: "Squirtle", hp: 75, attack: 20, defense: 25, xp: 40, gold: 25, level: 1, img: "https://i.gifer.com/origin/d8/d83e9951f28fc811c1166b16dcaec930_w200.gif" } ],
        currentEnemyIndex: 0, gameState: 'PLAYER_TURN', menuState: 'main', activeBagItemIndex: 0, activeTeamListIndex: 0,
    },
    get activePokemon() { return this.state.playerTeam[0]; },
    elements: {
        player: { pokemonSprite: document.getElementById('player-pokemon-sprite'), name: document.getElementById('playerName'), hpBar: document.getElementById('playerHpBar'), hpText: document.getElementById('playerHpText'), mpBar: document.getElementById('playerMpBar'), mpText: document.getElementById('playerMpText'), xpBar: document.getElementById('playerXpBar'), xpText: document.getElementById('playerXpText'), actions: document.getElementById('actionsGrid'), pokeballs: document.querySelector('#player-area .trainer-pokeballs-container') }, 
        enemy: { trainerSprite: document.getElementById('enemy-trainer-sprite'), monsterSprite: document.getElementById('enemy-monster-sprite'), name: document.getElementById('enemyName'), hpBar: document.getElementById('enemyHpBar'), hpText: document.getElementById('enemyHpText'), pokeballs: document.querySelector('#enemy-area .trainer-pokeballs-container') }, 
        log: document.getElementById('battleLog'), 
        modal: { container: document.getElementById('endgameModal') }, 
        attackEffect: document.querySelector('.attack-wind-slash'),
        bag: { screen: document.getElementById('bag-screen'), list: document.getElementById('bag-item-list'), description: document.getElementById('bag-item-description'), },
        team: { screen: document.getElementById('team-screen'), selectedPanel: document.getElementById('team-selected-pokemon-panel'), listPanel: document.getElementById('team-list-panel'), backButton: document.getElementById('team-back-button'), dialog: document.getElementById('team-dialog-box') },
        introDialogBox: document.getElementById('intro-dialog-box'),
        introDialogText: document.getElementById('intro-dialog-text'),
    },
    menus: {
        main: { fight: { name: 'Lutar', type: 'submenu', target: 'fight' }, bag: { name: 'Bolsa', type: 'open_bag' }, pokemon: { name: 'Pokémon', type: 'open_team_screen' }, run: { name: 'Sair', type: 'action', actionKey: 'run' } },
        fight: { tackle: { name: 'Ataque', type: 'action', actionKey: 'attack' }, skill: { name: 'Magia', type: 'action', actionKey: 'skill' }, defend: { name: 'Defender', type: 'action', actionKey: 'defend' }, back: { name: 'Voltar', type: 'back' } },
    },
    actions: { attack: { cost: 0, type: 'mp', target: 'enemy', basePower: 1, stat: 'attack' }, skill: { cost: 10, type: 'mp', target: 'enemy', basePower: 1.5, stat: 'sp_attack' }, defend: { cost: 0, type: 'mp', target: 'player', stat: 'defend_stance' }, potion: { cost: 1, type: 'inventory', target: 'player', basePower: 50, stat: 'heal' }, 'catch': { cost: 1, type: 'inventory', target: 'enemy', stat: 'catch' }, run: { cost: 0, stat: 'run' } },
    items: { potion: { name: "POÇÃO", description: "Restaura 50 de HP de um Pokémon.", actionKey: 'potion' }, pokeball: { name: "POKÉ BALL", description: "Um item para tentar capturar Pokémon selvagens.", actionKey: 'catch' }, greatball: { name: "GREAT BALL", description: "Uma Poké Ball com uma taxa de captura maior.", actionKey: 'catch' }, thunderstone: { name: "PEDRA DO TROVÃO", description: "Uma pedra peculiar. Não pode ser usada em batalha.", actionKey: 'none' } },

    init() {
        this.sanitizeStats();
        this.setupKeyboardNavigation();
        this.startBattleIntro();
    },

    startBattleIntro() {
        const playerTrainer = document.getElementById('player-trainer-sprite');
        const enemyTrainer = this.elements.enemy.trainerSprite;

        document.getElementById('player-battle-status').style.opacity = 0;
        document.getElementById('enemy-battle-status').style.opacity = 0;
        
        document.querySelector('.dialog-box').classList.remove('visible');

        playerTrainer.style.animationName = 'slideInFromLeft';
        enemyTrainer.style.animationName = 'slideInFromRight';
        playerTrainer.classList.add('sprite-intro-animation');
        enemyTrainer.classList.add('sprite-intro-animation');

        playerTrainer.addEventListener('animationend', () => {
            this.elements.player.pokeballs.style.opacity = '1';
            this.elements.enemy.pokeballs.style.opacity = '1';

            this.elements.introDialogBox.classList.add('visible');
            this.elements.introDialogText.innerHTML = `LIDER LT. SURGE<br>gostaria de batalhar!`;

            setTimeout(() => {
                this.elements.introDialogBox.classList.remove('visible');
                
                playerTrainer.style.animationName = 'slideOutToLeft';
                enemyTrainer.style.animationName = 'slideOutToRight';
                
                this.elements.player.pokeballs.style.opacity = '0';
                this.elements.enemy.pokeballs.style.opacity = '0';

                playerTrainer.addEventListener('animationend', () => {
                    playerTrainer.style.display = 'none';
                    enemyTrainer.style.display = 'none';

                    const playerPokemon = this.elements.player.pokemonSprite;
                    const enemyPokemon = this.elements.enemy.monsterSprite;
                    
                    playerPokemon.classList.add('visible');
                    enemyPokemon.classList.add('visible');
                    
                    document.getElementById('player-battle-status').style.transition = 'opacity 0.5s';
                    document.getElementById('enemy-battle-status').style.transition = 'opacity 0.5s';
                    document.getElementById('player-battle-status').style.opacity = 1;
                    document.getElementById('enemy-battle-status').style.opacity = 1;
                    document.querySelector('.dialog-box').classList.add('visible');

                    this.loadEnemy();
                    this.updateUI();
                    this.renderMenu();
                    this.setGameState('PLAYER_TURN');

                }, { once: true });

            }, 3500);

        }, { once: true });
    },

    renderMenu() { const menuKey = this.state.menuState; const menuItems = this.menus[menuKey]; const grid = this.elements.player.actions; grid.innerHTML = ''; if (menuKey === 'main') { this.logMessage(`O que ${this.activePokemon.name} fará?`); } else if (menuKey === 'fight') { this.logMessage('Selecione um ataque.'); } let isFirst = true; for (const key in menuItems) { const itemData = menuItems[key]; const itemEl = document.createElement('div'); itemEl.className = 'action-item'; if (key === 'bag' && menuKey === 'main') { const totalItems = Object.values(this.state.inventory).reduce((a, b) => a + b, 0); itemEl.textContent = `${itemData.name} (${totalItems})`; } else { itemEl.textContent = itemData.name; } if (isFirst) { itemEl.classList.add('active'); isFirst = false; } itemEl.onclick = () => { switch(itemData.type) { case 'submenu': this.state.menuState = itemData.target; this.renderMenu(); break; case 'action': this.executeTurn(itemData.actionKey); break; case 'back': this.state.menuState = 'main'; this.renderMenu(); break; case 'open_bag': this.openBag(); break; case 'open_team_screen': this.openTeamScreen(); break; } }; grid.appendChild(itemEl); } },
    openBag() { if (this.state.gameState !== 'PLAYER_TURN') return; this.elements.bag.screen.style.display = 'block'; this.state.activeBagItemIndex = 0; this.renderBag(); this.setGameState('BAG_OPEN'); },
    closeBag() { this.elements.bag.screen.style.display = 'none'; this.setGameState('PLAYER_TURN'); this.renderMenu(); },
    renderBag() { const listEl = this.elements.bag.list; listEl.innerHTML = ''; const availableItems = Object.keys(this.state.inventory).filter(itemKey => this.state.inventory[itemKey] > 0); availableItems.forEach((itemKey, index) => { const quantity = this.state.inventory[itemKey]; const itemData = this.items[itemKey]; if (!itemData) return;
        const itemEl = document.createElement('div'); itemEl.className = 'bag-item'; itemEl.dataset.itemKey = itemKey; itemEl.innerHTML = `<span>${itemData.name}</span> <span style="font-size: 0.8em;">x${quantity}</span>`; itemEl.onclick = () => { this.useItem(itemKey); }; itemEl.onmouseenter = () => { this.state.activeBagItemIndex = index; this.updateBagSelection(); }; listEl.appendChild(itemEl); }); const cancelButton = document.createElement('div'); cancelButton.id = 'bag-cancel-button'; cancelButton.textContent = 'CANCEL'; cancelButton.onclick = () => this.closeBag(); cancelButton.onmouseenter = () => { this.state.activeBagItemIndex = availableItems.length; this.updateBagSelection(); }; listEl.appendChild(cancelButton); this.updateBagSelection(); },
    updateBagSelection() { const allBagItems = this.elements.bag.list.querySelectorAll('.bag-item, #bag-cancel-button'); allBagItems.forEach((item, index) => { if (index === this.state.activeBagItemIndex) { item.classList.add('active'); if (item.id === 'bag-cancel-button') { this.elements.bag.description.innerHTML = '<p>Fechar a bolsa.</p>'; } else { const itemKey = item.dataset.itemKey; const itemData = this.items[itemKey]; this.elements.bag.description.innerHTML = `<p>${itemData ? itemData.description : 'Item desconhecido.'}</p>`; } } else { item.classList.remove('active'); } }); if (allBagItems[this.state.activeBagItemIndex]) { allBagItems[this.state.activeBagItemIndex].scrollIntoView({ block: 'nearest' }); } },
    useItem(itemKey) { if (this.state.inventory[itemKey] <= 0) { this.logMessage(`Você não tem ${this.items[itemKey].name}!`); this.closeBag(); return; } const itemData = this.items[itemKey]; if (!itemData) return; const actionKey = itemData.actionKey; if (actionKey === 'none') { this.logMessage('Este item não pode ser usado em batalha!'); return; } this.closeBag(); this.executeTurn(actionKey, itemKey); },
    openTeamScreen() { if (this.state.gameState !== 'PLAYER_TURN' && this.state.gameState !== 'MUST_SWITCH') return; this.elements.team.screen.style.display = 'block'; this.state.activeTeamListIndex = 0; const dialogTextContainer = this.elements.team.dialog.childNodes[0]; if (this.state.gameState === 'MUST_SWITCH') { dialogTextContainer.nodeValue = 'Escolha o próximo Pokémon. '; } else { dialogTextContainer.nodeValue = 'Escolha um Pokémon. '; } this.renderTeamScreen(); if (this.state.gameState !== 'MUST_SWITCH') { this.setGameState('TEAM_SCREEN_OPEN'); } },
    closeTeamScreen() { this.elements.team.screen.style.display = 'none'; if (this.state.gameState !== 'MUST_SWITCH') { this.setGameState('PLAYER_TURN'); this.renderMenu(); } },
    renderTeamScreen() { const selectedPanel = this.elements.team.selectedPanel; const listPanel = this.elements.team.listPanel; selectedPanel.innerHTML = ''; listPanel.innerHTML = ''; const battlingPokemon = this.state.playerTeam[0]; selectedPanel.innerHTML = this.createPokemonCard(battlingPokemon, true, false); let currentListIndex = 0; this.state.playerTeam.forEach((pokemon, index) => { if (index > 0) { const isActive = (currentListIndex === this.state.activeTeamListIndex); listPanel.insertAdjacentHTML('beforeend', this.createPokemonCard(pokemon, false, isActive)); currentListIndex++; } }); this.elements.team.backButton.onclick = () => this.closeTeamScreen(); this.addTeamClickListeners(); this.updateTeamSelection(); },
    createPokemonCard(pokemon, isSelected, isActive) { const hpPercentage = pokemon.isFainted ? 0 : (pokemon.hp / pokemon.maxHp) * 100; const activeClass = isActive ? 'active' : ''; return ` <div class="pokemon-card ${isSelected ? 'selected' : ''} ${pokemon.isFainted ? 'fainted' : ''} ${activeClass}" data-index="${this.state.playerTeam.indexOf(pokemon)}"> <img src="${pokemon.sprite}" alt="${pokemon.name}" class="${isSelected ? 'sprite-large' : 'sprite-small'}"> <div class="team-info"> <div class="team-name-level"><span class="team-name">${pokemon.name.toUpperCase()}</span><span class="team-level">Lv${pokemon.level}</span></div> <div class="team-hp-container"><span class="team-hp-label">HP</span><div class="team-hp-bar-background"><div class="team-hp-bar-foreground" style="width: ${hpPercentage}%;"></div></div></div> <div class="team-hp-values"><span>${Math.ceil(pokemon.hp)}/${pokemon.maxHp}</span></div> </div> </div> `; },
    updateTeamSelection() { const allTeamCards = this.elements.team.listPanel.querySelectorAll('.pokemon-card'); allTeamCards.forEach((card, index) => { if (index === this.state.activeTeamListIndex) { card.classList.add('active'); card.scrollIntoView({ block: 'nearest' }); } else { card.classList.remove('active'); } }); },
    addTeamClickListeners() { this.elements.team.listPanel.querySelectorAll('.pokemon-card').forEach((card, listIndex) => { card.onclick = () => { const teamIndex = parseInt(card.dataset.index, 10); this.swapPokemon(teamIndex); }; card.onmouseenter = () => { this.state.activeTeamListIndex = listIndex; this.updateTeamSelection(); }; }); },
    swapPokemon(newIndex) { if (newIndex === 0) { this.logMessage('Este Pokémon já está em batalha!'); return; } const targetPokemon = this.state.playerTeam[newIndex]; if (targetPokemon.isFainted) { this.logMessage(`${targetPokemon.name} não pode batalhar!`); return; } const wasMandatorySwitch = this.state.gameState === 'MUST_SWITCH'; this.closeTeamScreen(); this.logMessage(`Volte, ${this.activePokemon.name}!`); setTimeout(() => { const temp = this.state.playerTeam[0]; this.state.playerTeam[0] = this.state.playerTeam[newIndex]; this.state.playerTeam[newIndex] = temp; this.updateUI(); this.logMessage(`Vá, ${this.activePokemon.name}!`); if (!wasMandatorySwitch) { setTimeout(() => this.enemyTurn(), 1500); } else { this.setGameState('PLAYER_TURN'); this.renderMenu(); } }, 1000); },
    
    executeTurn(actionKey, itemKey = null) { if (this.state.gameState !== 'PLAYER_TURN' && this.state.gameState !== 'BAG_OPEN') return; const action = this.actions[actionKey]; if (!action) { this.logMessage(`Ação '${actionKey}' não implementada.`); return; } if (action.type === 'mp' && this.activePokemon.mp < action.cost) { this.logMessage('MP INSUFICIENTE!'); return; } if (action.type === 'inventory' && this.state.inventory[itemKey] <= 0) { this.logMessage('SEM ESTE ITEM NA BOLSA!'); return; } this.setGameState('PROCESSING'); if (action.type === 'mp') this.activePokemon.mp -= action.cost; if (action.type === 'inventory') this.state.inventory[itemKey]--; const itemName = itemKey ? this.items[itemKey].name : (actionKey === 'attack' ? 'Ataque' : 'Magia'); this.logMessage(`${this.activePokemon.name} usa ${itemName}!`); if (action.stat === 'heal') { const healAmount = action.basePower; this.activePokemon.hp = Math.min(this.activePokemon.maxHp, this.activePokemon.hp + healAmount); this.showPopup(healAmount, this.elements.player.pokemonSprite, true); this.logMessage(`${this.activePokemon.name} recuperou ${healAmount} HP.`); setTimeout(() => this.enemyTurn(), 1500); } else if (action.stat === 'defend_stance') { this.logMessage(`${this.activePokemon.name} está se defendendo!`); setTimeout(() => this.enemyTurn(), 1500); } else if (action.stat === 'run') { this.logMessage(`${this.state.trainer.name} tenta fugir...`); setTimeout(() => this.gameOver(false), 1500); return; } else if (action.stat === 'catch') { this.logMessage(`Você atira a ${itemName}...`); setTimeout(() => { this.logMessage('Oh, não! O Pokémon escapou!'); this.updateUI(); setTimeout(() => this.enemyTurn(), 1500); }, 2000); } else { this.triggerAttackEffect('player'); const power = this.activePokemon[action.stat] * action.basePower; let damage = this.calculateDamage(power, this.state.enemy.defense); if (Math.random() < 0.15) { damage = Math.floor(damage * 1.5); this.logMessage('ACERTO CRÍTICO!', 'log-crit'); this.elements.enemy.monsterSprite.classList.add('shake'); setTimeout(() => this.elements.enemy.monsterSprite.classList.remove('shake'), 400); } this.state.enemy.hp -= damage; this.elements.enemy.monsterSprite.classList.add('flash-red'); setTimeout(() => this.elements.enemy.monsterSprite.classList.remove('flash-red'), 200); this.showPopup(damage, this.elements.enemy.monsterSprite, false, damage > power); if (this.state.enemy.hp <= 0) { const defeatedEnemy = this.state.enemies[this.state.currentEnemyIndex]; this.logMessage(`${defeatedEnemy.name.toUpperCase()} DERROTADO!`); this.state.trainer.gold += defeatedEnemy.gold; this.logMessage(`+${defeatedEnemy.gold} OURO!`); this.gainXP(defeatedEnemy.xp); setTimeout(() => this.nextEnemy(), 2000); return; } setTimeout(() => this.enemyTurn(), 1500); } this.updateUI(); },
    enemyTurn() { this.setGameState('ENEMY_TURN'); const enemy = this.state.enemy; this.logMessage(`${enemy.name.toUpperCase()} ATACA!`); this.triggerAttackEffect('enemy'); let damage = this.calculateDamage(enemy.attack, this.activePokemon.defense); this.activePokemon.hp -= damage; this.elements.player.pokemonSprite.classList.add('flash-red'); setTimeout(() => this.elements.player.pokemonSprite.classList.remove('flash-red'), 200); this.showPopup(damage, this.elements.player.pokemonSprite, false); this.activePokemon.mp = Math.min(this.activePokemon.maxMp, this.activePokemon.mp + 5); this.updateUI(); if (this.activePokemon.hp <= 0) { this.activePokemon.isFainted = true; this.logMessage(`${this.activePokemon.name} foi derrotado!`, 'log-system'); const availablePokemon = this.state.playerTeam.filter(p => !p.isFainted); if (availablePokemon.length > 0) { this.setGameState('MUST_SWITCH'); this.openTeamScreen(); } else { this.gameOver(false); } return; } setTimeout(() => { this.state.menuState = 'main'; this.renderMenu(); this.setGameState('PLAYER_TURN'); }, 1000); },
    setGameState(newState) { this.state.gameState = newState; const menuContainer = this.elements.player.actions.parentElement; menuContainer.style.pointerEvents = (newState === 'PLAYER_TURN') ? 'auto' : 'none'; menuContainer.style.opacity = (newState === 'PLAYER_TURN') ? '1' : '0.7'; document.querySelector('.bag-title-wrapper').style.display = (newState === 'BAG_OPEN') ? 'block' : 'none'; },
    updateUI() { const player = this.activePokemon; const { player: pEl, enemy: eEl } = this.elements; pEl.pokemonSprite.src = player.sprite; pEl.name.innerHTML = `${player.name} <span style="font-size:0.7em;">LV ${player.level}</span>`; pEl.hpBar.style.width = `${Math.max(0, player.hp / player.maxHp * 100)}%`; pEl.hpText.textContent = `${Math.max(0, Math.ceil(player.hp))}/${player.maxHp}`; pEl.mpBar.style.width = `${Math.max(0, player.mp / player.maxMp * 100)}%`; pEl.mpText.textContent = `MP: ${Math.max(0, Math.ceil(player.mp))}/${player.maxMp}`; pEl.xpBar.style.width = `${Math.max(0, player.xp / player.xpToNextLevel * 100)}%`; pEl.xpText.textContent = `XP: ${player.xp}/${player.xpToNextLevel}`; if (this.state.enemy.name) { eEl.name.innerHTML = `${this.state.enemy.name.toUpperCase()} <span style="font-size:0.7em;">LV ${this.state.enemy.level || 1}</span>`; eEl.monsterSprite.src = this.state.enemy.img; eEl.hpBar.style.width = `${Math.max(0, this.state.enemy.hp / this.state.enemy.maxHp * 100)}%`; eEl.hpText.textContent = `${Math.max(0, Math.ceil(this.state.enemy.hp))}/${this.state.enemy.maxHp}`; } },
    sanitizeStats() { this.state.playerTeam.forEach(p => { Object.keys(p).forEach(stat => { if (typeof p[stat] === 'number' && isNaN(p[stat])) { p[stat] = 0; } }); p.hp = Math.min(p.hp, p.maxHp); if (p.hp <= 0) { p.isFainted = true; p.hp = 0;} else {p.isFainted = false;} }); this.state.inventory.potion = parseInt(this.state.inventory.potion, 10) || 0; this.state.inventory.pokeball = parseInt(this.state.inventory.pokeball, 10) || 0; this.state.inventory.greatball = parseInt(this.state.inventory.greatball, 10) || 0; this.state.inventory.thunderstone = parseInt(this.state.inventory.thunderstone, 10) || 0; },
    triggerAttackEffect(attacker) { const effectEl = this.elements.attackEffect; effectEl.className = 'attack-wind-slash'; void effectEl.offsetWidth; effectEl.classList.add(attacker === 'player' ? 'player-attack' : 'enemy-attack'); },
    calculateDamage(power, defense) { const effectiveDefense = defense * 0.5; const baseDamage = Math.max(1, power - effectiveDefense); return Math.floor(baseDamage * (Math.random() * 0.4 + 0.8)); },
    gainXP(amount) { this.logMessage(`+${amount} XP!`, 'log-crit'); this.activePokemon.xp += amount; while (this.activePokemon.xp >= this.activePokemon.xpToNextLevel) { this.activePokemon.xp -= this.activePokemon.xpToNextLevel; this.activePokemon.level++; this.activePokemon.xpToNextLevel = Math.floor(this.activePokemon.xpToNextLevel * 1.5); this.activePokemon.maxHp += 15; this.activePokemon.maxMp += 10; this.activePokemon.attack += 3; this.activePokemon.defense += 2; this.activePokemon.sp_attack += 3; const healAmount = Math.floor(this.activePokemon.maxHp * 0.5); this.activePokemon.hp = Math.min(this.activePokemon.maxHp, this.activePokemon.hp + healAmount); this.activePokemon.mp = this.activePokemon.maxMp; this.logMessage(`LEVEL UP! NÍVEL ${this.activePokemon.level}!`, 'log-lvlup'); } this.updateUI(); },
    nextEnemy() { this.state.currentEnemyIndex++; if (this.state.currentEnemyIndex >= this.state.enemies.length) { this.gameOver(true); return; } this.loadEnemy(); this.updateUI(); this.logMessage(`UM ${this.state.enemy.name.toUpperCase()} APARECE!`, 'log-system'); this.setGameState('PLAYER_TURN'); },
    loadEnemy() { this.state.enemy = { ...this.state.enemies[this.state.currentEnemyIndex] }; this.state.enemy.maxHp = this.state.enemy.hp; },
    async gameOver(isVictory) { this.setGameState('GAME_OVER');
        const saveData = { ...this.activePokemon, gold: this.state.trainer.gold, inventory: this.state.inventory }; if (isVictory) { this.logMessage('FASE CONCLUÍDA!', 'log-lvlup'); await this.saveProgress(saveData); this.logMessage('POKÉ MART ENCONTRADO...', 'log-system'); setTimeout(() => { window.location.href = "{{ route('character.shop', ['character' => $character->id, 'next_stage' => 'play2']) }}"; }, 1500); } else { const modal = this.elements.modal.container; modal.innerHTML = `<div class="modal-box"><h2>FIM DE JOGO</h2><p>SUA JORNADA TERMINA AQUI...</p><a href="{{ route('home') }}" class="btn">REINICIAR</a></div>`; modal.classList.add('is-visible'); } },
    async saveProgress(data) { try { await fetch("{{ route('character.saveProgress', $character->id) }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: JSON.stringify(data) }); } catch (error) { console.error('Erro ao salvar:', error); } },
    logMessage(message, className = '') { this.elements.log.querySelector('p').innerHTML = message; if (className) this.elements.log.querySelector('p').className = className; },
    showPopup(text, target, isHeal = false, isCrit = false) { const popup = document.createElement('div'); popup.className = 'damage-popup'; popup.textContent = text; if (isHeal) popup.classList.add('heal'); if (isCrit) popup.classList.add('crit'); const rect = target.getBoundingClientRect(); popup.style.left = `${rect.left + rect.width / 2}px`; popup.style.top = `${rect.top}px`; document.body.appendChild(popup); setTimeout(() => popup.remove(), 1000); },
    setupKeyboardNavigation() { document.addEventListener('keydown', (e) => { if (this.state.gameState === 'BAG_OPEN') { const allBagItems = this.elements.bag.list.querySelectorAll('.bag-item, #bag-cancel-button'); let newIndex = this.state.activeBagItemIndex; if (e.key === 'ArrowUp') { e.preventDefault(); newIndex = (newIndex - 1 + allBagItems.length) % allBagItems.length; } else if (e.key === 'ArrowDown') { e.preventDefault(); newIndex = (newIndex + 1) % allBagItems.length; } else if (e.key === 'Enter') { e.preventDefault(); allBagItems[this.state.activeBagItemIndex].click(); return; } else if (e.key === 'Escape') { e.preventDefault(); this.closeBag(); return; } if (newIndex !== this.state.activeBagItemIndex) { this.state.activeBagItemIndex = newIndex; this.updateBagSelection(); } } else if (this.state.gameState === 'TEAM_SCREEN_OPEN' || this.state.gameState === 'MUST_SWITCH') { const teamList = this.elements.team.listPanel.querySelectorAll('.pokemon-card'); if (teamList.length === 0) return; let newIndex = this.state.activeTeamListIndex; if (e.key === 'ArrowDown') { e.preventDefault(); newIndex = (newIndex + 1) % teamList.length; } else if (e.key === 'ArrowUp') { e.preventDefault(); newIndex = (newIndex - 1 + teamList.length) % teamList.length; } else if (e.key === 'Enter') { e.preventDefault(); const selectedCard = teamList[this.state.activeTeamListIndex]; if (selectedCard) { selectedCard.click(); } return; } else if (e.key === 'Escape') { e.preventDefault(); this.closeTeamScreen(); return; } if (newIndex !== this.state.activeTeamListIndex) { this.state.activeTeamListIndex = newIndex; this.updateTeamSelection(); } } }); }
};

document.addEventListener('DOMContentLoaded', () => Intro.start());
</script>

</body>
</html>