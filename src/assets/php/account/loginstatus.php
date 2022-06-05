<?php

require_once("../cors.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/model_errors.php");
require_once("../vendor/autoload.php");
require_once("../classes/model.php");
require_once("../classes/token.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;

//This script check if user is still logged
$response = array(
    'msg' => '',
    'logged' => false
);

$post = file_get_contents('php://input');
$postDecode = json_decode($post,true);
$response['post'] = $postDecode;

if(isset($postDecode['token_key'],$postDecode['username'])){
    $data = [
        'token_key' => $postDecode['token_key'],
        'username' => $postDecode['username']
    ];
    try{
        $token = new Token();
        $get = $token->token_get($data);
        if($get){
            //User logged in server side
            $response['logged'] = true;
        }//if($get){
    }catch(Exception $e){
        file_put_contents(C::FILE_LOG,$e->getMessage()."\r\n",FILE_APPEND);
    }
    
}//if(isset($postDecode['token_key'],$postDecode['username'])){

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>