<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/External/Savant3.php");

$FILENAME = 'header';
${PageBuilder::PAGELET_PREFIX.$FILENAME} = $tpl->fetch("$FILENAME.tpl.php");

?>