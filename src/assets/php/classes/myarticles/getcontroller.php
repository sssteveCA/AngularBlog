<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\MyArticles\GetControllerErrors as Gce;
use AngularBlog\Classes\Article\ArticleList;
use AngularBlog\Classes\Token;
use MongoDB\BSON\ObjectId;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

//Get the user created article list
class GetController implements Gce{

    use ErrorTrait, ResponseTrait;

    private ?string $token_key;
    private ?ArticleList $articleList;
    private ?Token $token;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        if(!isset($data['token_key']))throw new \Exception(Gce::NOTOKENKEY_EXC);
        $this->token_key = $data['token_key'];
        $this->articleList = new ArticleList();
        if($this->setToken())
            $this->setUserArticles();
        $this->setResponse();
    }

    public function getTokenKey(){return $this->token_key;}
    public function getArticleList():?ArticleList{return $this->articleList;}
    public function getError(){
        switch($this->errno){
            case Gce::NOUSERIDFOUND:
                $this->error = Gce::NOUSERIDFOUND_MSG;
                break;
            case Gce::NOARTICLESFOUND:
                $this->error = Gce::NOARTICLESFOUND_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Set the Token object
    private function setToken(): bool{
        $set = false;
        $this->errno = 0;
        $this->token = new Token();
        $filter = ['token_key' => $this->token_key];
        $get = $this->token->token_get($filter);
        if($get)
            $set = true;
        else
            $this->errno = Gce::NOUSERIDFOUND;
        //file_put_contents(GetController::$logFile,"setToken() result => ".var_export($set,true)."\r\n",FILE_APPEND);
        return $set;
    }

    //Get articles created by specific user
    private function setUserArticles():bool{
        $set = false;
        $this->errno = 0;
        $user_id = $this->token->getUserId();
        //user_id property is not null
        $filter = array('author' => new ObjectId($user_id));
        $articlesGet = $this->articleList->articlelist_get($filter);
        if($articlesGet){
            //Found at least one article created by logged user
            $set = true;
        }
        else 
            $this->errno = Gce::NOARTICLESFOUND;
        //file_put_contents(GetController::$logFile,"setUSerArticles() result => ".var_export($set,true)."\r\n",FILE_APPEND);
        return $set;
    }

    private function setResponse(){
        file_put_contents(GetController::$logFile,var_export($this->errno,true)."\r\n",FILE_APPEND);
        switch($this->errno){
            case 0:
                $this->response = "";
                break;
            case Gce::NOARTICLESFOUND:
                $this->response = Gce::NOARTICLESFOUND_MSG;
                break;
            case Gce::NOUSERIDFOUND:
                $this->response = C::SEARCH_ERROR;
                break;
                break;
            default:
                $this->response = C::ERROR_UNKNOWN;
        }
    }
}
?>