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
    <link rel="stylesheet" href="styles/review-home-page.css">

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
    <?php if (isset($_SESSION['login'])): ?>
        <h2>Home</h2>
        <p>Vítejte, recenzente!</p>

    <?php else: ?>
        <h1>Jste zde jako nepřihlášený uživatel</h1>
        <form action="login.php" method="post">
            <button type="submit">Přihlásit se</button>
        </form>
    <?php endif; ?>
</main>
<h1>Publikované recenze</h1>

<?php if (!empty($reviews)): ?>
    <ul>
        <?php foreach ($reviews as $review): ?>
            <?php $currentArticle = $articleController->getArticleById($review['article_id']); ?>
            <li>
                <div>
                    <!-- Ostatní recenze pro stejný článek -->
                    <?php
                    $foreign_reviews = $reviewController->listAllArticleReviews($review['article_id']);
                    ?>

                    <?php if (!empty($foreign_reviews)): ?>
                        <div>
                            <strong>Další recenze:</strong>
                            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                <?php foreach ($foreign_reviews as $f_review): ?>
                                    <div style="border:1px solid #ccc; padding:5px 10px; border-radius:5px;">
                                        <?php
                                        $reviewer = $userController->getNameById($f_review['user_id']);
                                        if($reviewer['name'] == $_SESSION['login']){
                                            echo "Moje recenze ";
                                        }
                                        else{
                                            echo $reviewer['name'] . " " . $reviewer['surname'];
                                        }

                                        $f_rating = (int)$f_review['total'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $f_rating ? '★' : '☆';
                                        }
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php if($review['state'] == 0){
                                    echo"Zamítnuta";
                                }
                                else if($review['state'] == 1){
                                    echo"Schválena";
                                }
                                else if($review['state'] == 2){
                                    echo"Odevzdána";
                                }
                                else if($review['state'] == 3){
                                    echo"Přidělena";
                                }?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Info o článku -->
                    <div>
                        <strong>Článek:</strong>
                        <?= htmlspecialchars(isset($currentArticle['article_name']) ? $currentArticle['article_name'] : 'NULL') ?><br>
                        <strong>Autor/Abstrakt:</strong>
                        <?= htmlspecialchars(isset($currentArticle['abstract']) ? $currentArticle['abstract'] : 'NULL') ?><br>
                    </div>

                        <!-- Tlačítko pro otevření formuláře -->
                        <button class="toggle-review-btn">Recenzovat</button>

                        <!-- Skrytý formulář -->
                        <form method="post" class="review-form" style="display: none;">
                            <input type="hidden" name="review_id" value="<?= htmlspecialchars($review['review_id']) ?>">

                            <hr>
                            <h4>Posuzované vlastnosti:</h4>

                            <label>Celkové hodnocení:
                                <input type="number" step="0.5" name="total"
                                       value="<?= htmlspecialchars(isset($review['total']) ? $review['total'] : '') ?>">
                            </label><br>

                            <label>Jazyk:
                                <input type="number" step="0.5" name="language"
                                       value="<?= htmlspecialchars(isset($review['language']) ? $review['language'] : '') ?>">
                            </label><br>

                            <label>Obsah:
                                <input type="number" step="0.5" name="content"
                                       value="<?= htmlspecialchars(isset($review['content']) ? $review['content'] : '') ?>">
                            </label><br>

                            <label>Novost:
                                <input type="number" step="0.5" name="novelty"
                                       value="<?= htmlspecialchars(isset($review['novelty']) ? $review['novelty'] : '') ?>">
                            </label><br><br>

                            <h4>Vlastní komentář:</h4>
                            <textarea name="comment" rows="4" cols="50"><?= htmlspecialchars($review['comment']) ?></textarea><br><br>

                            <button type="submit">Uložit recenzi</button>
                            <button type="button" class="cancel-btn">Zrušit</button>
                        </form>
                    </div>
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





</body>
</html>
