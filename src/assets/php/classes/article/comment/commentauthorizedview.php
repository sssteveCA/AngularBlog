<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\CommentAuthorizedViewErrors as Cave;

class CommentAuthorizedView implements Cave{
    use ResponseTrait;
}
?>