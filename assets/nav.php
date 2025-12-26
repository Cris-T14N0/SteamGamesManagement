<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Steam Games Management</title>

    <!-- Icons & Tailwind -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        body {
            font-family: "Motiva Sans", "Segoe UI", Arial, sans-serif;
        }

        /* User dropdown animation */
        .user-dropdown {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, opacity 0.25s ease;
        }

        .user-dropdown.active {
            max-height: 500px;
            opacity: 1;
        }
    </style>
</head>

<body class="bg-[#0d1218]">

<!-- SIDEBAR -->
<nav id="sidebar"
     class="fixed top-0 left-0 w-60 h-screen bg-gradient-to-br from-[#1b2838] to-[#171a21]
            border-r border-[#0d1218] shadow-2xl z-[1000]
            flex flex-col transition-transform duration-300 ease-out
            -translate-x-full md:translate-x-0">

    <!-- Header -->
    <div class="px-5 py-6 bg-[#171a21]/85 border-b border-[#2a475e] backdrop-blur-xl">
        <h2 class="text-[#66c0f4] text-xl font-medium tracking-wide">
            Steam Games Management
        </h2>
    </div>

    <!-- Menu -->
    <ul class="flex-1 py-2 overflow-y-auto">
        <?php
        $current = basename($_SERVER['PHP_SELF']);
        function active($page, $current) {
            return $page === $current
                ? 'bg-[#66c0f4]/24 text-[#66c0f4] font-semibold pl-7 shadow-[inset_4px_0_0_#66c0f4]'
                : '';
        }
        ?>

        <li>
            <a href="dashboard.php"
               class="flex items-center gap-4 px-5 py-3.5 text-[#acbccc]
                      transition-all hover:bg-[#66c0f4]/16 hover:text-white hover:pl-7 <?= active('dashboard.php',$current) ?>">
                <i class="bi bi-house-fill text-xl"></i>
                Home
            </a>
        </li>

        <li>
            <a href="search.php"
               class="flex items-center gap-4 px-5 py-3.5 text-[#acbccc]
                      transition-all hover:bg-[#66c0f4]/16 hover:text-white hover:pl-7 <?= active('search.php',$current) ?>">
                <i class="bi bi-cart-fill text-xl"></i>
                Search
            </a>
        </li>

        <li>
            <a href="library.php"
               class="flex items-center gap-4 px-5 py-3.5 text-[#acbccc]
                      transition-all hover:bg-[#66c0f4]/16 hover:text-white hover:pl-7 <?= active('library.php',$current) ?>">
                <i class="bi bi-book text-xl"></i>
                Library
            </a>
        </li>
    </ul>

    <!-- USER FOOTER -->
    <div class="px-5 py-4 bg-[#171a21]/95 border-t border-[#2a475e] backdrop-blur-lg">

        <!-- User Button -->
        <button id="userMenuBtn"
                class="w-full flex items-center justify-between text-left text-[#c7d5e0]
                       hover:text-[#66c0f4] transition-colors">

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#66c0f4] to-[#2a475e]
                            flex items-center justify-center text-white font-bold">
                    <?= strtoupper($username[0]) ?>
                </div>
                <div>
                    <p class="text-sm font-semibold"><?= htmlspecialchars($username) ?></p>
                    <p class="text-xs text-gray-500">Online</p>
                </div>
            </div>

            <!-- IMPORTANT: transform class -->
            <i id="dropdownIcon"
               class="bi bi-chevron-up text-lg transition-transform transform duration-300"></i>
        </button>

        <!-- Dropdown -->
        <div id="userDropdown"
             class="user-dropdown mt-3 bg-[#1b2838] rounded-lg border border-[#2a475e] shadow-lg">

            <a href="profile.php"
               class="flex items-center gap-3 px-4 py-3 text-[#acbccc]
                      hover:bg-[#66c0f4]/16 hover:text-white">
                <i class="bi bi-person-circle"></i>
                Profile Settings
            </a>

            <button id="logoutBtn"
                    class="w-full flex items-center gap-3 px-4 py-3 text-[#acbccc]
                           hover:bg-red-900/20 hover:text-red-400">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </button>
        </div>
    </div>
</nav>

<!-- MOBILE TOGGLE BUTTON -->
<button id="mobileToggle"
        class="md:hidden fixed top-4 left-4 z-[1001]
               bg-[#1b2838] text-[#66c0f4] p-3 rounded-lg shadow-lg">
    <i class="bi bi-list text-2xl"></i>
</button>

<!-- SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', () => {

    /* Mobile sidebar toggle */
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobileToggle');

    mobileToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        sidebar.classList.toggle('-translate-x-full');
    });

    /* User dropdown */
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    const dropdownIcon = document.getElementById('dropdownIcon');

    userMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('active');
        dropdownIcon.classList.toggle('rotate-180');
    });

    document.addEventListener('click', (e) => {
        if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('active');
            dropdownIcon.classList.remove('rotate-180');
        }
    });

    /* Logout */
    document.getElementById('logoutBtn').addEventListener('click', async () => {
        try {
            await fetch('../api/auth/logout.php', { method: 'POST' });
        } catch (e) {}
        window.location.href = 'login.php';
    });

});
</script>

</body>
</html>
