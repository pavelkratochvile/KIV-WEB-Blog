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
            header("Location: home.php");
            exit();
        }
        else{
            return "Wrong login or password";
        }
    }
}