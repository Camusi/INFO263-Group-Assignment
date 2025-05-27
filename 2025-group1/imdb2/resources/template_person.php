<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{NAME} | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p>{NAME}</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id={ID}" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text">{WARNINGS}</p>
        </div>
    </section>
    <main class="main-content">
        <div id="title"><h1>{NAME}</h1></div>
        <aside id="blurb">
            {BIO}
            <br>Born in {YEAR}.
        </aside>
        <div id="rating">
            <?php
                try {
                    $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
                    exit;
                }
                

                $stmt = $db->prepare('SELECT likes FROM name_basics_trim WHERE nconst = \'{ID}\'');
                $stmt->execute();
                $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $likes = $likes[0]['likes'] ?? '?'; // Default to ? if no likes found
                ?>
            <p><span><?php echo $likes; ?></span> Likes!</p>
            <button id="like-button">I like this!</button>
            <button id="dislike-button">I dislike this!</button>
        </div>
        <div id="roles" class="roles">
            <h2>Roles</h2>
            <ul class="roles-list">
                {ROLES}
            </ul>
        </div>
        <div id="comments"></div>
    </main>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>