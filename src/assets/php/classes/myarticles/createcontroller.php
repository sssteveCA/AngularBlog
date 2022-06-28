<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\MyArticles\CreateControllerErrors as Cce;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;

class CreateController implements Cce{
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
                if($this->uniquePermalinkVal())
                    $this->insertArticle();
            }
        }
        $this->setResponse();
    }

    public function getToken(){return $this->token;}
    public function getTokenKey(){return $this->token_key;}
    public function getArticle(){return $this->article;}
    public function getResponse(){return $this->response;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Cce::NOUSERIDFOUND:
                $this->error = Cce::NOUSERIDFOUND_MSG;
                break;
            case Cce::INVALIDARTICLEDATA:
                $this->error = Cce::INVALIDARTICLEDATA_MSG;
                break;
            case Cce::FROMARTICLE:
                $this->error = Cce::FROMARTICLE_MSG;
                break;
            case Cce::DUPLICATEDPERMALINK:
                $this->error = Cce::DUPLICATEDPERMALINK_MSG;
                break;
            case Cce::FROM_TOKEN:
                $this->error = Cce::FROM_TOKEN_MSG;
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
            $this->article_data['author'] = $this->token->getUserId();
            $this->article_data['categories'] = explode(",",$this->article_data['categories']);
            $this->article_data['tags'] = explode(",",$this->article_data['tags']);
            $this->article = new Article($this->article_data);
            $created = true;
        }//if($isset && $not_blank){
        else
            $this->errno = Cce::INVALIDARTICLEDATA;
        return $created;
    }

    //Insert Article in DB
    private function insertArticle(): bool{
        $inserted = false;
        $this->errno = 0;
        $inserted_db = $this->article->article_create();
        if($inserted_db){
            //Article inserted successfully
            $inserted = true;
        }
        else
            $this->errno = Cce::FROMARTICLE;
        return $inserted;
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
                $this->errno = Cce::FROM_TOKEN;
            }
            else
                $set = true;
        }
        else
            $this->errno = Cce::NOUSERIDFOUND;
        //file_put_contents(CreateController::$logFile,"setToken() result => ".var_export($set,true)."\r\n",FILE_APPEND);
        return $set;
    }

    //Set the response to send to the view
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = "L'articolo è stato inserito con successo";
                break;
            case Cce::NOUSERIDFOUND:
                $this->response = "Errore durante la creazione dell'articolo. Prova a rieseguire il login e ritenta";
                break;
            case Cce::INVALIDARTICLEDATA:
            case Cce::DUPLICATEDPERMALINK:
                $this->response = $this->getError();
                break;
            case Cce::FROMARTICLE:
                $this->response = C::ARTICLECREATION_ERROR;
                break;
            case Cce::FROM_TOKEN:
                $errnoT = $this->token->getErrno();
                switch($errnoT){
                    case Te::TOKENEXPIRED:
                        $this->response = Te::TOKENEXPIRED_MSG;
                        break;
                    default:
                        $this->response = C::ARTICLECREATION_ERROR;
                        break;
                }
                break;
            default:
                $this->response = C::ARTICLECREATION_ERROR;
                break;
        }
    }


    //Check if given permalink already exists
    private function uniquePermalinkVal(): bool{
        $unique = false;
        $this->errno = 0;
        $filter = ['permalink' => $this->article->getPermalink()];
        $got = $this->article->article_get($filter);
        if(!$got){
            //Permalink with given value found, can use this
            $unique = true;
        }
        else
            $this->errno = Cce::DUPLICATEDPERMALINK;
        return $unique;
    }

}
?>