<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Classes\Myarticles\GetController;
use AngularBlog\Interfaces\MyArticles\GetViewErrors as Gve;
use AngularBlog\Interfaces\MyArticles\GetControllerErrors as Gce;
use AngularBlog\Traits\MessageTrait;

//Presentation of MyArticles GetController data
class GetView implements Gve{

    use MessageTrait;

    private ?GetController $gc;
    private bool $foundArticles = false;
    private bool $emptyList = false; //True if the user has no articles to show

    public function __construct(?GetController $gc)
    {
        if(!$gc)throw new \Exception(Gve::NOGETCONTROLLERINSTANCE_EXC);
        $this->gc = $gc;
        $this->response_code = $this->gc->getResponseCode();
        $errnoGc = $this->gc->getErrno();
        if($errnoGc == 0)
            $this->foundArticles = true;
        else
            $this->message = $this->gc->getResponse();  
    }

    public function articlesFound(){return $this->foundArticles;}
}
?>