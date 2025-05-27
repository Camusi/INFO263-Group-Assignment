<?php

try {
    // Connection
    $db = new PDO('sqlite:./resources/imdb2-user.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set and sanitize values
    $userID = trim($_POST["userID"] ?? '');
    $dob = trim($_POST["dob"] ?? '');
    $email = filter_var($_POST["email"] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"] ?? '';
    $confirmPassword = $_POST["passconfirm"] ?? '';

    // Validate input
    if (!$userID || !$email || !$password || !$confirmPassword) {
        throw new Exception("Please fill all fields");
    }
    if ($password !== $confirmPassword) {
        throw new Exception("Passwords do not match - womp womp :(");
    }

    // Check username and email for uniqueness
    $check = $db->prepare("SELECT 1 FROM user WHERE userID = :uid OR email = :email");
    $check->execute([
        'uid' => $userID,
        'email' => $email
    ]);

    if ($check->fetch()) {
        throw new Exception("Username or email already exists - BIG double womp");
    }

    // Hash password for security obligations
    $hashedPass = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $insert = $db->prepare(
        "INSERT INTO user (
            userID,
            role,
            password,
            age,
            email,
            bio,
            edits
        ) VALUES (
            :uid,
            'user',
            :pwd,
            :dob,
            :email,
            '',
            0
        )
    ");

    // Execute
    $insert->execute([
        'uid' => $userID,
        'pwd' => $hashedPass,
        'dob' => $dob,
        'email' => $email,
    ]);

    // Success msg
    echo "<p>Signup successful - Click <a href='signin.php'>here</a> to logi in.</p>";

}

// Catch and handle errors
catch (Exception $e) {
    echo "<p style='color:red;'>Signup error: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>