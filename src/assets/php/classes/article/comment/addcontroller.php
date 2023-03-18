<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Classes\Action\Action;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\AddControllerErrors as Ace;
use MongoDB\BSON\ObjectId;

class AddController implements Ace{
    use ErrorTrait, ResponseTrait;

    private ?string $author_name; //Username of account that posts the comment
    private ?string $comment_text;
    private ?string $permalink;
    private ?string $token_key;
    private ?Article $article;
    private ?Comment $comment;
    private ?Token $token;
    private ?Action $action;
    private static $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->permalink = $data['permalink'];
        $this->comment_text = $data['comment_text'];
        $this->token_key = $data['token_key'];   
        if($this->setToken()){
            if($this->getArticleInfo()){
                if($this->insertComment())
                    $this->addAction();     
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
    public function getError(){
        switch($this->errno){
            case Ace::FROM_ARTICLE:
                $this->error = Ace::FROM_ARTICLE_MSG;
                break;
            case Ace::FROM_TOKEN:
                $this->error = Ace::FROM_TOKEN_MSG;
                break;
            case Ace::FROM_COMMENT:
                $this->error = Ace::FROM_COMMENT_MSG;
                break;
            case Ace::FROM_ACTION:
                $this->error = Ace::FROM_ACTION_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['permalink']))throw new \Exception(Ace::NOARTICLEPERMALINK_EXC);
        if(!isset($data['comment_text']))throw new \Exception(Ace::NOCOMMENT_EXC);
        if(!isset($data['token_key']))throw new \Exception(Ace::NOTOKENKEY_EXC);
    }

    /**
     * Add an action to rememeber the done operation
     */
    private function addAction(): bool{
        $this->action = new Action([
            'user_id' => $this->token->getUserId(),
            'title' => 'Creazione commento',
            'description' => <<<HTML
Hai creato un nuovo commento nell'articolo "{$this->article->getTitle()}" con testo "{$this->comment_text}"
HTML
        ]);
        $insert = $this->action->action_create();
        if(!$insert) $this->errno = Ace::FROM_ACTION;
        return true;
    }

    private function insertComment(): bool{
        $created = false;
        $this->errno = 0;
        $article_id = $this->article->getId();
        $author_id = $this->token->getUserId();
        $data = [
            'article' => new ObjectId($article_id),
            'author' => new ObjectId($author_id),
            'comment' => $this->comment_text
        ];
        //file_put_contents(AddCommentController::$logFile,"insertComment data => ".var_export($data,true)."\r\n",FILE_APPEND);
        $this->comment = new Comment($data);
        $insert = $this->comment->comment_create();
        if($insert){
            //Comment inserted in DB
            $created = true;
        }
        else
            $this->errno = Ace::FROM_COMMENT;
        return $created;
    }

    /**
     * Get article info from permalink
     **/
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
            $this->errno = Ace::FROM_ARTICLE;
        return $got;
    }

    private function setResponse(){
        switch($this->errno){
            case 0:
            case Ace::FROM_ACTION:
                $this->response_code = 200;
                $this->response = "";
                break;
            case Ace::NOUSERIDFOUND:
                $this->response_code = 401;
                $this->response = C::LOGIN_NOTLOGGED;
                break;
            case Ace::FROM_TOKEN:
                $errnoT = $this->token->getErrno();
                switch($errnoT){
                    case Te::TOKENEXPIRED:
                        $this->response_code = 401;
                        $this->response = Te::TOKENEXPIRED_MSG;
                        break;
                    default:
                        $this->response_code = 500;
                        $this->response = C::COMMENTCREATION_ERROR;
                        break;
                }
                break;
            case Ace::FROM_ARTICLE:
            case Ace::FROM_COMMENT:
            default:
                $this->response_code = 500;
                $this->response = C::COMMENTCREATION_ERROR;
                break;
        }
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
                $this->errno = Ace::FROM_TOKEN;
            }
            else{
                $this->author_name = $this->token->getUsername();
                $set = true;
            }         
        }
        else
            $this->errno = Ace::NOUSERIDFOUND;
        //file_put_contents(CreateController::$logFile,"setToken() result => ".var_export($set,true)."\r\n",FILE_APPEND);
        return $set;
    }

}
?>