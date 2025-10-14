<?php

namespace model;
use PDO;
class ReviewModel
{
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }
    public function listAllReviews(){
        $sql = "SELECT * FROM review WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $_SESSION['userId']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addReview($article_id, $user_id){
        $sql = "INSERT INTO review (article_id, user_id, state) VALUES (:article_id, :user_id, :state)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':article_id' => $article_id,
            ':user_id' => $user_id,
            ':state' => 3
        ]);
    }
    public function articleReviewersCount($article_id){
        $sql = "SELECT COUNT(*) FROM review WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':article_id' => $article_id
        ]);
        return $stmt->fetchColumn();
    }
    public function setReview($review_id, $total, $language, $content, $novelty, $comment){
        $sql = "UPDATE review 
            SET total = :total,
                language = :language,
                content = :content,
                novelty = :novelty,
                comment = :comment,
                state = :state
            WHERE review_id = :review_id";

        $stmt = $this->conn->prepare($sql);
        $two = 2;

        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':novelty', $novelty);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':state', $two);
        $stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function listAllArticleReviews($article_id){
        $sql = "SELECT * FROM review WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':article_id' => $article_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteReview($review_id){
        $sql = "DELETE FROM review WHERE review_id = :review_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':review_id' => $review_id
        ]);
    }
    public function hasUserReview($user_id, $article_id)
    {
        $sql = "SELECT COUNT(*) FROM review WHERE user_id = :user_id AND article_id = :article_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':article_id' => $article_id
        ]);
        return $stmt->fetchColumn();
    }
}