<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include '../config.php';

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
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
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: "Motiva Sans", "Segoe UI", Arial, sans-serif;
            background: #171a21;
            color: #c7d5e0;
        }
    </style>
</head>
<body class="min-h-screen">
    <?php include '../assets/nav.php'; ?>

    <main class="md:ml-60 p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Account Settings</h1>
                <p class="text-gray-400">Manage your profile and account security</p>
            </div>

            <div id="message" class="mb-6"></div>

            <!-- Profile Information Card -->
            <div class="bg-[#1b2838] rounded-lg border border-[#2a475e] p-6 mb-6">
                <h2 class="text-2xl font-semibold text-[#66c0f4] mb-6 flex items-center gap-2">
                    <i class="bi bi-person-circle"></i>
                    Profile Information
                </h2>

                <form id="updateProfileForm" class="space-y-5">
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">USERNAME</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                               class="w-full px-4 py-3 bg-[#0d1218] border border-gray-600 rounded text-white focus:outline-none focus:border-[#66c0f4] transition">
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">EMAIL</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                               class="w-full px-4 py-3 bg-[#0d1218] border border-gray-600 rounded text-white focus:outline-none focus:border-[#66c0f4] transition">
                    </div>

                    <button type="submit" class="bg-gradient-to-r from-[#66c0f4] to-[#2a475e] hover:from-[#57a9d8] hover:to-[#1e3a52] text-white font-semibold px-6 py-3 rounded shadow-lg transition-all duration-300">
                        <i class="bi bi-check-circle mr-2"></i>Update Profile
                    </button>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="bg-[#1b2838] rounded-lg border border-[#2a475e] p-6 mb-6">
                <h2 class="text-2xl font-semibold text-[#66c0f4] mb-6 flex items-center gap-2">
                    <i class="bi bi-shield-lock"></i>
                    Change Password
                </h2>

                <form id="changePasswordForm" class="space-y-5">
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">CURRENT PASSWORD</label>
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full px-4 py-3 bg-[#0d1218] border border-gray-600 rounded text-white focus:outline-none focus:border-[#66c0f4] transition">
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">NEW PASSWORD</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6"
                               class="w-full px-4 py-3 bg-[#0d1218] border border-gray-600 rounded text-white focus:outline-none focus:border-[#66c0f4] transition">
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">CONFIRM NEW PASSWORD</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="w-full px-4 py-3 bg-[#0d1218] border border-gray-600 rounded text-white focus:outline-none focus:border-[#66c0f4] transition">
                    </div>

                    <button type="submit" class="bg-gradient-to-r from-[#66c0f4] to-[#2a475e] hover:from-[#57a9d8] hover:to-[#1e3a52] text-white font-semibold px-6 py-3 rounded shadow-lg transition-all duration-300">
                        <i class="bi bi-key mr-2"></i>Change Password
                    </button>
                </form>
            </div>

            <!-- Danger Zone Card -->
            <div class="bg-red-900/20 rounded-lg border border-red-700/50 p-6">
                <h2 class="text-2xl font-semibold text-red-400 mb-4 flex items-center gap-2">
                    <i class="bi bi-exclamation-triangle"></i>
                    Danger Zone
                </h2>
                <p class="text-gray-400 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                
                <button id="deleteAccountBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded shadow-lg transition-all duration-300">
                    <i class="bi bi-trash mr-2"></i>Delete Account
                </button>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="hidden fixed inset-0 bg-black/80 z-[2000] flex items-center justify-center p-4">
                <div class="bg-[#1b2838] rounded-lg border border-red-700 p-6 max-w-md w-full">
                    <h3 class="text-2xl font-bold text-red-400 mb-4">Delete Account?</h3>
                    <p class="text-gray-300 mb-6">This action cannot be undone. All your data will be permanently deleted.</p>
                    
                    <div class="mb-6">
                        <label class="block text-gray-300 text-sm font-medium mb-2">TYPE YOUR PASSWORD TO CONFIRM</label>
                        <input type="password" id="delete_password" placeholder="Enter your password"
                               class="w-full px-4 py-3 bg-[#0d1218] border border-gray-600 rounded text-white focus:outline-none focus:border-red-500 transition">
                    </div>

                    <div class="flex gap-3">
                        <button id="confirmDeleteBtn" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-3 rounded transition">
                            Yes, Delete My Account
                        </button>
                        <button id="cancelDeleteBtn" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-semibold px-4 py-3 rounded transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../scripts/profile.js"></script>
</body>
</html>