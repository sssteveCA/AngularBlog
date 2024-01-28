<?php

require_once('interfaces/article/articlelist_errors.php');
require_once('../../../vendor/autoload.php');

use AngularBlog\Responses\Activate;
use AngularBlog\Responses\ArticleComments;
use AngularBlog\Responses\CreateArticle;
use AngularBlog\Responses\CreateComment;
use AngularBlog\Responses\DeleteArticle;
use AngularBlog\Responses\EditArticle;
use AngularBlog\Responses\EditComment;
use AngularBlog\Responses\ErrorMessage;
use AngularBlog\Responses\GetArticle;
use AngularBlog\Responses\GetArticlesByQuery;
use AngularBlog\Responses\GetUserArticles;
use AngularBlog\Responses\LastPosts;
use AngularBlog\Responses\Login;
use AngularBlog\Responses\Logout;
use AngularBlog\Responses\Register;
use AngularBlog\Interfaces\Constants as C;

//echo json_encode($_SERVER,JSON_PRETTY_PRINT);

$headers = getallheaders();
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$prefix = "/api/v1";
$regex = [
    "objectId" => "([0-9a-fA-F]{24})",
    "permalink" => "([a-zA-Z\d\-_]{5,30})",
    'prefix' => "\/api\/v1",
    "token" => "([0-9a-zA-Z]{64})"
];

if($method == "GET"){
    $params = [ 'get' => [], 'headers' => $headers ];
    if(preg_match("/^{$regex['prefix']}\/activate\/{$regex['token']}\/?$/",$uri,$matches)){
        $params['get']['emailVerif'] = $matches[1];
        $response = Activate::content($params);
    }
    else if(preg_match("/^{$regex['prefix']}\/articles\/{$regex['permalink']}\/?$/",$uri,$matches)){
        $params['get']['permalink'] = $matches[1];
        $response = GetArticle::content($params);
    }
    else if(preg_match("/^{$regex['prefix']}\/articles\/{$regex['permalink']}\/comments\/?$/",$uri,$matches)){
        $params['get']['permalink'] = $matches[1];
        $response = ArticleComments::content($params);
    }
    else if(preg_match("/^{$regex['prefix']}\/articles\?query=([a-zA-z0-9]{3,30})$/",$uri,$matches)){
        $params['get']['query'] = $matches[1];
        $response = GetArticlesByQuery::content($params);
    }
    else if(preg_match("/^{$regex['prefix']}\/lastposts\/?$/",$uri)){
        $response = LastPosts::content([]);
    }
    else if(preg_match("/^{$regex['prefix']}\/logout\/?$/",$uri)){
        $response = Logout::content($params);
    }
    else if(preg_match("/^{$regex['prefix']}\/profile\/articles\/?$/",$uri)){
        $response = GetUserArticles::content($params);
    }
    else{
        $params[C::KEY_CODE] = 404; 
        $params[C::KEY_MESSAGE] = 'Risorsa non trovata' ;
        $response = ErrorMessage::content($params);
    }
}
else if($method == "POST"){
    $input = file_get_contents("php://input");
    $post = json_decode($input, true);
    $params = [ 'headers' => $headers, 'post' => $post ];
    if(preg_match("/^{$regex['prefix']}\/articles\/?$/",$uri)){
        $response = CreateArticle::content($params);
    }
    else if(preg_match("/^{$regex['prefix']}\/articles\/{$regex['permalink']}\/comments\/?$/",$uri,$matches)){
        $params['post']['permalink'] = $matches[1];
        $response = CreateComment::content($params);
    }
    else if(preg_match("/^{$regex['prefix']}\/login\/?$/",$uri)){
        $response = Login::content($params);
    }
    else if(preg_match("/^{$regex['prefix']}\/register\/?$/",$uri)){
        $response = Register::content($params);
    }
    else{
        $params[C::KEY_CODE] = 404; 
        $params[C::KEY_MESSAGE] = 'Risorsa non trovata' ;
        $response = ErrorMessage::content($params);
    }
}

else if($method == "PUT"){
    $input = file_get_contents("php://input");
    $put = json_decode($input, true);
    $params = [ 'headers' => $headers, 'put' => $put ];
    if(preg_match("/^{$regex['prefix']}\/articles\/{$regex['objectId']}\/?$/",$uri,$matches)){
        $params['put']['article']['id'] = $matches[1];
        $response = EditArticle::content($params);
    }
    else if(preg_match("/^{$regex['prefix']}\/articles\/{$regex['permalink']}\/comments\/{$regex['objectId']}\/?$/",$uri,$matches)){
        $params['put']['comment_id'] = $matches[2];
        $response = EditComment::content($params);
    }
    else{
        $params[C::KEY_CODE] = 404; 
        $params[C::KEY_MESSAGE] = 'Risorsa non trovata' ;
        $response = ErrorMessage::content($params);
    }
}

else if($method == "DELETE"){
    $params = [ 'delete' => [], 'headers' => $headers ];
    if(preg_match("/^{$regex['prefix']}\/articles\/{$regex['objectId']}/",$uri,$matches)){
        $params['delete']['article_id'] = $matches[1];
        $response = DeleteArticle::content($params);
    }
    else{
        $params[C::KEY_CODE] = 404; 
        $params[C::KEY_MESSAGE] = 'Risorsa non trovata' ;
        $response = ErrorMessage::content($params);
    }
}
else{
    $params[C::KEY_CODE] = 404; 
    $params[C::KEY_MESSAGE] = 'Risorsa non trovata' ;
    $response = ErrorMessage::content($params);
}

http_response_code($response[C::KEY_CODE]);
echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

?>