<?php

require_once("../../../cors.php");

$response = [
    "done" => false, "msg" => ""
];

if(isset($_GET["token_key"])){

}//if(isset($get["token_key"])){

echo json_encode($response);
?>