<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\AddCommentControllerErrors as Acce;

class AddCommentController implements Acce{
    use ErrorTrait, ResponseTrait;

    private ?Article $article;
    private ?Token $token;
    private ?Comment $comment;

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['article']))throw new \Exception(Acce::NOARTICLEINSTANCE_EXC);
        if(!isset($data['comment']))throw new \Exception(Acce::NOCOMMENTINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Acce::NOTOKENINSTANCE_EXC);
        if(!$data['article'] instanceof Article)throw new \Exception(Acce::INVALIDARTICLETYPE_EXC);
        if(!$data['comment'] instanceof Comment)throw new \Exception(Acce::INVALIDCOMMENTTYPE_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Acce::INVALIDTOKENTYPE_EXC);
    }

}
?>