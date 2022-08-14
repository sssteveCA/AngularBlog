<?php

require_once("../../cors.php");
require_once("../../interfaces/constants.php");

use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;

$input = file_get_contents("php://input");
$patch = json_decode($input, true);

$response = [
    'done' => false,
    'msg' => '',
    'patch' => $patch
];

if(isset($patch['comment_id'],$patch['new_comment'],$patch['old_comment'],$patch['token_key']) && $patch['comment_id'] != '' && $patch['new_comment'] != '' && $patch['old_comment'] != '' && $patch['token_key'] != ''){
    $token_data = ['token_key' => $patch['token_key']];
    $comment_data = [
        'id' => $patch['comment_id']
    ];
    try{
        $token = new Token($token_data);
        $comment = new Comment($comment_data);
        $ec_data = [
            'token' => $token,
            'comment' => $comment
        ];
    }catch(Exception $e){
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response['msg'] = C::COMMENTUPDATE_ERROR;
    }
}//if(isset($patch['comment_id'],$patch['new_comment'],$patch['old_comment'],$patch['token_key']) && $patch['comment_id'] != '' && $patch['new_comment'] != '' && $patch['old_comment'] != '' && $patch['token_key'] != ''){
else{
    $response['msg'] = C::COMMENTUPDATE_ERROR;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>