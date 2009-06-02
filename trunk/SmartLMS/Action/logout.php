<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Business/Security.php");

Security::Logout('/dashboard');

?>