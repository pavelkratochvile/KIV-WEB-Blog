<?php

namespace controllers;
use model\UserModel;
include("../model/UserModel.php");
class UserController
{
    private $userModel;
    public function __construct($userModel){
        $this->userModel = $userModel;
    }
    public function listReviewers(){
        return $this->userModel->listAllReviewers();
    }

    public function login($login, $password){
        $user = $this->userModel->getUserWithLogin($login);
        if($user && password_verify($password, $user['password'])){
            $_SESSION['login'] = $login;
            $_SESSION['userRole'] = $user['role_id'];
            $_SESSION['userId'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['surname'] = $user['surname'];

            if($_SESSION['userRole'] == 1){
                header("Location: user-home-page.php");
            }
            elseif ($_SESSION['userRole'] == 2) {
                header("Location: review-home-page.php");
            }
            elseif ($_SESSION['userRole'] >= 3) {
                header("Location: admin-reviews.php");
            }
            exit();
        }
        else{
            return "Špatné jméno nebo heslo.";
        }
    }
    public function register($name, $surname, $login, $email, $password){
        if (empty($name) || empty($surname) || empty($login) || empty($email) || empty($password)) {
            return "Vyplň všechna pole!";
        }
        else{
            $user = $this->userModel->getUserWithLogin($login);
            if(!$user){
                if($this->userModel->insertUserToDatabase($name, $surname, $login, $email, $password)){
                    return "Registrace proběhla úspěšně!";
                }
                else{
                    return "Chyba v registraci!";
                }
            }
            else{
                return "Tento login je již registrovaný!";
            }
        }
    }
    public function getNameById($user_id){
        return $this->userModel->getNameById($user_id);
    }
    public function getAllUsers()
    {
        return $this->userModel->getAllUsers();
    }
    public function updateRoles($user_id, $role_id)
    {
        $this->userModel->updateRoles($user_id, $role_id);
    }
}