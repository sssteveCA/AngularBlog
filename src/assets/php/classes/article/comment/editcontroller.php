<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\EditControllerErrors as Ece;

class EditController implements Ece{
    use ErrorTrait, ResponseTrait;
}

?>