<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caverna Sombria - Tutorial</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --dialog-text-color: #4c4c4c;
            --dialog-bg: #f8f8f8;
            --dialog-border-outer: #7b94a1;
            --dialog-border-inner: #c6d3d9;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Press Start 2P', cursive;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: radial-gradient(ellipse at bottom, #89c4a5, #d6e2d9);
            color: var(--dialog-text-color);
            image-rendering: pixelated;
            overflow: hidden;
        }

        .character-area {
            flex-grow: 1;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-end;
            position: relative;
            padding-bottom: 20px;
        }

        .professor-sprite {
            width: 280px; /* MUDANÇA AQUI: Aumentado para um tamanho "top" */
            height: auto;
            position: relative;
            z-index: 2;
            filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.3));
        }

        .spotlight {
            position: absolute;
            bottom: 0;
            width: 320px; /* MUDANÇA AQUI: Aumentado para combinar com o novo tamanho */
            height: 60px;
            background-color: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            filter: blur(5px);
            z-index: 1;
        }
        
        .dialog-box {
            width: calc(100% - 20px);
            max-width: 1024px;
            height: 180px;
            margin: 10px;
            background: var(--dialog-bg);
            border: 8px solid var(--dialog-border-outer);
            box-shadow: inset 0 0 0 8px var(--dialog-border-inner);
            border-radius: 20px;
            padding: 25px;
            position: relative;
            cursor: pointer;
        }
        
        .dialog-content {
            font-size: 1.5rem;
            line-height: 1.6;
            text-align: left;
        }

        .blinking-indicator {
            color: #e53935;
            animation: blink 1.2s infinite steps(1);
            font-size: 1.2rem;
            vertical-align: middle;
        }
        
        @keyframes blink {
            50% { opacity: 0; }
        }
    </style>
</head>
<body>

    <main class="character-area">
        <img src="https://pbs.twimg.com/media/FERQsjNWQAU92hf.png" class="professor-sprite" alt="Professor Carvalho">
        <div class="spotlight"></div>
    </main>
    
    <footer class="dialog-box" id="dialogBox">
        <div class="dialog-content" id="dialogContent"></div>
    </footer>

<script>
// O SCRIPT CONTINUA O MESMO
document.addEventListener('DOMContentLoaded', () => {
    const dialogContent = document.getElementById('dialogContent');
    const dialogBox = document.getElementById('dialogBox');
    const finalRedirectURL = "{{ route('character.allocate', $character->id) }}";
    
    const pages = [
        `Olá! Bem-vindo ao mundo dos monstros!`,
        `Meu nome é Carvalho. Mas as pessoas me chamam de Professor Pokémon!`,
        `Este mundo é habitado por criaturas chamadas de POKEMON!`,
        `Para alguns, os Pokémon são animais de estimação. Outros os usam para lutar.`,
        `Sua própria lenda Pokémon está prestes a começar! Um mundo cheio de sonhos e aventuras no gelo o aguarda! Vamos lá!`
    ];
    
    let currentPage = 0;
    let isTyping = false;
    
    function typeWriter(text, i = 0) {
        isTyping = true;
        if (i < text.length) {
            dialogContent.innerHTML += text.charAt(i);
            setTimeout(() => typeWriter(text, i + 1), 40);
        } else {
            dialogContent.innerHTML += ' <span class="blinking-indicator">♥</span>';
            isTyping = false;
        }
    }

    function showNextPage() {
        if (isTyping) return;

        if (currentPage >= pages.length - 1) {
            window.location.href = finalRedirectURL;
            return;
        }
        
        currentPage++;
        dialogContent.innerHTML = '';
        typeWriter(pages[currentPage]);
    }

    typeWriter(pages[0]);
    
    dialogBox.addEventListener('click', showNextPage);
});
</script>

</body>
</html>