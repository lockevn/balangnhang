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
require($GLOBALS['where_framework'].'/lib/lib.utils.php');

$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] )
	die( "Can't connect to db. Check configurations" );

if( !mysql_select_db($dbname, $GLOBALS['dbConn']) )
	die( "Database not found. Check configurations" );

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);



$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner WHERE banner_id='".(int)$_GET["id"]."';";
$q=mysql_query($qtxt);

if (($q) && (mysql_num_rows($q) > 0)) {
	$row=mysql_fetch_array($q);

	$url=$row["banurl"];
	$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_banner SET click=click+1 WHERE banner_id='".(int)$_GET["id"]."';";
	$q=mysql_query($qtxt);

	header("location: ".fillSiteBaseUrlTag($url));
}

?>