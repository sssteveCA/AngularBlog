<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Classes\Myarticles\EditContoller;
use AngularBlog\Interfaces\MyArticles\EditViewErrors as Eve;
use Exception;

class EditView implements Eve{
    private ?EditContoller $ec;
    private string $message = "";
    private bool $done = false; //true if article was edited

    public function __construct(?EditContoller $ec){
        if(!$ec)throw new Exception(Eve::NOEDITCONTROLLERINSTANCE_EXC);
        $this->ec = $ec;
        $errnoEc = $this->ec->getErrno();
        if($errnoEc == 0)
            $this->done = true;
        $this->message = $this->ec->getResponse();
    }

    public function getMessage(){return $this->message;}
    public function isDone(){return $this->done;}
}
?>