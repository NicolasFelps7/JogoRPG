<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Poké Mart</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --pixel-font: 'Press Start 2P', cursive;
            --border-color: #000;
            --bg-light: #f8f8f8;
            --bg-blue: #3a5a9a;
            --text-light: #fff;
            --text-dark: #000;
            --success-color: #4CAF50;
            --error-color: #e53935;
        }

        *, *::before, *::after { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }

        body {
            font-family: var(--pixel-font);
            /* --- MODIFICAÇÃO AQUI: Adicionado o background GIF --- */
            background: url('https://i.pinimg.com/originals/22/2b/85/222b8545bea5db87448c2618c5ec8c0b.gif') no-repeat center center fixed;
            background-size: cover;
            /* Você pode adicionar a animação pixelFlow se quiser, mas pode ser pesado para o estilo GBA */
            /* animation: pixelFlow 60s linear infinite; */
            color: var(--text-dark);
            display: flex; 
            justify-content: center; 
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            image-rendering: pixelated;
        }
        
        /* O contêiner principal que imita a janela do jogo */
        .shop-container {
            max-width: 640px;
            width: 100%;
            border: 2px solid var(--border-color);
            background: #ddd;
            padding: 10px;
            box-shadow: 4px 4px 0px 0px rgba(0,0,0,1);
        }

        .shop-ui-panel {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        /* Painel da Esquerda (Dinheiro e Avatar) */
        .left-panel {
            flex-basis: 35%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .money-box {
            background: var(--bg-light);
            border: 2px solid var(--border-color);
            padding: 10px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .money-box div:first-child {
            margin-bottom: 8px;
        }

        .money-box #playerGold {
            float: right; /* Alinha o dinheiro à direita */
            font-weight: bold;
        }

        /* Usei o avatar do seu código antigo como o "sprite" do jogador */
        .player-avatar {
            width: 100%;
            aspect-ratio: 1 / 1;
            border: 2px solid var(--border-color);
            background: var(--bg-light);
            object-fit: contain;
            padding: 10px;
        }

        /* Painel da Direita (Lista de Itens) */
        .right-panel {
            flex-basis: 65%;
            background: var(--bg-light);
            border: 2px solid var(--border-color);
            padding: 5px;
            /* Altura fixa com scroll, como no jogo */
            height: 220px; 
            overflow-y: auto;
        }

        #item-list {
            list-style: none;
        }

        #item-list li {
            padding: 8px 10px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        /* Item selecionado com o cursor '►' */
        #item-list li.selected {
            background: #ccc;
        }

        #item-list li::before {
            content: ' ';
            display: inline-block;
            width: 12px; /* Espaço para o cursor */
        }

        #item-list li.selected::before {
            content: '►';
        }

        .item-price {
            font-weight: bold;
        }

        /* Caixa de Descrição (Inferior) */
        .description-box {
            background: var(--bg-blue);
            color: var(--text-light);
            border: 2px solid var(--border-color);
            padding: 15px;
            min-height: 100px;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        #desc-name {
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Área de Mensagem (Sucesso/Erro) */
        .message-area {
            background: var(--bg-light);
            border: 2px solid var(--border-color);
            padding: 10px;
            text-align: center;
            font-size: 12px;
            min-height: 40px;
            margin-bottom: 10px;
            /* Começa escondido */
            opacity: 0;
            transition: opacity 0.3s;
        }

        .message-area.visible {
            opacity: 1;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        /* Botões com estilo pixelado */
        .btn-pixel {
            font-family: var(--pixel-font);
            font-size: 14px;
            padding: 12px 20px;
            background: #ddd;
            border: 2px solid var(--border-color);
            color: var(--text-dark);
            cursor: pointer;
            text-decoration: none;
            box-shadow: 2px 2px 0px 0px rgba(0,0,0,1);
            transition: all 0.1s;
        }

        .btn-pixel:hover:not(:disabled) {
            background: #ccc;
        }

        .btn-pixel:active:not(:disabled) {
            transform: translate(2px, 2px);
            box-shadow: none;
        }
        
        .btn-pixel:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

    </style>
</head>
<body>
    <main class="shop-container">
        
        <div class="shop-ui-panel">
            <aside class="left-panel">
                <div class="money-box">
                    <div>MONEY</div>
                    <div><span id="playerGold">P {{ $character->gold }}</span></div>
                </div>
                <img src="{{ asset($character->avatar) }}" alt="Avatar" class="player-avatar">
            </aside>

            <section class="right-panel">
                <ul id="item-list">
                    </ul>
            </section>
        </div>

        <div id="description-box" class="description-box">
            <div id="desc-name">Selecione um item...</div>
            <div id="desc-text"></div>
        </div>

        <div class="message-area" id="messageArea"></div>
        
        <div class="action-buttons">
            <button id="buy-selected-btn" class="btn-pixel">COMPRAR</button>
            <a href="{{ route('character.' . $next_stage, $character->id) }}" class="btn-pixel">PROSSEGUIR</a>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const Shop = {
                state: {
                    gold: parseInt("{{ $character->gold ?? 0 }}", 10),
                    // Mantemos o inventário no state para a função saveProgress
                    inventory: {
                        potion: parseInt("{{ $character->potions ?? 0 }}", 10),
                        pokeball: parseInt("{{ $character->pokeballs ?? 0 }}", 10),
                        thunderstone: parseInt("{{ $character->thunderstones ?? 0 }}", 10),
                        greatball: parseInt("{{ $character->greatballs ?? 0 }}", 10),
                    },
                    selectedItemKey: null
                },
                // Seus itens originais do código
                itemsForSale: [
                    { key: 'pokeball', name: 'POKÉ BALL', price: 200, description: 'Um dispositivo para capturar Pokémon selvagens.', icon: '...' },
                    { key: 'potion', name: 'POTION', price: 300, description: 'Recupera 20 HP de um Pokémon.', icon: '...' },
                    { key: 'thunderstone', name: 'THUNDER STONE', price: 2100, description: 'Uma pedra peculiar que faz certos tipos de Pokémon evoluírem.', icon: '...' },
                    { key: 'greatball', name: 'GREAT BALL', price: 600, description: 'Uma bola com uma taxa de captura melhor que a Poké Ball.', icon: '...' }
                ],
                elements: {
                    goldDisplay: document.getElementById('playerGold'),
                    itemList: document.getElementById('item-list'),
                    messageArea: document.getElementById('messageArea'),
                    descName: document.getElementById('desc-name'),
                    descText: document.getElementById('desc-text'),
                    buyButton: document.getElementById('buy-selected-btn')
                },
                
                init() {
                    this.renderItems();
                    this.elements.buyButton.addEventListener('click', () => this.buyItem());
                    this.updateUI(); // Para desabilitar o botão de comprar no início
                },
                
                renderItems() {
                    this.elements.itemList.innerHTML = '';
                    this.itemsForSale.forEach(item => {
                        const li = document.createElement('li');
                        li.dataset.key = item.key;
                        li.innerHTML = `
                            <span>${item.name}</span>
                            <span class="item-price">P ${item.price}</span>
                        `;
                        this.elements.itemList.appendChild(li);
                    });
                    this.addSelectionListeners();
                },

                addSelectionListeners() {
                    this.elements.itemList.querySelectorAll('li').forEach(li => {
                        li.addEventListener('click', () => {
                            // Desmarca o item antigo
                            const oldSelected = this.elements.itemList.querySelector('li.selected');
                            if (oldSelected) {
                                oldSelected.classList.remove('selected');
                            }

                            // Marca o novo item
                            li.classList.add('selected');
                            const key = li.dataset.key;
                            this.state.selectedItemKey = key;
                            
                            // Atualiza a descrição
                            const item = this.itemsForSale.find(i => i.key === key);
                            if (item) {
                                this.elements.descName.textContent = item.name;
                                this.elements.descText.textContent = item.description;
                            }
                            this.updateUI(); // Atualiza o estado do botão "COMPRAR"
                        });
                    });
                },

                buyItem() {
                    const itemKey = this.state.selectedItemKey;
                    if (!itemKey) {
                        this.displayMessage('Selecione um item para comprar!', 'error');
                        return;
                    }

                    const item = this.itemsForSale.find(i => i.key === itemKey);
                    if (!item) return;

                    if (this.state.gold >= item.price) {
                        this.state.gold -= item.price;
                        this.state.inventory[item.key] = (this.state.inventory[item.key] || 0) + 1;
                        
                        this.displayMessage(`Você comprou ${item.name}!`, 'success');
                        this.updateUI();
                        this.saveProgress();
                    } else {
                        this.displayMessage('POKÉ$ insuficiente!', 'error');
                    }
                },

                updateUI() {
                    // Atualiza o ouro
                    this.elements.goldDisplay.textContent = `P ${this.state.gold}`;
                    
                    // Atualiza o botão de comprar
                    const key = this.state.selectedItemKey;
                    if (!key) {
                        this.elements.buyButton.disabled = true;
                        return;
                    }
                    
                    const item = this.itemsForSale.find(i => i.key === key);
                    if (item) {
                        this.elements.buyButton.disabled = this.state.gold < item.price;
                    } else {
                        this.elements.buyButton.disabled = true;
                    }
                },
                
                displayMessage(text, type) {
                    const msgArea = this.elements.messageArea;
                    msgArea.textContent = text;
                    msgArea.style.color = type === 'success' ? 'var(--success-color)' : 'var(--error-color)';
                    msgArea.classList.add('visible');
                    setTimeout(() => msgArea.classList.remove('visible'), 3000);
                },

                // Esta função permanece IDÊNTICA, pois é a sua lógica de back-end
                async saveProgress() {
                    try {
                        const dataToSend = {
                            gold: this.state.gold,
                            potions: this.state.inventory.potion,
                            pokeballs: this.state.inventory.pokeball,
                            thunderstones: this.state.inventory.thunderstone,
                            greatballs: this.state.inventory.greatball
                        };
                        
                        const response = await fetch("{{ route('character.updateStats', $character->id) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(dataToSend)
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Falha ao salvar o progresso no servidor.');
                        }
                        console.log('Progresso salvo com sucesso!', dataToSend);

                    } catch (error) {
                        console.error('Erro ao salvar:', error);
                        this.displayMessage('ERRO DE CONEXÃO AO SALVAR!', 'error');
                    }
                }
            };

            Shop.init();
        });
    </script>
</body>
</html>