<?php

namespace AngularBlog\Responses;

/**
 * JSON response for article comments GET route
 */
class ArticleComments{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_MESSAGE => '', C::KEY_DONE => false,'comments' => [],C::KEY_EMPTY => false,'error' => false
        ];
        return $response;
    }
}

?>