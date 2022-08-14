<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\EditControllerErrors as Ece;
use AngularBlog\Classes\Article\Comment\CommentAuthorizedController;

class EditController implements Ece{
    use ErrorTrait, ResponseTrait;

    private ?Comment $article;
    private ?Comment $cac_comment; //Comment used by CommentAuthorizationController class
    private ?CommentAuthorizedController $aac;
    private ?Token $token;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['comment']))throw new \Exception(Ece::NOACOMMENTINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Ece::NOTOKENINSTANCE_EXC);
        if(!$data['comment'] instanceof Comment)throw new \Exception(Ece::INVALIDCOMMENTTYPE_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Ece::INVALIDTOKENTYPE_EXC);
    }
}

?>