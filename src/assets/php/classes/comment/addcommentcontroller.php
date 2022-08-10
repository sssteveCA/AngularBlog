<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
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

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->permalink = $data['permalink'];
        $this->comment_text = $data['comment_text'];
        $this->token_key = $data['token_key'];   
        if($this->setToken()){
            if($this->getArticleInfo()){

            }//if($this->getArticleInfo()){
        }//if($this->setToken()){
    }

    public function getArticle(){return $this->article;}
    public function getAuthorName(){return $this->author_name;}
    public function getComment(){return $this->comment;}
    public function getCommentText(){return $this->comment_text;}
    public function getPermalink(){return $this->permalink;}
    public function getToken(){return $this->token;}
    public function getTokenKey(){return $this->token_key;}
    public function getError(){
        switch($this->errno){
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
        $created = false;
        $this->errno = 0;
        $article_id = $this->article->getId();
        $author_id = $this->article->getId();
        $data = [
            'article' => new ObjectId($article_id),
            'author' => new ObjectId($author_id),
            'comment' => $this->comment->getComment()
        ];
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

    //Set the Token object
    private function setToken(): bool{
        $set = false;
        $this->errno = 0;
        $this->token = new Token();
        $filter = ['token_key' => $this->token_key];
        $get = $this->token->token_get($filter);
        if($get){
            //Check if token is expired
            $this->token->expireControl();
            if($this->token->isExpired()){
                $this->errno = Acce::FROM_TOKEN;
            }
            else
                $this->author_name = $this->token->getUsername();
                $set = true;
        }
        else
            $this->errno = Acce::NOUSERIDFOUND;
        //file_put_contents(CreateController::$logFile,"setToken() result => ".var_export($set,true)."\r\n",FILE_APPEND);
        return $set;
    }

}
?>