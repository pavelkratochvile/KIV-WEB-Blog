<?php

namespace model;
use PDO;
class ArticleModel
{
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }
    public function insertArticleToDatabase($article_name, $abstract, $file, $authors)
    {
        $sql = "INSERT INTO article(user_id, article_name, abstract, state,	file, authors)VALUES (:user_id, :article_name, :abstract, :state, :file, :authors)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':user_id' => $_SESSION['userId'],
            ':article_name' => $article_name,
            ':abstract' => $abstract,
            ':state' => 2,
            ':file' => $file,
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
}