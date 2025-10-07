<?php

namespace controllers;
use model\UserModel;

include("../model/UserModel.php");
class ArticleAddController
{
    private $userModel;
    public function __construct($userModel){
        $this->userModel = $userModel;
    }
    public function addArticle($article_name, $abstract, $file){
        if($this->userModel->insertArticleToDatabase($article_name, $abstract, $file)){
            return "Article added successfully, it is now waiting to be confirmed.";
        }
        else{
            return "Article not added";
        }
    }
}