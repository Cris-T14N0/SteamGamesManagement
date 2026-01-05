<!-- Modal de Detalhes do Jogo - Reutilizável -->
<div id="gameDetailsModal" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-[#1b2838] border border-[#2a475e] rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl">
        <div id="gameDetailsContent" class="p-6">
            <!-- Loading state inicial -->
            <div class="flex items-center justify-center py-12">
                <i class="bi bi-arrow-clockwise animate-spin text-[#66c0f4] text-4xl"></i>
            </div>
        </div>
    </div>
</div>

<style>
    /* Scrollbar customizada para o modal */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #0d1218;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #2a475e;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #66c0f4;
    }
</style>

<script>
// Script do modal de detalhes do jogo - Funciona em qualquer página
document.addEventListener('DOMContentLoaded', function () {
    const gameDetailsModal = document.getElementById('gameDetailsModal');
    const gameDetailsContent = document.getElementById('gameDetailsContent');

    // Variável global para guardar o ID do jogo atual no modal
    window.currentModalGameId = null;

    // Função para abrir o modal com os detalhes do jogo
    window.openGameDetails = function(gameId) {
        window.currentModalGameId = gameId;
        gameDetailsModal.classList.remove('hidden');
        gameDetailsModal.classList.add('flex');

        // Mostra estado de loading
        gameDetailsContent.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <i class="bi bi-arrow-clockwise animate-spin text-[#66c0f4] text-4xl"></i>
            </div>
        `;

        // Busca os detalhes do jogo pela API
        fetch(`../api/library/get_game_details.php?id=${gameId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    displayGameDetails(data.game);
                } else {
                    gameDetailsContent.innerHTML = `
                        <div class="text-center py-12">
                            <i class="bi bi-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                            <p class="text-[#acbccc]">Falha ao carregar detalhes do jogo</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                gameDetailsContent.innerHTML = `
                    <div class="text-center py-12">
                        <i class="bi bi-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                        <p class="text-[#acbccc]">Ocorreu um erro ao carregar</p>
                    </div>
                `;
            });
    };

    // Função para atualizar o botão no modal de detalhes
    window.updateModalButton = function(gameId, inLibrary) {
        if (window.currentModalGameId !== gameId) return;

        const actionSection = document.querySelector('#gameDetailsContent .mt-6.flex.items-center.justify-end');
        if (!actionSection) return;

        const gameName = actionSection.querySelector('[data-game-name]')?.dataset.gameName || '';

        if (inLibrary) {
            actionSection.innerHTML = `
                <span class="bg-[#5c7e10] text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> Na Biblioteca
                </span>
            `;
        } else {
            actionSection.innerHTML = `
                <button class="add-btn bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-bold px-6 py-3 rounded-lg transition-all flex items-center gap-2"
                        data-game-id="${gameId}" data-game-name="${gameName}">
                    <i class="bi bi-plus-circle"></i> Adicionar à Biblioteca
                </button>
            `;
        }
    };

    // Função para exibir os detalhes do jogo no modal
    function displayGameDetails(game) {
        const imageUrl = `https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/${game.game_identifier}/header.jpg`;

        // Gera HTML das reviews se existirem
        let reviewHTML = '';
        if (game.overall_review) {
            const reviewColor = getReviewColor(game.overall_review_pct);
            reviewHTML = `
                <div class="bg-[#0d1218] rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-[#acbccc]">Avaliações Gerais</span>
                        <span class="text-${reviewColor} font-semibold">${game.overall_review}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-[#1b2838] rounded-full h-2">
                            <div class="bg-${reviewColor} h-2 rounded-full" style="width: ${game.overall_review_pct}%"></div>
                        </div>
                        <span class="text-sm text-[#acbccc]">${game.overall_review_pct}%</span>
                    </div>
                    <p class="text-xs text-[#acbccc] mt-2">${game.overall_review_count?.toLocaleString() || 0} avaliações</p>
                </div>
            `;
        }

        // Renderiza o conteúdo completo do modal
        gameDetailsContent.innerHTML = `
            <button onclick="closeGameDetailsModal()" 
                    class="absolute top-4 right-4 text-[#acbccc] hover:text-white transition-colors text-2xl z-10">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="mb-6">
                <img src="${imageUrl}" alt="${game.title}" class="w-full rounded-lg mb-4" 
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'460\' height=\'215\'%3E%3Crect fill=\'%231b2838\' width=\'460\' height=\'215\'/%3E%3C/svg%3E'">
                <h2 class="text-3xl font-bold text-white mb-2 steam-font">${game.title}</h2>
                <div class="flex flex-wrap gap-2 mb-4">
                    ${game.genres ? game.genres.split(', ').map(g => `<span class="bg-[#2a475e] text-[#acbccc] px-3 py-1 rounded-md text-sm">${g}</span>`).join('') : ''}
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-4">
                    ${game.release_date ? `
                    <div>
                        <span class="text-[#acbccc] text-sm">Data de Lançamento</span>
                        <p class="text-white font-medium">${new Date(game.release_date).toLocaleDateString('pt-PT', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                    </div>
                    ` : ''}
                    
                    ${game.developer ? `
                    <div>
                        <span class="text-[#acbccc] text-sm">Desenvolvedor</span>
                        <p class="text-white font-medium">${game.developer}</p>
                    </div>
                    ` : ''}
                    
                    ${game.publisher ? `
                    <div>
                        <span class="text-[#acbccc] text-sm">Editora</span>
                        <p class="text-white font-medium">${game.publisher}</p>
                    </div>
                    ` : ''}
                    
                    ${game.age_rating ? `
                    <div>
                        <span class="text-[#acbccc] text-sm">Classificação Etária</span>
                        <p class="text-white font-medium">${game.age_rating}</p>
                    </div>
                    ` : ''}
                </div>

                <div class="space-y-4">
                    ${game.platforms ? `
                    <div>
                        <span class="text-[#acbccc] text-sm">Plataformas</span>
                        <div class="flex gap-2 mt-1">
                            ${game.platforms.split(', ').map(p => `<i class="bi bi-${getPlatformIcon(p)} text-white text-xl" title="${p}"></i>`).join('')}
                        </div>
                    </div>
                    ` : ''}
                    
                    ${game.categories ? `
                    <div>
                        <span class="text-[#acbccc] text-sm">Características</span>
                        <div class="flex flex-wrap gap-2 mt-1">
                            ${game.categories.split(', ').slice(0, 5).map(c => `<span class="bg-[#0d1218] text-[#acbccc] px-2 py-1 rounded text-xs">${c}</span>`).join('')}
                        </div>
                    </div>
                    ` : ''}
                    
                    ${game.dlc_available ? `
                    <div>
                        <span class="bg-[#66c0f4] text-white px-3 py-1 rounded-md text-sm inline-flex items-center gap-2">
                            <i class="bi bi-puzzle"></i> DLC Disponível
                        </span>
                    </div>
                    ` : ''}
                </div>
            </div>

            ${reviewHTML}

            ${game.about_description ? `
            <div class="mt-6 bg-[#0d1218] rounded-lg p-4">
                <h3 class="text-lg font-semibold text-white mb-3">Sobre Este Jogo</h3>
                <p class="text-[#acbccc] leading-relaxed">${game.about_description}</p>
            </div>
            ` : ''}

            ${game.awards ? `
            <div class="mt-6 bg-[#0d1218] rounded-lg p-4">
                <h3 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                    <i class="bi bi-trophy-fill text-yellow-500"></i> Prémios
                </h3>
                <p class="text-[#acbccc]">${game.awards}</p>
            </div>
            ` : ''}

            <div class="mt-6 flex items-center justify-end p-4 bg-[#0d1218] rounded-lg">
                ${game.inLibrary ? `
                    <span class="bg-[#5c7e10] text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i> Na Biblioteca
                    </span>
                ` : `
                    <button class="add-btn bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-bold px-6 py-3 rounded-lg transition-all flex items-center gap-2"
                            data-game-id="${game.id_game}" data-game-name="${game.title}">
                        <i class="bi bi-plus-circle"></i> Adicionar à Biblioteca
                    </button>
                `}
            </div>
        `;
    }

    // Função para fechar o modal
    window.closeGameDetailsModal = function() {
        gameDetailsModal.classList.add('hidden');
        gameDetailsModal.classList.remove('flex');
        window.currentModalGameId = null;
    };

    // Função auxiliar para determinar cor da review
    function getReviewColor(percentage) {
        if (percentage >= 80) return '[#66c0f4]';
        if (percentage >= 70) return '[#a4d007]';
        if (percentage >= 40) return 'yellow-500';
        return 'red-500';
    }

    // Função auxiliar para obter icone da plataforma
    function getPlatformIcon(platform) {
        const icons = {
            'Windows': 'windows',
            'Mac': 'apple',
            'Linux': 'ubuntu',
        };
        return icons[platform] || 'display';
    }

    // Fecha modal ao clicar fora dele
    gameDetailsModal.addEventListener('click', function (e) {
        if (e.target === gameDetailsModal) {
            closeGameDetailsModal();
        }
    });

    // Fecha modal com tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !gameDetailsModal.classList.contains('hidden')) {
            closeGameDetailsModal();
        }
    });
});
</script>