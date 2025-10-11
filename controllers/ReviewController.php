<?php

namespace controllers;
use model\ReviewModel;
include("../model/ReviewModel.php");
class ReviewController
{
    private $reviewModel;
    public function __construct($reviewModel){
        $this->reviewModel = $reviewModel;
    }
    public function listReviews(){
        return $this->reviewModel->listAllReviews();
    }
    public function addReview($article_id, $user_id){
        $this->reviewModel->addReview($article_id, $user_id);
    }
    public function articleReviewersCount($article_id)
    {
        return $this->reviewModel->articleReviewersCount($article_id);
    }
    public function setReview($review_id, $total, $language, $content, $novelty, $comment){
        if($total <= 5 && $novelty <= 5 && $content <= 5 && $language <= 5){
            $this->reviewModel->setReview($review_id, $total, $language, $content, $novelty, $comment);
        }
        else{
            echo "Hodnocení musí být od 1 do 5";
        }
    }
    public function listAllArticleReviews($article_id)
    {
        return $this->reviewModel->listAllArticleReviews($article_id);
    }
}