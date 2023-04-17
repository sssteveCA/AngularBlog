<?php

require_once("../../../../../../vendor/autoload.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Myarticles\EditContoller;
use AngularBlog\Classes\Myarticles\EditView;
use Dotenv\Dotenv;

$input = file_get_contents('php://input');
$post = json_decode($input,true);

$response = array(
    C::KEY_DONE => false,
    C::KEY_EXPIRED => false,
    C::KEY_MESSAGE => ''
);
$headers = getallheaders();

if(isset($post['article'],$headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
    if(isset($post['article']['id'],$post['article']['title'],$post['article']['introtext'],$post['article']['content'],$post['article']['permalink'],$post['article']['categories'],$post['article']['tags']) && $post['article']['id'] != '' && $post['article']['title'] != '' && $post['article']['introtext'] != '' && $post['article']['content'] != '' && $post['article']['permalink'] != ''){
        $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
        $dotenv->safeLoad();
        $data = [
            'token_key' => $headers[C::KEY_AUTH],
            'article_id' => $post['article']['id']
        ];
        $token_data = ['token_key' => $headers[C::KEY_AUTH]];
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
            $response[C::KEY_MESSAGE] = $editView->getMessage();
            if($editView->isDone())
                $response[C::KEY_DONE] = true;
            else{
                $errnoT = $editController->getToken()->getErrno();
                if($errnoT == Te::TOKENEXPIRED){
                    $response[C::KEY_EXPIRED] = true;
                }
            }
            http_response_code($editView->getResponseCode());
        }catch(Exception $e){
            http_response_code(500);
            file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
            $response[C::KEY_MESSAGE] = C::ARTICLEEDITING_ERROR;
        }
    }//if(isset($post['article']['id'],$post['article']['title'],$post['article']['introtext'],$post['article']['content'],$post['article']['permalink'],$post['article']['categories'],$post['article']['tags']) && $post['article']['id'] != '' && $post['article']['title'] != '' && $post['article']['introtext'] != '' && $post['article']['content'] != '' && $post['article']['permalink'] != ''){
    else{
        http_response_code(400);
        $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
    }
}//if(isset($post['article'],$headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::ARTICLEEDITING_ERROR;
}
    

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);



?>