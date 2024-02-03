<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Account\UpdateNamesController;
use AngularBlog\Classes\Account\UpdateNamesView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for edit names PUT route
 */
class EditNames{

    public static function content(array $paras): array{
        $response = [
            C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
        ];
        $put = $params['put'];
        $headers = $params['headers'];
        return $response;
    }
}
?>