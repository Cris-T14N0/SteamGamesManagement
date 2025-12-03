<link rel="stylesheet" href="../assets/css/nav.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<nav class="sidebar">
    <div class="sidebar-header">
        <h2>Steam Games Management</h2>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <i class="bi bi-house-fill"></i>
                <span class="menu-text">Home</span>
            </a>
        </li>
        <li>
            <a href="store.php" class="<?= basename($_SERVER['PHP_SELF']) == 'store.php' ? 'active' : '' ?>">
                <i class="bi bi-cart-fill"></i>
                <span class="menu-text">Store</span>
            </a>
        </li>
        <li>
            <a href="library.php" class="<?= basename($_SERVER['PHP_SELF']) == 'library.php' ? 'active' : '' ?>">
                <i class="bi bi-book-fill"></i>
                <span class="menu-text">Library</span>
            </a>
        </li>
        <li>
            <a href="community.php" class="<?= basename($_SERVER['PHP_SELF']) == 'community.php' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i>
                <span class="menu-text">Community</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-info">
            <span>User Name</span>
        </div>
    </div>

    <script src="js/nav.js"></script>
</nav>