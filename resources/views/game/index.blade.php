<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caverna Sombria - Criar Personagem</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --text-color: #dce0e5;
            --highlight-color: #94e5ff;
            --shadow-color: #2a2a47;
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
            color: var(--text-color);
            image-rendering: pixelated;
        }

        .overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); z-index: -1; }

        .creation-panel {
            display: flex;
            flex-direction: column;
            gap: 25px;
            width: 100%;
            max-width: 750px;
            background: none;
            border: none;
            padding: 0;
        }

        h1 {
            font-size: clamp(2.5rem, 6vw, 3.2rem);
            color: var(--highlight-color);
            text-shadow: 4px 4px var(--shadow-color);
            margin: 0 0 25px;
            text-align: center;
        }

        .character-preview {
            text-align: center;
        }
        .preview-avatar {
            width: 200px; height: 200px;
            background-color: transparent;
            margin: 0 auto 20px auto;
            border: none;
            box-shadow: none;
            object-fit: contain;
            display: block;
        }
        .preview-name {
            font-size: 1.8rem;
            color: var(--highlight-color);
            text-shadow: 3px 3px var(--shadow-color);
            min-height: 2.5rem;
            word-break: break-all;
        }

        .form-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            width: 100%;
        }

        @media (max-width: 700px) {
            .form-columns { grid-template-columns: 1fr; }
        }

        .form-fieldset { 
            border: 2px solid var(--highlight-color);
            padding: 20px;
            background: rgba(10, 10, 25, 0.5);
        }
        
        .form-legend {
            font-size: 0.9rem;
            color: var(--highlight-color);
            margin-bottom: 15px;
            text-shadow: 2px 2px var(--shadow-color);
            padding: 0;
        }
        
        .form-input {
            width: 100%;
            padding: 15px;
            background: rgba(0,0,0,0.7);
            border: 2px solid var(--highlight-color);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.9rem;
            color: var(--text-color);
        }
        .form-input:focus {
            outline: none;
            box-shadow: 0 0 10px var(--highlight-color);
        }

        .avatar-gallery { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .avatar-option {
            width: 100%; aspect-ratio: 1 / 1;
            cursor: pointer;
            border: 4px solid transparent;
            transition: all 0.2s;
            object-fit: contain;
            background: rgba(0,0,0,0.5);
            padding: 5px;
        }
        .avatar-option.selected {
            border-color: var(--highlight-color);
        }
    
        .btn {
            background: transparent;
            color: var(--highlight-color);
            border: 2px solid var(--highlight-color);
            padding: 20px 40px;
            font-size: 1.2rem;
            transition: all 0.2s ease;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
            text-shadow: 2px 2px var(--shadow-color);
            margin-top: 15px;
            width: 100%;
            max-width: 350px;
            align-self: center;
        }
        .btn:hover:not(:disabled) {
            background: var(--highlight-color);
            color: var(--shadow-color);
            box-shadow: 0 0 15px var(--highlight-color);
            text-shadow: none;
        }
        .btn:disabled { background: rgba(51, 51, 51, 0.5); color: #777; border-color: #555; cursor: not-allowed; text-shadow: none; }
    </style>
</head>
<body>

<div class="overlay"></div>

<main>
    <form id="characterForm" class="creation-panel" method="POST" action="{{ route('character.store') }}">
        @csrf
        
        <h1>CRIAR PERSONAGEM</h1>
        
        <div class="character-preview">
            <img src="https://i.redd.it/3mmmx0dz9nmb1.gif" alt="Avatar Preview" id="avatarPreview" class="preview-avatar">
            <h2 id="namePreview" class="preview-name">NOME</h2>
        </div>

        <div class="form-columns">
            <fieldset class="form-fieldset">
                <legend class="form-legend">NOME</legend>
                <input type="text" id="nameInput" name="name" class="form-input" required minlength="3" autocomplete="off" placeholder="NOME DO SEU TREINADOR:">
            </fieldset>
            
            <fieldset class="form-fieldset">
                <legend class="form-legend">ESCOLHA SEU AVATAR:</legend>
                <div class="avatar-gallery" id="avatarGallery">
                    <img src="https://i.redd.it/3mmmx0dz9nmb1.gif" alt="Avatar 1" class="avatar-option" data-value="https://i.redd.it/3mmmx0dz9nmb1.gif">
                    <img src="https://pa1.aminoapps.com/6826/93b00040cdf43d4259de7b1c44b7fb7226d2c570_hq.gif" alt="Avatar 2" class="avatar-option" data-value="https://pa1.aminoapps.com/6826/93b00040cdf43d4259de7b1c44b7fb7226d2c570_hq.gif">
                    <img src="https://i.pinimg.com/originals/ea/23/a1/ea23a163a66fd6850341344887e399cf.gif" alt="Avatar 3" class="avatar-option" data-value="https://i.pinimg.com/originals/ea/23/a1/ea23a163a66fd6850341344887e399cf.gif">
                </div>
                <input type="hidden" name="avatar" id="avatarInput" required>
            </fieldset>
        </div>
        
        <button type="submit" id="submitBtn" class="btn" disabled>INICIAR JOGO</button>
        
    </form>
</main>

<script>
// O SCRIPT CONTINUA O MESMO
document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('nameInput');
    const avatarInput = document.getElementById('avatarInput');
    const avatarGallery = document.getElementById('avatarGallery');
    const allAvatars = avatarGallery.querySelectorAll('.avatar-option');
    const submitBtn = document.getElementById('submitBtn');
    
    const namePreview = document.getElementById('namePreview');
    const avatarPreview = document.getElementById('avatarPreview');

    const validateForm = () => {
        const isNameValid = nameInput.value.trim().length >= 3;
        const isAvatarSelected = avatarInput.value !== '';
        submitBtn.disabled = !(isNameValid && isAvatarSelected);
    };

    nameInput.addEventListener('input', () => {
        const currentName = nameInput.value.trim();
        namePreview.textContent = currentName === '' ? '[NOME]' : currentName;
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

    if (allAvatars.length > 0) {
        const firstAvatar = allAvatars[0];
        firstAvatar.classList.add('selected');
        avatarInput.value = firstAvatar.dataset.value;
        avatarPreview.src = firstAvatar.src;
    }

    validateForm();
});
</script>

</body>
</html>