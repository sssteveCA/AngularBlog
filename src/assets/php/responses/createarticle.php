<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\MyArticles\CreateControllerErrors as Cce;
use AngularBlog\Interfaces\MyArticles\CreateViewErrors as Cve;
use AngularBlog\Classes\Myarticles\CreateController;
use AngularBlog\Classes\Myarticles\CreateView;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for create article POST route
 */
class CreateArticle{

    public static function content(array $params): array{
        $response = [ C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => '' ];
        $headers = $params['headers'];
        $post = $params['post'];
        if(isset($headers[C::KEY_AUTH],$post['article']) && $headers[C::KEY_AUTH] != ''){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $data = [ 'token_key' => $headers[C::KEY_AUTH], 'article' => $post['article'] ];
                $createController = new CreateController($data);
                $createView = new CreateView($createController);
                $response[C::KEY_MESSAGE] = $createView->getMessage();
                if($createView->isDone())
                    $response[C::KEY_DONE] = true;
                else{
                    $errnoT = $createController->getToken()->getErrno();
                    if($errnoT == Te::TOKENEXPIRED) $response[C::KEY_EXPIRED] = true;
                }
                $response[C::KEY_CODE] = $createView->getResponseCode();
            }catch(Exception $e){
                $msg = $e->getMessage();
                switch($msg){
                    case Cce::NOARTICLEDATA_EXC:
                        $response[C::KEY_CODE] = 400;
                        $response[C::KEY_MESSAGE] = $msg;
                        break;
                    case Cce::NOTOKENKEY_EXC:
                    case Cve::NOCREATECONTROLLERINSTANCE_EXC:
                    default:
                        $response[C::KEY_CODE] = 500;
                        $response[C::KEY_MESSAGE] = C::ARTICLECREATION_ERROR;
                        break;
                }
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
        }
        return $response;
    }

}

?>