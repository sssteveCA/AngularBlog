<?php

require_once("../cors.php");
require_once("../config.php");
require_once("../classes/model.php");
require_once("../classes/token.php");

use AngularBlog\Classes\Token;

$post = file_get_contents('php://input');
$postDecode = json_decode($post,true);

if(isset($post['id'],$post['username'])){
    $filter = [
        '$and' => [
            ['user_id' => $post['id']].
            ['username' => $post['username']]
        ]
    ];
    try{
        $token = new Token();
    }catch(Exception $e){
        
    }
}//if(isset($post['id'],$post['username'])){

?>