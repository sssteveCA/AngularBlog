<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\MyArticles\EditControllerErrors as Ece;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\ArticleAuthorizedController;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;

class EditContoller implements Ece,C,Aace{
    private ?Article $article;
    private ?ArticleAuthorizedController $aac;
    private ?Token $token;
    private static string $logFile = C::FILE_LOG;
    private string $response = "";
    private int $errno = 0;
    private ?string $error = null;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->article = $data['article'];
        $this->token = $data['token'];
        $auth = $this->checkAuthorization();
        if($auth){
            
        }
    }

    public function getResponse(){return $this->response;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER:
                $this->error = Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['article']))throw new \Exception(Ece::NOARTICLEINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Ece::NOTOKENINSTANCE_EXC);
        if(!$data['article'] instanceof Article)throw new \Exception(Ece::INVALIDARTICLETYPE_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Ece::INVALIDTOKENTYPE_EXC);

    }

    //Check if user is authorized to edit the article
    private function checkAuthorization(): bool{
        $authorized = false;
        $this->errno = 0;
        $this->aac = new ArticleAuthorizedController([
            'article' => $this->article,
            'token' => $this->token
        ]);
        $aacErrno = $this->aac->getErrno();
        if($aacErrno == 0){
            $authorized = true;
        }
        else
            $this->errno = Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER;
        return $authorized;
    }
}
?>