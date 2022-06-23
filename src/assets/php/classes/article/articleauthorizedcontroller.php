<?php

namespace AngularBlog\Classes\Article;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use AngularBlog\Classes\Token;
use MongoDB\BSON\ObjectId;

//Check if user is authorized to do write operation with a certain article
class ArticleAuthorizedController implements Aace,C{
    private ?Article $article;
    private ?Token $token;
    private bool $authorized = false;
    private string $response = "";
    private int $errno = 0;
    private ?string $error = null;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data){
        file_put_contents(ArticleAuthorizedController::$logFile,"ArticleAuthorizedController construct\r\n",FILE_APPEND);
        $this->checkVariables($data);
        $this->article = $data['article'];
        $this->token = $data['token'];
        $tokenOk = $this->getTokenByKey();
        if($tokenOk){
            //Token exists
            $articleOk = $this->getArticleById();
            if($articleOk){
                //Article exists
                $authOk = $this->isUserAuthorizedCheck();
            }
        }
        file_put_contents(ArticleAuthorizedController::$logFile,var_export($this->errno,true)."\r\n",FILE_APPEND);
        $this->setResponse();
    }

    public function getArticle(){return $this->article;}
    public function getToken(){return $this->token;}
    public function getResponse(){return $this->response;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Aace::ARTICLE_NOTFOUND:
                $this->error = Aace::ARTICLE_NOTFOUND_MSG;
                break;
            case Aace::TOKEN_NOTFOUND:
                $this->error = Aace::TOKEN_NOTFOUND_MSG;
                break;
            case Aace::FORBIDDEN:
                $this->error = Aace::FORBIDDEN_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    public function isAuthorized(){return $this->authorized;}

    //Cntrol if values inside array are Article,User,Token types
    private function checkVariables(array $data){
        if(!isset($data['article']))throw new \Exception(Aace::NOARTICLEINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Aace::NOTOKENINSTANCE_EXC);
        if(!$data['article'] instanceof Article)throw new \Exception(Aace::ARTICLETYPEMISMATCH_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Aace::TOKENTYPEMISMATCH_EXC);
    }

    //Get token by token key
    private function getTokenByKey(): bool{
        $got = false;
        $this->errno = 0;
        $key = $this->token->getTokenKey();
        $data = ['token_key' => $key];
        $token_got = $this->token->token_get($data);
        if($token_got){
            $got = true;
        }
        else
            $this->errno = Aace::TOKEN_NOTFOUND;
        return $got;
    }

    //Get article info by id
    private function getArticleById(): bool{
        $got = false;
        $this->errno = 0;
        $article_id = $this->article->getId();
        file_put_contents(ArticleAuthorizedController::$logFile,"getArticle article id => ".var_export($article_id,true)."\r\n",FILE_APPEND);
        $data = ['_id' => new ObjectId($article_id)];
        $article_got = $this->article->article_get($data);
        if($article_got){
            $got = true;
        }
        else
            $this->errno = Aace::ARTICLE_NOTFOUND;
        return $got;
    }

    //Check if user is authorized to edit this article
    private function isUserAuthorizedCheck(): bool{
        $this->authorized = false;
        $this->errno = 0;
        $user_id = $this->token->getUserId();
        $article_author = $this->article->getAuthor();
        if($user_id == $article_author){
            //User is the owner of the article
            $this->authorized = true;
        }
        else
            $this->errno = Aace::FORBIDDEN;
        return $this->authorized;
    }

    //Set the response to send to the view
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = "OK";
                break;
            case Aace::TOKEN_NOTFOUND:
            case Aace::FORBIDDEN:
                $this->response = Aace::FORBIDDEN_MSG;
                break;
            case Aace::ARTICLE_NOTFOUND:
                $this->response = Aace::ARTICLE_NOTFOUND_MSG;
                break;
            default:
                $this->response = C::ARTICLEEDITING_ERROR;
                break;
        }
    }




}
?>