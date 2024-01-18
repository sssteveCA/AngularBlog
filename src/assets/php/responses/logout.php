<?php

namespace AngularBlog\Responses;

/**
 * JSON response for account logout GET route
 */
class Logout{

    public static function content(array $params): array{
        $response = [ C::KEY_MESSAGE => '',C::KEY_DONE => false ];
        return $response;
    }
}

?>