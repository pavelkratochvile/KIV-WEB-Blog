<?php

namespace model;
use PDO;
class UserModel
{
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }
    public function getUserWithLogin($login) {
        $sql = "SELECT * FROM user WHERE login = :login";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insertUserToDatabase($name, $surname, $login, $email, $password) {
        $sql = "INSERT INTO user (name, surname, login, email, password, role_id)VALUES (:name, :surname, :login, :email, :password, :role_id)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':surname' => $surname,
            ':login' => $login,
            ':email' => $email,
            ':password' => $password,
            ':role_id' => 1
        ]);
    }
    public function insertArticleToDatabase($article_name, $abstract, $file)
    {
        $sql = "INSERT INTO article(user_id, article_name, abstract, state,	file)VALUES (:user_id, :article_name, :abstract, :state, :file)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'user_id' => $_SESSION['userId'],
            ':article_name' => $article_name,
            ':abstract' => $abstract,
            ':state' => 2,
            ':file' => $file
        ]);
    }
}