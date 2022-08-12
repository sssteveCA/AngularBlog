<?php

require_once("../../cors.php");
require_once("../../interfaces/constants.php");
require_once("../../interfaces/model_errors.php");
require_once("../../interfaces/token_errors.php");
require_once("../../interfaces/article/comment/comment_errors.php");
require_once("../../interfaces/article/comment/commentauthorizedcontroller_errors.php");
require_once("../../interfaces/article/comment/commentauthorizedview_errors.php");
require_once("../../vendor/autoload.php");
require_once("../../traits/error.trait.php");
require_once("../../traits/message.trait.php");
require_once("../../traits/response.trait.php");
require_once("../../classes/model.php");
require_once("../../classes/token.php");
require_once("../../classes/article/comment/comment.php");
require_once("../../classes/article/comment/commentauthorizedcontroller.php");
require_once("../../classes/article/comment/commentauthorizedview.php");

use AngularBlog\Interfaces\Constants as C;

$input = file_get_contents("php://input");
$delete = json_decode($input,true);

$response = [
    'done' => false,
    'expired' => false,
    'msg' => '',
    'delete' => $delete
];

if(isset($delete['token_key'],$delete['comment_id']) && $delete['token_key'] != '' && $delete['comment_id'] != ''){

}//if(isset($delete['token_key'],$delete['comment_id']) && $delete['token_key'] != '' && $delete['comment_id'] != ''){
else
    //$response['msg'] = C::FILL_ALL_FIELDS;
    $response['msg'] = C::COMMENTDELETE_ERROR;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>