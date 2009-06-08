<?php

require_once("Lib/External/Savant3.php");
$tpl = new Savant3();
// $tpl->setPath('template', array(''));
$test = $tpl->fetch("doceboLms/Template/search.tpl.php");

echo $test;
//$er = $tpl->error('ERR_TEMPLATE')  ;
//print_r($er);

?>