<?php
// Preload crap
/* NOT YET
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php?error=Sorry,%20you%20cannot%20edit%20the%20page%20at%20this%20time.") ;
    exit;
}
$type = $_GET['type'] ?? null;
if (!$type || !in_array($type, ['title', 'person'])) {
    header("Location: index.php?error=Sorry,%20you%20cannot%20edit%20the%20page%20at%20this%20time.");
    exit;
}*/

// Read page information
$pageurl = '/' . htmlspecialchars($type) . '/' . htmlspecialchars($id) . '.php';
$pagedata = file_get_contents($pageurl);
if ($pagedata === false) {
    header("Location: index.php?error=Sorry,%20you%20cannot%20edit%20the%20page%20at%20this%20time.");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editing {TITLE} | IMDB2.0</title>
    <!--JS Files-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="resources/search.js"></script>
    <!-- External CSS Stylesheet Import-->
    <link rel="stylesheet" href="resources/style.css" />
</head>
<body>
    <header class="header">
        <h1>Editing {TITLE}</h1>
        <p>{PAGE_URL}</p>
    </header>
    <?php include 'resources/navbar.php'; ?>
    <main class="main-content">
        <?php 
        if ($type === 'title'){
            echo `
            <form id="edit-form-title" class="edit-form" action="edit.php" method="post">
            <label for="title">Name:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-title" name="title" value="{TITLE}" required>
            
            <label for="year">Year:</label>
            <input class="page-edit-form-entry" type="number" id="title-edit-year" name="year" value="{YEAR}" required>

            <label for="blurb">Blurb:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-blurb" name="blurb" value="{BLURB}" required>

            <label for="plot">Plot:</label>
            <textarea class="page-edit-form-entry" id="title-edit-plot" name="plot" rows="4" required>{PLOT}</textarea>

            <label for="director">Director(s):</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-director" name="director" value="{DIRECTOR}" required>

            <label for="writers">Writer(s):</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-writers" name="writers" value="{WRITERS}" required>

            <label for="stars">Starring:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-stars" name="stars" value="{STARS}" required>

            <label for="notable">Other Notable People:</label>
            <input class="page-edit-form-entry" type="text" id="title-edit-notable" name="notable" value="{NOTABLE}">

            <label for="sources">Sources/Notes</label>
            <textarea class="page-edit-form-entry" id="title-edit-sources" name="sources" rows="4" placeholder="Please enter any notes or sources for your edits" required></textarea>

            <label for="warnings">Page Warnings</label>
            <p>Please check the relevant page warnings below.</p>
            <input type="checkbox" id="title-edit-warnings-stub" name="warnings" value="1">
            <label for="title-edit-warnings-stub">Stub Page</label>
            <input type="checkbox" id="title-edit-warnings-unverified" name="warnings" value="2">
            <label for="title-edit-warnings-unverified">Unverified Information</label>
            <input type="checkbox" id="title-edit-warnings-duplicate" name="warnings" value="3">
            <label for="title-edit-warnings-duplicate">Duplicate Page</label>
            <input type="checkbox" id="title-edit-warnings-outdated" name="warnings" value="4">
            <label for="title-edit-warnings-outdated">Outdated Information</label>

            <button type="submit">Save Changes</button>
        </form>
            `;
        } elseif ($type === 'person') {
            echo `
            <form id="edit-form-person" class="edit-form" action="edit.php" method="post">
            <label for="title">Name:</label>
            <input class="page-edit-form-entry" type="text" id="person-edit-title" name="title" value="{TITLE}" required>
            
            <label for="year">Year of Birth:</label>
            <input class="page-edit-form-entry" type="number" id="person-edit-year" name="year" value="{YEAR}" required>

            <label for="bio">Bio:</label>
            <input class="page-edit-form-entry" type="text" id="person-edit-bio" name="bio" value="{BIO}" required>

            <label for="roles">Roles:</label>
            <input class="page-edit-form-entry" type="text" id="person-edit-roles" name="roles" value="{ROLES}">

            <label for="sources">Sources/Notes</label>
            <textarea class="page-edit-form-entry" id="person-edit-sources" name="sources" rows="4" placeholder="Please enter any notes or sources for your edits" required></textarea>

            <label for="warnings">Page Warnings</label>
            <p>Please check the relevant page warnings below.</p>
            <input type="checkbox" id="person-edit-warnings-stub" name="warnings" value="1">
            <label for="person-edit-warnings-stub">Stub Page</label>
            <input type="checkbox" id="person-edit-warnings-unverified" name="warnings" value="2">
            <label for="person-edit-warnings-unverified">Unverified Information</label>
            <input type="checkbox" id="person-edit-warnings-duplicate" name="warnings" value="3">
            <label for="person-edit-warnings-duplicate">Duplicate Page</label>
            <input type="checkbox" id="person-edit-warnings-outdated" name="warnings" value="4">
            <label for="person-edit-warnings-outdated">Outdated Information</label>

            <button type="submit">Save Changes</button>
            </form>
            `;
        }
        ?>
</main>
    <?php include 'resources/footer.php'; ?>
</body>
</html>