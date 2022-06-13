<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;

class CreateController implements C{
    private ?string $token_key;
    private ?Token $token;
    private ?Article $article;
    private string $response = "";
    private int $errno = 0;
    private ?string $error = null;
    private static string $logFile = C::FILE_LOG;

    public function getTokenKey(){return $this->token_key;}
    public function getArticle(){return $this->article;}
    public function getResponse(){return $this->response;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            default:
                $this->error = null;
        }
        return $this->error;
    }
}
?>