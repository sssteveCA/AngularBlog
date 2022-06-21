<?php

namespace AngularBlog\Classes\Article;

use AngularBlog\Classes\Article\Article;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use AngularBlog\Classes\User;
use AngularBlog\Classes\Token;
use MongoDB\BSON\ObjectId;

//Check if user is authorized to do write operation with a certain article
class ArticleAuthorizedController implements Aace{
    private ?Article $article;
    private ?User $user;
    private ?Token $token;
    private bool $authorized = false;
    private int $errno = 0;
    private ?string $error = null;

    public function __construct(array $data){
        $this->checkVariables($data);
        $tokenOk = $this->getTokenByKey();
        if($tokenOk){
            //Token exists
            $articleOk = $this->getArticle();
            if($articleOk){
                //Article exists
            }
        }
    }

    public function isAuthorized(){return $this->authorized;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Aace::ARTICLE_NOTFOUND:
                $this->error = Aace::ARTICLE_NOTFOUND_MSG;
                break;
            case Aace::USER_NOTFOUND:
                $this->error = Aace::USER_NOTFOUND_MSG;
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
        $data = ['token_key' => new ObjectId($key)];
        $token_got = $this->token->token_get($data);
        if($token_got){
            $got = true;
        }
        else
            $this->errno = Aace::TOKEN_NOTFOUND;
        return $got;
    }

    //Get article info by id
    private function getArticle(): bool{
        $got = false;
        $this->errno = 0;
        $user_id = $this->token->getUserId();
        $article_id = $this->article->getId();
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


}
?>