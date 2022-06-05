<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\MyArticles\GetControllerErrors as Gce;
use AngularBlog\Classes\Article\ArticleList;
use AngularBlog\Classes\Token;
use MongoDB\BSON\ObjectId;

//Get the user created article list
class GetController implements Gce,C{
    private ?string $token_key;
    private ?ArticleList $articleList;
    private ?Token $token;
    private string $response = "";
    private int $errno = 0;
    private ?string $error = null;

    public function __construct(?Token $token)
    {
        if(!$token)throw new \Exception(Gce::NOTOKENINSTANCE_EXC);
        $this->token = $token;
        $this->articleList = new ArticleList();
        $this->setUserArticles();
        $this->setResponse();
    }

    public function getTokenKey(){return $this->token_key;}
    public function getArticleList(){return $this->articleList;}
    public function getResponse(){return $this->response;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Gce::USERIDISNULL:
                $this->error = Gce::USERIDISNULL_MSG;
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

    //Get articles created by specific user
    private function setUserArticles():bool{
        $set = false;
        $this->errno = 0;
        $user_id = $this->token->getUserId();
        if(isset($user_id)){
            //user_id property is not null
            $filter = array('user_id', new ObjectId($user_id));
            $articlesGet = $this->articleList->articlelist_get($filter);
            if($articlesGet){
                //Found at least one article created by logged user
                $set = true;
            }
        }//if(isset($user_id)){
        else 
            $this->errno = Gce::USERIDISNULL;
        return $set;
    }

    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = "";
                break;
            case Gce::NOARTICLESFOUND:
                $this->response = $this->getError();
            case Gce::USERIDISNULL:
                $this->response = C::SEARCH_ERROR;
                break;
                break;
            default:
                $this->response = C::ERROR_UNKNOWN;
        }
    }
}
?>