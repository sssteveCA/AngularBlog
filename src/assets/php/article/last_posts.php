<?php

require_once("../cors.php");
require_once("../interfaces/constants.php");
require_once("../../../../vendor/autoload.php");

use AngularBlog\Interfaces\Constants as C;

$response = [
    C::KEY_DATA => [], C::KEY_DONE => false, C::KEY_MESSAGE => ""
];



echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);


?>