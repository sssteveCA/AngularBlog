<?php

require_once("../cors.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/token_errors.php");
require_once("../vendor/autoload.php");
require_once("../traits/error.trait.php");
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
            $token->expireControl();
            if(!$token->isExpired())
                $response['logged'] = true;
            else{
                $response['msg'] = $token->getError();
                //$token->token_delete(['token_key' => $token->getTokenKey(), 'username' => $token->getUsername()]);
            }
                
        }//if($get){
    }catch(Exception $e){
        file_put_contents(C::FILE_LOG,$e->getMessage()."\r\n",FILE_APPEND);
    }
    
}//if(isset($postDecode['token_key'],$postDecode['username'])){
else{
    file_put_contents(C::FILE_LOG,"loginStatus response not set => \r\n",FILE_APPEND);
}

file_put_contents(C::FILE_LOG,"loginStatus response => ".var_export($response,true)."\r\n",FILE_APPEND);
echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>