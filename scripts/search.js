document.addEventListener('DOMContentLoaded', () => {
    
    // UI Elements
    const searchInput = document.getElementById('searchInput');
    const genreSelect = document.getElementById('genreSelect');
    const sortSelect = document.getElementById('sortSelect');
    
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
    //  FILTER & SORT LOGIC
    // ==========================================
    function filterAndSortGames() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedGenre = genreSelect.value;
        const sortValue = sortSelect.value;
        
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

        // Ordenação (Preço)
        if (sortValue !== 'default') {
            visibleCards.sort((a, b) => {
                const priceA = parseFloat(a.dataset.price) || 0;
                const priceB = parseFloat(b.dataset.price) || 0;

                if (sortValue === 'price-asc') {
                    return priceA - priceB;
                } else if (sortValue === 'price-desc') {
                    return priceB - priceA;
                }
                return 0;
            });
        }

        // Re-inserir no DOM pela ordem correta
        visibleCards.forEach(card => gamesGrid.appendChild(card));

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
    if (searchInput) searchInput.addEventListener('input', filterAndSortGames);
    if (genreSelect) genreSelect.addEventListener('change', filterAndSortGames);
    if (sortSelect) sortSelect.addEventListener('change', filterAndSortGames);

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
    
    document.addEventListener('click', async function(e) {
        
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