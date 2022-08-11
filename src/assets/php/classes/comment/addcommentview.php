<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Traits\MessageTrait;
use AngularBlog\Interfaces\Article\Comment\AddCommentViewErrors as Acve;

class AddCommentView implements Acve{
    use MessageTrait;

    private ?AddCommentController $acc;

    public function __construct(?AddCommentController $acc){
        if(!$acc)throw new \Exception(Acve::NOADDCOMMENTCONTROLLERINSTANCE_EXC);
        $this->acc = $acc;
        $errnoAcc = $this->acc->getErrno();
        if($errnoAcc)
            $this->done = true;
        $this->message = $this->acc->getResponse();
    }

}
?>