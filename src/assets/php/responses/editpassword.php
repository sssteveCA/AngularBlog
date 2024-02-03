<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Account\UpdatePasswordController;
use AngularBlog\Classes\Account\UpdatePasswordView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

/**
 * JSON response for edit account password PUT route
 */
class EditPassword{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
        ];
        $put = $params['put'];
        $headers = $params['headers'];
        return $response;
    }

}

?>