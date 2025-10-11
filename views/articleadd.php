<?php
session_start();
use model\ArticleModel;
use controllers\ArticleController;

global $conn;
include("../dbconfig.php");
include("../controllers/ArticleController.php");

$message = "";
$articleModel = new ArticleModel($conn);
$articleAddController = new ArticleController($articleModel);

if(isset($_POST['add-article'])){
    $articleName = $_POST['articleName'];
    $abstract = $_POST['abstract'];
    $file = $_POST['file'];
    $authors = $_POST['authors'];

    $message = $articleAddController->addArticle($articleName, $abstract, $file, $authors);
}

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přidání článku</title>
    <link rel="stylesheet" href="styles/articleadd.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-brand">
        <a href="user-home-page.php">Moje Webová Stránka</a>
    </div>
    <ul class="navbar-menu">
        <li><a href="user-home-page.php">Home</a></li>
        <?php if(isset($_SESSION['login'])): ?>
            <li>
                <?php echo htmlspecialchars($_SESSION['name'] . " " . $_SESSION['surname']); ?>
            </li>
            <li>
                <form action="logout.php" method="post">
                    <button type="submit">Odhlásit se</button>
                </form>
            </li>
        <?php endif; ?>

    </ul>
</nav>

<main>
    <section class="article-form-section">
        <h2>Přidání článku</h2>
        <form action="articleadd.php" method="post" enctype="multipart/form-data">
            <label for="articleName">Název článku</label>
            <input type="text" id="articleName" name="articleName" required>

            <label for="authors">Jména autorů</label>
            <input type="text" id="authors" name="authors" required>

            <label for="abstract">Obsah</label>
            <textarea id="abstract" name="abstract" rows="6" required></textarea>

            <label for="file">Soubor</label>
            <div class="custom-file">
                <input type="text" id="file" name="file" required>
                <span class="custom-file-label">Vyber soubor</span>
            </div>

            <input type="submit" name="add-article" value="Přidat článek">
        </form>

        <?php if(!empty($message)) : ?>
            <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </section>
</main>

<footer>
    &copy; 2025 Moje webová aplikace
</footer>


</body>
</html>




