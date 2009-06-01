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

// load platform manager
require_once($GLOBALS['where_framework']."/lib/lib.platform.php");

// load regional setting
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('cms');

// Utils and so on
require($GLOBALS['where_cms'].'/lib/lib.php');

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();

// ----------------------------------------------------------------------------


require_once($GLOBALS['where_framework'].'/lib/lib.rss.php');
$fg=new FeedGenerator();

$fg->setAllowDebug(FALSE);
header('Content-Type: text/xml');
echo($fg->writeFeed());


// ----------------------------------------------------------------------------
/*End database connection*************************************************/
mysql_close($GLOBALS['dbConn']);
?>
