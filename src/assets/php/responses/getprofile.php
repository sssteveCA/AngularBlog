<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Account\GetUserInfoControllerErrors as Guice;
use AngularBlog\Classes\Account\GetUserInfoController;
use AngularBlog\Classes\Account\GetUserInfoView;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use Dotenv\Dotenv;
use AngularBlog\Interfaces\Constants as C;

/**
 * JSON response for get profile info GET route
 */
class GetProfile{
    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => "", C::KEY_DATA => []
        ];
        return $response;
    }
}
?>