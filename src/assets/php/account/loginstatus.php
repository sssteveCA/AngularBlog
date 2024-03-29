<?php

require_once('../../../../vendor/autoload.php');

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use Dotenv\Dotenv;

//This script check if user is still logged
$response = array( C::KEY_MESSAGE => '', 'logged' => false );

$post = file_get_contents('php://input');
$postDecode = json_decode($post,true);
$response['post'] = $postDecode;

if(isset($postDecode['token_key'],$postDecode['username'])){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
    $dotenv->safeLoad();
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
                $response[C::KEY_MESSAGE] = $token->getError();
                //$token->token_delete(['token_key' => $token->getTokenKey(), 'username' => $token->getUsername()]);
            }
                
        }//if($get){
    }catch(Exception $e){
    }
    
}//if(isset($postDecode['token_key'],$postDecode['username'])){
else{
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>