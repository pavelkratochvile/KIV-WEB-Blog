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
$addreviewmessage = "";

session_start();
$articleModel = new ArticleModel($conn);
$listController = new ArticleController($articleModel);
$articles = $listController->listAllArticlesByUser();

$reviewModel = new ReviewModel($conn);
$reviewController = new ReviewController($reviewModel);

$userModel = new UserModel($conn);
$userController = new UserController($userModel);
?>


    <!DOCTYPE html>
    <html lang="cs">
    <head>
        <meta charset="UTF-8">
        <title>Home</title>
        <link rel="stylesheet" href="styles/user-home-page.css">
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
        <?php if(isset($_SESSION['login'])): ?>
            <h2>Home</h2>

            <?php if($_SESSION['userRole'] == 1): ?>
                <form action="articleadd.php" method="post" style="margin-bottom: 1rem;">
                    <button type="submit">Přidat článek</button>
                </form>
            <?php endif; ?>

        <?php else: ?>
            <h1>Jste zde jako nepřihlášený uživatel</h1>
            <form action="login.php" method="post">
                <button type="submit">Přihlásit se</button>
            </form>
        <?php endif; ?>
    </main>

    <h1>Publikované články</h1>

    <?php if (!empty($articles)): ?>
        <ul>
            <?php foreach ($articles as $article): ?>
                <?php $foreign_reviews = $reviewController->listAllArticleReviews($article['article_id']) ?>


                <?php if (!empty($foreign_reviews)): ?>
                    <div>
                        <strong>Další recenze:</strong>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <?php foreach ($foreign_reviews as $f_review): ?>
                                <div style="border:1px solid #ccc; padding:5px 10px; border-radius:5px;">
                                    <?php
                                    $reviewer = $userController->getNameById($f_review['user_id']);
                                    if ($reviewer['name'] == $_SESSION['login']) {
                                        echo "Moje recenze ";
                                    } else {
                                        echo htmlspecialchars($reviewer['name']) . " " . htmlspecialchars($reviewer['surname']);
                                    }

                                    $f_rating = (int)$f_review['total'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $f_rating ? '★' : '☆';
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>


                <li>
                    <strong><?= htmlspecialchars($article['article_name']) ?></strong><br>
                    <?= htmlspecialchars($article['abstract']) ?>
                </li>
                <div style="display: flex; gap: 10px;">
                    <form action="user-home-page.php" method="POST">
                        <input type="hidden" name="akce" value="upravit">
                        <button type="submit">Upravit</button>
                    </form>

                    <form action="user-home-page.php" method="POST">
                        <input type="hidden" name="akce" value="zobrazit">
                        <button type="submit">Zobrazit</button>
                    </form>

                    <form action="user-home-page.php" method="POST">
                        <input type="hidden" name="akce" value="odebrat">
                        <button type="submit">Odebrat</button>
                    </form>
                </div>

            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Žádné články nebyly nalezeny.</p>
    <?php endif; ?>

    </body>
    </html>
