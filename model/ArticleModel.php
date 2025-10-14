<?php

namespace model;
use PDO;
class ArticleModel
{
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }
    public function insertArticleToDatabase($articleName, $abstract, $filePathForDB, $authors)
    {
        $sql = "INSERT INTO article(user_id, article_name, abstract, state,	file, authors)VALUES (:user_id, :article_name, :abstract, :state, :file, :authors)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':user_id' => $_SESSION['userId'],
            ':article_name' => $articleName,
            ':abstract' => $abstract,
            ':state' => 2,
            ':file' => $filePathForDB,
            ':authors' => $authors
        ]);
    }
    public function listAllArticlesByState($state) {
        $sql = "SELECT * FROM article WHERE state = :state";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':state' => $state]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getArticleById($articleId) {
        $sql = "SELECT * FROM article WHERE article_id = :articleId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':articleId' => $articleId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function listAllArticlesByUser() {
        $sql = "SELECT * FROM article WHERE user_id = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':userId' => $_SESSION['userId']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function changeState($article_id, $state){
        $sql = "UPDATE article SET state = :state WHERE article_id = :articleId";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':state' => $state,
            ':articleId' => $article_id
        ]);
    }
    public function listAllArticles()
    {
        $sql = "SELECT * FROM article";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteArticleById($articleId) {
        try {
            // 1️⃣ Smazání recenzí článku
            $sqlReviews = "DELETE FROM review WHERE article_id = :articleId";
            $stmtReviews = $this->conn->prepare($sqlReviews);
            $stmtReviews->execute([':articleId' => $articleId]);

            // 2️⃣ Smazání samotného článku
            $sqlArticle = "DELETE FROM article WHERE article_id = :articleId";
            $stmtArticle = $this->conn->prepare($sqlArticle);
            return $stmtArticle->execute([':articleId' => $articleId]);

        } catch (PDOException $e) {
            error_log("Chyba při mazání článku a recenzí: " . $e->getMessage());
            return false;
        }
    }
    public function remakeArticle($articleId, $articleName, $abstract, $authors){
        $sql = "UPDATE article 
            SET article_name = :articleName, abstract = :abstract, authors = :authors
            WHERE article_id = :articleId";

        try {
            $stmt = $this->conn->prepare($sql);

            // bindování parametrů
            $stmt->bindValue(':articleName', $articleName, PDO::PARAM_STR);
            $stmt->bindValue(':abstract', $abstract, PDO::PARAM_STR);
            $stmt->bindValue(':authors', $authors, PDO::PARAM_STR);
            $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);

            // vykonání dotazu
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Chyba při úpravě článku: " . $e->getMessage());
            return false;
        }
    }
}