<?php
use controllers\ArticleController;
use controllers\ReviewController;
use controllers\UserController;
use model\ArticleModel;
use model\ReviewModel;
use model\UserModel;

global $conn;
include("../dbconfig.php");
include("../controllers/ArticleController.php");
include("../controllers/ReviewController.php");
include("../controllers/UserController.php");
$message = "";
$modifyarticlemessage = "";

session_start();
$articleModel = new ArticleModel($conn);
$listController = new ArticleController($articleModel);
$articles = $listController->listAllArticlesByUser();

$reviewModel = new ReviewModel($conn);
$reviewController = new ReviewController($reviewModel);

$userModel = new UserModel($conn);
$userController = new UserController($userModel);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akce'])) {
    $akce = $_POST['akce'];
    $article_id = $_POST['article_id'];

    if ($akce === 'upravit') {
        $article_name = $_POST['article_name'];
        $abstract = $_POST['abstract'];
        $authors = $_POST['authors'];

        $listController->remakeArticle($article_id, $article_name, $abstract, $authors);
        header("Location: user-home-page.php");
    } elseif ($akce === 'odebrat') {
        $listController->deleteArticleById($article_id);
    }
}

?>


    <!DOCTYPE html>
    <html lang="cs">
    <head>
        <meta charset="UTF-8">
        <title>Home</title>
        <link rel="stylesheet" href="styles/user-home-page.css?v=1.2">
    </head>
    <body>

    <nav class="navbar">
        <div class="navbar-brand">
            <a href="user-home-page.php">Moje Webová Stránka</a>
        </div>
        <ul class="navbar-menu">
            <li><a href="user-home-page.php">Publikované články</a></li>
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

    <main class="user-home">

        <!-- Přihlašovací sekce / Home sekce -->
        <?php if(isset($_SESSION['login'])): ?>
            <section class="home-header">

                <?php if($_SESSION['userRole'] == 1): ?>
                    <form action="articleadd.php" method="post" class="add-article-form">
                        <button type="submit" class="btn btn-primary">Přidat článek</button>
                    </form>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <section class="guest-message">
                <h1>Jste zde jako nepřihlášený uživatel</h1>
                <form action="login.php" method="post">
                    <button type="submit" class="btn btn-primary">Přihlásit se</button>
                </form>
            </section>
        <?php endif; ?>

        <!-- Sekce publikovaných článků -->
        <section class="articles-section">
            <h1 class="section-title">Publikované články</h1>

            <?php if (!empty($articles)): ?>
                <ul class="articles-list">
                    <?php foreach ($articles as $article): ?>
                        <?php $foreign_reviews = $reviewController->listAllArticleReviews($article['article_id']); ?>

                        <!-- Jednotlivý článek -->
                        <li class="article-card">
                            <!-- Název článku -->

                            <h3>
                                <strong style="color: #007BFF; font-weight: bold;">
                                    <?= htmlspecialchars($article['authors']) . ": " . htmlspecialchars($article['article_name']); ?>
                                </strong>
                            </h3>
                            <!-- Abstrakt článku -->
                            <p class="article-abstract"><?= htmlspecialchars($article['abstract']) ?></p>

                            <!-- Tlačítko pro zobrazení obsahu -->
                            <button type="button"
                                    class="btn btn-toggle toggle-btn"
                                    data-target="content-<?= $article['article_id'] ?>">
                                Zobrazit
                            </button>

                            <!-- Skrytý obsah (PDF + recenze) -->
                            <div id="content-<?= $article['article_id'] ?>" class="toggle-content">

                                <!-- PDF soubor -->
                                <div class="pdf-container">
                                    <iframe src="<?= htmlspecialchars($article['file']) ?>" class="pdf-frame"></iframe>
                                </div>

                                <!-- Sekce dalších recenzí -->
                                <?php if (!empty($foreign_reviews)): ?>
                                    <div class="other-reviews">
                                        <strong>Další recenze:</strong>
                                        <div class="reviews-list">
                                            <?php foreach ($foreign_reviews as $f_review): ?>
                                                <?php
                                                $reviewer = $userController->getNameById($f_review['user_id']);
                                                $reviewName = $reviewer['name'] === $_SESSION['login'] ? "Moje recenze" : $reviewer['name'] . " " . $reviewer['surname'];
                                                ?>
                                                <div class="review-badge">
                                                    <?= htmlspecialchars($reviewName) ?>
                                                    <span class="stars">
                                                    <?php
                                                    $f_rating = (int)$f_review['total'];
                                                    for ($i=1; $i<=5; $i++) echo $i <= $f_rating ? '★' : '☆';
                                                    ?>
                                                </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Akční tlačítka -->
                            <div class="article-actions">

                                <!-- Upravit článek -->
                                <button type="button"
                                        class="btn btn-edit edit-btn"
                                        data-target="edit-<?= $article['article_id'] ?>">
                                    Upravit
                                </button>

                                <!-- Formulář pro úpravu -->
                                <div id="edit-<?= $article['article_id'] ?>" class="edit-content">
                                    <form action="user-home-page.php" method="post" enctype="multipart/form-data" class="edit-article-form">
                                        <input type="hidden" name="article_id" value="<?= htmlspecialchars($article['article_id']) ?>">

                                        <label>Název článku:</label>
                                        <input type="text" name="article_name" value="<?= htmlspecialchars($article['article_name']) ?>" required>

                                        <label>Autoři článku:</label>
                                        <input type="text" name="authors" value="<?= htmlspecialchars($article['authors']) ?>" required>

                                        <label>Abstrakt:</label>
                                        <textarea name="abstract" rows="4" required><?= htmlspecialchars($article['abstract']) ?></textarea>

                                        <button type="submit" name="akce" value="upravit" class="btn btn-save">Uložit změny</button>
                                    </form>
                                </div>

                                <form action="user-home-page.php" method="POST" class="delete-article-form">
                                    <input type="hidden" name="akce" value="odebrat">
                                    <input type="hidden" name="article_id" value="<?= htmlspecialchars($article['article_id']) ?>">
                                    <button type="submit" class="btn btn-danger">Odebrat</button>
                                </form>

                                <!-- Zprávy -->
                                <div class="message">
                                    <label><?= htmlspecialchars($modifyarticlemessage) ?></label>
                                </div>
                            </div>

                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-articles">Žádné články nebyly nalezeny.</p>
            <?php endif; ?>
        </section>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll(".toggle-btn");

            buttons.forEach(button => {
                button.addEventListener("click", () => {
                    const targetId = button.getAttribute("data-target");
                    const target = document.getElementById(targetId);

                    if (target.style.display === "none" || target.style.display === "") {
                        target.style.display = "block";
                        button.textContent = "Skrýt";
                    } else {
                        target.style.display = "none";
                        button.textContent = "Zobrazit";
                    }
                });
            });
        });
    </script>

    <script>
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = document.getElementById(btn.dataset.target);
                target.style.display = (target.style.display === 'none' || target.style.display === '') ? 'block' : 'none';
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Najdi všechny formuláře pro mazání
            const deleteForms = document.querySelectorAll('.delete-article-form');

            deleteForms.forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    const potvrzeni = confirm('Opravdu chcete položku smazat?');
                    if (!potvrzeni) {
                        // Zruší odeslání formuláře, pokud uživatel klikne na "Zrušit"
                        event.preventDefault();
                    }
                });
            });
        });
    </script>

    </body>
</html>
