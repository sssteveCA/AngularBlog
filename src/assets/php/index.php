<?php

require_once('interfaces/article/articlelist_errors.php');
require_once('../../../vendor/autoload.php');

use AngularBlog\Responses\Activate;
use AngularBlog\Responses\LastPosts;
use AngularBlog\Responses\Login;
use AngularBlog\Responses\Register;
use AngularBlog\Interfaces\Constants as C;

//echo json_encode($_SERVER,JSON_PRETTY_PRINT);

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$prefix = "/api/v1";

if($method == "GET"){
    $prefixSlashes = str_replace('/','\/',$prefix);
    if(preg_match("/^{$prefixSlashes}\/activate\/([0-9a-zA-Z]{64})/",$uri,$matches)){
        $params = [ 'get' => ['emailVerif' => $matches[1]]];
        $response = Activate::content($params);
        http_response_code($response[C::KEY_CODE]);
        echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    else if($uri = $prefix."/lastposts"){
        $response = LastPosts::content([]);
        http_response_code($response[C::KEY_CODE]);
        echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}
else if($method == "POST"){
    $input = file_get_contents("php://input");
    $post = json_decode($input, true);
    $params = [ 'post' => $post ];
    if($uri == $prefix."/login"){
        $response = Login::content($params);
        http_response_code($response[C::KEY_CODE]);
        echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    else if($uri == $prefix."/register"){
        $response = Register::content($params);
        http_response_code($response[C::KEY_CODE]);
        echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}

?>