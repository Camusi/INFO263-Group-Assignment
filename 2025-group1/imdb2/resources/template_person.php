<?php session_start(); ?>
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
    <p id="person-name">{NAME}</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=person&id={ID}" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text">{WARNINGS}</p>
        </div>
    </section>
    <main class="main-content">
        <div id="person-name"><h1>{NAME}</h1></div>
        <div class="blurb-image">
            <div>
                <aside id="blurb">
                    <p id="blurb-text">{BLURB}</p>
                    <ul>
                        <li>Born: <span id="person-year"></span>{YEAR}</span></li>
                        <li>Name: <span id="person-name">{NAME}</span></li>
                    </ul>
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
                    <p><span id="like-count"><?php echo $likes; ?></span> Likes</p>
                    <?php if (!isset($_SESSION['userID'])){echo '<button id="rate-login-prompt">Login to rate "{NAME} ({YEAR})"!</button>';} else{echo '<button id="like-button">👍 Like</button><button id="dislike-button">👎 Dislike</button>';} ?> 
                </div>
            </div>

            <div class="svg-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="19vw" height="19vw" viewBox="0 0 24 24" fill="black">
                    <path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.2c-3.1 0-9.3 1.6-9.3 4.9v2.7h18.6v-2.7c0-3.3-6.2-4.9-9.3-4.9z"/>
                </svg>
            </div>
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