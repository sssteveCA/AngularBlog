<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Traits\MessageTrait;
use AngularBlog\Interfaces\Article\Comment\AddViewErrors as Ave;

class AddView implements Ave{
    use MessageTrait;

    private ?AddController $acc;

    public function __construct(?AddController $acc){
        if(!$acc)throw new \Exception(Ave::NOADDCOMMENTCONTROLLERINSTANCE_EXC);
        $this->acc = $acc;
        $errnoAcc = $this->acc->getErrno();
        if($errnoAcc == 0)
            $this->done = true;
        $this->message = $this->acc->getResponse();
    }

    public function getController(){return $this->acc;}

}
?>