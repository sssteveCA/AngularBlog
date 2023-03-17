<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../interfaces/action/action_errors.php");
require_once("../../../interfaces/action/getuseractionscontroller_errors.php");
require_once("../../../interfaces/action/getuseractionsview_errors.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/messagearray.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../traits/responsemultiple.trait.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/user.php");
require_once("../../../classes/action/action.php");
require_once("../../../classes/action/actionlist.php");
require_once("../../../classes/action/getuseractionscontroller.php");
require_once("../../../classes/action/getuseractionsview.php");

use AngularBlog\Classes\Action\GetUserActionsController;
use AngularBlog\Classes\Action\GetUserActionsView;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;

$response = [ C::KEY_DONE => false, C::KEY_EMPTY => false, C::KEY_DATA => [], C::KEY_MESSAGE => ""];

if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $token_data = ["token_key" => $_GET["token_key"]];
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
}//if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::ERROR_TOKEN_MISSED;
}
    
echo json_encode($response, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>