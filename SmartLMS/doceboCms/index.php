<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');
if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);


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
require_once($GLOBALS['where_cms'].'/setting.php');

if($GLOBALS['framework']['do_debug'] == 'on') {
	@error_reporting(E_ALL);
	@ini_set('display_errors', 1);
} else {
	@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
}

/*Start session***********************************************************/

//cookie lifetime
session_set_cookie_params( 0 );
//session lifetime ( max inactivity time )
ini_set('session.gc_maxlifetime', $GLOBALS['cms']['ttlSession']);

session_name("docebo_session");
session_start();

// load platform manager
require_once($GLOBALS['where_framework']."/lib/lib.platform.php");

// load regional setting
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');

$GLOBALS['current_user']->saveUserSectionSTInSession("/cms/");

//$_SESSION['sesUser'] = getLogUserId();


// Utils and so on
require($GLOBALS['where_cms'].'/lib/lib.php');
require_once($GLOBALS['where_cms'].'/lib/lib.preoperation.php');

// Set cms title, keywords and description..
getPageId();
setPageLanguage();
setPageTemplate();
// ----------------------------------------

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();


// create instance of onecolPageWriter
onecolPageWriter::createInstance();

require_once($GLOBALS['where_cms'].'/lib/lib.reloadperm.php');
if (checkCmsReloadPerm())
	reloadCmsPerm();

$query =	"SELECT COUNT(*)"
			." FROM ".$GLOBALS["prefix_cms"]."_area"
			." WHERE last_modify > '".$_SESSION['user_enter_time']."'";

list($control) = mysql_fetch_row(mysql_query($query));

if($control)
{
	reloadCmsPerm();
	
	$GLOBALS['current_user']->load_user_role();
	
	$_SESSION['user_enter_time'] = date('Y-m-d H:i:s');
}

require_once($GLOBALS['where_cms'].'/lib/lib.autopublish.php');
$ap=new CmsAutoPublish();


//header
/* if(isset($modname) && $modname != '') {
	if($module_cfg->useStdHeader()) include(dirname(__FILE__).'/templates/header.php');
} else {
	include(dirname(__FILE__).'/templates/header.php');
} */


include($GLOBALS['where_cms'].'/templates/header.php');

if (defined("POPUP_MODE")) {
	if ((isset($_GET["close_popup"])) && ($_GET["close_popup"] == 1)) {
		$code ='<script type="text/javascript">window.close();</script>';
		$GLOBALS["page"]->add($code, "page_head");
	}

	include($GLOBALS['where_cms'].'/lib/lib.compose_popup.php');
}
else {
	include($GLOBALS['where_cms'].'/lib/lib.compose.php');
}


if (defined("POPUP_MODE")) {
	$footer =" ";
	$GLOBALS["page"]->add($footer, "footer");
}
else {
	//footer
	include($GLOBALS['where_cms'].'/templates/footer.php');
}

/*Save user info*/
if( $GLOBALS['current_user']->isLoggedIn() )
	$GLOBALS['current_user']->SaveInSession();

/*
$totaltime = getmicrotime() - $time_start;
echo ($totaltime)." ms";
$cpuload="N/A";
echo " load: ";
	if (@file_exists('/proc/loadavg')) {
		if ($load = @file('/proc/loadavg')) {
			list($cpuload) = explode(' ', $load[0]);
		}
	}
echo $cpuload; */

/*End database connection*************************************************/

mysql_close($GLOBALS['dbConn']);

/*Add google stat code if used*********************************************/

if (($GLOBALS['google_stat_in_cms'] == 1) && (!empty($GLOBALS['google_stat_code']))) {
	$GLOBALS['page']->addEnd($GLOBALS['google_stat_code'], 'footer');
}

/*Flush buffer************************************************************/

/* output all */
$GLOBALS['page']->add(ob_get_contents(), 'debug');
ob_clean();

echo $GLOBALS['page']->getContent();
ob_end_flush(); //-debug-// print_r($_SESSION);


/* Includes the counter************************************************************/
if (($GLOBALS['cms']['use_bbclone'] == 1) && (defined("COUNTER")) && (is_readable(COUNTER))) {
	require_once(COUNTER); // The "COUNTER" constant has been defined in lib.area.php
}
?>
