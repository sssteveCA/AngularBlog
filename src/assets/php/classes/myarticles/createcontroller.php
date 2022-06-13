<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\MyArticles\CreateControllerErrors as Cce;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;

class CreateController implements C,Cce{
    private ?string $token_key;
    private array $article_data = array();
    private ?Token $token;
    private ?Article $article;
    private string $response = "";
    private int $errno = 0;
    private ?string $error = null;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        if(!isset($data['token_key']))throw new \Exception(Cce::NOTOKENKEY_EXC);
        if(!isset($data['article']))throw new \Exception(Cce::NOARTICLEDATA_EXC);
        $this->token_key = $data['token_key'];
        $this->article_data = $data['article'];
        //Check if user is logged
        if($this->setToken()){
            if($this->createArticle()){
                
            }
        }
    }

    public function getTokenKey(){return $this->token_key;}
    public function getArticle(){return $this->article;}
    public function getResponse(){return $this->response;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Cce::NOUSERIDFOUND:
                $this->error = Cce::NOUSERIDFOUND_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    private function createArticle(): bool{
        $created = false;
        $this->errno = 0;
        $isset = isset($this->article_data['title'],$this->article_data['introtext'],$this->article_data['content'],$this->article_data['permalink'],$this->article_data['categories'],$this->article_data['tags']);
        $not_blank = ($this->article_data['title'] != '' && $this->article_data['introtext'] != '' && $this->article_data['content'] != '' && $this->article_data['permalink'] != '');
        if($isset && $not_blank){
            $this->article = new Article($this->article_data);
            $created = true;
        }
        else
            $this->errno = Cce::INVALIDARTICLEDATA;
        return $created;
    }

    //Insert Article in DB
    private function insertArticle(): bool{
        $inserted = false;
        $this->errno = 0;
        return $inserted;
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
            $this->errno = Cce::NOUSERIDFOUND;
        //file_put_contents(CreateController::$logFile,"setToken() result => ".var_export($set,true)."\r\n",FILE_APPEND);
        return $set;
    }

}
?>