<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Article\Comment\AddControllerErrors as Ace;
use AngularBlog\Interfaces\Article\Comment\AddViewErrors as Ave;
use AngularBlog\Classes\Article\Comment\AddController;
use AngularBlog\Classes\Article\Comment\AddView;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for create comment POST route
 */
class CreateComment{

    public static function content(array $params): array{
        $response = [
           C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
        ];
        $headers = $params['headers'];
        $post = $params['post'];
        return $response;
    }

}

?>