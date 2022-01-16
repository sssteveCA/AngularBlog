<?php
session_start();

require_once("../cors.php");

unset($_SESSION['username']);
session_destroy();

?>