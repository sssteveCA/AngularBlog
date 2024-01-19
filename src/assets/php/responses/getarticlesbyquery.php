<?php

namespace AngularBlog\Responses;

/**
 * JSON response for get articles list by query GET route
 */
class GetArticlesByQuery{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_MESSAGE => ''
        ];
        return $response;
    }
}

?>