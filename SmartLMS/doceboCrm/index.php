<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');

define("IN_DOCEBO", true);

//get config with position of the others application
require(dirname(__FILE__).'/config.php');
require($GLOBALS['where_config'].'/config.php');

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

require_once($GLOBALS['where_framework'].'/setting.php');
require_once(dirname(__FILE__).'/setting.php');

/*Start session***********************************************************/

//cookie lifetime
session_set_cookie_params( 0 );
//session lifetime ( max inactivity time )
ini_set('session.gc_maxlifetime', $GLOBALS['crm']['ttlSession']);

session_name("docebo_crm");
session_start();

// load regional setting
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('crm');

// Utils and so on
require($GLOBALS['where_crm'].'/lib/lib.php');

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();

require_once($GLOBALS['where_crm'].'/lib/lib.preoperation.php');

// create instance of StdPageWriter
require_once($GLOBALS['where_framework'].'/lib/lib.pagewriter.php');
StdPageWriter::createInstance();

if(isset($GLOBALS['modname']) && $GLOBALS['modname'] != '') {
	//create the class for management the called module
	$module_cfg =& createCrmModule($GLOBALS['modname']);
}

//header
if(isset($modname) && $modname != '') {
	if($module_cfg->useStdHeader()) {

		if(is_file(getAbsolutePathTemplate('crm').'header.php')) {
			include(getAbsolutePathTemplate('crm').'header.php');
		} else include($GLOBALS['where_crm'].'/templates/header.php');
	}
} else {
	if(is_file(getAbsolutePathTemplate('crm').'header.php')) {
		include(getAbsolutePathTemplate('crm').'header.php');
	} else include($GLOBALS['where_crm'].'/templates/header.php');
}
//menu over
if(!$GLOBALS['current_user']->isAnonymous()) {

	require($GLOBALS['where_crm'].'/menu/menu_over.php');
	require($GLOBALS['where_crm'].'/menu/menu_lat.php');
} else {

	require($GLOBALS['where_crm'].'/menu/menu_login.php');
}

// load module body
if(isset($GLOBALS['modname']) && $GLOBALS['modname'] != '') {
	$module_cfg->loadBody();
}

//footer
if(is_file(getAbsolutePathTemplate('crm').'footer.php')) {
	include(getAbsolutePathTemplate('crm').'footer.php');
} else include($GLOBALS['where_crm'].'/templates/footer.php');

/*Save user info*/
if( $GLOBALS['current_user']->isLoggedIn() )
	$GLOBALS['current_user']->SaveInSession();

/*End database connection*************************************************/

mysql_close($GLOBALS['dbConn']);

/*Flush buffer************************************************************/

/* output all */
$GLOBALS['page']->add(ob_get_contents(), 'debug');
ob_clean();

echo $GLOBALS['page']->getContent();
ob_end_flush();

?>
