<?php

namespace AngularBlog\Classes\Article;

use AngularBlog\Interfaces\Article\ArticleAuthorizedViewErrors as Aave;
use AngularBlog\Classes\Article\ArticleAuthorizedController;
use AngularBlog\Traits\MessageTrait;

class ArticleAuthorizedView implements Aave{

    use MessageTrait;

    private ?ArticleAuthorizedController $aac;

    public function __construct(?ArticleAuthorizedController $aac)
    {
        if(!$aac)throw new \Exception(Aave::NOARTICLEAUTHORIZEDCONTROLLERINSTANCE_EXC);
        $this->aac = $aac;
        $errnoAcc = $this->aac->getErrno();
        if($errnoAcc == 0)
            $this->done = true;
        $this->message = $this->aac->getResponse();
    }

    public function getController(){return $this->aac;}
}
?>