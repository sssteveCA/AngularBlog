<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Myarticles\EditContoller;
use AngularBlog\Classes\Myarticles\EditView;
use Dotenv\Dotenv;

/**
 * JSON response for edit article PUT route
 */
class EditArticle{

    public static function content(array $params): array{
        $response = [
            C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
        ];
        return $response;
    }
}

?>