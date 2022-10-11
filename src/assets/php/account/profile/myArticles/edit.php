<?php

require_once("../../../cors.php");
require_once("../../../../../../config.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/article/article_errors.php");
require_once("../../../interfaces/article/articleauthorizedcontroller_errors.php");
require_once("../../../interfaces/article/articleauthorizedview_errors.php");
require_once("../../../interfaces/myarticles/editcontroller_errors.php");
require_once("../../../interfaces/myarticles/editview_errors.php");
require_once("../../../vendor/autoload.php");
require_once("../../../traits/authorized.trait.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/article/article.php");
require_once("../../../classes/article/articleauthorizedcontroller.php");
require_once("../../../classes/article/articleauthorizedview.php");
require_once("../../../classes/myarticles/editcontroller.php");
require_once("../../../classes/myarticles/editview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Myarticles\EditContoller;
use AngularBlog\Classes\Myarticles\EditView;

$input = file_get_contents('php://input');
$post = json_decode($input,true);

$response = array(
    'done' => false,
    'expired' => false,
    'msg' => ''
);

if(isset($post['article'],$post['token_key']) && $post['token_key'] != ''){
    if(isset($post['article']['id'],$post['article']['title'],$post['article']['introtext'],$post['article']['content'],$post['article']['permalink'],$post['article']['categories'],$post['article']['tags']) && $post['article']['id'] != '' && $post['article']['title'] != '' && $post['article']['introtext'] != '' && $post['article']['content'] != '' && $post['article']['permalink'] != ''){
        $data = [
            'token_key' => $post['token_key'],
            'article_id' => $post['article']['id']
        ];
        $token_data = ['token_key' => $post['token_key']];
        $article_data = [
            'id' => $post['article']['id'],
            'title' => $post['article']['title'],
            'introtext' => $post['article']['introtext'],
            'content' => $post['article']['content'],
            'permalink' => $post['article']['permalink'],
            'categories' => explode(",",$post['article']['categories']),
            'tags' => explode(",",$post['article']['tags'])
        ];
        try{
            $token = new Token($token_data);
            $article = new Article($article_data);
            $ec_data = [
                'article' => $article,
                'token' => $token
            ];
            $editController = new EditContoller($ec_data);
            $editView = new EditView($editController);
            $response['msg'] = $editView->getMessage();
            if($editView->isDone())
                $response['done'] = true;
            else{
                $errnoT = $editController->getToken()->getErrno();
                if($errnoT == Te::TOKENEXPIRED){
                    $response['expired'] = true;
                }
            }
        }catch(Exception $e){
            file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
            $response['msg'] = C::ARTICLEEDITING_ERROR;
        }
    }//if(isset($post['article']['id'],$post['article']['title'],$post['article']['introtext'],$post['article']['content'],$post['article']['permalink'],$post['article']['categories'],$post['article']['tags']) && $post['article']['id'] != '' && $post['article']['title'] != '' && $post['article']['introtext'] != '' && $post['article']['content'] != '' && $post['article']['permalink'] != ''){
    else{
        $response['msg'] = C::FILL_ALL_FIELDS;
    }
}//if(isset($post['article'],$post['token_key']) && $post['token_key'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);



?>