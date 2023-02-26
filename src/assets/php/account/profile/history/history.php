<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../../../../vendor/autoload.php");

use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;

$response = [ C::KEY_DONE => false ];

if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
}//if(isset($_GET['token_key']) && $_GET['token_key'] != ''){

echo json_encode($response, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>