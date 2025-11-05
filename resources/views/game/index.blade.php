<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POKEMON - CROWN OF ICE - Registro de Treinador</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --pkmn-blue-dark: #1e3a5f;
            --pkmn-blue-light: #94e5ff;
            --pkmn-white: #f8f8f8;
            --pkmn-ui-bg: #212529;
            --pkmn-ui-border: #495057;
            --pkmn-yellow-accent: #ffcb05;

            --font-family: 'Press Start 2P', cursive;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-family);
            /* IMAGEM DE NEVE ORIGINAL REVERTIDA AQUI */
            background: url('https://i.pinimg.com/originals/22/2b/85/222b8545bea5db87448c2618c5ec8c0b.gif') no-repeat center center fixed; 
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--pkmn-white);
            image-rendering: pixelated;
        }

        .overlay { 
            position: fixed; 
            inset: 0; 
            background: rgba(10, 20, 40, 0.7); 
            z-index: -1; 
        }

        /* Estrutura principal da Pokedex */
        .pokedex-container {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            width: 100%;
            max-width: 950px;
            gap: 10px;
            background: var(--pkmn-blue-dark);
            border: 8px solid #343a40;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            padding: 25px;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Painel Esquerdo (Preview) */
        .pokedex-left {
            background: #000;
            border: 4px solid var(--pkmn-ui-border);
            border-radius: 5px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* Efeito de Scanline na tela */
        .pokedex-left::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
            background-size: 100% 3px, 4px 100%;
            pointer-events: none;
            z-index: 2;
        }
        
        .preview-avatar {
            width: clamp(150px, 100%, 220px);
            height: auto;
            object-fit: contain;
            display: block;
            margin-bottom: 20px;
        }
        
        .preview-name-box {
            background: var(--pkmn-ui-bg);
            border: 2px solid var(--pkmn-ui-border);
            padding: 15px 25px;
            width: 100%;
            text-align: center;
        }

        .preview-name {
            font-size: clamp(1rem, 4vw, 1.5rem);
            color: var(--pkmn-blue-light);
            text-shadow: 2px 2px #111;
            min-height: 2rem;
            word-break: break-all;
            text-transform: uppercase;
        }

        /* Painel Direito (Formulário) */
        .pokedex-right {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        h1 {
            font-size: clamp(1.5rem, 5vw, 2.2rem);
            color: var(--pkmn-yellow-accent);
            text-shadow: 3px 3px var(--pkmn-blue-dark);
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 4px solid var(--pkmn-ui-border);
            margin-bottom: 10px;
        }
        
        /* Estilo da caixa de diálogo clássica */
        .form-fieldset { 
            border: 8px solid transparent;
            border-image: url('https://i.imgur.com/uI0LDRD.png') 8 round;
            padding: 20px;
            background: rgba(248, 248, 248, 0.9);
            color: #333;
            text-shadow: none;
        }
        
        /* Ajuste de cor e sombra da legenda */
        .form-legend {
            font-size: 1rem;
            color: var(--pkmn-blue-dark); /* Cor escura para melhor contraste */
            margin-bottom: 15px;
            text-shadow: 1px 1px var(--pkmn-white); /* Sombra branca para destacar */
        }
        
        .form-input {
            width: 100%;
            padding: 10px;
            background: #ddd;
            border: 2px solid #555;
            font-family: var(--font-family);
            font-size: 0.9rem;
            color: #222;
        }
        .form-input:focus {
            outline: 2px solid var(--pkmn-yellow-accent);
            background: #fff;
        }

        .choice-gallery { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); 
            gap: 10px; 
            align-items: center;
            justify-content: center;
        }

        .choice-option-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .choice-option {
            width: 100%; 
            aspect-ratio: 1 / 1;
            cursor: pointer;
            border: 4px solid transparent;
            transition: all 0.2s ease;
            object-fit: contain;
            background: rgba(0,0,0,0.1);
            border-radius: 5px;
            padding: 5px;
        }
        .choice-option:hover {
            transform: scale(1.1);
            border-color: var(--pkmn-yellow-accent);
        }
        .choice-option.selected {
            border-color: var(--pkmn-blue-dark);
            background-color: var(--pkmn-blue-light);
        }
        /* Ajuste de cor e sombra do nome do Pokémon */
        .pokemon-name {
            margin-top: 8px;
            font-size: 0.75rem;
            color: var(--pkmn-blue-dark); /* Cor escura para melhor contraste */
            text-shadow: 1px 1px var(--pkmn-white); /* Sombra branca para destacar */
        }

        .btn {
            background: #3d7dca;
            color: var(--pkmn-yellow-accent);
            border: 4px solid var(--pkmn-blue-dark);
            padding: 15px 30px;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            cursor: pointer;
            font-family: var(--font-family);
            text-transform: uppercase;
            text-shadow: 2px 2px var(--pkmn-blue-dark);
            margin-top: 15px;
            width: 100%;
            border-radius: 5px;
        }
        .btn:hover:not(:disabled) {
            background: #2a62a8;
            box-shadow: 0 0 10px var(--pkmn-yellow-accent);
        }
        .btn:disabled { 
            background: #6c757d; 
            color: #adb5bd; 
            border-color: #495057; 
            cursor: not-allowed; 
            text-shadow: none; 
        }

        @media (max-width: 800px) {
            .pokedex-container { grid-template-columns: 1fr; }
            h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<form id="characterForm" class="pokedex-container" method="POST" action="{{ route('character.store') }}">
    @csrf
    
    <aside class="pokedex-left">
        <img src="https://i.redd.it/3mmmx0dz9nmb1.gif" alt="Avatar Preview" id="avatarPreview" class="preview-avatar">
        <div class="preview-name-box">
            <h2 id="namePreview" class="preview-name">TREINADOR</h2>
        </div>
    </aside>

    <main class="pokedex-right">
        <h1>REGISTRO DE TREINADOR</h1>
        
        <fieldset class="form-fieldset">
            <legend class="form-legend">SEU NOME:</legend>
            <input type="text" id="nameInput" name="name" class="form-input" required minlength="3" autocomplete="off" placeholder="Digite seu nome...">
        </fieldset>

        <fieldset class="form-fieldset">
            <legend class="form-legend">SUA APARÊNCIA:</legend>
            <div class="choice-gallery" id="avatarGallery">
                <img src="https://i.redd.it/3mmmx0dz9nmb1.gif" alt="Avatar 1" class="choice-option" data-value="https://i.redd.it/3mmmx0dz9nmb1.gif">
                <img src="https://pa1.aminoapps.com/6826/93b00040cdf43d4259de7b1c44b7fb7226d2c570_hq.gif" alt="Avatar 2" class="choice-option" data-value="https://pa1.aminoapps.com/6826/93b00040cdf43d4259de7b1c44b7fb7226d2c570_hq.gif">
                <img src="https://i.pinimg.com/originals/ea/23/a1/ea23a163a66fd6850341344887e399cf.gif" alt="Avatar 3" class="choice-option" data-value="https://i.pinimg.com/originals/ea/23/a1/ea23a163a66fd6850341344887e399cf.gif">
            </div>
            <input type="hidden" name="avatar" id="avatarInput" required>
        </fieldset>

        <fieldset class="form-fieldset">
            <legend class="form-legend">POKÉMON INICIAL:</legend>
            <div class="choice-gallery" id="pokemonGallery">
                <div class="choice-option-wrapper">
                    <img src="https://i.gifer.com/origin/d8/d83e9951f28fc811c1166b16dcaec930_w200.gif" alt="Squirtle" class="choice-option" data-value="squirtle">
                    <span class="pokemon-name">Squirtle</span>
                </div>
                <div class="choice-option-wrapper">
                     <img src="https://i.gifer.com/origin/fe/fe4ebd8a9c0547e94000a9c759acf591_w200.gif" alt="Bulbasaur" class="choice-option" data-value="bulbasaur">
                     <span class="pokemon-name">Bulbasaur</span>
                </div>
                <div class="choice-option-wrapper">
                     <img src="https://i.pinimg.com/originals/9f/b1/25/9fb125f1fedc8cc62ab5b20699ebd87d.gif" alt="Pikachu" class="choice-option" data-value="pikachu">
                     <span class="pokemon-name">Pikachu</span>
                </div>
            </div>
            <input type="hidden" name="pokemon_choice" id="pokemon_choice" required>
        </fieldset>
        
        <button type="submit" id="submitBtn" class="btn" disabled>INICIAR JORNADA</button>
    </main>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- ELEMENTOS GERAIS ---
    const nameInput = document.getElementById('nameInput');
    const namePreview = document.getElementById('namePreview');
    const submitBtn = document.getElementById('submitBtn');

    // --- ELEMENTOS DO TREINADOR (AVATAR) ---
    const avatarInput = document.getElementById('avatarInput');
    const avatarGallery = document.getElementById('avatarGallery');
    const allAvatars = avatarGallery.querySelectorAll('.choice-option');
    const avatarPreview = document.getElementById('avatarPreview');

    // --- ELEMENTOS DO POKÉMON INICIAL ---
    const pokemonInput = document.getElementById('pokemon_choice');
    const pokemonGallery = document.getElementById('pokemonGallery');
    const allPokemons = pokemonGallery.querySelectorAll('.choice-option');

    const validateForm = () => {
        const isNameValid = nameInput.value.trim().length >= 3;
        const isAvatarSelected = avatarInput.value !== '';
        const isPokemonSelected = pokemonInput.value !== '';
        
        submitBtn.disabled = !(isNameValid && isAvatarSelected && isPokemonSelected);
    };

    // --- LÓGICA DO NOME ---
    nameInput.addEventListener('input', () => {
        const currentName = nameInput.value.trim();
        namePreview.textContent = currentName === '' ? 'TREINADOR' : currentName;
        validateForm();
    });
    
    // --- LÓGICA DA ESCOLHA (FUNÇÃO REUTILIZÁVEL) ---
    function setupGallery(gallery, input, allOptions, isAvatarGallery = false) {
        gallery.addEventListener('click', (event) => {
            const clickedOption = event.target.closest('.choice-option');
            if (!clickedOption) return;

            allOptions.forEach(img => img.classList.remove('selected'));
            clickedOption.classList.add('selected');
            
            input.value = clickedOption.dataset.value;
            
            if (isAvatarGallery) {
                avatarPreview.src = clickedOption.src;
            }
            
            validateForm();
        });
    }

    setupGallery(avatarGallery, avatarInput, allAvatars, true);
    setupGallery(pokemonGallery, pokemonInput, allPokemons);

    // --- INICIALIZAÇÃO DO FORMULÁRIO ---
    function initializeDefaultSelection(gallery) {
        const firstOption = gallery.querySelector('.choice-option');
        if (firstOption) {
            firstOption.click(); // Simula um clique para rodar toda a lógica
        }
    }
    
    initializeDefaultSelection(avatarGallery);
    initializeDefaultSelection(pokemonGallery);

    validateForm();
});
</script>

</body>
</html>