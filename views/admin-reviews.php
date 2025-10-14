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
$confirmmessage = "";
$declinemessage = "";
$confirmaddreviewmessage = "";
$declineaddreviewmessage = "";

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
                header("Location: admin-reviews.php");
                exit;

            case 'reject':
                $articleController->changeState($article_id, 0);
                header("Location: admin-reviews.php");
                exit;

            default:
                $confirmmessage = "Neznámá akce!";
        }
    }
    elseif (!empty($_POST['article_id']) && !empty($_POST['reviewer_id'])) {
        if($reviewController->articleReviewersCount($_POST['article_id']) <= 2){
            if($reviewController->hasUserReview($_POST['reviewer_id'], $_POST['article_id']) < 1){
                $confirmaddreviewmessage = "Recenzent byl přidán.";
                $reviewController->addReview($_POST['article_id'], $_POST['reviewer_id']);
                header("Location: admin-reviews.php?success=1");
                exit;
            }
            elseif ($reviewController->hasUserReview($_POST['reviewer_id'], $_POST['article_id']) >= 1){
                $declineaddreviewmessage = "Tento uživatel již daný článek recenzuje";
            }
        }
        else{
            $declineaddreviewmessage = "Přesáhli jste počet recenzentů pro článek.";
        }
    }

    elseif (!empty($_POST['review_id'])){
        $reviewController->deleteReview($_POST['review_id']);
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles/admin-review.css?v=1.2">
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

<main class="articles-container">
    <h1 class="articles-title">Publikované články</h1>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <p class="form-message success-message">Recenzent byl přidán.</p>
    <?php endif; ?>

    <?php if (isset($declineaddreviewmessage)): ?>
        <p class="form-message error-message"><?= htmlspecialchars($declineaddreviewmessage) ?></p>
    <?php endif; ?>

    <?php if (!empty($articles)): ?>
        <ul class="articles-list">
            <?php foreach ($articles as $article): ?>
                <li class="article-item">
                    <div class="article-status">
                        <?php if ($article['state'] == 2): ?>
                            <div class="status-pending">Čeká na posouzení</div>
                        <?php elseif ($article['state'] == 1): ?>
                            <div class="status-approved">Článek je schválen</div>
                        <?php elseif ($article['state'] == 0): ?>
                            <div class="status-rejected">Článek je zamítnut</div>
                        <?php endif; ?>
                    </div>
                    <div class="article-header">
                        <strong class="article-name">
                            <?= htmlspecialchars($article['authors']) . ": " . htmlspecialchars($article['article_name']); ?>
                        </strong>
                    </div>

                    <div class="article-abstract">
                        <?= htmlspecialchars($article['abstract']) ?>
                    </div>

                    <?php $foreign_reviews = $reviewController->listAllArticleReviews($article['article_id']); ?>

                    <?php if (!empty($foreign_reviews)): ?>
                        <div class="article-reviews">
                            <strong class="article-reviews-title">Další recenze:</strong>
                            <div class="reviews-container">
                                <?php foreach ($foreign_reviews as $f_review): ?>
                                    <div class="review-box">
                                        <?php
                                        $reviewer = $userController->getNameById($f_review['user_id']);
                                        if ($reviewer['name'] == $_SESSION['login']) {
                                            echo "<span class='review-author my-review'>Moje recenze</span> ";
                                        } else {
                                            echo "<span class='review-author'>"
                                                . htmlspecialchars($reviewer['name']) . " "
                                                . htmlspecialchars($reviewer['surname']) .
                                                "</span> ";
                                        }

                                        $f_rating = (int)$f_review['total'];
                                        echo "<span class='review-stars'>";
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $f_rating ? '★' : '☆';
                                        }
                                        echo "</span>";
                                        ?>
                                        <form method="POST" action="admin-reviews.php">
                                            <input type="hidden" name="review_id" value="<?= $f_review['review_id'] ?>">
                                            <button type="submit" style="color:red; font-size:1.2rem;">❌</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="admin-reviews.php" class="assign-reviewer-form">
                        <select name="reviewer_id" class="reviewer-select">
                            <option value="">Vyber recenzenta</option>
                            <?php foreach ($reviewers as $r): ?>
                                <option value="<?= htmlspecialchars($r['user_id']) ?>">
                                    <?= htmlspecialchars($r['name']) . " " . htmlspecialchars($r['surname'])?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="hidden" name="article_id" value="<?= $article['article_id'] ?>">
                        <button type="submit" class="btn btn-success">Přidat</button>
                    </form>

                    <form method="POST" action="admin-reviews.php" class="approval-form">
                        <input type="hidden" name="article_id" value="<?= $article['article_id'] ?>">
                        <button type="submit" name="action" value="approve" class="btn btn-success">Schválit</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger">Zamítnout</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="no-articles-message">Žádné články nebyly nalezeny.</p>
    <?php endif; ?>
</main>

</body>
</html>
