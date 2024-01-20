<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\MyArticles\CreateControllerErrors as Cce;
use AngularBlog\Interfaces\MyArticles\CreateViewErrors as Cve;
use AngularBlog\Classes\Myarticles\CreateController;
use AngularBlog\Classes\Myarticles\CreateView;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for create article POST route
 */
class CreateArticle{

    public static function content(array $params): array{
        $response = [ 
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => '' ];
        return $response;
    }

}

?>