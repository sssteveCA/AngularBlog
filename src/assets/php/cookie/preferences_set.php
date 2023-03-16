<?php
//Create a cookie with the expressed privacy user preferences
require_once("../cors.php");
require_once("../interfaces/constants.php");

use AngularBlog\Interfaces\Constants as C;

$input = file_get_contents("php://input");
$post = json_decode($input,true);

$response = [ C::KEY_DONE => false ];

if(isset($post['option'])){
    if(in_array($post['option'],['accepted','rejected'])){
        setcookie('preference',$post['option'],C::COOKIE_PREFERENCE_TIME,"/");
        http_response_code(200);
        $response[C::KEY_DONE] = true;
    }
    else http_response_code(400);  
}else http_response_code(400);

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>