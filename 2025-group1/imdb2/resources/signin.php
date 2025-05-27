<?php
session_start();
$error = '';
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        // Connect to DB
        $db = new PDO('sqlite:./resources/imdb2-user.sqlite3');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// Accept inputs and sanitize
        $userID = trim($_POST["userID"] ?? '');
        $password = trim($_POST["password"] ?? '');// Validate entry
        if (!$userID || !$password) {
            throw new Exception("Missing username or password");
        }

        // Obtain user details
        $stmt = $db->prepare("SELECT userID, password FROM user WHERE userID = :uid OR email = :uid");
        $stmt->execute([':uid' => $userID]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            throw new Exception("Sorry, User not found");
        }

        // Check password
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Incorrect password");
        }

        // Success - login
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign In | IMDB2.0</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header class="header">
    <h1>Sign In</h1>
    <p>Welcome back!</p>
  </header>

  <?php include 'resources/navbar.php'; ?>

  <main class="main-content">
    <form action="signin.php" method="post" class="signin-form">
      <label>Email or username:<br>
          <input
                  type="text"
                  name="userID"
                  placeholder="Email or username"
                  required>
      </label><br><br>

      <label>Password:<br>
          <input
                  type="password"
                  name="password"
                  placeholder="Enter password"
                  required>
      </label><br><br>

      <button type="submit">Sign In</button>
      <p>Don't have an account? <a href="../signup.php">Sign Up!</a></p>

        <!--Feedback-->
      <?php if ($error): ?>
        <div class="form-message error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </form>
  </main>

  <?php include 'resources/footer.php'; ?>
</body>
</html>