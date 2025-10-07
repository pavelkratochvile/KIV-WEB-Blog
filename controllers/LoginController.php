<?php

namespace controllers;
use model\UserModel;

include("../model/UserModel.php");

class LoginController
{
    private $userModel;
    public function __construct(UserModel $userModel){
        $this->userModel = $userModel;
    }
    public function login($login, $password){
        $user = $this->userModel->getUserWithLogin($login);
        if($user && password_verify($password, $user['password'])){
            $_SESSION['login'] = $login;
            $_SESSION['userRole'] = $user['role_id'];
            $_SESSION['userId'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['surname'] = $user['surname'];
            header("Location: home.php");
            exit();
        }
        else{
            return "Wrong login or password";
        }
    }
}