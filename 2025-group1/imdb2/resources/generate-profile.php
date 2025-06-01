<?php
session_start();
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : '';

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$pagePath = "../user/{$id}.php";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finding {id}</title>
</head>
<body>
    <p>Welcome back <?php echo htmlspecialchars($userID); ?>!</p>
</body>
</html>

<?php

global $warningsArr;
$warningsArr = array();

?>
<?php
    if (copy(__DIR__ . '/template_user.php', $pagePath)) {
        echo "Copying template to {$pagePath}<br>";
        $isGenerated = true;
    } else {
        echo "Failed to copy template to {$pagePath}<br>";
        $error = error_get_last();
        if ($error) {
            echo "Error details: " . htmlspecialchars($error['message']) . "<br>";
            header("Refresh: 0; URL=../index.php?error=Failed%20to%20create%20page%20{$id}. $error[message]");
        }
    }
    $isWritten = false;
        // Read the copied file
        $content = file_get_contents($pagePath);
        if ($content !== false) {
            // Replace placeholders
            $content = str_replace('{NAME}', $id, $content);
            $content = str_replace('{ID}', $id, $content);
            $content = str_replace('{USER}', $id, $content);
            // Write back to the file
            if (file_put_contents($pagePath, $content) !== false) {
                $isWritten = true;
            }
        }
    
    
    if ($isGenerated && $isWritten) {
        echo "Page {$pagePath} created successfully.";
        header("Refresh: 0; URL={$pagePath}");
    }

    ?>
</body>
</html>