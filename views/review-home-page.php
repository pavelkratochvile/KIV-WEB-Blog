<?php

use controllers\ReviewController;
use controllers\ArticleController;
use controllers\UserController;
use model\ReviewModel;
use model\ArticleModel;
use model\UserModel;

global $conn;
include("../dbconfig.php");
include("../controllers/ReviewController.php");
include("../controllers/ArticleController.php");
include("../controllers/UserController.php");

$message = "";

session_start();
$reviewModel = new ReviewModel($conn);
$reviewController = new ReviewController($reviewModel);
$reviews = $reviewController->listReviews();

$articleModel = new ArticleModel($conn);
$articleController = new ArticleController($articleModel);

$userModel = new UserModel($conn);
$userController = new UserController($userModel);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    // Bezpečné načtení hodnot (bez ?? pro kompatibilitu)
    $review_id = (int) $_POST['review_id'];
    $total     = isset($_POST['total']) ? $_POST['total'] : null;
    $language  = isset($_POST['language']) ? $_POST['language'] : null;
    $content   = isset($_POST['content']) ? $_POST['content'] : null;
    $novelty   = isset($_POST['novelty']) ? $_POST['novelty'] : null;
    $comment   = isset($_POST['comment']) ? $_POST['comment'] : '';

    // (volitelně) validace / přetypování čísel
    if ($total !== null)  $total = (float) $total;
    if ($language !== null) $language = (float) $language;
    if ($content !== null)  $content = (float) $content;
    if ($novelty !== null)  $novelty = (float) $novelty;

    $reviewController->setReview($review_id, $total, $language, $content, $novelty, $comment);
    $message = "Recenze úspešně uložena.";
}

?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles/review-home-page.css?v=1.2">

</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <a href="review-home-page.php">Moje Webová Stránka</a>
    </div>
    <ul class="navbar-menu">
        <li><a href="review-home-page.php">Home</a></li>
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



<h1 class="reviews-title">Publikované recenze</h1>

<?php if (!empty($reviews)): ?>
    <ul class="reviews-list">
        <?php foreach ($reviews as $review): ?>
            <?php $currentArticle = $articleController->getArticleById($review['article_id']); ?>
            <li class="review-item">
                <div class="review-card">

                    <?php
                    $foreign_reviews = $reviewController->listAllArticleReviews($review['article_id']);
                    ?>
                    <?php if (!empty($foreign_reviews)): ?>
                        <div class="other-reviews">
                            <strong>Další recenze:</strong>
                            <div class="other-reviews-list">
                                <?php foreach ($foreign_reviews as $f_review): ?>
                                    <div class="other-review">
                                        <?php
                                        $reviewer = $userController->getNameById($f_review['user_id']);
                                        echo $reviewer['name'] === $_SESSION['login'] ? "Moje recenze " : $reviewer['name'] . " " . $reviewer['surname'];

                                        $f_rating = (int)$f_review['total'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $f_rating ? '★' : '☆';
                                        }
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                                <div class="review-status">
                                    <?php
                                    switch ($review['state']) {
                                        case 0: echo "Zamítnuta"; break;
                                        case 1: echo "Schválena"; break;
                                        case 2: echo "Odevzdána"; break;
                                        case 3: echo "Přidělena"; break;
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="article-info">
                        <strong>Článek:</strong>
                        <?= htmlspecialchars($currentArticle['article_name'] ?? 'NULL') ?><br>
                        <strong>Autor/Abstrakt:</strong>
                        <?= htmlspecialchars($currentArticle['abstract'] ?? 'NULL') ?><br>
                    </div>

                    <a class="download-btn" href="<?= htmlspecialchars($currentArticle['file']) ?>" download>Stáhnout PDF</a>

                    <button class="toggle-review-btn">Recenzovat</button>

                    <form method="post" class="review-form" style="display: none;">
                        <input type="hidden" name="review_id" value="<?= htmlspecialchars($review['review_id']) ?>">
                        <hr>
                        <h4>Posuzované vlastnosti:</h4>

                        <label>Celkové hodnocení:
                            <input type="number" step="0.5" name="total" value="<?= htmlspecialchars($review['total'] ?? '') ?>">
                        </label><br>

                        <label>Jazyk:
                            <input type="number" step="0.5" name="language" value="<?= htmlspecialchars($review['language'] ?? '') ?>">
                        </label><br>

                        <label>Obsah:
                            <input type="number" step="0.5" name="content" value="<?= htmlspecialchars($review['content'] ?? '') ?>">
                        </label><br>

                        <label>Novost:
                            <input type="number" step="0.5" name="novelty" value="<?= htmlspecialchars($review['novelty'] ?? '') ?>">
                        </label><br><br>

                        <h4>Vlastní komentář:</h4>
                        <textarea name="comment" rows="4" cols="50"><?= htmlspecialchars($review['comment']) ?></textarea><br><br>

                        <button type="submit" class="save-review-btn">Uložit recenzi</button>
                        <button type="button" class="cancel-review-btn">Zrušit</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Žádné články nebyly nalezeny.</p>
    <?php echo $_SESSION['userId'] ?>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var buttons = document.querySelectorAll('.toggle-review-btn');

        buttons.forEach(function(button) {
            button.addEventListener('click', function() {
                var form = this.nextElementSibling;

                if (form.style.display === 'block') {
                    form.style.display = 'none';
                    this.textContent = 'Recenzovat';
                } else {
                    form.style.display = 'block';
                    this.textContent = 'Skrýt formulář';
                }
            });
        });
    });
</script>

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





</body>
</html>
