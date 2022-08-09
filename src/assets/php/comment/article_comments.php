<?php

//List of comments of single article

require_once("../cors.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/models_errors.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/comment/comment_errors.php");
require_once("../interfaces/comment/commentlist_errors.php");
require_once("../vendor/autoload.php");
require_once("../traits/error.trait.php");
require_once("../classes/model.php");
require_once("../classes/models.php");
require_once("../classes/comment/comment.php");
require_once("../classes/comment/commentlist.php");

$response = [
    'msg' => '',
    'done' => false
];

echo json_encode($response);
?>