<?php

namespace AngularBlog\Classes\Article;

use AngularBlog\Interfaces\Article\ArticleAuthorizedViewErrors as Aave;
use AngularBlog\Classes\Article\ArticleAuthorizedController;

class ArticleAuthorizedView implements Aave{
    private ?ArticleAuthorizedController $aac;
    private string $message = "";
    private bool $done = false; //true if article editing authorization is ok
    
    public function __construct(?ArticleAuthorizedController $aac)
    {
        if(!$aac)throw new \Exception(Aave::NOARTICLEAUTHORIZEDCONTROLLERINSTANCE_EXC);
        $this->aac = $aac;
        $errnoAcc = $this->aac->getErrno();
        if($errnoAcc == 0)
            $this->done = true;
        $this->message = $this->aac->getResponse();
    }

    public function getMessage(){return $this->message;}
    public function isDone(){return $this->done;}
}
?>