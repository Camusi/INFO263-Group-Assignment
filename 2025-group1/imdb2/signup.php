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
    <form class="signin-form" action="newuser.php" method="post">
      <label>Username:<br><input type="text" name="userID" placeholder="DrZakkaroff" required></label><br><br>
        <label>Birth date:<br><input type="date" name="dob"></label><br><br>
        <label>Email:<br><input type="email" name="email" placeholder="you@example.com" required></label><br><br>
      <label>Password:<br><input type="password" name="password" placeholder="Enter password" required></label><br><br>
      <label>Confirm Password:<br><input type="password" name="passconfirm" placeholder="Re-enter password" required></label><br><br>
      <button type="submit">Join for Free!</button>
        <p>Already have an account? <a href="signin.php">Sign In</a></p>
    </form>
  </main>

  <?php include 'resources/footer.php'; ?>
</body>
</html>