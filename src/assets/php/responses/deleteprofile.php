<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Account\DeleteAccountController;
use AngularBlog\Classes\Account\DeleteAccountView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for delete profile POST route
 */
class DeleteProfile{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
        ];
        $post = $params['post'];
        $headers = $params['headers'];
        return $response;
    }

}

?>