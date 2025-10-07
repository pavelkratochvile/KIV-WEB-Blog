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
    public function insertToDatabase($name, $surname, $login, $email, $password) {
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
}