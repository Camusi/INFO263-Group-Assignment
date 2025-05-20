<?php 
$id = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
if ($id === '') {
    echo  'Missing a Query. Bad ID?';
    exit;
};

if ($type === 'title') {
    $pagePath = '../title/' . $id . '.php';
} else if ($type === 'person') {
    $pagePath = '../person/' . $id . '.php';
} else {
    echo  'Invalid Type. Bad Request?';
    exit;
}

echo 'Page Path: ' . $pagePath . '<br>';
echo 'Normally we would generate a page there now but missing the template.';
echo '<hr>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creating <?php echo isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '' ?> <?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ' '; ?></title>
    <link rel="stylesheet" href="../resources/style.css">
</head>
<body>
    Currently creating a page for <?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ' '; ?>.
    Your patience is appreciated and you will be redirected to the page once it is created.
    <br><img src="img/load.gif" alt="Loading..." style="width: 250px; height: 250px;">
    <hr>
    <?php
    if (copy(__DIR__ . '/template_title.php', $pagePath)) {
        echo "Copying template to {$pagePath}<br>";
    } else {
        echo "Failed to copy template to {$pagePath}<br>";
        $error = error_get_last();
        if ($error) {
            echo "Error details: " . htmlspecialchars($error['message']) . "<br>";
        }
    }

    ?>
</body>
</html>
