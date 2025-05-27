<nav class="navbar">
    <div class="nav-left">
      <a href="/">Home</a>
    </div>

    <div class="nav-center">
      <input type="text" id="search-input" name="search-input" placeholder="Search any movie..." />
    </div>

    <div class="nav-right" id="account">
      <?php if (!empty($_SESSION['userID'])): ?>
        <a href="logout.php">Logout</a>
        <p class="account-preview">👤 <?= htmlspecialchars($_SESSION['userID']) ?></p>
        <?php else: ?>
        <a href="signin.php">Sign In</a>
        <p class="account=preview">👤 Guest</p>
        <?php endif; ?>
    </div>
</nav>