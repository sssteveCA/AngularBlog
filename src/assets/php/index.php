<?php

require_once('interfaces/article/articlelist_errors.php');
require_once('../../../vendor/autoload.php');

use AngularBlog\Responses\Activate;
use AngularBlog\Responses\ArticleComments;
use AngularBlog\Responses\CreateArticle;
use AngularBlog\Responses\DeleteArticle;
use AngularBlog\Responses\EditArticle;
use AngularBlog\Responses\GetArticle;
use AngularBlog\Responses\GetArticlesByQuery;
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
$objectIdRegex = "([0-9a-fA-F]{24})";
$prefixSlashes = str_replace('/','\/',$prefix);
$permalinkRegex = "([a-zA-Z\d\-_]{5,30})";
$tokenRegex = "([0-9a-zA-Z]{64})";

if($method == "GET"){
    $params = [ 'get' => [], 'headers' => $headers ];
    if(preg_match("/^{$prefixSlashes}\/activate\/{$tokenRegex}/",$uri,$matches)){
        $params['get']['emailVerif'] = $matches[1];
        $response = Activate::content($params);
    }
    else if(preg_match("/^{$prefixSlashes}\/articles\/{$permalinkRegex}\/?$/",$uri,$matches)){
        $params['get']['permalink'] = $matches[1];
        $response = GetArticle::content($params);
    }
    else if(preg_match("/^{$prefixSlashes}\/articles\/{$permalinkRegex}\/comments/",$uri,$matches)){
        $params['get']['permalink'] = $matches[1];
        $response = ArticleComments::content($params);
    }
    else if(substr($uri,0,strlen($prefix."/articles")) == $prefix."/articles" && isset($_GET['query'])){
        $params['get']['query'] = $_GET['query'];
        $response = GetArticlesByQuery::content($params);
    }
    else if($uri == $prefix."/lastposts"){
        $response = LastPosts::content([]);
    }
    else if($uri == $prefix."/logout"){
        $response = Logout::content($params);
    }
}
else if($method == "POST"){
    $input = file_get_contents("php://input");
    $post = json_decode($input, true);
    $params = [ 'headers' => $headers, 'post' => $post ];
    if($uri == $prefix."/articles"){
        $response = CreateArticle::content($params);
    }
    else if($uri == $prefix."/login"){
        $response = Login::content($params);
    }
    else if($uri == $prefix."/register"){
        $response = Register::content($params);
    }
}
else if($method == "PUT"){
    $input = file_get_contents("php://input");
    $put = json_decode($input, true);
    $params = [ 'headers' => $headers, 'put' => $put ];
    if(preg_match("/^{$prefixSlashes}\/articles\/{$objectIdRegex}/",$uri,$matches)){
        $params['put']['article']['id'] = $matches[1];
        $response = EditArticle::content($params);
    }
}

else if($method == "DELETE"){
    $params = [ 'delete' => [], 'headers' => $headers ];
    if(preg_match("/^{$prefixSlashes}\/articles\/{$objectIdRegex}/",$uri,$matches)){
        $params['delete']['article_id'] = $matches[1];
        $response = DeleteArticle::content($params);
    }
}

http_response_code($response[C::KEY_CODE]);
echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

?>