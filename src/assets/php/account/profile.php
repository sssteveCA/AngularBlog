<?php
session_start();

require_once("../cors.php");
require_once("../../../../config.php");
require_once("../config.php");
require_once("../interfaces/constants.php");

use AngularBlog\Interfaces\Constants as C;

$response = array();

if(isset($_SESSION[C::COOKIE_NAME])){
    //if user is logged
    $response['session'] = true;
}
else{
    //if user is not logged
    $response['session'] = false;
}
$response['session_array'] = $_SESSION;

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>