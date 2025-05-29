<?php
// Preload crap
session_start();
if (!isset($_SESSION["userID"] )) {
    header("Location: signin.php?error=You%20must%20be%20logged%20in%20to%20edit%20pages.");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $id = $_GET['id'];
    if (!$id) {
        header("Location: index.php?error=Sorry,%20you%20cannot%20edit%20the%20page%20at%20this%20time.");
        exit;
    }
    $type = $_GET['type'];
    if (!$type || !in_array($type, ['title', 'person'])) {
        header("Location: index.php?error=Sorry,%20you%20cannot%20edit%20the%20page%20at%20this%20time.");
        exit;
    }
}

$userRank = $_SESSION['role'];

// Read page information
$pageurl =  $type . '/' . $id . '.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
$pagedata = file_get_contents($pageurl);

    if ($pagedata === false) {
        header("Location: index.php?error=Sorry,%20you%20cannot%20edit%20the%20page%20at%20this%20time. PAGEDATA NOT FOUND");
        exit;
    }
// Extract data from the page
if (preg_match('/<span\s+id="movie-title"\s*>(.*?)<\/span>/is', $pagedata, $matches)) {
    $title = trim($matches[1]);
}
if (preg_match('/<span\s+id="movie-year"\s*>(.*?)<\/span>/is', $pagedata, $matches)) {
    $year = trim($matches[1]);
}
if (preg_match('/<span\s+id="movie-runtime"\s*>(.*?)<\/span>/is', $pagedata, $matches)) {
    $runtime = trim($matches[1]);
}
if (preg_match('/<p\s+id="blurb-text"\s*>(.*?)<\/p>/is', $pagedata, $matches)) {
    $blurb = trim($matches[1]);
}
if (preg_match('/<p\s+id="plot-text"\s*>(.*?)<\/p>/is', $pagedata, $matches)) {
    $plot = trim($matches[1]);
}
if (preg_match('/<span\s+id="director"\s*>(.*?)<\/span>/is', $pagedata, $matches)) {
    $director = trim($matches);
}
if (preg_match('/<span\s+id="writers"\s*>(.*?)<\/span>/is', $pagedata, $matches)) {
    $writers = trim($matches);
}
if (preg_match('/<span\s+id="stars"\s*>(.*?)<\/span>/is', $pagedata, $matches)) {
    $stars = trim($matches);
}
if (preg_match('/<span\s+id="notable"\s*>(.*?)<\/span>/is', $pagedata, $matches)) {
    $notable = trim($matches);
}
//people
if (preg_match('/<span\s+id="person-year"\s*>(.*?)<\/span>/is', $pagedata, $matches)) {
    $year = trim($matches[1]);
}
if (preg_match('/<span\s+id="person-name"\s*>(.*?)<\/span>/is', $pagedata, $matches)) {
    $title = trim($matches[1]);
}
if (preg_match('/<ul\s+class="roles-list"\s*>(.*?)<\/ul>/is', $pagedata, $matches)) {
    $roles = trim($matches[1]);
}}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editing <?php echo $title; ?> | IMDB2.0</title>
    <!--JS Files-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="resources/search.js"></script>
    <!-- External CSS Stylesheet Import-->
    <link rel="stylesheet" href="resources/style.css" />
</head>
<body>
    <header class="header">
        <h1>IMDB2.0</h1>
        <p>Editing <?php echo $title; ?></p>
    </header>
    <?php include 'resources/navbar.php'; ?>
    <main class="main-content">
    <div id="warnings">
    <?php if (!empty($_GET['error'])): ?>
        <?php if ($_GET['error'] == 1): ?>
            <p id="page-warning">Failed to save, please try again soon or contact a site administrator.</p>
        <?php elseif ($_GET['error'] == 2): ?>
            <p id="page-warning">Failed to apply page edits due to an outdated Session. Please refresh the page and try again.</p>
        <?php elseif ($_GET['error'] == 3): ?>
            <p id="page-warning">Failed to find page ID. Please try again soon or contact a site administrator.</p>
        <?php else: ?>
            <p id="page-warning">An unknown error occured. Please try again soon or contact a site administrator.</p>
        <?php endif; ?>
    <?php endif; ?>
    </div>
        <?php 
        $pageurl = '/INFO263-Group-Assignment/2025-group1/imdb2/' . $pageurl;
        if ($type === 'title'){
            echo '
            <form id="edit-form-title" class="edit-form" action="resources/pageeditor.php" method="post">
            <label for="title">Name:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-title" name="title" value="' . htmlspecialchars($title) . '" required>
            
            <label for="year">Year:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-year" name="year" value="' . htmlspecialchars($year) . '" required>
            
            <label for="runtime">Runtime:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-runtime" name="runtime" value="' . htmlspecialchars($runtime) . '" required>

            <label for="blurb">Blurb:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-blurb" name="blurb" value="' . htmlspecialchars($blurb) . '" required>

            <label for="plot">Plot:</label>
            <textarea class="page-edit-form-entry" id="title-edit-plot" name="plot" rows="4" required>' . htmlspecialchars($plot) . '</textarea>
            <!-- The following fields are not working yet.
            <label for="director">Director(s):</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-director" name="director" value="' . htmlspecialchars($director) . '" required>

            <label for="writers">Writer(s):</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-writers" name="writers" value="' . htmlspecialchars($writers) . '" required>

            <label for="stars">Starring:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-stars" name="stars" value="' . htmlspecialchars($stars) . '" required>

            <label for="notable">Other Notable People:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-notable" name="notable" value="' . htmlspecialchars($notable) . '" required>
            -->
            <br><hr><br><p>Error with the people involved? Contact a site administrator to edit this content.</p><br><hr><br>
            <label for="sources">Edit Summary, Sources, and Notes:</label>
            <textarea class="page-edit-form-entry" id="title-edit-sources" name="sources" rows="6" minlength="10" maxlength="800" placeholder="Please enter any notes or sources for your edits. This edit message is limited to 800 characters and will be visible for all site users." required></textarea>

            <input type="hidden" name="type" value="title">
            <input type="hidden" name="id" value="'. htmlspecialchars($id) .'">
            <input type="hidden" name="pageurl" value=" . $pageurl . ">
            <button type="submit">Save Changes</button>
        </form>
            ';
            if (isset($userRank) && $userRank === 'admin') {
                echo '<button id="reset-page-button" onclick="if(confirm(\'Are you sure you want to RESET this page? All user modified data will be wiped and return to the database entry. Likes and Comments will not be affected.\')){window.location.href=\'resources/deletepage.php?type=title&id=' . htmlspecialchars($id) . '\';}">Delete Page</button>';
            }
        } elseif ($type === 'person') {
            echo "
            <form id=\"edit-form-person\" class=\"edit-form\" action=\"resources/pageeditor.php\" method=\"post\">
            <label for=\"title\">Name:</label>
            <input class=\"page-edit-form-entry\" type=\"text\" id=\"person-edit-title\" name=\"title\" value=\"$title\" required>
            
            <label for=\"year\">Year of Birth:</label>
            <input class=\"page-edit-form-entry\" type=\"number\" id=\"person-edit-year\" name=\"year\" value=\"$year\" required>

            <label for=\"bio\">Bio:</label>
            <input class=\"page-edit-form-entry\" type=\"text\" id=\"person-edit-bio\" name=\"bio\" value=\"$bio\" required>

            <label for=\"roles\">Roles:</label>
            <input class=\"page-edit-form-entry\" type=\"text\" id=\"person-edit-roles\" name=\"roles\" value=\"$roles\" required>

            <label for=\"sources\">Sources/Notes</label>
            <textarea class=\"page-edit-form-entry\" id=\"person-edit-sources\" name=\"sources\" maxlength=\"800\" placeholder=\"Please enter any notes or sources for your edits. This edit message is limited to 800 characters and will be visible for all site users.\" required></textarea>


            <input type=\"hidden\" name=\"type\" value=\"person\">
            <input type=\"hidden\" name=\"id\" value=\"" . htmlspecialchars($id) . "\">
            <input type=\"hidden\" name=\"pageurl\" value=\"" . htmlspecialchars($pageurl) . "\">

            <button type=\"submit\">Save Changes</button>
            </form>
            ";
        }
        ?>
</main>
    <?php include 'resources/footer.php'; ?>
</body>
</html>
