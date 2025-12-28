document.addEventListener('DOMContentLoaded', () => {
    
    // ==========================================
    //  1. UI VARIABLES & HELPER FUNCTIONS
    // ==========================================
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const listsContainer = document.getElementById('listsContainer');
    const noSearchResults = document.getElementById('noSearchResults');

    // Toast Notification Function
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
    //  2. SEARCH & SORT LOGIC
    // ==========================================
    function filterAndSortLists() {
        const listCards = Array.from(document.querySelectorAll('.list-card'));
        const searchTerm = searchInput.value.toLowerCase().trim();
        const sortValue = sortSelect.value;
        let visibleCount = 0;

        listCards.forEach(card => {
            const listName = card.dataset.listName;
            if (listName.includes(searchTerm)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const visibleLists = listCards.filter(card => card.style.display !== 'none');
        
        visibleLists.sort((a, b) => {
            const aName = a.dataset.listName;
            const bName = b.dataset.listName;
            const aGames = a.querySelectorAll('.games-list > div').length;
            const bGames = b.querySelectorAll('.games-list > div').length;

            switch(sortValue) {
                case 'name-asc': return aName.localeCompare(bName);
                case 'name-desc': return bName.localeCompare(aName);
                case 'games-desc': return bGames - aGames;
                case 'games-asc': return aGames - bGames;
                default: return 0;
            }
        });

        visibleLists.forEach(list => listsContainer.appendChild(list));

        if (visibleCount === 0 && listCards.length > 0) {
            noSearchResults.classList.remove('hidden');
        } else {
            noSearchResults.classList.add('hidden');
        }
    }

    if (searchInput && sortSelect) {
        searchInput.addEventListener('input', filterAndSortLists);
        sortSelect.addEventListener('change', filterAndSortLists);
    }

    // ==========================================
    // (DROPDOWNS & MENUS)
    // ==========================================
    
    document.addEventListener('click', function(e) {
        const toggle = e.target.closest('.toggle-games');
        if (toggle) {
            if (e.target.closest('.menu-btn') || e.target.closest('.menu-dropdown')) return;

            const listCard = toggle.closest('.list-card');
            const gamesList = listCard.querySelector('.games-list');
            const chevron = listCard.querySelector('.chevron-icon');

            gamesList.classList.toggle('hidden');
            chevron.classList.toggle('rotate-90');
        }
    });

    // Toggle Action Menus 
    document.addEventListener('click', function(e) {
        const menuBtn = e.target.closest('.menu-btn');
        
        if (menuBtn) {
            e.stopPropagation();
            document.querySelectorAll('.menu-dropdown').forEach(d => d.classList.add('hidden'));
            
            const dropdown = menuBtn.nextElementSibling;
            dropdown.classList.toggle('hidden');
            return;
        }

        if (!e.target.closest('.menu-dropdown')) {
            document.querySelectorAll('.menu-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    // ==========================================
    //  CRUD OPERATIONS (MODAL & API)
    // ==========================================
    const listModal = document.getElementById('listModal');
    const modalTitle = document.getElementById('modalTitle');
    const listNameInput = document.getElementById('listNameInput');
    const createListBtn = document.getElementById('createListBtn');
    const saveBtn = document.getElementById('saveBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    
    let editingListId = null;

    // Modal 
    function openModal(title, listName = '', listId = null) {
        modalTitle.textContent = title;
        listNameInput.value = listName;
        editingListId = listId;
        listModal.classList.remove('hidden');
        listModal.classList.add('flex');
        listNameInput.focus();
    }

    function closeModal() {
        listModal.classList.add('hidden');
        listModal.classList.remove('flex');
        listNameInput.value = '';
        editingListId = null;
    }

    if (createListBtn) {
        createListBtn.addEventListener('click', () => openModal('Create New List'));
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    // CREATE / UPDATE ACTION
    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
            const name = listNameInput.value.trim();
            if (!name) return showToast('Please enter a list name', false);

            const url = editingListId 
                ? '../api/library/update.php' 
                : '../api/library/create.php';
            
            const payload = editingListId 
                ? { id: editingListId, name: name }
                : { name: name };

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const text = await res.text();
                let json;
                try {
                    json = JSON.parse(text);
                } catch (e) {
                    console.error("Server Response:", text);
                    showToast('Server error. Check console.', false);
                    return;
                }

                if (json.success) {
                    showToast(json.message, true);
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(json.message || 'Error saving list', false);
                }
            } catch (err) {
                console.error(err);
                showToast('Connection error', false);
            }
        });
    }

    // EDIT BUTTON CLICK
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-list');
        if (editBtn) {
            const id = editBtn.dataset.listId;
            const name = editBtn.dataset.listName;
            openModal('Edit List', name, id);
        }
    });

    // DELETE BUTTON CLICK
    document.addEventListener('click', async function(e) {
        const deleteBtn = e.target.closest('.delete-list');
        if (deleteBtn) {
            const id = deleteBtn.dataset.listId;
            const name = deleteBtn.dataset.listName;

            if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
                try {
                    const res = await fetch('../api/library/delete.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    });
                    
                    const json = await res.json();

                    if (json.success) {
                        showToast(json.message, true);
                        const card = document.getElementById(`list-${id}`);
                        if (card) {
                            card.style.transition = 'all 0.3s';
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 300);
                        }
                        setTimeout(() => {
                            if (document.querySelectorAll('.list-card').length === 0) {
                                document.getElementById('noResults')?.classList.remove('hidden'); // O ID do empty state inicial
                            }
                        }, 350);

                    } else {
                        showToast(json.message || 'Error deleting list', false);
                    }
                } catch (err) {
                    showToast('Connection error', false);
                }
            }
        }
    });
});