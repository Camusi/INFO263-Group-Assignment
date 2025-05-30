<nav class="navbar">
    <div class="nav-left">
        <a href="/INFO263-Group-Assignment/2025-group1/imdb2">Home</a>
        <a href="/INFO263-Group-Assignment/2025-group1/imdb2/titles.php">Titles</a>
        <a href="/INFO263-Group-Assignment/2025-group1/imdb2/stats.php">Stats</a>
    </div>

    <div class="nav-center">
        <input type="text" id="search-input" name="search-input" placeholder="Search any movie..."/>
    </div>

    <div class="nav-right" id="account">
      <?php if (!empty($_SESSION['userID'])): ?>
        <a href="/INFO263-Group-Assignment/2025-group1/imdb2/logout.php">Logout</a>
        <p class="account-preview">👤 <?= htmlspecialchars($_SESSION['userID']) ?></p>
        <?php else: ?>
        <a href="/INFO263-Group-Assignment/2025-group1/imdb2/signin.php">Sign In</a>
        <p class="account-preview">👤 Guest</p>
        <?php endif; ?>
    </div>
</nav>