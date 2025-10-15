<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caverna Sombria</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --text-light: #ffffff; 
            --text-highlight: #ffffff;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Press Start 2P', cursive;
            background: url('https://i.pinimg.com/originals/22/2b/85/222b8545bea5db87448c2618c5ec8c0b.gif') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-light);
        }

        .overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); z-index: -1; }


        .creation-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }

        @media (min-width: 992px) {
            .creation-grid { grid-template-columns: 320px 1fr; }
        }

        h1 {
            font-size: clamp(2rem, 5vw, 2.8rem);
            color: var(--text-highlight);
            text-shadow: 3px 3px #000;
            margin: 0 0 30px;
            text-align: center;
            grid-column: 1 / -1;
        }

        .form-legend {
            font-size: 1rem;
            color: var(--text-highlight);
            margin-bottom: 15px;
            width: 100%;
            text-align: left;
            text-shadow: 2px 2px #000000;
        }
        .form-fieldset { border: none; padding: 0; margin-bottom: 25px; }

        .form-input {
            width: 100%;
            padding: 15px;
            background: var(--bg-dark);
            border: 2px solid #ffffff;
            font-family: 'Press Start 2P', cursive;
            font-size: 1rem;
            color: var(--text-light);
        }
        .form-input:focus { outline: 2px solid var(--text-highlight); }

        .selection-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .class-option {
            background: #3f2a1f;
            border: 2px solid #ffffff;
            padding: 15px 10px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            color: var(--text-light);
        }
        .class-option:hover { background: #ffffff; color: var(--bg-dark); }
        .class-option.selected {
            background: var(--text-highlight);
            color: var(--bg-dark);
            border-color: var(--text-light);
        }
        .class-icon { font-size: 2rem; margin-bottom: 10px; }
        .class-name { font-size: 0.8rem; }

        .avatar-gallery { display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 10px; }
        .avatar-option {
            width: 100%; aspect-ratio: 1 / 1;
            cursor: pointer;
            border: 4px solid transparent;
            transition: all 0.2s;
            object-fit: cover;
            background: var(--bg-dark);
        }
        .avatar-option:hover { border-color: #ffffff; }
        .avatar-option.selected { border-color: var(--text-highlight); }
    
        .preview-avatar {
            width: 150px; height: 150px;
            background-color: #000;
            margin: 0 auto 15px auto;
            border: 4px solid #000000;
            object-fit: cover;
            display: block;
        }
        .preview-name {
            font-size: 1.5rem;
            color: var(--text-highlight);
            text-shadow: 2px 2px #000;
            min-height: 2.5rem;
            word-break: break-all;
        }
        .preview-stats {
            margin-top: 20px;
            list-style: none;
            padding: 15px;
            text-align: left;
            font-size: 0.9rem;
            line-height: 1.8;
            background-color: rgba(0, 0, 0, 0.7); /* Usando um fundo mais transparente, mas removendo o marrom */
        }
        
        .btn {
            background: #5a3a2b; color: var(--text-light);
            border: 4px solid #3f2a1f;
            box-shadow: inset 0 0 0 4px #a18c7c;
            padding: 15px 35px; text-decoration: none;
            font-size: 1.2rem; transition: all 0.1s;
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 8 8"><path fill="%23ffc800" d="M0 0v8l4-4-4-4z"/></svg>') 8 8, auto;
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
        }
        .btn:hover:not(:disabled) { background: #a18c7c; color: var(--bg-dark); }
        .btn:disabled { background: #555; color: #999; cursor: not-allowed; border-color: #333; box-shadow: inset 0 0 0 4px #777; }
        
        @keyframes fadeIn { to { opacity: 1; } }
    </style>
</head>
<body>

<div class="overlay"></div>

<main class="main-container">
    <form id="characterForm" method="POST" action="{{ route('character.store') }}">
        @csrf
        <div class="creation-grid">
            <h1>CRIAR PERSONAGEM</h1>
            
            <aside class="character-preview-panel">
                <div class="preview-card">
                    <img src="{{ asset('img/avatar-8.png') }}" alt="Avatar Preview" id="avatarPreview" class="preview-avatar">
                    <h2 id="namePreview" class="preview-name">NOME</h2>
                    <ul class="preview-stats">
                        <li>HP: <span id="stat-hp">--</span></li>
                        <li>ATAQUE: <span id="stat-attack">--</span></li>
                        <li>DEFESA: <span id="stat-defense">--</span></li>
                    </ul>
                </div>
            </aside>

            <section class="creation-panel">
                <fieldset class="form-fieldset">
                    <legend class="form-legend">NOME:</legend>
                    <input type="text" id="nameInput" name="name" class="form-input" required minlength="3" autocomplete="off">
                </fieldset>
                
                    </div>
                    <input type="hidden" name="class" id="classInput" required>
                </fieldset>

                <fieldset class="form-fieldset">
                    <legend class="form-legend">AVATAR:</legend>
                    <div class="avatar-gallery" id="avatarGallery">
                        <img src="{{ asset('img/cavaleiro.webp') }}" alt="Avatar 1" class="avatar-option" data-value="img/cavaleiro.webp">
                        <img src="{{ asset('img/arqueiro.gif') }}" alt="Avatar 2" class="avatar-option" data-value="img/arqueiro.gif">
                        <img src="{{ asset('img/mago.webp') }}" alt="Avatar 3" class="avatar-option" data-value="img/mago.webp">
                    </div>
                    <input type="hidden" name="avatar" id="avatarInput" required>
                </fieldset>
            </section>
            
            <div class="grid-span-2">
                <button type="submit" id="submitBtn" class="btn" disabled>INICIAR JOGO</button>
            </div>
        </div>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('characterForm');
    const nameInput = document.getElementById('nameInput');
    const avatarInput = document.getElementById('avatarInput');
    const classInput = document.getElementById('classInput');
    const avatarGallery = document.getElementById('avatarGallery');
    const allAvatars = avatarGallery.querySelectorAll('.avatar-option');
    const classSelection = document.getElementById('classSelection');
    const allClasses = classSelection.querySelectorAll('.class-option');
    const submitBtn = document.getElementById('submitBtn');
    
    // Preview Elements
    const namePreview = document.getElementById('namePreview');
    const avatarPreview = document.getElementById('avatarPreview');
    const statHp = document.getElementById('stat-hp');
    const statAttack = document.getElementById('stat-attack');
    const statDefense = document.getElementById('stat-defense');

    const validateForm = () => {
        const isNameValid = nameInput.value.trim().length >= 3;
        const isAvatarSelected = avatarInput.value !== '';
        const isClassSelected = classInput.value !== '';
        submitBtn.disabled = !(isNameValid && isAvatarSelected && isClassSelected);
    };

    nameInput.addEventListener('input', () => {
        const currentName = nameInput.value.trim();
        namePreview.textContent = currentName === '' ? '[NOME]' : currentName;
        validateForm();
    });
    
    classSelection.addEventListener('click', (event) => {
        const clickedClass = event.target.closest('.class-option');
        if (!clickedClass) return;

        allClasses.forEach(opt => opt.classList.remove('selected'));
        clickedClass.classList.add('selected');
        
        classInput.value = clickedClass.dataset.class;
        
        // Update preview stats
        statHp.textContent = clickedClass.dataset.hp;
        statAttack.textContent = clickedClass.dataset.attack;
        statDefense.textContent = clickedClass.dataset.defense;

        validateForm();
    });

    avatarGallery.addEventListener('click', (event) => {
        const clickedAvatar = event.target.closest('.avatar-option');
        if (!clickedAvatar) return;

        allAvatars.forEach(img => img.classList.remove('selected'));
        clickedAvatar.classList.add('selected');
        
        avatarInput.value = clickedAvatar.dataset.value;
        avatarPreview.src = clickedAvatar.src;
        
        validateForm();
    });

    validateForm();
});
</script>

</body>
</html>
