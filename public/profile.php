<?php
session_start();

// Verificar Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include '../config.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, email FROM `USER` WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Steam</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="../assets/css/authentication.css">
    
    <style>
        body { font-family: "Motiva Sans", "Roboto", sans-serif; }
    </style>
</head>
<body class="bg-[#0d1218] text-gray-100 min-h-screen">
    
    <?php include '../assets/nav.php'; ?>

    <main class="md:ml-60 p-4 sm:p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2 steam-font tracking-wider">ACCOUNT SETTINGS</h1>
                <p class="text-[#acbccc]">Manage your profile and account security</p>
            </div>

            <div id="message" class="hidden mb-6"></div>

            <div class="bg-[#1b2838] rounded-lg border border-[#2a475e] p-6 mb-6 shadow-lg">
                <h2 class="text-2xl font-semibold text-[#66c0f4] mb-6 flex items-center gap-2 steam-font">
                    <i class="bi bi-person-circle"></i>
                    Profile Information
                </h2>

                <form id="updateProfileForm" class="space-y-5">
                    <div>
                        <label class="block text-[#acbccc] text-xs font-bold mb-2 tracking-widest">USERNAME</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                               class="w-full px-4 py-3 bg-[#0d1218] border border-[#2a475e] rounded text-white focus:outline-none focus:border-[#66c0f4] focus:ring-1 focus:ring-[#66c0f4]/50 transition">
                    </div>

                    <div>
                        <label class="block text-[#acbccc] text-xs font-bold mb-2 tracking-widest">EMAIL ADDRESS</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                               class="w-full px-4 py-3 bg-[#0d1218] border border-[#2a475e] rounded text-white focus:outline-none focus:border-[#66c0f4] focus:ring-1 focus:ring-[#66c0f4]/50 transition">
                    </div>

                    <button type="submit" class="bg-gradient-to-r from-[#66c0f4] to-[#2a475e] hover:from-[#57a9d8] hover:to-[#1e3a52] text-white font-bold px-6 py-3 rounded shadow-lg transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <div class="bg-[#1b2838] rounded-lg border border-[#2a475e] p-6 mb-6 shadow-lg">
                <h2 class="text-2xl font-semibold text-[#66c0f4] mb-6 flex items-center gap-2 steam-font">
                    <i class="bi bi-shield-lock"></i>
                    Change Password
                </h2>

                <form id="changePasswordForm" class="space-y-5">
                    <div>
                        <label class="block text-[#acbccc] text-xs font-bold mb-2 tracking-widest">CURRENT PASSWORD</label>
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full px-4 py-3 bg-[#0d1218] border border-[#2a475e] rounded text-white focus:outline-none focus:border-[#66c0f4] focus:ring-1 focus:ring-[#66c0f4]/50 transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[#acbccc] text-xs font-bold mb-2 tracking-widest">NEW PASSWORD</label>
                            <input type="password" id="new_password" name="new_password" required minlength="6"
                                   class="w-full px-4 py-3 bg-[#0d1218] border border-[#2a475e] rounded text-white focus:outline-none focus:border-[#66c0f4] focus:ring-1 focus:ring-[#66c0f4]/50 transition">
                        </div>
                        <div>
                            <label class="block text-[#acbccc] text-xs font-bold mb-2 tracking-widest">CONFIRM NEW PASSWORD</label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   class="w-full px-4 py-3 bg-[#0d1218] border border-[#2a475e] rounded text-white focus:outline-none focus:border-[#66c0f4] focus:ring-1 focus:ring-[#66c0f4]/50 transition">
                        </div>
                    </div>

                    <button type="submit" class="bg-[#2a475e] hover:bg-[#3a5770] text-white font-bold px-6 py-3 rounded shadow-lg transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-key"></i> Change Password
                    </button>
                </form>
            </div>

            <div class="bg-red-900/10 rounded-lg border border-red-900/30 p-6">
                <h2 class="text-2xl font-semibold text-red-400 mb-4 flex items-center gap-2 steam-font">
                    <i class="bi bi-exclamation-triangle"></i>
                    Danger Zone
                </h2>
                <p class="text-[#acbccc] mb-4 text-sm">Once you delete your account, there is no going back. All your libraries and saved games will be permanently removed.</p>
                
                <button id="deleteAccountBtn" class="bg-red-600/80 hover:bg-red-600 text-white font-bold px-6 py-3 rounded shadow-lg transition-all duration-300 flex items-center gap-2">
                    <i class="bi bi-trash"></i> Delete Account
                </button>
            </div>

        </div>
    </main>

    <div id="deleteModal" class="hidden fixed inset-0 bg-black/80 z-[2000] items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-[#1b2838] rounded-lg border border-red-500/50 p-6 max-w-md w-full shadow-2xl">
            <h3 class="text-2xl font-bold text-white mb-2 steam-font">Delete Account?</h3>
            <p class="text-[#acbccc] mb-6 text-sm">Please confirm by typing your password below.</p>
            
            <div class="mb-6">
                <input type="password" id="delete_password" placeholder="Enter your password"
                       class="w-full px-4 py-3 bg-[#0d1218] border border-red-900/50 rounded text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/50 transition">
            </div>

            <div class="flex gap-3">
                <button id="cancelDeleteBtn" class="flex-1 bg-[#2a475e] hover:bg-[#3a5770] text-white font-medium px-4 py-3 rounded transition">
                    Cancel
                </button>
                <button id="confirmDeleteBtn" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold px-4 py-3 rounded transition shadow-lg">
                    Delete Permanently
                </button>
            </div>
        </div>
    </div>

    <script src="../scripts/profile.js"></script>
</body>
</html>