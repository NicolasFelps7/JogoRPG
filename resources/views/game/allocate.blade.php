<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTUÁRIO DE GELO - DISTRIBUIÇÃO</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
    <style>
        /* ======================================= */
        /* PALETA DE CORES "GELO" - LAYOUT HORIZONTAL (SEM BARRAS) */
        /* ======================================= */
        :root {
            --ui-bg-main: rgba(10, 25, 47, 0.85); /* Azul noturno semi-transparente */
            --ui-border-color-dark: #76E5FF;   /* Ciano gélido brilhante */
            --ui-border-color-light: #B2FFFF;  /* Ciano muito claro */
            --text-color-primary: #F0F8FF;     /* Branco azulado */
            --text-color-secondary: #A9CCE3;   /* Cinza-azulado claro */
            --button-bg: #4FC3F7;              /* Azul celeste para botões */
            --button-text: #0D47A1;            /* Azul escuro para texto dos botões */
            --input-bg: #E3F2FD;               /* Fundo claro para o valor do atributo */
            --success-color: #69F0AE;          /* Verde menta para sucesso */
            --control-group-bg: rgba(0, 0, 0, 0.3); /* Fundo para agrupamento de controles */
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Press Start 2P', cursive;
            /* IMAGEM DE FUNDO - TEMA DE GELO */
            background: url("https://i.pinimg.com/originals/22/2b/85/222b8545bea5db87448c2618c5ec8c0b.gif") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-color-primary);
            image-rendering: pixelated;
        }

        .overlay { 
            position: fixed; inset: 0; 
            background: rgba(0, 0, 0, 0.5); /* Escurece o fundo, mantendo a visibilidade */
            backdrop-filter: blur(4px); /* Blur para o fundo */
            z-index: -1; 
        }
        
        .main-container {
            position: relative; z-index: 1;
            padding: 20px;
            max-width: 900px; /* Mais largo para o layout horizontal */
            width: 90%;
            background: var(--ui-bg-main);
            border: 4px solid var(--ui-border-color-dark);
            box-shadow: 0 0 25px var(--ui-border-color-light), inset 0 0 10px rgba(255,255,255,0.2);
            opacity: 0;
            animation: fadeIn 0.5s 0.2s forwards, glow-ice 3s infinite alternate;
            text-align: center;
            border-radius: 10px;
            backdrop-filter: blur(8px);
        }
        
        h1 {
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            color: var(--text-color-primary);
            text-shadow: 3px 3px 0 var(--ui-border-color-dark), 0 0 15px var(--ui-border-color-light);
            margin: 0 0 25px;
            letter-spacing: 1.5px;
            padding: 10px 0;
            border-bottom: 2px solid var(--ui-border-color-light);
        }

        .points-display {
            font-size: 1.1rem;
            margin-bottom: 25px;
            background: rgba(0,0,0,0.4);
            padding: 12px;
            border: 2px solid var(--ui-border-color-light);
            color: var(--text-color-secondary);
            border-radius: 5px;
            box-shadow: inset 0 0 8px rgba(255,255,255,0.1);
        }
        .points-display.complete { 
            border-color: var(--success-color); 
            animation: pulse-success 1.5s infinite;
            color: var(--success-color);
        }
        .points-display span { 
            font-size: 1.6rem; 
            color: var(--ui-border-color-light);
            transition: color 0.3s;
        }
        .points-display.complete span { 
            color: var(--success-color); 
            animation: none; 
        }

        #attributesContainer {
            display: grid; /* Usar grid para organização horizontal */
            grid-template-columns: repeat(3, 1fr); /* Três colunas para atributos */
            gap: 20px; /* Espaço entre os atributos */
        }

        .attribute-row {
            margin-bottom: 0; /* Removido margin-bottom */
            padding-bottom: 0; /* Removido padding-bottom */
            text-align: center; /* Centraliza conteúdo de cada atributo */
            border-bottom: none; /* Removido separador */
        }

        .attribute-row label {
            font-size: 0.9rem;
            display: block;
            margin-bottom: 10px;
            color: var(--text-color-primary);
            text-shadow: 1px 1px 0 rgba(0,0,0,0.5);
        }

        /* REMOVIDO .attr-bar-wrapper E .attr-bar-fill COMPLETAMENTE */

        .attribute-controls {
            display: flex;
            align-items: center;
            justify-content: center; /* Centraliza os controles */
            margin-top: 10px;
        }
        
        .control-group {
            display: flex;
            gap: 4px;
            background: var(--control-group-bg);
            border: 2px solid var(--ui-border-color-dark);
            padding: 4px;
            border-radius: 5px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.5);
        }

        .attr-value { 
            font-size: 1.1rem; 
            color: var(--button-text);
            min-width: 45px;
            text-align: center;
            background: var(--input-bg);
            border: 2px solid var(--ui-border-color-dark);
            padding: 8px 6px;
            border-radius: 5px;
            box-shadow: inset 1px 1px 5px rgba(0,0,0,0.3);
            margin: 0 8px;
        }
        
        /* Botões +/- estilo Gelo */
        .attr-btn {
            background: var(--button-bg);
            color: var(--button-text);
            border: 2px solid var(--ui-border-color-dark);
            width: 40px; height: 35px;
            font-family: inherit; font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease-out;
            border-radius: 5px;
            text-shadow: 1px 1px 0 rgba(0,0,0,0.5);
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        .attr-btn:hover:not(:disabled) {
            background: var(--ui-border-color-light);
            color: var(--button-text);
            box-shadow: 0 0 10px var(--ui-border-color-light);
            transform: translateY(-2px);
        }
        .attr-btn:active { 
            transform: translateY(0); 
            box-shadow: 0 1px 2px rgba(0,0,0,0.3);
            background: var(--button-bg);
        }
        .attr-btn:disabled { 
            background: #546E7A;
            color: #BBDEFB; 
            cursor: not-allowed; 
            border-color: #37474F; 
            box-shadow: none;
        }

        /* Botão principal "INICIAR JORNADA" */
        .btn {
            background: var(--button-bg);
            color: var(--button-text);
            border: 4px solid var(--ui-border-color-dark);
            padding: 15px 30px;
            text-decoration: none;
            font-size: 1.1rem; 
            transition: all 0.2s ease-out;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
            margin-top: 30px;
            text-shadow: 2px 2px 0 rgba(0,0,0,0.5);
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        }
        .btn:hover:not(:disabled) {
            background: var(--ui-border-color-light);
            color: var(--button-text);
            box-shadow: 0 0 20px var(--ui-border-color-light);
            transform: translateY(-3px);
        }
        .btn:disabled {
            background: #546E7A;
            color: #BBDEFB;
            cursor: not-allowed;
            border-color: #37474F;
            box-shadow: none;
            animation: none; 
            text-shadow: none;
            transform: none;
        }
        
        /* Animação de brilho para o botão principal quando ativo */
        .btn:not(:disabled) { 
            animation: btn-shine-ice 2s infinite alternate; 
        }
        .btn.no-shine {
            animation: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        }
        
        /* Animações */
        @keyframes fadeIn { to { opacity: 1; } }
        
        @keyframes glow-ice {
            from { box-shadow: 0 0 25px var(--ui-border-color-light), inset 0 0 10px rgba(255,255,255,0.2); }
            to { box-shadow: 0 0 35px var(--ui-border-color-light), inset 0 0 15px rgba(255,255,255,0.3); }
        }

        @keyframes pulse-success { 
            0%, 100% { box-shadow: 0 0 15px var(--success-color); border-color: var(--success-color); } 
            50% { box-shadow: 0 0 25px var(--success-color); } 
        }

        @keyframes btn-shine-ice { 
            0%, 100% { box-shadow: 0 5px 15px rgba(0,0,0,0.4); } 
            50% { box-shadow: 0 0 20px var(--ui-border-color-light), 0 5px 15px rgba(0,0,0,0.6); } 
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<main class="main-container">
    <form method="POST" action="{{ route('character.allocate.store', $character->id) }}">
        @csrf
        <h1>UPE SEU POKÉMON</h1> 
        <p class="points-display">PONTOS: <span id="pointsLeft">75</span></p>

        <div id="attributesContainer">
            @php
                $attributes = ['hp' => 'HP', 'mp' => 'MP', 'attack' => 'ATAQUE', 'defense' => 'DEFESA', 'speed' => 'VELOCIDADE', 'special_attack' => 'AT. ESPECIAL', 'special_defense' => 'DEF. ESPECIAL'];
            @endphp

            @foreach($attributes as $attr => $label)
            <div class="attribute-row" data-attr="{{ $attr }}">
                <label for="{{ $attr }}">{{ $label }}</label>
                
                <div class="attribute-controls">
                    <div class="control-group minus">
                        <button type="button" class="attr-btn minus-btn" data-amount="5">-5</button>
                        <button type="button" class="attr-btn minus-btn" data-amount="1">-1</button>
                    </div>
                    <span class="attr-value" id="{{ $attr }}Value">0</span>
                    <div class="control-group plus">
                        <button type="button" class="attr-btn plus-btn" data-amount="1">+1</button>
                        <button type="button" class="attr-btn plus-btn" data-amount="5">+5</button>
                    </div>
                    <input type="hidden" id="{{ $attr }}" name="{{ $attr }}" value="0">
                </div>
            </div>
            @endforeach
        </div>
        
        <button type="submit" id="submitBtn" class="btn" disabled>INICIAR JORNADA</button>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const totalPoints = 75;
    const maxPerAttr = 50;
    
    const pointsLeftEl = document.getElementById('pointsLeft');
    const pointsDisplayEl = pointsLeftEl.parentElement;
    const attributesContainer = document.getElementById('attributesContainer');
    const submitBtn = document.getElementById('submitBtn');
    
    let pointsLeft = totalPoints;

    const updateUI = () => {
        pointsLeftEl.textContent = pointsLeft;
        submitBtn.disabled = (pointsLeft !== 0);
        submitBtn.classList.toggle('no-shine', pointsLeft !== 0); 

        if (pointsLeft === 0) {
            pointsDisplayEl.classList.add('complete');
        } else {
            pointsDisplayEl.classList.remove('complete');
        }

        document.querySelectorAll('.attribute-row').forEach(row => {
            const input = row.querySelector('input[type="hidden"]');
            const currentValue = parseInt(input.value);
            
            const plusBtns = row.querySelectorAll('.plus-btn');
            const minusBtns = row.querySelectorAll('.minus-btn');

            plusBtns.forEach(btn => {
                const amount = parseInt(btn.dataset.amount) || 1;
                btn.disabled = (pointsLeft < amount || currentValue >= maxPerAttr);
            });
            minusBtns.forEach(btn => {
                const amount = parseInt(btn.dataset.amount) || 1;
                btn.disabled = (currentValue < amount);
            });
        });
    };

    attributesContainer.addEventListener('click', (event) => {
        const target = event.target;
        if (target.id === 'submitBtn') return; 
        
        if (!target.classList.contains('attr-btn') || target.disabled) return;

        const row = target.closest('.attribute-row');
        const attrName = row.dataset.attr;
        const input = document.getElementById(attrName);
        const valueEl = document.getElementById(`${attrName}Value`);
        let currentValue = parseInt(input.value);
        const amount = parseInt(target.dataset.amount) || 1;
        
        if (target.classList.contains('plus-btn')) {
            const addAmount = Math.min(amount, maxPerAttr - currentValue, pointsLeft);
            currentValue += addAmount;
            pointsLeft -= addAmount;
        } else if (target.classList.contains('minus-btn')) {
            const subAmount = Math.min(amount, currentValue);
            currentValue -= subAmount;
            pointsLeft += subAmount;
        }

        input.value = currentValue;
        valueEl.textContent = currentValue;
        
        updateUI();
    });

    updateUI();
});
</script>

</body>
</html>