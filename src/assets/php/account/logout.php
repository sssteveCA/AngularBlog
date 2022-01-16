<?php
session_start();

require_once("../cors.php");
require_once("../config.php");

unset($_SESSION[COOKIE_NAME]);
session_destroy();

?>