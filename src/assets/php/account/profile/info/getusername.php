<?php

use AngularBlog\Classes\Account\GetUsernameController;
use AngularBlog\Interfaces\Account\GetUsernameControllerErrors as Guce;
use AngularBlog\Classes\Info\GetUsernameView;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;

require_once("../../../cors.php");

$response = [
    "done" => false, "expired" => false, "msg" => "", "username" => ""
];

if(isset($_GET["token_key"])){
    $token_data = ["token_key" => $_GET["token_key"]];
    try{
        $token = new Token($token_data);
        $user = new User([]);
        $guc_data = [
            'token' => $token, 'user' => $user
        ];
        $guc = new GetUsernameController($guc_data);
        $guv = new GetUsernameView($guc);
        if($guv->isDone()){
            $response["done"] = true;
            $response["username"] = $guv->getMessage();
        }//if($guv->isDone()){
        else{
            if($guc->getErrno() == Guce::FROM_TOKEN){
                if($guc->getToken()->getErrno() == Te::TOKENEXPIRED)
                    $response["expired"] = true;
            }
        }//else di if($guv->isDone()){
    }catch(Exception $e){
        $error = $e->getMessage();
        file_put_contents(C::FILE_LOG, "{$error}\r\n",FILE_APPEND);
    }
}//if(isset($get["token_key"])){
else
    $response["msg"] = "Fornisci un token di autorizzazione per continuare";

echo json_encode($response);
?>