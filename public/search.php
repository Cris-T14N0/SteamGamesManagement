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

$stmt = $conn->prepare("SELECT id_library FROM LIBRARY WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$libResult = $stmt->get_result();

if ($libResult->num_rows === 0) {
    $stmtCreate = $conn->prepare("INSERT INTO LIBRARY (user_id, name) VALUES (?, 'My Library')");
    $stmtCreate->bind_param("i", $user_id);
    $stmtCreate->execute();
    $library_id = $stmtCreate->insert_id;
    $stmtCreate->close();
} else {
    $libRow = $libResult->fetch_assoc();
    $library_id = $libRow['id_library'];
}
$stmt->close();

// Lista de Géneros
$genres = [];
$sqlGenres = "SELECT DISTINCT name FROM GENRE ORDER BY name ASC";
$resultGenres = $conn->query($sqlGenres);
if ($resultGenres) {
    while ($row = $resultGenres->fetch_assoc()) {
        $genres[] = $row['name'];
    }
}

$sql = "
    SELECT 
        GAME.id_game, 
        GAME.title as name, 
        GAME.game_identifier, 
        YEAR(GAME.release_date) as year,
        GROUP_CONCAT(GENRE.name SEPARATOR ', ') as genre,
        (CASE WHEN LIBRARY_GAME.game_id IS NOT NULL THEN 1 ELSE 0 END) as inLibrary
    FROM GAME
    LEFT JOIN GAMEGENRE ON GAME.id_game = GAMEGENRE.game_id
    LEFT JOIN GENRE ON GAMEGENRE.genre_id = GENRE.id_genre
    LEFT JOIN LIBRARY_GAME ON (LIBRARY_GAME.library_id = ? AND LIBRARY_GAME.game_id = GAME.id_game)
    GROUP BY GAME.id_game
    ORDER BY GAME.title ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $library_id);
$stmt->execute();
$result = $stmt->get_result();

$games = [];
while ($row = $result->fetch_assoc()) {
    $games[] = $row;
}
$stmt->close();

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
    <link rel="stylesheet" href="../assets/css/authentication.css">
</head>
<body class="bg-[#0d1218] text-gray-100">
    
    <main class="md:ml-60 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2 steam-font tracking-wider">SEARCH GAMES</h1>
                <p class="text-[#acbccc] text-lg">Discover and add games to your library</p>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mb-8">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Search for games..."
                        class="w-full bg-[#1b2838] border border-[#2a475e] rounded-lg px-4 py-3 pl-12 text-white placeholder-[#acbccc] focus:outline-none focus:border-[#66c0f4] focus:ring-2 focus:ring-[#66c0f4]/20 transition-all"
                    >
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-[#acbccc] text-xl"></i>
                </div>

                <select id="genreSelect" class="bg-[#1b2838] border border-[#2a475e] rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#66c0f4] focus:ring-2 focus:ring-[#66c0f4]/20 transition-all md:w-48 cursor-pointer">
                    <option value="all">All Genres</option>
                    <?php foreach ($genres as $g): ?>
                        <option value="<?php echo strtolower($g); ?>"><?php echo htmlspecialchars($g); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <p class="text-[#acbccc]">
                    Showing <span id="resultCount"><?php echo count($games); ?></span> games
                </p>
            </div>

            <div id="gamesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($games as $game): ?>
                    
                    <?php 
                        $imageUrl = "https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/" . $game['game_identifier'] . "/header.jpg"; 
                    ?>

                <div class="game-card bg-[#1b2838] border border-[#2a475e] rounded-lg overflow-hidden hover:border-[#66c0f4]/50 transition-all group shadow-lg flex flex-col" 
                     data-name="<?php echo strtolower($game['name']); ?>"
                     data-genre="<?php echo strtolower($game['genre'] ?? ''); ?>">
                    
                    <!-- Imagem clicável para abrir modal -->
                    <div class="aspect-[16/9] bg-[#000] relative overflow-hidden group-hover:brightness-110 transition-all cursor-pointer" 
                         onclick="openGameDetails(<?php echo $game['id_game']; ?>)">
                        <img src="<?php echo $imageUrl; ?>" 
                             alt="<?php echo htmlspecialchars($game['name']); ?>" 
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                        
                        <i class="bi bi-controller text-6xl text-[#66c0f4]/30 hidden absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></i>

                        <?php if ($game['inLibrary']): ?>
                        <div class="library-badge absolute top-2 right-2 bg-[#5c7e10] text-white text-xs px-2 py-1 rounded-md shadow-md">
                            <i class="bi bi-check-circle-fill"></i> In Library
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="text-white font-semibold text-lg mb-1 truncate"><?php echo htmlspecialchars($game['name']); ?></h3>
                        
                        <div class="flex items-center justify-between text-sm text-[#acbccc] mb-3">
                            <span class="truncate max-w-[60%]"><?php echo htmlspecialchars($game['genre'] ?? 'Unknown'); ?></span>
                            <span><?php echo $game['year']; ?></span>
                        </div>

                        <div class="mt-auto action-container">
                            <?php if ($game['inLibrary']): ?>
                                <button class="remove-btn w-full bg-red-900/30 hover:bg-red-900/50 border border-red-700/50 text-red-400 font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                                        data-game-id="<?php echo $game['id_game']; ?>"
                                        data-game-name="<?php echo htmlspecialchars($game['name']); ?>">
                                    <i class="bi bi-dash-circle"></i> Remove
                                </button>
                            <?php else: ?>
                                <button class="cursor-pointer add-btn w-full bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                                        data-game-id="<?php echo $game['id_game']; ?>"
                                        data-game-name="<?php echo htmlspecialchars($game['name']); ?>">
                                    <i class="bi bi-plus-circle"></i> Add to Library
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div id="noResults" class="hidden text-center py-16">
                <i class="bi bi-inbox text-6xl text-[#2a475e] mb-4"></i>
                <p class="text-[#acbccc] text-lg">No games found</p>
            </div>

        </div>
    </main>

    <!-- Include do Modal Reutilizável -->
    <?php include '../assets/game_details_modal.php'; ?>

    <!-- Add Game Modal -->
    <div id="addGameModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-[#1b2838] border border-[#2a475e] rounded-lg max-w-sm w-full p-6 shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-2 steam-font">Add to Library</h3>
            <p id="modalGameTitle" class="text-[#acbccc] text-sm mb-4 truncate">Select lists...</p>
            <div id="listsCheckboxes" class="space-y-2 mb-6 max-h-60 overflow-y-auto custom-scrollbar">
                <div class="flex items-center justify-center py-4">
                    <i class="bi bi-arrow-clockwise animate-spin text-[#66c0f4] text-2xl"></i>
                </div>
            </div>
            <div class="flex gap-3">
                <button id="cancelAddBtn" class="flex-1 bg-[#2a475e] hover:bg-[#3a5770] text-white font-medium py-3 px-4 rounded-lg transition-all text-sm">Cancel</button>
                <button id="confirmAddBtn" class="flex-1 bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-bold py-3 px-4 rounded-lg transition-all shadow-lg text-sm">Save</button>
            </div>
        </div>
    </div>

    <div id="toast" class="fixed bottom-6 right-6 bg-[#1b2838] border border-[#2a475e] rounded-lg px-6 py-4 shadow-2xl transform translate-y-32 opacity-0 transition-all duration-300 z-50 flex items-center gap-4">
        <i id="toastIcon" class="text-2xl"></i>
        <span id="toastMessage" class="text-white font-medium"></span>
    </div>

    <script src="../scripts/search.js"></script>
</body>
</html>