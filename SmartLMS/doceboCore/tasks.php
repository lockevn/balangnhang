<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/
/*
function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
} 

$time_start = getmicrotime();
*/

/* 
 * Verify that request was made by localhost.
 * Remove this control if you want to call tasks porcessor by
 * remote but it's *dangerous*
 */
if( $_SERVER['REMOTE_ADDR'] != '127.0.0.1' ) {
	die("You can't do this opertation from remote.");
}
if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');

define("IN_DOCEBO", true);

define("IN_CORE", true);
//define("IN_CORE", true);

// stop all warning in language module.
$GLOBALS['lang_hide_edit'] = '1';

require_once(dirname(__FILE__).'/config.php');
require_once($GLOBALS['where_config'].'/config.php');

/*Start buffer************************************************************/

ob_start();

/*Start database connection***********************************************/

$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] ) 
	die( "Can't connect to db. Check configurations" );

if( !mysql_select_db($dbname, $GLOBALS['dbConn']) ) 
	die( "Database not found. Check configurations" );

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);

require_once(dirname(__FILE__).'/setting.php');

if($GLOBALS['do_debug'] == 'on') {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

/*Start session***********************************************************/

require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');

// load regional setting
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& new DoceboUser( '/Anonymous', 'docebotasks' );

// Utils and so on
require_once($GLOBALS['where_framework'].'/lib/lib.php');

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();

require_once($GLOBALS['where_framework'].'/lib/lib.preoperation.php');

header('Content-type: text/xml');
echo '<?xml version="1.0"?>';
echo '<tasks>';


// do io task operations
echo '<iotasks>';
require_once($GLOBALS['where_framework'].'/lib/lib.istance.php');
$module_cfg =& createModule('iotask');
echo $module_cfg->doTasks();
echo '</iotasks>';

echo '</tasks>';
/*End database connection*************************************************/

mysql_close();

/*Flush buffer************************************************************/

//$time_end = getmicrotime();
//$GLOBALS['page']->add('time : '.($time_end - $time_start), 'footer');

?>