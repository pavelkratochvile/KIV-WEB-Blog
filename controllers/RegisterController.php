<?php

namespace controllers;
use model\UserModel;

include("../model/UserModel.php");
class RegisterController
{
    private $userModel;
    public function __construct(UserModel $userModel){
        $this->userModel = $userModel;
    }
    public function register($name, $surname, $login, $email, $password){
        if (empty($name) || empty($surname) || empty($login) || empty($email) || empty($_POST['password-reg'])) {
            return "Vyplň všechna pole!";
        }
        else{
            $user = $this->userModel->getUserWithLogin($login);
            if(!$user){
                if($this->userModel->insertUserToDatabase($name, $surname, $login, $email, $password)){
                    return "Register success!";
                }
                else{
                    return "Register error!";
                }
            }
            else{
                return "Login already exists!";
            }
        }

    }
}