<?php


require_once('../../../../vendor/autoload.php');

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Logout\LogoutView;
use AngularBlog\Classes\Logout\LogoutController;
use Dotenv\Dotenv;

$response = array(
    C::KEY_MESSAGE => '',
    C::KEY_DONE => false
);

$headers = getallheaders();

if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
    $dotenv->safeLoad();
    $token_key = $headers[C::KEY_AUTH];
    $filter = ['token_key' => $token_key];
    try{
        $token = new Token();
        $get = $token->token_get($filter);
        if($get){
            //Found in DB a collection with token key passed from client
            $logoutController = new LogoutController($token);
            $logoutView = new LogoutView($logoutController);
            $logout = $logoutView->isLogout();
            if($logout){
                //Server side logout done
                $response[C::KEY_DONE] = true;
            }
            else
                $response[C::KEY_MESSAGE] = $logoutView->getMessage();
            http_response_code($logoutView->getResponseCode());
        }//if($get){
        else{
            http_response_code(404);
            $response[C::KEY_MESSAGE] = C::LOGOUT_ERROR_USERNOTFOUND;
        }          
    }catch(Exception $e){
        http_response_code(500);
        file_put_contents(C::FILE_LOG,$e->getMessage()."\r\n",FILE_APPEND);
        $response[C::KEY_MESSAGE] = C::LOGOUT_ERROR;
    }
}//if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}
    

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

?>