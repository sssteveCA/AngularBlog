<?php

require_once("../../../cors.php");

$response = [
    "done" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$delete = json_decode($input,true);

if(isset($delete["token_key"],$delete["password"],$delete["conf_password"])){

}//if(isset($delete["token_key"],$delete["password"],$delete["conf_password"])){

echo json_encode($response);
?>