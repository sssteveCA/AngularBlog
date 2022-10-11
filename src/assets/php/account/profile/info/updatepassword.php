<?php

require_once("../../../cors.php");

$response = [
    "done" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);

if(isset($update["token_key"],$update["conf_new_password"],$update["new_password"],$update["old_password"])){

}//if(isset($update["token_key"],$update["conf_new_password"],$update["new_password"],$update["old_password"])){

echo json_encode($response);
?>