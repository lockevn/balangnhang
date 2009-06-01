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

/*Start buffer************************************************************/

ob_start();

//get config with position of the others application
require(dirname(__FILE__).'/config.php');

require($GLOBALS['where_config'].'/config.php');

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

if($GLOBALS['framework']['do_debug'] == 'on') {
	@error_reporting(E_ALL);
	@ini_set('display_errors', 1);
} else {
	@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
}

/*Start session***********************************************************/

//cookie lifetime
//session_cache_limiter('public');
session_set_cookie_params( 0 );
//session lifetime ( max inactivity time )
ini_set('session.gc_maxlifetime', $GLOBALS['lms']['ttlSession']);

session_name("docebo_session");
session_start();

// load regional setting
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');

// Utils and so on
require($GLOBALS['where_lms'].'/lib/lib.php');

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();

require_once($GLOBALS['where_lms'].'/lib/lib.preoperation.php');

$module_cfg = false;
if(isset($GLOBALS['modname']) && $GLOBALS['modname'] != '') {

	//create the class for management the called module
	$module_cfg =& createModule($GLOBALS['modname']);
}

if(method_exists($module_cfg, 'beforeLoad')) $module_cfg->beforeLoad();

// create instance of LmsPageWriter
if(isset($_GET['no_redirect']) || isset($_POST['no_redirect'])) {

	require_once($GLOBALS['where_framework'].'/lib/lib.pagewriter.php');
	onecolPageWriter::createInstance();
} elseif(!isset($_SESSION['idCourse']) && !$GLOBALS['current_user']->isAnonymous()) {

	require_once($GLOBALS['where_framework'].'/lib/lib.pagewriter.php');
	onecolPageWriter::createInstance();
} elseif($module_cfg !== false && $module_cfg->hideLateralMenu()) {

	require_once($GLOBALS['where_framework'].'/lib/lib.pagewriter.php');
	onecolPageWriter::createInstance();
} else {

	require_once($GLOBALS['where_lms'].'/lib/lib.lmspagewriter.php');
	LmsPageWriter::createInstance();
}

//header
if($module_cfg !== false) {
	if($module_cfg->useStdHeader()) {

		if(is_file(getAbsolutePathTemplate('lms').'header.php')) {
			include(getAbsolutePathTemplate('lms').'header.php');
		} else include($GLOBALS['where_lms'].'/templates/header.php');
	}
} else {
	if(is_file(getAbsolutePathTemplate('lms').'header.php')) {
		include(getAbsolutePathTemplate('lms').'header.php');
	} else include($GLOBALS['where_lms'].'/templates/header.php');
}
if($module_cfg !== false && $module_cfg->hideLateralMenu()) {

	require($GLOBALS['where_lms'].'/menu/menu_over.php');
} else {

	if(!$GLOBALS['current_user']->isAnonymous()) {

		require($GLOBALS['where_lms'].'/menu/menu_over.php');
		if(isset($_SESSION['idCourse'])) {
			require($GLOBALS['where_lms'].'/menu/menu_lat.php');
		}
	} else {

		require($GLOBALS['where_lms'].'/menu/menu_login.php');
	}
}
// load module body
if(isset($GLOBALS['modname']) && $GLOBALS['modname'] != '') {
	$module_cfg->loadBody();
}

//footer
if(is_file(getAbsolutePathTemplate('lms').'footer.php')) {
	include(getAbsolutePathTemplate('lms').'footer.php');
} else include($GLOBALS['where_lms'].'/templates/footer.php');

/*Save user info*/
if( $GLOBALS['current_user']->isLoggedIn() )
	$GLOBALS['current_user']->SaveInSession();

/*End database connection*************************************************/

mysql_close($GLOBALS['dbConn']);

/*Add google stat code if used*********************************************/

if (($GLOBALS['google_stat_in_lms'] == 1) && (!empty($GLOBALS['google_stat_code']))) {
	$GLOBALS['page']->addEnd($GLOBALS['google_stat_code'], 'footer');
}

/*Flush buffer************************************************************/

/* output all */
$GLOBALS['page']->add(ob_get_contents(), 'debug');
ob_clean();
echo $GLOBALS['page']->getContent();

ob_end_flush();

?>
