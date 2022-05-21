<?php

require_once('../cors.php');
require_once('../interfaces/constants.php');
require_once('../interfaces/bloguser_errors.php');
require_once('../class/bloguser.php');

use AngularBlog\Classes\BlogUser;

$response = array();
$response['msg'] = '';
$response['done'] = false;

$input = file_get_contents('php://input');
$post = json_decode($input,true);

$response['post'] = $post;

echo json_encode($response);
?>