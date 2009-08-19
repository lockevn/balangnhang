<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/External/Savant3.php");



$FILENAME = 'footer';
${PageBuilder::PAGELET_PREFIX.$FILENAME} = $tpl->fetch("$FILENAME.tpl.php");

?>