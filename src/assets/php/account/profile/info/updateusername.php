<?php

require_once("../../../cors.php");

$response = [
    "done" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);

if(isset($update["token_key"],$update["new_username"])){

}//if(isset($update["token_key"],$update["new_username"])){

echo json_encode($response);
?>