<?php

/************************************************************************/
/* DOCEBO 																*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2008													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/


$list = array('GLOBALS', '_POST', '_GET', '_COOKIE', '_SESSION'); 
while(list(, $elem) = each($list)) {
	if(isset($_REQUEST[$elem])) die('Request overwrite attempt detected');
}

define("IN_DOCEBO", true);

// Start buffer ---------------------------------------------------------

ob_start();

//get config with position of the others application --------------------
require(dirname(__FILE__).'/config.php');
require($GLOBALS['where_config'].'/config.php');

// connect to database ----------------------------------------------------------------

$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] )
	die( "Can't connect to db. Check configurations" );

if( !mysql_select_db($dbname, $GLOBALS['dbConn']) )
	die( "Database not found. Check configurations" );

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);

// load lms setting ------------------------------------------------------------------
require_once($GLOBALS['where_framework'].'/setting.php');
require_once($GLOBALS['where_lms'].'/setting.php');

if($GLOBALS['framework']['do_debug'] == 'on') {
	@error_reporting(E_ALL);
	@ini_set('display_errors', 1);
} else {
	@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
}

session_name("docebo_session");
session_start();

// load regional setting --------------------------------------------------------------
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session -----------------------------------------------------
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');

require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
require_once($GLOBALS['where_framework'].'/lib/lib.template.php');
require_once($GLOBALS['where_framework'].'/lib/lib.utils.php');
require_once($GLOBALS['where_lms'].'/lib/lib.permission.php');

// security check --------------------------------------------------------------------

chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();

$GLOBALS['operation_result'] = '';
if(!function_exists("docebo_cout")) {
	function docebo_cout($string) { $GLOBALS['operation_result'] .= $string; }
}

// here all the specific code ==========================================================

$mn = get_req('mn', DOTY_ALPHANUM, '');
$plf = get_req('plf', DOTY_ALPHANUM, $GLOBALS['platform']);
$aj_file = $GLOBALS['where_'.$plf].'/modules/'.$mn.'/ajax.'.$mn.'.php';

include($aj_file);

// =====================================================================================

// close database connection

mysql_close($GLOBALS['dbConn']);

ob_clean();
echo $GLOBALS['operation_result'];
ob_end_flush();

?>
