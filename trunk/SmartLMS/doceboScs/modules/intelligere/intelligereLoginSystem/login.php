<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package intelligere
 * @subpackage intelligere login sistem
 * @category ajax server
 * @version $Id:$
 *
 */

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');
@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
define("IN_DOCEBO", true);

$path_to_root = '../../../..';

// prepare refer ------------------------------------------------------------------

require_once(dirname(__FILE__).'/'.$path_to_root.'/config.php');
require_once(dirname(__FILE__).'/'.$path_to_root.'/DoceboScs/config.php');
require_once($GLOBALS['where_config'].'/config.php');

if ($GLOBALS["where_cms_relative"] != false)
	$GLOBALS["where_cms_relative"]=$path_to_root.'/'.$GLOBALS["where_cms_relative"];

//if ($GLOBALS["where_kms_relative"] != false)
//	$GLOBALS["where_kms_relative"]=$path_to_root.'/'.$GLOBALS["where_kms_relative"];

if ($GLOBALS["where_lms_relative"] != false)
	$GLOBALS["where_lms_relative"]=$path_to_root.'/'.$GLOBALS["where_lms_relative"];

if ($GLOBALS["where_framework_relative"] != false)
	$GLOBALS["where_framework_relative"]=$path_to_root.'/'.$GLOBALS["where_framework_relative"];

if ($GLOBALS["where_files_relative"] != false) {
	$GLOBALS["where_files_relative"]=$path_to_root.'/'.$GLOBALS["where_files_relative"];
}
ob_start();

// connect to database -------------------------------------------------------------------

$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] )
	die( "Can't connect to db. Check configurations" );

if( !mysql_select_db($dbname, $GLOBALS['dbConn']) )
	die( "Database not found. Check configurations" );

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);

// load setting ------------------------------------------------------------------
require_once($GLOBALS['where_framework'].'/setting.php');
require_once($GLOBALS['where_scs'].'/setting.php');

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
require_once($GLOBALS['where_framework'].'/lib/lib.donotdo.php');

// security check --------------------------------------------------------------------

chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

$GLOBALS['operation_result'] = '';
function docebo_cout($string) { $GLOBALS['operation_result'] .= $string; }

// here all the specific code ==========================================================
require_once(dirname(__FILE__).'/'.$path_to_root.'/DoceboScs/lib/lib.intelligere.php');
include("xml_class.php");
	
	$isLogged = "no";
	
	$message = $login = isset($_GET['message']) ?  urlencode(str_replace(" ","%20",$_GET['message'])  )  : '';;
	
	$login = isset($_GET['login']) ?  urlencode(str_replace(" ","%20",$_GET['login'])  )  : '';
	$password = isset($_GET['password']) ? urlencode(str_replace(" ","%20",$_GET['password'])) : '';
	
	$conference = new IntelligereManager();
	
	$id_user = getLogUserId();
	//die ($login.' - '.$password.' - '.$id_user);
	$xmlFileName = 'users.xml';
	
	$xml = new XMLFile();
	$fh = fopen( $xmlFileName, 'r' );
	$xml->read_file_handle( $fh );
	fclose($fh);
	
	$root = &$xml->roottag;
	$trovato = false;
	
	/*for ($i= 0; $i< $root->num_subtags(); $i++)
	{
		if($root->tags[$i]->attributes["login"] == $login && $root->tags[$i]->attributes["password"] == $password)
		{
			$res = "<user status = 'logged' username = '" . $root->tags[$i]->attributes["login"] . "' role = '" . $root->tags[$i]->attributes["role"] . "' message = '" . $message . "' />";
			$isLogged = "yes";
			echo($res);
			break;
		}
	}*/
	
	if($conference->controllToken($id_user, $password))
	{
		$res = "<user status = 'logged' username = '" . $login . "' role = '" . $conference->readRole($id_user, $password) . "' message = '" . $message . "' />";
		$isLogged = "yes";
		echo($res);
	}

	if($isLogged == "no")
		echo("<user status = 'notLogged' username = '" . $login . "' />");

	$xml->cleanup();

// =====================================================================================

// close database connection

mysql_close($GLOBALS['dbConn']);

ob_end_flush();

?>

