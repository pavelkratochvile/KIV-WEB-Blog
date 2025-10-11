<?php
use controllers\ArticleController;
use controllers\UserController;
use controllers\ReviewController;
use model\ArticleModel;
use model\UserModel;
use model\ReviewModel;

global $conn;
include("../dbconfig.php");
include("../controllers/ArticleController.php");
include("../controllers/UserController.php");
include("../controllers/ReviewController.php");
$message = "";
$addreviewmessage = "";

session_start();
$articleModel = new ArticleModel($conn);
$articleController = new ArticleController($articleModel);
$articles = $articleController->listAllArticles();

$userModel = new UserModel($conn);
$userController = new UserController($userModel);
$reviewers = $userController->listReviewers();

$reviewModel = new ReviewModel($conn);
$reviewController = new ReviewController($reviewModel);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : null;
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    if ($_POST && $action) {
        switch ($action) {
            case 'approve':
                $articleController->changeState($article_id, 1);
                break;

            case 'reject':
                $articleController->changeState($article_id, 0);
                break;

            default:
                echo "Neznámá akce!";
        }
    } else {
        echo "Chybí ID článku nebo akce!";
    }

    if (!empty($_POST['article_id']) && !empty($_POST['reviewer_id'])) {
        if($reviewController->articleReviewersCount($_POST['article_id']) <= 2){
            $reviewController->addReview($_POST['article_id'], $_POST['reviewer_id']);
            header("Location: admin-reviews.php?success=1");
            exit;
        }
        else{
            $addreviewmessage = "Přesáhli jste počet recenzentů pro článek.";
        }
    } else {
        $addreviewmessage = "Chybí článek nebo recenzent.";
    }
}
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
        <a href="admin-reviews.php">Moje Webová Stránka</a>
    </div>
    <ul class="navbar-menu">
        <li><a href="admin-users.php">Správa uživatelů</a></li>
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


<h1>Publikované články</h1>

<?php if (!empty($articles)): ?>
    <ul>
        <?php foreach ($articles as $article): ?>
            <li>
                <strong><?= htmlspecialchars($article['article_name']) . ": " . htmlspecialchars($article['authors']);
                    ?></strong><br>
                <?= htmlspecialchars($article['abstract']) ?>


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

                <form method="POST" action="admin-reviews.php">
                    <select name="reviewer_id">
                        <option value="">Vyber recenzenta</option>
                        <?php foreach ($reviewers as $r): ?>
                            <option value="<?= htmlspecialchars($r['user_id']) ?>">
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="hidden" name="article_id" value="<?= $article['article_id'] ?>">
                    <button type="submit" class="btn btn-success">Přidat</button>
                </form>

                <form method="POST" action="admin-reviews.php">
                    <input type="hidden" name="article_id" value="<?= $article['article_id'] ?>">
                    <button type="submit" name="action" value="approve" class="btn btn-success">Schválit</button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger">Zamítnout</button>
                </form>

            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Žádné články nebyly nalezeny.</p>
<?php endif; ?>

</body>
</html>
