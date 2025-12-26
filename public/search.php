<?php
session_start();
// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'User';

// Static game data with some games marked as "in library"
$games = [
    ['id' => 1, 'name' => 'Cyberpunk 2077', 'genre' => 'RPG', 'year' => 2020, 'inLibrary' => true],
    ['id' => 2, 'name' => 'Elden Ring', 'genre' => 'Action RPG', 'year' => 2022, 'inLibrary' => true],
    ['id' => 3, 'name' => 'Baldur\'s Gate 3', 'genre' => 'RPG', 'year' => 2023, 'inLibrary' => false],
    ['id' => 4, 'name' => 'Hades II', 'genre' => 'Roguelike', 'year' => 2024, 'inLibrary' => true],
    ['id' => 5, 'name' => 'Stardew Valley', 'genre' => 'Simulation', 'year' => 2016, 'inLibrary' => false],
    ['id' => 6, 'name' => 'Hollow Knight', 'genre' => 'Metroidvania', 'year' => 2017, 'inLibrary' => false],
    ['id' => 7, 'name' => 'The Witcher 3', 'genre' => 'RPG', 'year' => 2015, 'inLibrary' => true],
    ['id' => 8, 'name' => 'Red Dead Redemption 2', 'genre' => 'Action', 'year' => 2019, 'inLibrary' => false],
    ['id' => 9, 'name' => 'God of War', 'genre' => 'Action', 'year' => 2018, 'inLibrary' => false],
    ['id' => 10, 'name' => 'Sekiro', 'genre' => 'Action', 'year' => 2019, 'inLibrary' => false],
    ['id' => 11, 'name' => 'Terraria', 'genre' => 'Sandbox', 'year' => 2011, 'inLibrary' => true],
    ['id' => 12, 'name' => 'Portal 2', 'genre' => 'Puzzle', 'year' => 2011, 'inLibrary' => false],
];

include '../assets/nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Games</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#0d1218] text-gray-100">
    
    <!-- Main Content -->
    <main class="md:ml-60 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Search Games</h1>
                <p class="text-[#acbccc] text-lg">Discover and add games to your library</p>
            </div>

            <!-- Search Bar -->
            <div class="mb-8">
                <div class="relative max-w-2xl">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Search for games..."
                        class="w-full bg-[#1b2838] border border-[#2a475e] rounded-lg px-4 py-4 pl-12 text-white placeholder-[#acbccc] focus:outline-none focus:border-[#66c0f4] focus:ring-2 focus:ring-[#66c0f4]/20 transition-all"
                    >
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-[#acbccc] text-xl"></i>
                </div>
            </div>

            <!-- Results Count -->
            <div class="mb-4">
                <p class="text-[#acbccc]">
                    Showing <span id="resultCount"><?php echo count($games); ?></span> games
                </p>
            </div>

            <!-- Games Grid -->
            <div id="gamesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($games as $game): ?>
                <div class="game-card bg-[#1b2838] border border-[#2a475e] rounded-lg overflow-hidden hover:border-[#66c0f4]/50 transition-all group" 
                     data-name="<?php echo strtolower($game['name']); ?>"
                     data-genre="<?php echo strtolower($game['genre']); ?>">
                    
                    <!-- Game Image Placeholder -->
                    <div class="aspect-[16/9] bg-gradient-to-br from-[#2a475e] to-[#171a21] flex items-center justify-center relative overflow-hidden">
                        <i class="bi bi-controller text-6xl text-[#66c0f4]/30 group-hover:scale-110 transition-transform"></i>
                        <?php if ($game['inLibrary']): ?>
                        <div class="absolute top-2 right-2 bg-[#5c7e10] text-white text-xs px-2 py-1 rounded-md">
                            <i class="bi bi-check-circle-fill"></i> In Library
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Game Info -->
                    <div class="p-4">
                        <h3 class="text-white font-semibold text-lg mb-1 truncate"><?php echo htmlspecialchars($game['name']); ?></h3>
                        <div class="flex items-center justify-between text-sm text-[#acbccc] mb-4">
                            <span><?php echo htmlspecialchars($game['genre']); ?></span>
                            <span><?php echo $game['year']; ?></span>
                        </div>

                        <!-- Action Button -->
                        <?php if ($game['inLibrary']): ?>
                        <button 
                            class="remove-btn w-full bg-red-900/30 hover:bg-red-900/50 border border-red-700/50 text-red-400 font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                            data-game-id="<?php echo $game['id']; ?>"
                            data-game-name="<?php echo htmlspecialchars($game['name']); ?>">
                            <i class="bi bi-dash-circle"></i>
                            Remove
                        </button>
                        <?php else: ?>
                        <button 
                            class="add-btn w-full bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                            data-game-id="<?php echo $game['id']; ?>"
                            data-game-name="<?php echo htmlspecialchars($game['name']); ?>">
                            <i class="bi bi-plus-circle"></i>
                            Add to Library
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden text-center py-16">
                <i class="bi bi-inbox text-6xl text-[#2a475e] mb-4"></i>
                <p class="text-[#acbccc] text-lg">No games found</p>
            </div>

        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 right-6 bg-[#1b2838] border border-[#2a475e] rounded-lg px-6 py-4 shadow-2xl transform translate-y-32 opacity-0 transition-all duration-300 z-50">
        <div class="flex items-center gap-3">
            <i id="toastIcon" class="text-2xl"></i>
            <span id="toastMessage" class="text-white font-medium"></span>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const gameCards = document.querySelectorAll('.game-card');
        const gamesGrid = document.getElementById('gamesGrid');
        const noResults = document.getElementById('noResults');
        const resultCount = document.getElementById('resultCount');

        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();
            let visibleCount = 0;

            gameCards.forEach(card => {
                const gameName = card.dataset.name;
                const gameGenre = card.dataset.genre;
                
                if (gameName.includes(searchTerm) || gameGenre.includes(searchTerm)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            resultCount.textContent = visibleCount;
            
            if (visibleCount === 0) {
                gamesGrid.classList.add('hidden');
                noResults.classList.remove('hidden');
            } else {
                gamesGrid.classList.remove('hidden');
                noResults.classList.add('hidden');
            }
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

        // Add to library
        document.querySelectorAll('.add-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const gameId = this.dataset.gameId;
                const gameName = this.dataset.gameName;
                
                // Change button to remove
                this.outerHTML = `
                    <button 
                        class="remove-btn w-full bg-red-900/30 hover:bg-red-900/50 border border-red-700/50 text-red-400 font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                        data-game-id="${gameId}"
                        data-game-name="${gameName}">
                        <i class="bi bi-dash-circle"></i>
                        Remove
                    </button>
                `;
                
                // Add badge
                const card = this.closest('.game-card');
                const imageDiv = card.querySelector('.aspect-\\[16\\/9\\]');
                if (!imageDiv.querySelector('.absolute')) {
                    const badge = document.createElement('div');
                    badge.className = 'absolute top-2 right-2 bg-[#5c7e10] text-white text-xs px-2 py-1 rounded-md';
                    badge.innerHTML = '<i class="bi bi-check-circle-fill"></i> In Library';
                    imageDiv.appendChild(badge);
                }
                
                showToast(`${gameName} added to library`, true);
                
                // Re-attach event listeners
                attachRemoveListeners();
            });
        });

        // Remove from library
        function attachRemoveListeners() {
            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.replaceWith(btn.cloneNode(true));
            });

            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const gameId = this.dataset.gameId;
                    const gameName = this.dataset.gameName;
                    
                    // Change button to add
                    this.outerHTML = `
                        <button 
                            class="add-btn w-full bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                            data-game-id="${gameId}"
                            data-game-name="${gameName}">
                            <i class="bi bi-plus-circle"></i>
                            Add to Library
                        </button>
                    `;
                    
                    // Remove badge
                    const card = this.closest('.game-card');
                    const badge = card.querySelector('.absolute');
                    if (badge) badge.remove();
                    
                    showToast(`${gameName} removed from library`, false);
                    
                    // Re-attach event listeners
                    attachAddListeners();
                });
            });
        }

        function attachAddListeners() {
            document.querySelectorAll('.add-btn').forEach(btn => {
                btn.replaceWith(btn.cloneNode(true));
            });

            document.querySelectorAll('.add-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const gameId = this.dataset.gameId;
                    const gameName = this.dataset.gameName;
                    
                    this.outerHTML = `
                        <button 
                            class="remove-btn w-full bg-red-900/30 hover:bg-red-900/50 border border-red-700/50 text-red-400 font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                            data-game-id="${gameId}"
                            data-game-name="${gameName}">
                            <i class="bi bi-dash-circle"></i>
                            Remove
                        </button>
                    `;
                    
                    const card = this.closest('.game-card');
                    const imageDiv = card.querySelector('.aspect-\\[16\\/9\\]');
                    if (!imageDiv.querySelector('.absolute')) {
                        const badge = document.createElement('div');
                        badge.className = 'absolute top-2 right-2 bg-[#5c7e10] text-white text-xs px-2 py-1 rounded-md';
                        badge.innerHTML = '<i class="bi bi-check-circle-fill"></i> In Library';
                        imageDiv.appendChild(badge);
                    }
                    
                    showToast(`${gameName} added to library`, true);
                    attachRemoveListeners();
                });
            });
        }

        // Initial attachment
        attachRemoveListeners();
    </script>

</body>
</html>