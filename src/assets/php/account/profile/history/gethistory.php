<?php

require_once("../../../../../../vendor/autoload.php");

use AngularBlog\Classes\Action\GetUserActionsController;
use AngularBlog\Classes\Action\GetUserActionsView;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;

$response = [ C::KEY_DONE => false, C::KEY_EMPTY => false, C::KEY_DATA => [], C::KEY_MESSAGE => ""];
$headers = getallheaders();

if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $token_data = ["token_key" => $headers[C::KEY_AUTH]];
    try{
        $guac = new GetUserActionsController($token_data);
        $guav = new GetUserActionsView($guac);
        $done = $guav->isDone();
        $actions_found = $guav->areActionsFound();
        if($actions_found){
            $response[C::KEY_DONE] = true;
            $response[C::KEY_DATA]['actions'] = $guav->getMessageArray()['actions'];
        }
        else if(!$actions_found && $done){
            $response[C::KEY_DONE] = $guav->isDone();
            $response[C::KEY_EMPTY] = true;
            $response[C::KEY_MESSAGE] = $guav->getMessageArray()[C::KEY_MESSAGE];          
        }
        else{
            $response[C::KEY_MESSAGE] = $guav->getMessageArray()[C::KEY_MESSAGE];
        }
        http_response_code($guav->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        $error = $e->getMessage();
        file_put_contents(C::FILE_LOG, "{$error}\r\n",FILE_APPEND);
    }
}//if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::ERROR_TOKEN_MISSED;
}
    
echo json_encode($response, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>