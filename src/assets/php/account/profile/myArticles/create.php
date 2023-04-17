<?php

require_once("../../../../../../vendor/autoload.php");


use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\MyArticles\CreateControllerErrors as Cce;
use AngularBlog\Interfaces\MyArticles\CreateViewErrors as Cve;
use AngularBlog\Classes\Myarticles\CreateController;
use AngularBlog\Classes\Myarticles\CreateView;
use Dotenv\Dotenv;

$response = array(
    C::KEY_DONE => false,
    C::KEY_EXPIRED => false,
    C::KEY_MESSAGE => ''
);
$headers = getallheaders();

$input = file_get_contents('php://input');
$post = json_decode($input,true);
//$response['post'] = $post;

if(isset($headers[C::KEY_AUTH],$post['article']) && $headers[C::KEY_AUTH] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $data = [
        'token_key' => $headers[C::KEY_AUTH],
        'article' => $post['article']
    ];
    try{
        $createController = new CreateController($data);
        $createView = new CreateView($createController);
        $response[C::KEY_MESSAGE] = $createView->getMessage();
        if($createView->isDone())
            $response[C::KEY_DONE] = true;
        else{
            $errnoT = $createController->getToken()->getErrno();
            if($errnoT == Te::TOKENEXPIRED){
                $response[C::KEY_EXPIRED] = true;
            }
        }
        http_response_code($createView->getResponseCode());
    }catch(Exception $e){
        $msg = $e->getMessage();
        switch($msg){
            case Cce::NOARTICLEDATA_EXC:
                http_response_code(400);
                $response[C::KEY_MESSAGE] = $msg;
                break;
            case Cce::NOTOKENKEY_EXC:
            case Cve::NOCREATECONTROLLERINSTANCE_EXC:
            default:
                http_response_code(500);
                $response[C::KEY_MESSAGE] = C::ARTICLECREATION_ERROR;
                break;
        }
    }
}//if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}
    

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>