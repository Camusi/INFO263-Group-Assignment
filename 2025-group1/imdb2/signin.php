<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign In</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header class="header">
    <h1>Sign In</h1>
    <p>Welcome back!</p>
  </header>

  <nav class="navbar">
    <div class="nav-left">
      <a href="index.html">Home</a>
      <a href="about.html">About</a>
    </div>
    <div class="nav-center">
      <input type="text" class="search-bar" placeholder="Search...">
    </div>
    <div class="nav-right">
      <a href="signin.html" class="active">Sign In</a>
      <div class="account-preview">👤 Guest</div>
    </div>
  </nav>

  <main class="main-content">
    <form class="signin-form">
      <label>Email:<br><input type="email" placeholder="you@example.com"></label><br><br>
      <label>Password:<br><input type="password" placeholder="Enter password"></label><br><br>
      <button type="submit">Sign In</button>
    </form>
  </main>

  <footer class="footer">
    <p>&copy; 2025 Test Website. All rights Test.</p>
  </footer>
</body>
</html>