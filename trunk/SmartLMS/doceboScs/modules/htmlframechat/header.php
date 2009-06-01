<?php

/*************************************************************************/
/* DOCEBO FRAMEWORK                                                      */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <giovanni[AT]docebo-com>         */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

define("_REFRESH_RATE","2");

$path_to_root = "../..";

require_once(dirname(__FILE__).'/'.$path_to_root.'/config.php');
require_once($GLOBALS['where_config'].'/config.php');

if ($GLOBALS["where_cms_relative"] != false)
	$GLOBALS["where_cms_relative"]=$path_to_root.'/'.$GLOBALS["where_cms_relative"];

if ($GLOBALS["where_lms_relative"] != false)
	$GLOBALS["where_lms_relative"]=$path_to_root.'/'.$GLOBALS["where_lms_relative"];

if ($GLOBALS["where_framework_relative"] != false)
	$GLOBALS["where_framework_relative"]=$path_to_root.'/'.$GLOBALS["where_framework_relative"];

if ($GLOBALS["where_files_relative"] != false)
	$GLOBALS["where_files_relative_popup"]=$path_to_root.'/'.$GLOBALS["where_files_relative"];


if ((isset($_GET["sn"])) && ($_GET["sn"] != ""))
	$GLOBALS['platform']=$_GET["sn"];
else
	$GLOBALS['platform']="framework";


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

/*Start session***********************************************************/

//cookie lifetime ( valid until browser closed )
session_set_cookie_params( 0 );
//session lifetime ( max inactivity time )
ini_set('session.gc_maxlifetime', $GLOBALS['ttlSession']);

switch ($GLOBALS['platform']) {

	case "lms":
	case "cms":
	case "kms": {
		$sn = "docebo_session";
		$user_session = 'public_area';
	} break;

	case "framework":
	default: {
		if($GLOBALS['framework']['common_admin_session'] == 'on') {

			$sn = "docebo_session";
			$user_session = 'public_area';
		} else {

			$sn = "docebo_core";
			$user_session = 'admin_area';
		}
	} break;
}
session_name($sn);
session_start();

// load regional setting
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession($user_session);

// Utils and so on
require_once($GLOBALS['where_framework'].'/lib/lib.php');

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();

require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');

// create instance of StdPageWriter
StdPageWriter::createInstance();

require_once($GLOBALS['where_framework'].'/lib/lib.preoperation.php');


$GLOBALS["template_path"]=$path_to_root.'/'.getPathTemplate('scs').'tools/htmlframechat/';
$GLOBALS["img_path"]=$path_to_root.'/'.getPathTemplate('scs').'tools/htmlframechat/';


$GLOBALS['page']->add(
		'<link href="'.$GLOBALS["template_path"].'style_chat.css" rel="stylesheet" type="text/css" />'."\n",
		'page_head');

$out=& $GLOBALS["page"];
$out->setWorkingZone("content");
$lang=& DoceboLanguage::createInstance('htmlframechat', 'scs');

require_once($GLOBALS["where_scs"].'/lib/lib.html_chat_common.php');
require_once(dirname(__FILE__).'/functions.php');

$GLOBALS["chat_emo"]=new HtmlChatEmoticons_FrameChat();

?>
