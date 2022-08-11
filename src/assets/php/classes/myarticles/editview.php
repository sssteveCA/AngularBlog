<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Classes\Myarticles\EditContoller;
use AngularBlog\Interfaces\MyArticles\EditViewErrors as Eve;
use AngularBlog\Traits\MessageTrait;
use Exception;

class EditView implements Eve{

    use MessageTrait;

    private ?EditContoller $ec;

    public function __construct(?EditContoller $ec){
        if(!$ec)throw new Exception(Eve::NOEDITCONTROLLERINSTANCE_EXC);
        $this->ec = $ec;
        $errnoEc = $this->ec->getErrno();
        if($errnoEc == 0)
            $this->done = true;
        $this->message = $this->ec->getResponse();
    }

    public function getController(){return $this->ec;}
}
?>