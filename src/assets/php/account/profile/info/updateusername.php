<?php

require_once("../../../cors.php");
require_once("../../../../../../config.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/user.php");
require_once("../../../classes/account/editusernamecontroller.php");
require_once("../../../classes/account/editusernameview.php");

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;

$response = [
    "done" => false, "expired" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);

if(isset($update["token_key"],$update["new_username"])){
    $token_data = [ "token_key" => $update["token_key"] ];
    $user_data = ["username" => $update["username"]]; 
    try{
        $token = new Token($token_data);
        $user = new User($user_data);
    }catch(Exception $e){

    }
}//if(isset($update["token_key"],$update["new_username"])){
else{
    $response["msg"] = C::FILL_ALL_FIELDS;
}

echo json_encode($response);
?>