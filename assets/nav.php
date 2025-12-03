<nav class="sidebar">
    <div class="sidebar-header">
        <h2>Steam Clone</h2>
    </div>
    
    <ul class="sidebar-menu">
        <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <span class="icon">🏠</span>
            <span>Home</span>
        </a></li>
        
        <li><a href="store.php" class="<?= basename($_SERVER['PHP_SELF']) == 'store.php' ? 'active' : '' ?>">
            <span class="icon">🛒</span>
            <span>Store</span>
        </a></li>
        
        <li><a href="library.php" class="<?= basename($_SERVER['PHP_SELF']) == 'library.php' ? 'active' : '' ?>">
            <span class="icon">📚</span>
            <span>Library</span>
        </a></li>
        
        <li><a href="community.php" class="<?= basename($_SERVER['PHP_SELF']) == 'community.php' ? 'active' : '' ?>">
            <span class="icon">👥</span>
            <span>Community</span>
        </a></li>
    </ul>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <span>User Name</span>
        </div>
    </div>

    <script src="js/nav.js"></script>
</nav>