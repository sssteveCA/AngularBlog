<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Myarticles\GetController;
use AngularBlog\Interfaces\MyArticles\GetControllerErrors as Gce;
use AngularBlog\Classes\Myarticles\GetView;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for get user articles list GET route
 */
class GetUserArticles{
    
    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EMPTY => false, C::KEY_MESSAGE => '', 'articles' => []
        ];
        return $response;
    }
}

?>