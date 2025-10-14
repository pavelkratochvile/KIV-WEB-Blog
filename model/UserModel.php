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
    public function listAllReviewers()
    {
        $sql = "SELECT * FROM user WHERE role_id = 2";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getNameById($user_id)
    {
        $sql = "SELECT * FROM user WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllUsers(){
        $sql = "SELECT * FROM user";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateRoles($user_id, $role_id){
        $stmt = $this->conn->prepare("UPDATE user SET role_id = :role WHERE user_id = :id");
        $stmt->bindParam(':role', $role_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}