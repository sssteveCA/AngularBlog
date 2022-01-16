<?php
session_start();

require_once("../cors.php");
require_once("../config.php");

$response = array();

if(isset($_SESSION[COOKIE_NAME])){
    //if user is logged
    $response['session'] = true;
}
else{
    //if user is not logged
    $response['session'] = false;
}

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>