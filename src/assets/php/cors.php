<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');

$method = $_SERVER['REQUEST_METHOD'];
if($method == 'OPTIONS') die();
?>