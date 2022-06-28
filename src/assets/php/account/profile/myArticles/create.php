<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/article/article_errors.php");
require_once("../../../interfaces/myarticles/createcontroller_errors.php");
require_once("../../../interfaces/myarticles/createview_errors.php");
require_once("../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/article/article.php");
require_once("../../../classes/myarticles/createcontroller.php");
require_once("../../../classes/myarticles/createview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\MyArticles\CreateControllerErrors as Cce;
use AngularBlog\Interfaces\MyArticles\CreateViewErrors as Cve;
use AngularBlog\Classes\Myarticles\CreateController;
use AngularBlog\Classes\Myarticles\CreateView;

$response = array(
    'done' => false,
    'expired' => false,
    'msg' => ''
);

$input = file_get_contents('php://input');
$post = json_decode($input,true);
//$response['post'] = $post;

if(isset($post['token_key'],$post['article']) && $post['token_key'] != ''){
    $data = [
        'token_key' => $post['token_key'],
        'article' => $post['article']
    ];
    try{
        $createController = new CreateController($data);
        $createView = new CreateView($createController);
        $response['msg'] = $createView->getMessage();
        if($createView->isDone())
            $response['done'] = true;
        else{
            $errnoT = $createController->getToken()->getErrno();
            if($errnoT == Te::TOKENEXPIRED){
                $response['expired'] = true;
            }
        }
    }catch(Exception $e){
        $msg = $e->getMessage();
        switch($msg){
            case Cce::NOARTICLEDATA_EXC:
                $response['msg'] = $msg;
                break;
            case Cce::NOTOKENKEY_EXC:
            case Cve::NOCREATECONTROLLERINSTANCE_EXC:
                $response['msg'] = C::ARTICLECREATION_ERROR;
                break;
            default:
                $response['msg'] = C::ERROR_UNKNOWN;
                break;
        }
    }
}//if(isset($post['token_key']) && $post['token_key'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>