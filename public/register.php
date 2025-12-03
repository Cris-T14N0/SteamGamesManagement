<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Steam</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Tailwind + Our shared Steam CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/authentication.css">
</head>
<body class="flex items-center justify-center min-h-screen px-4">

    <div class="card-steam p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mt-6 steam-font tracking-wider">Oh look! <br> A new friend :)</h1>
        </div>

        <div id="message" class="mb-4"></div>

        <form id="registerForm" class="space-y-5">
            <div>
                <label class="block text-gray-300 text-sm font-medium mb-2 steam-font" for="username">CHOOSE YOUR ACCOUNT NAME</label>
                <input type="text" id="username" name="username" required minlength="3" maxlength="32"
                       class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none glow-input transition"
                       placeholder="Your public username">
                <p class="text-xs text-gray-500 mt-1">This is the name people will see</p>
            </div>

            <div>
                <label class="block text-gray-300 text-sm font-medium mb-2 steam-font" for="email">EMAIL ADDRESS</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none glow-input transition"
                       placeholder="you@example.com">
            </div>

            <div>
                <label class="block text-gray-300 text-sm font-medium mb-2 steam-font" for="password">PASSWORD</label>
                <input type="password" id="password" name="password" required minlength="6"
                       class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none glow-input transition"
                       placeholder="Minimum 6 characters">
            </div>

            <div>
                <label class="block text-gray-300 text-sm font-medium mb-2 steam-font" for="password_confirm">CONFIRM PASSWORD</label>
                <input type="password" id="password_confirm" name="password_confirm" required
                       class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none glow-input transition">
            </div>

            <button type="submit" class="btn-steam w-full py-4 text-white rounded shadow-lg text-lg steam-font">
                CREATE ACCOUNT
            </button>
        </form>

        <div class="mt-6 text-center text-gray-400 text-sm">
            Already have an account? 
            <a href="login.php" class="text-[#66c0f4] hover:text-white steam-font font-semibold">Sign In Instead</a>
            <div class="mt-6 pt-6 border-t border-gray-700 text-xs text-gray-500">
                By creating an account you agree to the 
                <a href="#" class="text-[#66c0f4] hover:underline">Terms of Service</a> and 
                <a href="#" class="text-[#66c0f4] hover:underline">Privacy Policy</a>
            </div>
        </div>
    </div>

    <script src="js/register.js"></script>
</body>
</html>