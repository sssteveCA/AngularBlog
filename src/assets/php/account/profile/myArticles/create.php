<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/article/article_errors.php");
require_once("../../../interfaces/action/action_errors.php");
require_once("../../../interfaces/myarticles/createcontroller_errors.php");
require_once("../../../interfaces/myarticles/createview_errors.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/action/action.php");
require_once("../../../classes/article/article.php");
require_once("../../../classes/myarticles/createcontroller.php");
require_once("../../../classes/myarticles/createview.php");

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

$input = file_get_contents('php://input');
$post = json_decode($input,true);
//$response['post'] = $post;

if(isset($post['token_key'],$post['article']) && $post['token_key'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $data = [
        'token_key' => $post['token_key'],
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
}//if(isset($post['token_key']) && $post['token_key'] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}
    

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>