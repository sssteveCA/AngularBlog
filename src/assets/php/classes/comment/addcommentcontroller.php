<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\AddCommentControllerErrors as Acce;
use MongoDB\BSON\ObjectId;

class AddCommentController implements Acce{
    use ErrorTrait, ResponseTrait;

    private ?string $author_name; //Username of account that posts the comment
    private ?string $comment_text;
    private ?string $permalink;
    private ?string $token_key;
    private ?Article $article;
    private ?Comment $comment;
    private ?Token $token;
    private static $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        file_put_contents(AddCommentController::$logFile,"AddCommentController constructor => \r\n",FILE_APPEND);
        $this->checkValues($data);
        $this->permalink = $data['permalink'];
        $this->comment_text = $data['comment_text'];
        $this->token_key = $data['token_key'];   
        if($this->setToken()){
            if($this->getArticleInfo()){
                $this->insertComment();     
            }//if($this->getArticleInfo()){
        }//if($this->setToken()){
        $this->setResponse(); 
    }

    public function getArticle(){return $this->article;}
    public function getAuthorName(){return $this->author_name;}
    public function getComment(){return $this->comment;}
    public function getCommentText(){return $this->comment_text;}
    public function getPermalink(){return $this->permalink;}
    public function getToken(){return $this->token;}
    public function getTokenKey(){return $this->token_key;}
    public function getUser(){return $this->user;}
    public function getError(){
        switch($this->errno){
            case Acce::FROM_ARTICLE:
                $this->error = Acce::FROM_ARTICLE_MSG;
                break;
            case Acce::FROM_TOKEN:
                $this->error = Acce::FROM_TOKEN_MSG;
                break;
            case Acce::FROM_COMMENT:
                $this->error = Acce::FROM_COMMENT_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['permalink']))throw new \Exception(Acce::NOARTICLEPERMALINK_EXC);
        if(!isset($data['comment_text']))throw new \Exception(Acce::NOCOMMENT_EXC);
        if(!isset($data['token_key']))throw new \Exception(Acce::NOTOKENKEY_EXC);
    }

    private function insertComment(): bool{
        file_put_contents(AddCommentController::$logFile,"AddCommentController insertComment => \r\n",FILE_APPEND);
        $created = false;
        $this->errno = 0;
        $article_id = $this->article->getId();
        $author_id = $this->token->getUserId();
        $data = [
            'article' => new ObjectId($article_id),
            'author' => new ObjectId($author_id),
            'comment' => $this->getCommentText()
        ];
        file_put_contents(AddCommentController::$logFile,"insertComment data => ".var_export($data,true)."\r\n",FILE_APPEND);
        $this->comment = new Comment($data);
        $insert = $this->comment->comment_create();
        if($insert){
            //Comment inserted in DB
            $created = true;
        }
        else
            $this->errno = Acce::FROM_COMMENT;
        return $created;
    }

    //Get article info from permalink
    private function getArticleInfo(): bool{
        file_put_contents(AddCommentController::$logFile,"AddCommentController getArticleInfo => \r\n",FILE_APPEND);
        $got = false;
        $this->errno = 0;
        $this->article = new Article();
        $filter = ['permalink' => $this->permalink];
        $get_article = $this->article->article_get($filter);
        if($get_article){
            //Article found with given permalink
            $got = true;
        }//if($get_article){
        else
            $this->errno = Acce::FROM_ARTICLE;
        return $got;
    }

    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = "";
                break;
            case Acce::FROM_ARTICLE:
                $this->response = C::COMMENTCREATION_ERROR;
                break;
            case Acce::FROM_COMMENT:
                $this->response = C::COMMENTCREATION_ERROR;
                break;
            case Acce::FROM_TOKEN:
                $errnoT = $this->token->getErrno();
                switch($errnoT){
                    case Te::TOKENEXPIRED:
                        $this->response = Te::TOKENEXPIRED_MSG;
                        break;
                    default:
                        $this->response = C::COMMENTCREATION_ERROR;
                        break;
                }
                break;
            case Acce::NOUSERIDFOUND:
                $this->response = C::LOGIN_NOTLOGGED;
                break;
        }
    }

    //Set the Token object
    private function setToken(): bool{
        file_put_contents(AddCommentController::$logFile,"AddCommentController setToken => \r\n",FILE_APPEND);
        $set = false;
        $this->errno = 0;
        $this->token = new Token();
        $filter = ['token_key' => $this->token_key];
        $get = $this->token->token_get($filter);
        file_put_contents(AddCommentController::$logFile,"AddCommentController setToken get => ".var_export($get,true)."\r\n",FILE_APPEND);
        if($get){
            //Check if token is expired
            $this->token->expireControl();
            if($this->token->isExpired()){
                $this->errno = Acce::FROM_TOKEN;
            }
            else{
                $this->author_name = $this->token->getUsername();
                $set = true;
            }
                
        }
        else
            $this->errno = Acce::NOUSERIDFOUND;
        //file_put_contents(CreateController::$logFile,"setToken() result => ".var_export($set,true)."\r\n",FILE_APPEND);
        return $set;
    }

}
?>