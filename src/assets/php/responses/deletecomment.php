<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Article\Comment\DeleteController;
use AngularBlog\Classes\Article\Comment\DeleteView;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Comment\Comment;
use Dotenv\Dotenv;

/**
 * JSON response for delete comment DELETE route
 */
class DeleteComment{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
        ];
        return $response;
    }
}

?>