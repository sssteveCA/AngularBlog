<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;

/**
 * JSON response with custom error message
 */
class ErrorMessage{

    public static function content(array $params): array{
        $response = [ C::KEY_CODE => 400, C::KEY_MESSAGE => "" ];
        $isset = isset($params[C::KEY_CODE],$params[C::KEY_MESSAGE]);
        $statusCode = (filter_var($params[C::KEY_CODE], FILTER_VALIDATE_INT, array('options' => array('min_range' => 100, 'max_range' => 599))) !== false);
        $notEmpty = ($params[C::KEY_MESSAGE] != '');
        if($isset && $statusCode && $notEmpty)
            $response = [ C::KEY_CODE => $params[C::KEY_CODE], C::KEY_MESSAGE => $params[C::KEY_MESSAGE]];
        return $response;
    }
}

?>