<?php
session_start();
// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'User';

// Game lists
$gameLists = [
    [
        'id' => 1,
        'name' => 'Games I Played',
        'games' => ['Cyberpunk 2077', 'Elden Ring', 'The Witcher 3', 'Terraria', 'Hades II']
    ],
    [
        'id' => 2,
        'name' => 'Games I Will Play',
        'games' => ['Baldur\'s Gate 3', 'Hollow Knight', 'Sekiro', 'Portal 2']
    ],
    [
        'id' => 3,
        'name' => 'Games I Loved',
        'games' => ['Elden Ring', 'The Witcher 3', 'Portal 2']
    ],
    [
        'id' => 4,
        'name' => 'Games I Hated',
        'games' => ['No Man\'s Sky']
    ],
];

include '../assets/nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Library</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#0d1218] text-gray-100">
    
    <!-- Main Content -->
    <main class="md:ml-60 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">My Library</h1>
                <p class="text-[#acbccc] text-lg">Organize your games into lists</p>
            </div>

            <!-- Search, Sort and Create Button -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Search lists..."
                        class="w-full bg-[#1b2838] border border-[#2a475e] rounded-lg px-4 py-3 pl-12 text-white placeholder-[#acbccc] focus:outline-none focus:border-[#66c0f4] focus:ring-2 focus:ring-[#66c0f4]/20 transition-all"
                    >
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-[#acbccc] text-xl"></i>
                </div>
                <select id="sortSelect" class="bg-[#1b2838] border border-[#2a475e] rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#66c0f4] focus:ring-2 focus:ring-[#66c0f4]/20 transition-all">
                    <option value="default">Default Order</option>
                    <option value="name-asc">Name (A-Z)</option>
                    <option value="name-desc">Name (Z-A)</option>
                    <option value="games-desc">Most Games</option>
                    <option value="games-asc">Least Games</option>
                </select>
                <button id="createListBtn" class="bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-medium py-3 px-6 rounded-lg transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                    <i class="bi bi-plus-circle"></i>
                    Create New List
                </button>
            </div>

            <!-- Game Lists -->
            <div id="listsContainer" class="space-y-6">
                <?php foreach ($gameLists as $list): ?>
                <div class="list-card bg-[#1b2838] border border-[#2a475e] rounded-lg p-6 hover:border-[#66c0f4]/50 transition-all"
                     data-list-name="<?php echo strtolower($list['name']); ?>">
                    
                    <!-- List Header -->
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3 flex-1 cursor-pointer toggle-games">
                            <i class="bi bi-chevron-right text-[#acbccc] text-xl transition-transform chevron-icon"></i>
                            <div>
                                <h2 class="text-2xl font-bold text-white mb-1"><?php echo htmlspecialchars($list['name']); ?></h2>
                                <p class="text-[#acbccc] text-sm"><?php echo count($list['games']); ?> game<?php echo count($list['games']) != 1 ? 's' : ''; ?></p>
                            </div>
                        </div>
                        
                        <!-- Actions Menu -->
                        <div class="relative">
                            <button class="menu-btn text-[#acbccc] hover:text-white p-2 hover:bg-[#2a475e] rounded-lg transition-all" data-list-id="<?php echo $list['id']; ?>">
                                <i class="bi bi-three-dots-vertical text-xl"></i>
                            </button>
                            <div class="menu-dropdown hidden absolute right-0 mt-2 w-48 bg-[#1b2838] border border-[#2a475e] rounded-lg shadow-2xl z-10">
                                <button class="edit-list w-full text-left px-4 py-3 text-white hover:bg-[#2a475e] transition-all flex items-center gap-3 rounded-t-lg"
                                        data-list-id="<?php echo $list['id']; ?>"
                                        data-list-name="<?php echo htmlspecialchars($list['name']); ?>">
                                    <i class="bi bi-pencil"></i>
                                    Edit List
                                </button>
                                <button class="delete-list w-full text-left px-4 py-3 text-red-400 hover:bg-red-900/30 transition-all flex items-center gap-3 rounded-b-lg"
                                        data-list-id="<?php echo $list['id']; ?>"
                                        data-list-name="<?php echo htmlspecialchars($list['name']); ?>">
                                    <i class="bi bi-trash"></i>
                                    Delete List
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Games in List (Hidden by default) -->
                    <div class="games-list hidden mt-4 space-y-2">
                        <?php if (empty($list['games'])): ?>
                        <p class="text-[#acbccc] text-sm italic">No games in this list yet</p>
                        <?php else: ?>
                            <?php foreach ($list['games'] as $game): ?>
                            <div class="bg-[#0d1218] border border-[#2a475e] rounded-lg px-4 py-3 flex items-center gap-3 hover:border-[#66c0f4]/30 transition-all">
                                <i class="bi bi-controller text-[#66c0f4]"></i>
                                <span class="text-white"><?php echo htmlspecialchars($game); ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden text-center py-16 bg-[#1b2838] border border-[#2a475e] rounded-lg">
                <i class="bi bi-search text-6xl text-[#2a475e] mb-4"></i>
                <p class="text-[#acbccc] text-lg">No lists found</p>
            </div>

        </div>
    </main>

    <!-- Create/Edit List Modal -->
    <div id="listModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
        <div class="bg-[#1b2838] border border-[#2a475e] rounded-lg max-w-md w-full p-6">
            <h3 id="modalTitle" class="text-2xl font-bold text-white mb-4">Create New List</h3>
            <input 
                type="text" 
                id="listNameInput"
                placeholder="Enter list name..."
                class="w-full bg-[#0d1218] border border-[#2a475e] rounded-lg px-4 py-3 text-white placeholder-[#acbccc] focus:outline-none focus:border-[#66c0f4] focus:ring-2 focus:ring-[#66c0f4]/20 transition-all mb-6"
            >
            <div class="flex gap-3">
                <button id="cancelBtn" class="flex-1 bg-[#2a475e] hover:bg-[#3a5770] text-white font-medium py-3 px-6 rounded-lg transition-all">
                    Cancel
                </button>
                <button id="saveBtn" class="flex-1 bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-medium py-3 px-6 rounded-lg transition-all">
                    Save
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 right-6 bg-[#1b2838] border border-[#2a475e] rounded-lg px-6 py-4 shadow-2xl transform translate-y-32 opacity-0 transition-all duration-300 z-50">
        <div class="flex items-center gap-3">
            <i id="toastIcon" class="text-2xl"></i>
            <span id="toastMessage" class="text-white font-medium"></span>
        </div>
    </div>

    <script>
        // Search and Sort functionality
        const searchInput = document.getElementById('searchInput');
        const sortSelect = document.getElementById('sortSelect');
        const listCards = document.querySelectorAll('.list-card');
        const listsContainer = document.getElementById('listsContainer');
        const noResults = document.getElementById('noResults');

        function filterAndSortLists() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const sortValue = sortSelect.value;
            let visibleLists = [];

            // Filter lists
            listCards.forEach(card => {
                const listName = card.dataset.listName;
                
                if (listName.includes(searchTerm)) {
                    card.style.display = '';
                    visibleLists.push(card);
                } else {
                    card.style.display = 'none';
                }
            });

            // Sort visible lists
            visibleLists.sort((a, b) => {
                const aName = a.dataset.listName;
                const bName = b.dataset.listName;
                const aGames = a.querySelectorAll('.bg-\\[\\#0d1218\\]').length;
                const bGames = b.querySelectorAll('.bg-\\[\\#0d1218\\]').length;

                switch(sortValue) {
                    case 'name-asc':
                        return aName.localeCompare(bName);
                    case 'name-desc':
                        return bName.localeCompare(aName);
                    case 'games-desc':
                        return bGames - aGames;
                    case 'games-asc':
                        return aGames - bGames;
                    default:
                        return 0;
                }
            });

            // Re-append in sorted order
            visibleLists.forEach(list => {
                listsContainer.appendChild(list);
            });

            // Show/hide no results
            if (visibleLists.length === 0) {
                listsContainer.classList.add('hidden');
                noResults.classList.remove('hidden');
            } else {
                listsContainer.classList.remove('hidden');
                noResults.classList.add('hidden');
            }
        }

        searchInput.addEventListener('input', filterAndSortLists);
        sortSelect.addEventListener('change', filterAndSortLists);

        // Toggle games dropdown
        document.querySelectorAll('.toggle-games').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                // Don't trigger if clicking on menu button
                if (e.target.closest('.menu-btn')) return;
                
                const listCard = this.closest('.list-card');
                const gamesList = listCard.querySelector('.games-list');
                const chevron = listCard.querySelector('.chevron-icon');
                
                gamesList.classList.toggle('hidden');
                chevron.classList.toggle('rotate-90');
            });
        });

        // Toast notification
        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');

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

        // Menu toggle
        document.querySelectorAll('.menu-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdown = this.nextElementSibling;
                
                // Close all other dropdowns
                document.querySelectorAll('.menu-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.add('hidden');
                });
                
                dropdown.classList.toggle('hidden');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.menu-dropdown').forEach(d => {
                d.classList.add('hidden');
            });
        });

        // Modal functionality
        const listModal = document.getElementById('listModal');
        const modalTitle = document.getElementById('modalTitle');
        const listNameInput = document.getElementById('listNameInput');
        const createListBtn = document.getElementById('createListBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const saveBtn = document.getElementById('saveBtn');
        let editingListId = null;

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

        createListBtn.addEventListener('click', () => {
            openModal('Create New List');
        });

        cancelBtn.addEventListener('click', closeModal);

        saveBtn.addEventListener('click', () => {
            const listName = listNameInput.value.trim();
            if (!listName) {
                showToast('Please enter a list name', false);
                return;
            }

            if (editingListId) {
                showToast(`List "${listName}" updated successfully`, true);
            } else {
                showToast(`List "${listName}" created successfully`, true);
            }
            
            closeModal();
            // In real app, this would save to database
        });

        // Edit list
        document.querySelectorAll('.edit-list').forEach(btn => {
            btn.addEventListener('click', function() {
                const listId = this.dataset.listId;
                const listName = this.dataset.listName;
                openModal('Edit List', listName, listId);
            });
        });

        // Delete list
        document.querySelectorAll('.delete-list').forEach(btn => {
            btn.addEventListener('click', function() {
                const listId = this.dataset.listId;
                const listName = this.dataset.listName;
                
                if (confirm(`Are you sure you want to delete "${listName}"?`)) {
                    const listCard = this.closest('.list-card');
                    listCard.style.opacity = '0';
                    listCard.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        listCard.remove();
                        showToast(`List "${listName}" deleted`, false);
                    }, 300);
                }
            });
        });

        // Close modal on escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>

</body>
</html>