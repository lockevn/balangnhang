<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once(ABSPATH."Lib/HttpNavigation.php");

$nickname = $_GET['pu'];
HttpNavigation::OutputRedirectToBrowser("/profile/$nickname");

?>