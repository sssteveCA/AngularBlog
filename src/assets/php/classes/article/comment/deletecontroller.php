<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\DeleteControllerErrors as Dce;

class DeleteController implements Dce{
    use ErrorTrait, ResponseTrait;

    private ?Comment $comment;
    private ?Token $token;

    public function __construct(array $data)
    {
        
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['comment']))throw new \Exception(Dce::NOCOMMENTINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Dce::NOTOKENINSTANCE_EXC);
        if(!$data['comment'] instanceof Comment)throw new \Exception(Dce::INVALIDCOMMENTTYPE_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Dce::INVALIDTOKENTYPE_EXC);
    }


}
?>