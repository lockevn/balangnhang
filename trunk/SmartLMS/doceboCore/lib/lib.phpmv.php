<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2007                                                    */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');
if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);

function phpmvDoceboInit() {

	$path_to_root = "..";

	require_once(dirname(__FILE__).'/'.$path_to_root.'/config.php');
	require_once($GLOBALS['where_config'].'/config.php');

	if ($GLOBALS["where_framework_relative"] != false) {
		$GLOBALS["base_where_framework_relative"]=$GLOBALS["where_framework_relative"];
		$GLOBALS["where_framework_relative"]=$path_to_root.'/'.$GLOBALS["where_framework_relative"];
	}

	/*Start buffer************************************************************/

	ob_start();

	/*Start database connection***********************************************/

	$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
	if( !$GLOBALS['dbConn'] )
		die( "Can't connect to db. Check configurations" );

	if( !mysql_select_db($GLOBALS['dbname'], $GLOBALS['dbConn']) )
		die( "Database not found. Check configurations" );


	require_once($GLOBALS['where_framework'].'/setting.php');

	$GLOBALS['do_debug'] =$GLOBALS['framework']['do_debug'];
	//$GLOBALS['user_session'] =$GLOBALS['framework']['user_session'];
	//$GLOBALS[''] =$GLOBALS['framework'][''];

	/*Start session***********************************************************/

	//cookie lifetime ( valid until browser closed )
	session_set_cookie_params( 0 );
	//session lifetime ( max inactivity time )
	ini_set('session.gc_maxlifetime', $GLOBALS['framework']['ttlSession']);

	if($GLOBALS['framework']['common_admin_session'] == 'on') {

		$sn = "docebo_session";
		$GLOBALS['user_session'] = 'public_area';
	} else {

		$sn = "docebo_core";
		$GLOBALS['user_session'] = 'admin_area';
	}
	session_name($sn);
	session_start();
unset($_SESSION["user_is_phpmv_admin"]);
}


function phpmvIsAuthorized() {

	if (!isset($_SESSION["user_is_phpmv_admin"])) {

		// load regional setting
		require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
		$GLOBALS['regset'] = new RegionalSettings();

		// load current user from session
		require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
		$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession($GLOBALS['user_session']);

		// Utils and so on
		require_once($GLOBALS['where_framework'].'/lib/lib.php');

		// create instance of StdPageWriter
		StdPageWriter::createInstance();

		$level =$GLOBALS["current_user"]->getUserLevelId();
		$is_admin =($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
		$is_god_admin =($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);

		$_SESSION["user_is_phpmv_admin"] =FALSE;

		if ($is_god_admin) {
			$_SESSION["user_is_phpmv_admin"] =TRUE;
		}
		else if ( ($is_admin) && ( ($GLOBALS['current_user']->matchUserRole('/lms/admin/stats/view')) || ($GLOBALS['current_user']->matchUserRole('/cms/admin/stats/view')) )) {
			$_SESSION["user_is_phpmv_admin"] =TRUE;
		}

	}

	return (bool)$_SESSION["user_is_phpmv_admin"];
}


function phpmvCanViewSite($site_id) {
	$res =FALSE;

	$level =$GLOBALS["current_user"]->getUserLevelId();
	$is_admin =($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
	$is_god_admin =($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);

	if ($is_god_admin) {
		$res =TRUE;
	}

	if ($is_admin) {

		switch ($site_id) {
			case 1: {
					$res =$GLOBALS['current_user']->matchUserRole('/cms/admin/stats/view');
			} break;
			case 2: {
					$res =$GLOBALS['current_user']->matchUserRole('/lms/admin/stats/view');
			} break;
		}
	}

	return $res;
}


function phpmvDoceboClose() {
	mysql_close($GLOBALS['dbConn']);

	/*Flush buffer************************************************************/

	/* output all */
	//$GLOBALS['page']->add(ob_get_contents(), 'debug');
	//ob_clean();

	//echo $GLOBALS['page']->getContent();
	ob_end_flush();
}



?>
