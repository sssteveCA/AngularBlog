<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Interfaces\Article\Comment\CommentAuthorizedControllerErrors as Cace;

//Determine if user is authorized to do operations with specificf comment
class CommentAuthorizedController implements Cace{
    use ErrorTrait;
}
?>