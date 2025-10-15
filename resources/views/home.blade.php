<!DOCTYPE html>
<html lang="pt-br" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caverna Sombria | Selecione seu Herói</title>

    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --ui-main: #000000; /* Marrom principal da UI */
            --ui-border-light: #000000; /* Marrom mais claro para bordas e destaques */
            --text-light: #ffffff; /* Branco para o texto */
            --text-highlight: #ffffff; /* Amarelo para destaques (ouro) */
            --danger-color: #000000; /* Vermelho para ações de perigo */
            --success-color: #7cb342; /* Verde para ações de sucesso */
        }

        *, *::before, *::after { box-sizing: border-box; }
        .no-js body { visibility: hidden; }

        body {
            margin: 0; padding: 20px; font-family: 'Press Start 2P', cursive;
            background-color: var(--bg-dark);
            background: url('https://i.redd.it/86qeqi3lbu8c1.gif') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-light); text-align: center;
            min-height: 100vh; display: flex; justify-content: center; align-items: center;
            overflow-x: hidden;
            image-rendering: pixelated; /* Garante que as imagens fiquem em estilo pixel art */
        }
        
        .preloader {
            position: fixed; inset: 0; z-index: 100;
            display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 20px;
            background-color: #000;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        .preloader.is-hidden { opacity: 0; visibility: hidden; }
        .preloader__spinner {
            width: 40px; height: 40px;
            /* Ícone de spinner pixelado em branco */
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8"><path fill="%23FFF" d="M3 0v1h1v1h1V1H4V0H3zm1 4v1H3v1H2V5h1V4h1zM0 3v1h1v1h1V4H1V3H0zM5 3v1h1v1h1V4H6V3H5z"/></svg>');
            animation: spin 0.5s steps(4) infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .game-container {
            position: relative; z-index: 2; padding: clamp(20px, 4vw, 30px);
            max-width: 800px; width: 95%; 
            
            /* --- ALTERAÇÃO PRINCIPAL AQUI --- */
            background: transparent; /* Remove o fundo marrom */
            border: none;            /* Remove a borda */
            box-shadow: none;        /* Remove a sombra da borda */
            /* --- FIM DA ALTERAÇÃO --- */
        }

        h1 {
            font-size: clamp(2rem, 6vw, 3rem);
            color: var(--text-highlight);
            text-shadow: 3px 3px #000;
            margin: 0 0 40px;
        }

        .btn-group { display: flex; flex-direction: column; align-items: center; gap: 20px; }
        
        .btn {
            position: relative;
            background: var(--ui-main);
            color: var(--text-light);
            border: 4px solid var(--ui-border-dark);
            box-shadow: inset 0 0 0 4px var(--ui-border-light);
            padding: 15px 35px;
            text-decoration: none; font-size: 1.2rem;
            transition: all 0.1s ease-in-out;
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 8 8"><path fill="%23ffc800" d="M0 0v8l4-4-4-4z"/></svg>') 8 8, auto; /* Cursor de mãozinha pixelada */
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
        }
        .btn:hover, .btn:focus-visible {
            background: var(--ui-border-light);
            color: var(--bg-dark);
            outline: none;
        }
        .btn:active { transform: translateY(2px); /* Efeito de botão pressionado */ }
        .btn.is-active { background: var(--danger-color); color: white; } /* Estado ativo para o botão de toggle */
        
        .modal-overlay {
            position: fixed; inset: 0; z-index: 102; display: flex;
            justify-content: center; align-items: center;
            background: rgba(0,0,0,0.8); opacity: 0; visibility: hidden;
            transition: all 0.3s ease;
        }
        .modal-overlay.is-visible { opacity: 1; visibility: visible; }
        .modal-box {
            background: var(--ui-main);
            border: 4px solid var(--ui-border-dark);
            box-shadow: inset 0 0 0 4px var(--ui-border-light);
            padding: 30px; max-width: 500px; width: 90%;
            color: var(--text-light);
            transform: scale(0.9); transition: transform 0.3s ease;
        }
        .modal-overlay.is-visible .modal-box { transform: scale(1); }
        .modal-box h3 { font-size: 1.2rem; margin-bottom: 20px; color: var(--text-highlight); }
        .modal-input {
            width: 100%; padding: 12px; font-family: 'Press Start 2P', cursive; font-size: 1rem;
            background: var(--bg-dark); color: var(--text-light);
            border: 2px solid var(--ui-border-light);
            margin-bottom: 20px;
        }
        .modal-actions { display: flex; justify-content: center; gap: 15px; }
        .modal-message { margin-top: 15px; font-weight: bold; min-height: 1.2em; font-size: 0.8rem; }
        .modal-message.success { color: var(--success-color); }
        .modal-message.error { color: var(--danger-color); }
        /* Botões dentro do modal */
        .btn--secondary { background: var(--ui-border-dark); } /* Um tom mais escuro de marrom para o botão secundário */
        .btn--danger { background: var(--danger-color); } /* Botão de perigo */

        .character-list-container {
            max-height: 0; opacity: 0;
            overflow: hidden;
            transition: all 0.5s ease-in-out;
        }
        .character-list-container.is-visible { max-height: 100vh; opacity: 1; margin-top: 40px; }
        
        .scroll-content {
            background: var(--bg-dark);
            border: 4px solid var(--ui-border-dark);
            box-shadow: inset 0 0 0 4px var(--ui-border-light);
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .character-card {
            background: var(--ui-main);
            border: 2px solid var(--ui-border-light);
            padding: 15px;
            display: grid;
            grid-template-columns: 64px 1fr auto; /* Layout para Mobile: Avatar, Info, Ações */
            align-items: center;
            gap: 15px;
            opacity: 0; transform: translateX(-20px);
            transition: all 0.3s ease-out;
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 8 8"><path fill="%23ffc800" d="M0 0v8l4-4-4-4z"/></svg>') 8 8, pointer;
        }
         @media (min-width: 576px) { /* Layout para telas maiores */
            .character-card { grid-template-columns: 80px 1fr auto; }
        }
        .character-card:hover { background: var(--ui-border-dark); }
        .character-card.is-in-view { opacity: 1; transform: translateX(0); }
        .character-card.is-deleting { opacity: 0; transform: scale(0.9); }

        .character-card__avatar {
            width: 100%; /* Ajusta a largura ao tamanho do slot */
            aspect-ratio: 1 / 1; /* Mantém proporção quadrada */
            border: 2px solid var(--ui-border-light);
            object-fit: cover;
            background-color: var(--bg-dark); /* Fundo caso o avatar não carregue */
        }
        .char-info { text-align: left; }
        .char-name { font-size: 1.1rem; color: var(--text-highlight); text-shadow: 2px 2px #000; word-break: break-all; }
        .char-meta { font-size: 0.8rem; color: var(--ui-border-light); margin-top: 8px; }
        
        .card-actions { display: flex; flex-direction: column; gap: 10px; align-items: flex-end; }
        .action-btn { 
            font-family: 'Press Start 2P', cursive; background: none; border: none; 
            color: var(--text-light); cursor: inherit; text-decoration: underline; font-size: 0.8rem; 
            padding: 0; /* Remove padding padrão do botão */
        }
        .action-btn:hover { color: var(--text-highlight); }
        .play-btn { font-size: 1rem; color: var(--success-color); font-weight: bold; }
        .empty-message { 
            color: var(--ui-border-light); 
            font-size: 0.9rem; 
            padding: 20px; 
            background: var(--ui-border-dark); /* Fundo para a mensagem de vazio */
            border: 2px solid var(--ui-border-light);
        }
    </style>
</head>
<body>
    <div class="preloader" id="preloader"><div class="preloader__spinner"></div><p>CARREGANDO...</p></div>

    <main class="game-container">
        <h1>CAVERNA SOMBRIA</h1>
        <div class="btn-group">
            <a href="{{ route('character.create') }}" class="btn">START GAME</a>
            <button id="toggleListBtn" class="btn" aria-controls="characterList" aria-expanded="false"
                    data-open-text="Carregar Jogo" data-close-text="Fechar">
                LOAD GAME
            </button>
        </div>

        <section id="characterList" class="character-list-container">
            <div class="scroll-content" id="cardGroup">
                @if($characters->isEmpty())
                    <p class="empty-message">NENHUM ARQUIVO ENCONTRADO</p>
                @else
                    @foreach($characters as $char)
                        <div class="character-card" data-character-id="{{ $char->id }}" data-character-name="{{ $char->name }}">
                            <img src="{{ asset($char->avatar ?? 'img/default-avatar.png') }}" alt="Avatar de {{ $char->name }}" class="character-card__avatar">
                            <div class="char-info">
                                <p class="char-name">{{ $char->name }}</p>
                                <p class="char-meta">LV: {{ $char->level }} | HP: {{ $char->hp }}</p>
                            </div>
                            <div class="card-actions">
                                <a href="{{ route('character.play', $char->id) }}" class="action-btn play-btn">Jogar</a>
                                <button class="action-btn edit-btn">Renomear</button>
                                <button class="action-btn delete-btn">Apagar</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>
    </main>
    
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <h3>RENOMEAR PERSONAGEM</h3>
            <input type="text" id="editNameInput" class="modal-input" placeholder="NOVO NOME...">
            <div class="modal-actions">
                <button class="btn btn--secondary" data-close-modal>Voltar</button>
                <button class="btn" id="saveEditBtn">Salvar</button>
            </div>
            <div class="modal-message" id="editMessage"></div>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <h3>APAGAR ARQUIVO</h3>
            <p id="deleteModalText" style="font-size: 1rem; line-height: 1.5; margin-bottom: 20px;">Tem certeza que deseja apagar este herói?</p>
            <div class="modal-actions">
                <button class="btn btn--secondary" data-close-modal>Cancelar</button>
                <button class="btn btn--danger" id="confirmDeleteBtn">Apagar</button>
            </div>
            <div class="modal-message" id="deleteMessage"></div>
        </div>
    </div>

    <script>
        document.documentElement.classList.remove('no-js');
        document.addEventListener('DOMContentLoaded', () => {
            const app = {
                ui: {
                    preloader: document.getElementById('preloader'),
                    toggleBtn: document.getElementById('toggleListBtn'),
                    characterList: document.getElementById('characterList'),
                    cardGroup: document.getElementById('cardGroup'),
                    editModal: {
                        overlay: document.getElementById('editModal'),
                        input: document.getElementById('editNameInput'),
                        saveBtn: document.getElementById('saveEditBtn'),
                        message: document.getElementById('editMessage'),
                    },
                    deleteModal: {
                        overlay: document.getElementById('deleteModal'),
                        text: document.getElementById('deleteModalText'),
                        confirmBtn: document.getElementById('confirmDeleteBtn'),
                        message: document.getElementById('deleteMessage'),
                    },
                },
                state: {
                    isListVisible: false,
                    cardsAnimated: false,
                    activeCard: null,
                },

                init() {
                    this.bindEvents();
                    this.hidePreloader(); // Esconde o preloader ao carregar o DOM
                    document.body.style.visibility = 'visible'; // Garante que o body fique visível
                },

                hidePreloader() {
                    this.ui.preloader.classList.add('is-hidden');
                },

                bindEvents() {
                    if (this.ui.toggleBtn) {
                        this.ui.toggleBtn.addEventListener('click', () => this.handleToggleList());
                    }
                    
                    if (this.ui.cardGroup) {
                        this.ui.cardGroup.addEventListener('click', (e) => {
                            const editBtn = e.target.closest('.edit-btn');
                            const deleteBtn = e.target.closest('.delete-btn');
                            if (editBtn) this.openEditModal(editBtn.closest('.character-card'));
                            if (deleteBtn) this.openDeleteModal(deleteBtn.closest('.character-card'));
                        });
                    }
                    
                    // Event listeners para os botões dos modais
                    this.ui.editModal.saveBtn.addEventListener('click', () => this.handleSaveEdit());
                    this.ui.deleteModal.confirmBtn.addEventListener('click', () => this.handleConfirmDelete());
                    
                    // Event listeners para fechar modais
                    document.querySelectorAll('[data-close-modal]').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            this.closeModal(e.target.closest('.modal-overlay'));
                        });
                    });
                },

                handleToggleList() {
                    this.state.isListVisible = !this.state.isListVisible;
                    this.ui.characterList.classList.toggle('is-visible', this.state.isListVisible);
                    this.ui.toggleBtn.classList.toggle('is-active', this.state.isListVisible);
                    this.ui.toggleBtn.textContent = this.state.isListVisible ? this.ui.toggleBtn.dataset.closeText : this.ui.toggleBtn.dataset.openText;

                    if (this.state.isListVisible && !this.state.cardsAnimated) {
                        this.animateCards();
                        this.state.cardsAnimated = true;
                    } else if (!this.state.isListVisible) {
                        // Reset animation state if list is closed to re-animate next time
                        this.ui.cardGroup.querySelectorAll('.character-card').forEach(card => {
                            card.classList.remove('is-in-view');
                        });
                        this.state.cardsAnimated = false;
                    }
                },

                animateCards() {
                    this.ui.cardGroup.querySelectorAll('.character-card').forEach((card, index) => {
                        // Atraso para animar os cards um por um
                        setTimeout(() => card.classList.add('is-in-view'), 100 * index);
                    });
                },

                openModal(modal) { modal.classList.add('is-visible'); },
                closeModal(modal) { modal.classList.remove('is-visible'); },

                openEditModal(card) {
                    this.state.activeCard = card;
                    this.ui.editModal.input.value = card.dataset.characterName;
                    this.ui.editModal.message.textContent = '';
                    this.openModal(this.ui.editModal.overlay);
                    this.ui.editModal.input.focus();
                },

                openDeleteModal(card) {
                    this.state.activeCard = card;
                    const name = card.dataset.characterName;
                    this.ui.deleteModal.text.innerHTML = `TEM CERTEZA QUE DESEJA APAGAR <strong style="color: var(--text-highlight);">${name}</strong>?`;
                    this.ui.deleteModal.message.textContent = '';
                    this.openModal(this.ui.deleteModal.overlay);
                },

                async sendRequest(url, options) {
                    try {
                        const response = await fetch(url, options);
                        if (!response.ok) throw new Error('FALHA NO SERVIDOR.');
                        const data = await response.json();
                        if (!data.success) throw new Error(data.message || 'ERRO.');
                        return data;
                    } catch (error) {
                        console.error("Erro na requisição:", error); // Adicionado para depuração
                        throw error;
                    }
                },

                async handleSaveEdit() {
                    const id = this.state.activeCard.dataset.characterId;
                    const newName = this.ui.editModal.input.value.trim();
                    const messageEl = this.ui.editModal.message;

                    if (!newName || newName.length < 3) {
                        messageEl.textContent = 'NOME MUITO CURTO (MIN. 3 CARACTERES).';
                        messageEl.className = 'modal-message error';
                        return;
                    }

                    try {
                        // Usando asset() para construir a URL corretamente no Blade
                        await this.sendRequest(`{{ url('/game/update') }}/${id}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ name: newName })
                        });
                        
                        this.state.activeCard.querySelector('.char-name').textContent = newName;
                        this.state.activeCard.dataset.characterName = newName;
                        messageEl.textContent = 'NOME ATUALIZADO!';
                        messageEl.className = 'modal-message success';
                        setTimeout(() => this.closeModal(this.ui.editModal.overlay), 1500);
                    } catch (error) {
                        messageEl.textContent = `ERRO: ${error.message}`;
                        messageEl.className = 'modal-message error';
                    }
                },

                async handleConfirmDelete() {
                    const id = this.state.activeCard.dataset.characterId;
                    const messageEl = this.ui.deleteModal.message;

                    try {
                         // Usando asset() para construir a URL corretamente no Blade
                         await this.sendRequest(`{{ url('/game/delete') }}/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });

                        messageEl.textContent = 'ARQUIVO APAGADO.';
                        messageEl.className = 'modal-message success';
                        this.state.activeCard.classList.add('is-deleting');
                        
                        this.state.activeCard.addEventListener('transitionend', () => {
                            this.state.activeCard.remove();
                            this.closeModal(this.ui.deleteModal.overlay);
                            if (this.ui.cardGroup.querySelectorAll('.character-card').length === 0) {
                                this.ui.cardGroup.innerHTML = '<p class="empty-message">NENHUM ARQUIVO ENCONTRADO</p>';
                            }
                        }, { once: true }); // Adiciona { once: true } para o listener ser removido após a primeira execução

                    } catch (error) {
                        messageEl.textContent = `ERRO: ${error.message}`;
                        messageEl.className = 'modal-message error';
                    }
                }
            };
            app.init();
        });
    </script>
</body>
</html>