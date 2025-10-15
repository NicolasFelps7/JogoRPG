<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Last SENAI | O Chamado do Herói</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #1a1c2c;
            --ui-main: #5a3a2b; /* Marrom principal */
            --ui-border-light: #a18c7c; /* Marrom claro */
            --ui-border-dark: #3f2a1f; /* Marrom escuro */
            --text-light: #ffffff;
            --text-highlight: #ffc800;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Press Start 2P', cursive;
            background: url("{{ asset('img/giphy.gif') }}") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background-color: var(--bg-dark);
            background-blend-mode: multiply;
            color: var(--text-light);
            image-rendering: pixelated;
        }

        .overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); z-index: -1; }
        
        .dialog-box {
            position: relative; z-index: 1;
            padding: 25px;
            max-width: 800px;
            width: 100%;
            background: var(--ui-main);
            border: 4px solid var(--ui-border-dark);
            box-shadow: inset 0 0 0 4px var(--ui-border-light), 0 10px 30px rgba(0,0,0,0.5);
            opacity: 0;
            animation: fadeIn 0.5s 0.2s forwards;
            min-height: 400px;
            display: flex;
            flex-direction: column;
        }

        .dialog-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 4px solid var(--ui-border-dark);
        }
        
        .npc-avatar {
            width: 80px;
            height: 80px;
            border: 4px solid var(--ui-border-light);
            object-fit: cover;
            background: var(--bg-dark);
            flex-shrink: 0;
        }
        
        .dialog-title {
            font-size: 1.2rem;
            color: var(--text-highlight);
            text-shadow: 2px 2px #000;
            text-align: left;
        }
        
        .dialog-content {
            font-size: 1rem;
            line-height: 1.8;
            text-align: left;
            flex-grow: 1;
        }
        .dialog-content strong {
            color: var(--text-highlight);
        }

        .next-button {
            position: absolute;
            bottom: 15px;
            right: 25px;
            background: none;
            border: none;
            color: var(--text-light);
            font-size: 1rem;
            text-transform: uppercase;
            text-shadow: 2px 2px #000;
            animation: blink 1.5s infinite steps(1);
            text-decoration: none;
            font-family: 'Press Start 2P', cursive;
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 8 8"><path fill="%23ffc800" d="M0 0v8l4-4-4-4z"/></svg>') 8 8, auto;
        }
        .next-button::after {
            content: ' ▼';
            color: var(--text-highlight);
        }
        
        @keyframes fadeIn { to { opacity: 1; } }
        @keyframes blink {
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<main class="dialog-box">
    <header class="dialog-header">
        <img src="{{ asset('img/avatar-4.png') }}" class="npc-avatar" alt="Mestre do Jogo">
        <h1 class="dialog-title">Mestre Splinter</h1>
    </header>
    
    <div class="dialog-content" id="dialogContent">
        </div>

    <a href="{{ route('character.allocate', $character->id) }}" id="nextButton" class="next-button" style="display: none;">Prosseguir</a>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dialogContent = document.getElementById('dialogContent');
    const nextButton = document.getElementById('nextButton');
    
    // As diferentes "páginas" do diálogo
    const pages = [
        `Saudações, {{ $character->name }}! O destino de Pixelândia repousa sobre seus ombros. A jornada será árdua, mas sua lenda começa agora...`,
        `O 'Grande Reset' falhou, deixando um Eco Sombrio para trás. Criaturas corrompidas agora vagam pela terra. Sua missão é purificá-las.`,
        `Para sobreviver, você deve dominar seus atributos: HP para resistir, MP para conjurar magias e ATAQUE para destruir seus inimigos.`,
        `Prepare-se. Na próxima tela, você irá forjar seu poder distribuindo seus pontos de atributo. Escolha com sabedoria. Boa sorte, herói!`
    ];
    
    let currentPage = 0;
    
    function typeWriter(text, i = 0) {
        if (i < text.length) {
            dialogContent.innerHTML += text.charAt(i);
            setTimeout(() => typeWriter(text, i + 1), 30); // Velocidade da digitação
        } else {
            // Quando terminar de digitar, mostra o botão para avançar
            nextButton.style.display = 'block';
            if (currentPage >= pages.length - 1) {
                nextButton.textContent = "FORJAR HERÓI";
            } else {
                nextButton.textContent = "CONTINUAR";
            }
        }
    }

    function showNextPage(e) {
        if(currentPage >= pages.length - 1) {
            // Se for a última página, o link já vai para o 'allocate'
            return;
        }
        
        e.preventDefault(); // Impede o link de navegar se não for a última página
        currentPage++;
        dialogContent.innerHTML = '';
        nextButton.style.display = 'none';
        typeWriter(pages[currentPage]);
    }

    // Inicia o diálogo
    typeWriter(pages[0]);
    
    // Configura o evento para o botão de avançar
    nextButton.addEventListener('click', showNextPage);
});
</script>

</body>
</html>