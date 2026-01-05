<?php
session_start();

// Verificação de Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Conexão à BD
include '../config.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

// Obter Estatísticas Reais

// Contar Total de Listas
$stmtLists = $conn->prepare("SELECT COUNT(*) FROM LIBRARY WHERE user_id = ?");
$stmtLists->bind_param("i", $user_id);
$stmtLists->execute();
$stmtLists->bind_result($totalLists);
$stmtLists->fetch();
$stmtLists->close();

// Contar Total de Jogos 
$sqlGames = "
    SELECT LIBRARY_GAME.game_id 
    FROM LIBRARY_GAME
    INNER JOIN LIBRARY ON LIBRARY_GAME.library_id = LIBRARY.id_library
    WHERE LIBRARY.user_id = ?
";
$stmtGames = $conn->prepare($sqlGames);
$stmtGames->bind_param("i", $user_id);
$stmtGames->execute();
$resultGames = $stmtGames->get_result();

$uniqueGameIds = [];
while ($row = $resultGames->fetch_assoc()) {
    $gameId = $row['game_id'];
    if (!in_array($gameId, $uniqueGameIds)) {
        $uniqueGameIds[] = $gameId;
    }
}
$savedGames = count($uniqueGameIds); 
$stmtGames->close();

// Obter jogos para o Quick Access
$sqlQuick = "
    SELECT GAME.title, GAME.game_identifier, GAME.id_game
    FROM GAME
    INNER JOIN LIBRARY_GAME ON GAME.id_game = LIBRARY_GAME.game_id
    INNER JOIN LIBRARY ON LIBRARY_GAME.library_id = LIBRARY.id_library
    WHERE LIBRARY.user_id = ?
";
$stmtQuick = $conn->prepare($sqlQuick);
$stmtQuick->bind_param("i", $user_id);
$stmtQuick->execute();
$resultQuick = $stmtQuick->get_result();

$quickGames = [];
$seenIds = [];

while ($row = $resultQuick->fetch_assoc()) {
    // Parar quando tivermos 4 jogos únicos
    if (count($quickGames) >= 4) {
        break;
    }

    // Se este jogo ainda não foi adicionado à lista visual
    if (!in_array($row['id_game'], $seenIds)) {
        $quickGames[] = $row;
        $seenIds[] = $row['id_game'];
    }
}
$stmtQuick->close();

include '../assets/nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Game Library</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../assets/css/authentication.css">
</head>
<body class="bg-[#0d1218] text-gray-100 font-sans">
    
    <main class="md:ml-60 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2 steam-font tracking-wider">
                    Hi, <?php echo htmlspecialchars($username); ?>! 👋
                </h1>
                <p class="text-[#acbccc] text-lg">Welcome back to your game library.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                
                <div class="bg-gradient-to-br from-[#1b2838] to-[#171a21] border border-[#2a475e] rounded-xl p-8 shadow-lg hover:shadow-2xl hover:border-[#66c0f4]/50 transition-all duration-300 hover:scale-[1.02] group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-[#66c0f4]/20 rounded-lg p-4 group-hover:bg-[#66c0f4]/30 transition-colors">
                            <i class="bi bi-controller text-4xl text-[#66c0f4]"></i>
                        </div>
                        <span class="text-[#66c0f4] text-xs font-medium uppercase tracking-widest">Library</span>
                    </div>
                    <h3 class="text-[#acbccc] text-base font-medium mb-1">Total Saved Games</h3>
                    <p class="text-5xl font-bold text-white"><?php echo $savedGames; ?></p>
                </div>

                <div class="bg-gradient-to-br from-[#1b2838] to-[#171a21] border border-[#2a475e] rounded-xl p-8 shadow-lg hover:shadow-2xl hover:border-[#5c7e10]/50 transition-all duration-300 hover:scale-[1.02] group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-[#5c7e10]/20 rounded-lg p-4 group-hover:bg-[#5c7e10]/30 transition-colors">
                            <i class="bi bi-list-ul text-4xl text-[#a4d007]"></i>
                        </div>
                        <span class="text-[#a4d007] text-xs font-medium uppercase tracking-widest">Collections</span>
                    </div>
                    <h3 class="text-[#acbccc] text-base font-medium mb-1">Active Lists</h3>
                    <p class="text-5xl font-bold text-white"><?php echo $totalLists; ?></p>
                </div>
                
            </div>

            <?php if ($savedGames > 0): ?>
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-[#2a475e] pb-2 inline-block">Quick Access</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($quickGames as $game): ?>
                        <?php 
                            $imageUrl = "https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/" . $game['game_identifier'] . "/header.jpg"; 
                        ?>
                        <div class="block group relative rounded-lg overflow-hidden border border-[#2a475e] hover:border-[#66c0f4] transition-all shadow-lg hover:shadow-[#66c0f4]/20 cursor-default">
                            <div class="aspect-video bg-black">
                                <img src="<?php echo $imageUrl; ?>" 
                                     alt="<?php echo htmlspecialchars($game['title']); ?>" 
                                     class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500"
                                     onerror="this.style.display='none'">
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-[#0d1218] to-transparent p-4 pt-12">
                                <h3 class="text-white font-bold truncate group-hover:text-[#66c0f4] transition-colors"><?php echo htmlspecialchars($game['title']); ?></h3>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-6 text-right">
                    <a href="library.php" class="text-[#66c0f4] hover:text-white text-sm font-medium transition-colors">
                        View all games <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <?php else: ?>
                <div class="bg-[#1b2838] border border-[#2a475e] rounded-xl p-12 text-center">
                    <i class="bi bi-controller text-6xl text-[#2a475e] mb-4 block"></i>
                    <h2 class="text-xl text-white font-bold mb-2">Your library is empty</h2>
                    <p class="text-[#acbccc] mb-6">Start adding games to your collection to see stats here.</p>
                    <a href="search.php" class="bg-[#66c0f4] hover:bg-[#4a9fd8] text-white font-bold py-3 px-8 rounded-lg transition-all inline-block shadow-lg hover:shadow-[#66c0f4]/30">
                        Find Games
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>