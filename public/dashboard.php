<?php
session_start();
// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Static data for demonstration
$username = $_SESSION['username'] ?? 'User';
$savedGames = 24;
$totalLists = 8;
$hoursPlayed = 156;
$achievements = 47;

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
</head>
<body class="bg-[#0d1218] text-gray-100">
    
    <!-- Main Content with proper spacing for sidebar -->
    <main class="md:ml-60 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">
                    Hi, <?php echo htmlspecialchars($username); ?>! 👋
                </h1>
                <p class="text-[#acbccc] text-lg">Welcome back to your game library</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Saved Games Card -->
                <div class="bg-gradient-to-br from-[#1b2838] to-[#171a21] border border-[#2a475e] rounded-xl p-6 shadow-lg hover:shadow-2xl hover:border-[#66c0f4]/50 transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-[#66c0f4]/20 rounded-lg p-3">
                            <i class="bi bi-controller text-3xl text-[#66c0f4]"></i>
                        </div>
                        <span class="text-[#66c0f4] text-sm font-medium">+3 this week</span>
                    </div>
                    <h3 class="text-[#acbccc] text-sm font-medium mb-1">Saved Games</h3>
                    <p class="text-4xl font-bold text-white"><?php echo $savedGames; ?></p>
                </div>

                <!-- Lists Card -->
                <div class="bg-gradient-to-br from-[#1b2838] to-[#171a21] border border-[#2a475e] rounded-xl p-6 shadow-lg hover:shadow-2xl hover:border-[#5c7e10]/50 transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-[#5c7e10]/20 rounded-lg p-3">
                            <i class="bi bi-list-ul text-3xl text-[#a4d007]"></i>
                        </div>
                        <span class="text-[#a4d007] text-sm font-medium">Active</span>
                    </div>
                    <h3 class="text-[#acbccc] text-sm font-medium mb-1">Game Lists</h3>
                    <p class="text-4xl font-bold text-white"><?php echo $totalLists; ?></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>