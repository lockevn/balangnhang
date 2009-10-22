<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/External/Savant3.php");

$FILENAME = 'header';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>