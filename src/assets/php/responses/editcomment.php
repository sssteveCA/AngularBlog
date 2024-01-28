<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Article\Comment\EditController;
use AngularBlog\Classes\Article\Comment\EditView;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

/**
 * JSON response for edit comment PUT route
 */
class EditComment{

    public static function content(array $params): array{
        $response = [
            C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
        ];
        return $response;
    }

}

?>