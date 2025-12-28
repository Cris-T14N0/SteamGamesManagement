<?php
session_start();

// Verificação de Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include '../config.php';
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

// Buscar as Bibliotecas do Utilizador
$sqlLibs = "SELECT * FROM LIBRARY WHERE user_id = ? ORDER BY id_library ASC";
$stmt = $conn->prepare($sqlLibs);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultLibs = $stmt->get_result();

$gameLists = [];

while ($lib = $resultLibs->fetch_assoc()) {
    $libId = $lib['id_library'];
    
    $sqlGames = "
        SELECT GAME.title 
        FROM GAME
        JOIN LIBRARY_GAME ON GAME.id_game = LIBRARY_GAME.game_id
        WHERE LIBRARY_GAME.library_id = ?
        ORDER BY GAME.title ASC
    ";
    
    $stmtGames = $conn->prepare($sqlGames);
    $stmtGames->bind_param("i", $libId);
    $stmtGames->execute();
    $resultGames = $stmtGames->get_result();
    
    $games = [];
    while ($game = $resultGames->fetch_assoc()) {
        $games[] = $game['title'];
    }
    
    $gameLists[] = [
        'id' => $libId,
        'name' => $lib['name'],
        'games' => $games
    ];
    $stmtGames->close();
}
$stmt->close();

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
    <link rel="stylesheet" href="../assets/css/authentication.css">
</head>
<body class="bg-[#0d1218] text-gray-100 font-sans">
    
    <main class="md:ml-60 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2 steam-font tracking-wider">MY LIBRARY</h1>
                <p class="text-[#acbccc] text-lg">Organize your games into lists</p>
            </div>

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
                <button id="createListBtn" class="bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-medium py-3 px-6 rounded-lg transition-all flex items-center justify-center gap-2 whitespace-nowrap shadow-lg">
                    <i class="bi bi-plus-circle"></i>
                    Create New List
                </button>
            </div>

            <div id="listsContainer" class="space-y-6">
                <?php if (empty($gameLists)): ?>
                    <div id="noResults" class="text-center py-16 bg-[#1b2838] border border-[#2a475e] rounded-lg">
                        <i class="bi bi-folder-plus text-6xl text-[#2a475e] mb-4 block"></i>
                        <p class="text-[#acbccc] text-lg">You don't have any lists yet. Create one!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($gameLists as $list): ?>
                    <div class="list-card bg-[#1b2838] border border-[#2a475e] rounded-lg p-6 hover:border-[#66c0f4]/50 transition-all"
                         data-list-name="<?php echo strtolower($list['name']); ?>"
                         id="list-<?php echo $list['id']; ?>">
                        
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3 flex-1 cursor-pointer toggle-games">
                                <i class="bi bi-chevron-right text-[#acbccc] text-xl transition-transform chevron-icon"></i>
                                <div>
                                    <h2 class="text-2xl font-bold text-white mb-1 list-title-text"><?php echo htmlspecialchars($list['name']); ?></h2>
                                    <p class="text-[#acbccc] text-sm"><span class="game-count"><?php echo count($list['games']); ?></span> game<?php echo count($list['games']) != 1 ? 's' : ''; ?></p>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <button class="menu-btn text-[#acbccc] hover:text-white p-2 hover:bg-[#2a475e] rounded-lg transition-all">
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

                        <div class="games-list hidden mt-4 space-y-2">
                            <?php if (empty($list['games'])): ?>
                            <p class="text-[#acbccc] text-sm italic ml-8">No games in this list yet</p>
                            <?php else: ?>
                                <?php foreach ($list['games'] as $game): ?>
                                <div class="ml-8 bg-[#0d1218] border border-[#2a475e] rounded-lg px-4 py-3 flex items-center gap-3 hover:border-[#66c0f4]/30 transition-all">
                                    <i class="bi bi-controller text-[#66c0f4]"></i>
                                    <span class="text-white"><?php echo htmlspecialchars($game); ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div id="noSearchResults" class="hidden text-center py-16 bg-[#1b2838] border border-[#2a475e] rounded-lg mt-6">
                <i class="bi bi-search text-6xl text-[#2a475e] mb-4 block"></i>
                <p class="text-[#acbccc] text-lg">No lists found matching your search</p>
            </div>

        </div>
    </main>

    <div id="listModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-[#1b2838] border border-[#2a475e] rounded-lg max-w-md w-full p-6 shadow-2xl">
            <h3 id="modalTitle" class="text-2xl font-bold text-white mb-4 steam-font tracking-wide">Create New List</h3>
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
                <button id="saveBtn" class="flex-1 bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-bold py-3 px-6 rounded-lg transition-all shadow-lg">
                    Save
                </button>
            </div>
        </div>
    </div>

    <div id="toast" class="fixed bottom-6 right-6 bg-[#1b2838] border border-[#2a475e] rounded-lg px-6 py-4 shadow-2xl transform translate-y-32 opacity-0 transition-all duration-300 z-50 flex items-center gap-4">
        <i id="toastIcon" class="text-2xl"></i>
        <span id="toastMessage" class="text-white font-medium"></span>
    </div>

    <script src="../scripts/library.js"></script>
</body>
</html>