<?php
session_start();

// Redirect to dashboard if already logged in
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
    <title>Steam Login</title>

    <link rel="stylesheet" href="../assets/css/authentication.css">

    <!-- Google Fonts: Rajdhani (Steam's main font) + Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen px-4">

    <div class="card-steam p-8 w-full max-w-md">
        <!-- Steam Logo (using official SVG from CDN) -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mt-4 steam-font tracking-wider">SIGN IN</h1>
            <p class="text-gray-400 text-sm mt-2">to continue to your account</p>
        </div>

        <div id="message" class="mb-4"></div>

        <form id="loginForm" class="space-y-5">
            <div>
                <label class="block text-gray-300 text-sm font-medium mb-2 steam-font" for="email">
                    ACCOUNT NAME OR EMAIL
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none glow-input transition"
                    placeholder="Enter your email"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-sm font-medium mb-2 steam-font" for="password">
                    PASSWORD
                </label>
                <div class="password-field-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none glow-input transition"
                        placeholder="Enter your password"
                    >
                    <button type="button" class="password-toggle" data-target="password">
                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" 
                    class="btn-steam w-full py-4 text-white rounded shadow-lg text-lg steam-font">
                SIGN IN
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm">
                Need an account? 
                <a href="register.php" class="text-[#66c0f4] hover:text-white transition steam-font font-semibold">
                    Join us → 
                </a>
            </p>
            <div class="mt-6 pt-6 border-t border-gray-700">
                <p class="text-xs text-gray-500">
                    By signing in you agree to the 
                    <a href="#" class="text-[#66c0f4] hover:underline">Steam Terms of Service</a>
                </p>
            </div>
        </div>
    </div>

    <script src="../scripts/auth.js"></script>
</body>
</html>
