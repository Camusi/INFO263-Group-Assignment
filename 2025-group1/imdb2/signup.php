<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up | IMDB2.0</title>
  <link rel="stylesheet" href="resources/style.css" />
</head>
<body>
  <header class="header">
    <h1>Sign Up</h1>
    <p>Welcome to IMDB2.0!</p>
  </header>

  <?php include 'resources/navbar.php'; ?>

  <main class="main-content">
    <form class="signin-form">
      <label>Username:<br><input type="name" placeholder="DrZakkaroff" required></label><br><br>
      <label>Email:<br><input type="email" placeholder="you@example.com" required></label><br><br>
      <label>Password:<br><input type="password" placeholder="Enter password" required></label><br><br>
      <label>Confirm Password:<br><input type="password" placeholder="Re-enter password" required></label><br><br>
      <button type="submit">Join for Free!</button>
        <p>Already have an account? <a href="signin.php">Sign In</a></p>
    </form>
  </main>

  <footer class="footer">
    <p>&copy; 2025 Test Website. All rights Test.</p>
  </footer>
</body>
</html>