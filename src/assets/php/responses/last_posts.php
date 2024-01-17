<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;

/**
 * JSON response for last post GET route
 */
class LastPosts{

    public function content(array $params): array{
        $response = [
            C::KEY_DATA => [], C::KEY_DONE => false, C::KEY_EMPTY => false, C::KEY_MESSAGE => ""
        ];
        return $response;
    }
}

?>