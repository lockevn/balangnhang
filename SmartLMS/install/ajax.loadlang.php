<?php

function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

$time_start = getmicrotime();

ob_start();

session_name("docebo_installer");
session_start();

require_once(dirname(__FILE__).'/config.php');

$to_load = $_GET['to_load'];
$split = explode('_',$to_load);

require_once($GLOBALS['where_upgrade'].'/lib/lib.docebosql.php');
require_once($GLOBALS['where_upgrade'].'/lib/lib.lang.php');
$GLOBALS["db"] = new DoceboSql($_SESSION['dbhost'], $_SESSION['dbuname'], $_SESSION['dbpass'], $_SESSION['dbname'], false);

$platform_code = $split[0];
$lang = $split[1];
$fn=$GLOBALS['where_upgrade'].'/../xml_language/platform['.$platform_code.']_lang['.$lang.'].xml';

if (file_exists($fn)) {
	lang_importXML($fn);
	
	$result = true;
} else {
	
	$result = false;
}

$GLOBALS["db"]->closeConn();
ob_end_clean();


$time_end = getmicrotime();
$time = $time_end - $time_start;
echo '{"result":"'.($result?'success':'fail').'","exe_time":'.number_format($time, 3).'}';

?>