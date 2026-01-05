document.addEventListener('DOMContentLoaded', () => {

    // UI Elements
    const searchInput = document.getElementById('searchInput');
    const genreSelect = document.getElementById('genreSelect');

    const gamesGrid = document.getElementById('gamesGrid');
    const noResults = document.getElementById('noResults');
    const resultCount = document.getElementById('resultCount');

    // Modal Elements
    const addGameModal = document.getElementById('addGameModal');
    const listsCheckboxes = document.getElementById('listsCheckboxes');
    const cancelAddBtn = document.getElementById('cancelAddBtn');
    const confirmAddBtn = document.getElementById('confirmAddBtn');
    const modalGameTitle = document.getElementById('modalGameTitle');

    let selectedGameId = null;
    let selectedGameName = null;
    let selectedAddBtn = null;

    // Toast Notification
    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('toast');
        const toastIcon = document.getElementById('toastIcon');
        const toastMessage = document.getElementById('toastMessage');

        if (!toast) return;

        toastIcon.className = isSuccess
            ? 'bi bi-check-circle-fill text-[#5c7e10] text-2xl'
            : 'bi bi-x-circle-fill text-red-500 text-2xl';
        toastMessage.textContent = message;

        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';

        setTimeout(() => {
            toast.style.transform = 'translateY(8rem)';
            toast.style.opacity = '0';
        }, 3000);
    }

    // ==========================================
    //  FILTER LOGIC
    // ==========================================
    function filterGames() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedGenre = genreSelect.value;

        const allCards = Array.from(document.querySelectorAll('.game-card'));
        let visibleCards = [];

        // Filtragem (Texto e Género)
        allCards.forEach(card => {
            const gameName = card.dataset.name;
            const gameGenre = card.dataset.genre || '';

            const matchesSearch = gameName.includes(searchTerm);
            const matchesGenre = selectedGenre === 'all' || gameGenre.includes(selectedGenre);

            if (matchesSearch && matchesGenre) {
                card.style.display = '';
                visibleCards.push(card);
            } else {
                card.style.display = 'none';
            }
        });

        // Atualizar contadores e estado vazio
        if (resultCount) resultCount.textContent = visibleCards.length;

        if (visibleCards.length === 0) {
            gamesGrid.classList.add('hidden');
            noResults.classList.remove('hidden');
        } else {
            gamesGrid.classList.remove('hidden');
            noResults.classList.add('hidden');
        }
    }

    // Event Listeners para Filtros
    if (searchInput) searchInput.addEventListener('input', filterGames);
    if (genreSelect) genreSelect.addEventListener('change', filterGames);

    // ==========================================
    //  MODAL LOGIC
    // ==========================================

    async function fetchListsAndRender() {
        listsCheckboxes.innerHTML = '<div class="text-center text-[#acbccc] text-sm">Loading lists...</div>';
        try {
            const res = await fetch('../api/library/get_lists.php');
            const json = await res.json();

            if (json.success && json.lists.length > 0) {
                listsCheckboxes.innerHTML = '';
                json.lists.forEach((list, index) => {
                    const isDefault = index === 0;
                    const item = document.createElement('label');
                    item.className = 'flex items-center gap-3 p-3 border border-[#2a475e] rounded bg-[#0d1218] hover:bg-[#2a475e] cursor-pointer transition-colors';
                    item.innerHTML = `
                        <input type="checkbox" value="${list.id_library}" class="w-5 h-5 bg-[#1b2838] border-[#66c0f4] rounded focus:ring-0 text-[#66c0f4]" ${isDefault ? 'checked' : ''}>
                        <span class="text-white text-sm select-none">${list.name}</span>
                    `;
                    listsCheckboxes.appendChild(item);
                });
            } else {
                listsCheckboxes.innerHTML = '<div class="text-center text-red-400 text-sm">No lists found. Please create one first.</div>';
            }
        } catch (err) {
            console.error(err);
            listsCheckboxes.innerHTML = '<div class="text-center text-red-400 text-sm">Error loading lists.</div>';
        }
    }

    function closeAddModal() {
        addGameModal.classList.add('hidden');
        addGameModal.classList.remove('flex');
        selectedGameId = null;
        selectedGameName = null;
        selectedAddBtn = null;
    }

    if (cancelAddBtn) cancelAddBtn.addEventListener('click', closeAddModal);

    if (confirmAddBtn) {
        confirmAddBtn.addEventListener('click', async () => {
            const checkboxes = listsCheckboxes.querySelectorAll('input[type="checkbox"]:checked');
            const listIds = Array.from(checkboxes).map(cb => cb.value);

            if (listIds.length === 0) {
                showToast('Please select at least one list', false);
                return;
            }

            const originalBtnContent = confirmAddBtn.innerHTML;
            confirmAddBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i>';
            confirmAddBtn.disabled = true;

            try {
                const res = await fetch('../api/library/add_game.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ game_id: selectedGameId, list_ids: listIds })
                });
                const json = await res.json();

                if (json.success) {
                    showToast(json.message, true);
                    if (selectedAddBtn) {
                        const card = selectedAddBtn.closest('.game-card');
                        const imageContainer = card.querySelector('.aspect-\\[16\\/9\\]');

                        selectedAddBtn.outerHTML = `
                            <button class="remove-btn w-full bg-red-900/30 hover:bg-red-900/50 border border-red-700/50 text-red-400 font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                                    data-game-id="${selectedGameId}"
                                    data-game-name="${selectedGameName}">
                                <i class="bi bi-dash-circle"></i> Remove
                            </button>
                        `;

                        if (!imageContainer.querySelector('.library-badge')) {
                            const badge = document.createElement('div');
                            badge.className = 'library-badge absolute top-2 right-2 bg-[#5c7e10] text-white text-xs px-2 py-1 rounded-md animate-fade-in';
                            badge.innerHTML = '<i class="bi bi-check-circle-fill"></i> In Library';
                            imageContainer.appendChild(badge);
                        }
                    }
                    closeAddModal();
                } else {
                    showToast(json.message || 'Error adding game', false);
                }
            } catch (err) {
                showToast('Connection error', false);
            } finally {
                confirmAddBtn.innerHTML = originalBtnContent;
                confirmAddBtn.disabled = false;
            }
        });
    }

    // ==========================================
    //  CLICK HANDLERS (ADD / REMOVE)
    // ==========================================

    document.addEventListener('click', async function (e) {

        // ADD
        const addBtn = e.target.closest('.add-btn');
        if (addBtn) {
            selectedGameId = addBtn.dataset.gameId;
            selectedGameName = addBtn.dataset.gameName;
            selectedAddBtn = addBtn;
            modalGameTitle.textContent = `Adding: ${selectedGameName}`;

            addGameModal.classList.remove('hidden');
            addGameModal.classList.add('flex');
            await fetchListsAndRender();
        }

        // REMOVE
        const removeBtn = e.target.closest('.remove-btn');
        if (removeBtn) {
            const card = removeBtn.closest('.game-card');
            const gameId = removeBtn.dataset.gameId;
            const gameName = removeBtn.dataset.gameName;
            const imageContainer = card.querySelector('.aspect-\\[16\\/9\\]');

            const originalContent = removeBtn.innerHTML;
            removeBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i>';
            removeBtn.disabled = true;

            try {
                const res = await fetch('../api/library/remove_game.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ game_id: gameId })
                });

                const json = await res.json();

                if (json.success) {
                    showToast(`${gameName} removed from all lists`, false);
                    removeBtn.outerHTML = `
                        <button class="add-btn w-full bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                                data-game-id="${gameId}"
                                data-game-name="${gameName}">
                            <i class="bi bi-plus-circle"></i> Add to Library
                        </button>
                    `;
                    const badge = imageContainer.querySelector('.library-badge');
                    if (badge) badge.remove();
                } else {
                    showToast(json.message || 'Error removing game', false);
                    removeBtn.innerHTML = originalContent;
                    removeBtn.disabled = false;
                }
            } catch (err) {
                showToast('Connection error', false);
                removeBtn.innerHTML = originalContent;
                removeBtn.disabled = false;
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !addGameModal.classList.contains('hidden')) {
            closeAddModal();
        }
    });
});

// Game Details Modal Functionality
document.addEventListener('DOMContentLoaded', function () {
    const gameImages = document.querySelectorAll('.game-image');
    const gameDetailsModal = document.getElementById('gameDetailsModal');
    const gameDetailsContent = document.getElementById('gameDetailsContent');

    gameImages.forEach(img => {
        img.addEventListener('click', function () {
            const gameId = this.dataset.gameId;
            openGameDetails(gameId);
        });
    });

    function openGameDetails(gameId) {
        gameDetailsModal.classList.remove('hidden');
        gameDetailsModal.classList.add('flex');

        // Show loading state
        gameDetailsContent.innerHTML = `
                    <div class="flex items-center justify-center py-12">
                        <i class="bi bi-arrow-clockwise animate-spin text-[#66c0f4] text-4xl"></i>
                    </div>
                `;

        // Fetch game details
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
                                    <p class="text-[#acbccc]">Failed to load game details</p>
                                </div>
                            `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                gameDetailsContent.innerHTML = `
                            <div class="text-center py-12">
                                <i class="bi bi-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                                <p class="text-[#acbccc]">An error occurred</p>
                            </div>
                        `;
            });
    }

    function displayGameDetails(game) {
        const imageUrl = `https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/${game.game_identifier}/header.jpg`;

        let reviewHTML = '';
        if (game.overall_review) {
            const reviewColor = getReviewColor(game.overall_review_pct);
            reviewHTML = `
                        <div class="bg-[#0d1218] rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-[#acbccc]">Overall Reviews</span>
                                <span class="text-${reviewColor} font-semibold">${game.overall_review}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-[#1b2838] rounded-full h-2">
                                    <div class="bg-${reviewColor} h-2 rounded-full" style="width: ${game.overall_review_pct}%"></div>
                                </div>
                                <span class="text-sm text-[#acbccc]">${game.overall_review_pct}%</span>
                            </div>
                            <p class="text-xs text-[#acbccc] mt-2">${game.overall_review_count?.toLocaleString() || 0} reviews</p>
                        </div>
                    `;
        }

        gameDetailsContent.innerHTML = `
                    <button onclick="document.getElementById('gameDetailsModal').classList.add('hidden'); document.getElementById('gameDetailsModal').classList.remove('flex');" 
                            class="absolute top-4 right-4 text-[#acbccc] hover:text-white transition-colors text-2xl">
                        <i class="bi bi-x-lg"></i>
                    </button>

                    <div class="mb-6">
                        <img src="${imageUrl}" alt="${game.title}" class="w-full rounded-lg mb-4" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'460\' height=\'215\'%3E%3Crect fill=\'%231b2838\' width=\'460\' height=\'215\'/%3E%3C/svg%3E'">
                        <h2 class="text-3xl font-bold text-white mb-2 steam-font">${game.title}</h2>
                        <div class="flex flex-wrap gap-2 mb-4">
                            ${game.genres ? game.genres.split(', ').map(g => `<span class="bg-[#2a475e] text-[#acbccc] px-3 py-1 rounded-md text-sm">${g}</span>`).join('') : ''}
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-4">
                            ${game.release_date ? `
                            <div>
                                <span class="text-[#acbccc] text-sm">Release Date</span>
                                <p class="text-white font-medium">${new Date(game.release_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                            ` : ''}
                            
                            ${game.developer ? `
                            <div>
                                <span class="text-[#acbccc] text-sm">Developer</span>
                                <p class="text-white font-medium">${game.developer}</p>
                            </div>
                            ` : ''}
                            
                            ${game.publisher ? `
                            <div>
                                <span class="text-[#acbccc] text-sm">Publisher</span>
                                <p class="text-white font-medium">${game.publisher}</p>
                            </div>
                            ` : ''}
                            
                            ${game.age_rating ? `
                            <div>
                                <span class="text-[#acbccc] text-sm">Age Rating</span>
                                <p class="text-white font-medium">${game.age_rating}</p>
                            </div>
                            ` : ''}
                        </div>

                        <div class="space-y-4">
                            ${game.platforms ? `
                            <div>
                                <span class="text-[#acbccc] text-sm">Platforms</span>
                                <div class="flex gap-2 mt-1">
                                    ${game.platforms.split(', ').map(p => `<i class="bi bi-${getPlatformIcon(p)} text-white text-xl" title="${p}"></i>`).join('')}
                                </div>
                            </div>
                            ` : ''}
                            
                            ${game.categories ? `
                            <div>
                                <span class="text-[#acbccc] text-sm">Features</span>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    ${game.categories.split(', ').slice(0, 5).map(c => `<span class="bg-[#0d1218] text-[#acbccc] px-2 py-1 rounded text-xs">${c}</span>`).join('')}
                                </div>
                            </div>
                            ` : ''}
                            
                            ${game.dlc_available ? `
                            <div>
                                <span class="bg-[#66c0f4] text-white px-3 py-1 rounded-md text-sm inline-flex items-center gap-2">
                                    <i class="bi bi-puzzle"></i> DLC Available
                                </span>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    ${reviewHTML}

                    ${game.about_description ? `
                    <div class="mt-6 bg-[#0d1218] rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-white mb-3">About This Game</h3>
                        <p class="text-[#acbccc] leading-relaxed">${game.about_description}</p>
                    </div>
                    ` : ''}

                    ${game.awards ? `
                    <div class="mt-6 bg-[#0d1218] rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                            <i class="bi bi-trophy-fill text-yellow-500"></i> Awards
                        </h3>
                        <p class="text-[#acbccc]">${game.awards}</p>
                    </div>
                    ` : ''}

                    <div class="mt-6 flex items-center justify-end p-4 bg-[#0d1218] rounded-lg">
                        ${game.inLibrary ? `
                            <span class="bg-[#5c7e10] text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i> In Library
                            </span>
                        ` : `
                            <button class="add-btn bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-bold px-6 py-3 rounded-lg transition-all flex items-center gap-2"
                                    data-game-id="${game.id_game}" data-game-name="${game.title}">
                                <i class="bi bi-plus-circle"></i> Add to Library
                            </button>
                        `}
                    </div>
                `;
    }

    function getReviewColor(percentage) {
        if (percentage >= 80) return '[#66c0f4]';
        if (percentage >= 70) return '[#a4d007]';
        if (percentage >= 40) return 'yellow-500';
        return 'red-500';
    }

    function getPlatformIcon(platform) {
        const icons = {
            'Windows': 'windows',
            'Mac': 'apple',
            'Linux': 'ubuntu',
        };
        return icons[platform] || 'display';
    }

    // Close modal when clicking outside
    gameDetailsModal.addEventListener('click', function (e) {
        if (e.target === gameDetailsModal) {
            gameDetailsModal.classList.add('hidden');
            gameDetailsModal.classList.remove('flex');
        }
    });
});