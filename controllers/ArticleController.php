<?php

namespace controllers;
use model\ArticleModel;
include("../model/ArticleModel.php");
class ArticleController
{
    private $articleModel;
    public function __construct($articleModel){
        $this->articleModel = $articleModel;
    }
    public function addArticle($articleName, $abstract, $filePathForDB, $authors){
        if($this->articleModel->insertArticleToDatabase($articleName, $abstract, $filePathForDB, $authors)){
            return "Article added successfully, it is now waiting to be confirmed.";
        }
        else{
            return "Article not added";
        }
    }
    public function listAllArticlesByState($state){
        return $this->articleModel->listAllArticlesByState($state);
    }
    public function getArticleById($articleId)
    {
        return $this->articleModel->getArticleById($articleId);
    }
    public function listAllArticlesByUser(){
        return $this->articleModel->listAllArticlesByUser();
    }

    public function changeState($article_id, $state){
        return $this->articleModel->changeState($article_id, $state);
    }
    public function listAllArticles()
    {
        return $this->articleModel->listAllArticles();
    }
    public function deleteArticleById($articleId){
        $this->articleModel->deleteArticleById($articleId);
    }
    public function remakeArticle($articleId, $articleName, $abstract, $authors){
        $this->articleModel->remakeArticle($articleId, $articleName, $abstract, $authors);
    }
}