<?php
session_start();
// ─── 1) Setup message buckets ──────────────────────────────────────────
$error   = '';
$success = '';

// ─── 2) Handle POST ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new PDO('sqlite:./resources/imdb2-user.sqlite3');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // pull & sanitize
        $userID   = trim($_POST['userID']      ?? '');
        $dob      = trim($_POST['dob']         ?? null);
        $email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $pw       = $_POST['password']         ?? '';
        $pw2      = $_POST['passconfirm']      ?? '';

        // validation
        if (!$userID || !$email || !$pw || !$pw2) {
            throw new Exception("Please fill in all fields.");
        }
        if ($pw !== $pw2) {
            throw new Exception("Passwords do not match.");
        }

        // uniqueness
        $chk = $db->prepare("SELECT 1 FROM user WHERE userID = :u OR email = :e");
        $chk->execute([':u'=>$userID, ':e'=>$email]);
        if ($chk->fetch()) {
            throw new Exception("That username or email is already taken.");
        }

        // insert
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $ins  = $db->prepare("
          INSERT INTO user (userID, role, password, age, email, bio, edits)
          VALUES (:u, 'user', :h, :d, :e, '', 0)
        ");
        $ins->execute([
            ':u' => $userID,
            ':h' => $hash,
            ':d' => $dob,
            ':e' => $email,
        ]);

        $success = "🎉 Signup successful! <a href='resources/signin.php'>Click here to log in.</a>";

    } catch (Exception $ex) {
        $error = $ex->getMessage();
    }
}
// ─── 3) Now close PHP and start your HTML ─────────────────────────────
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign Up | IMDB2.0</title>
    <!-- this link will definitely be the first thing the browser sees -->
    <link rel="stylesheet" href="resources/style.css" />
</head>
<body>

<header class="header">
    <h1>Sign Up</h1>
    <p>Welcome to IMDB2.0!</p>
</header>

<?php include 'resources/navbar.php'; ?>

<main class="main-content">


    <form action="signup.php" method="post" class="signin-form">
        <label>Username:<br>
            <input type="text" name="userID" required>
        </label><br><br>

        <label>Birth date:<br>
            <input type="date" name="dob">
        </label><br><br>

        <label>Email:<br>
            <input type="email" name="email" required>
        </label><br><br>

        <label>Password:<br>
            <input type="password" name="password" required>
        </label><br><br>

        <label>Confirm Password:<br>
            <input type="password" name="passconfirm" required>
        </label><br><br>

        <button type="submit">Join for Free!</button>
        <p>Already have an account? <a href="resources/signin.php">Sign In</a></p>

        <!-- 👇 only one, centered & styled feedback -->
        <?php if ($error): ?>
            <div class="form-message error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="form-message success"><?= $success ?></div>
        <?php endif; ?>
    </form>
</main>

<?php include 'resources/footer.php'; ?>
</body>
</html>
