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
 * @package course management
 * @subpackage course catalogue
 * @category ajax server
 * @version $Id:$
 *
 */

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');

if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);

$path_to_root = '../../..';

// prepare refer ------------------------------------------------------------------

require_once(dirname(__FILE__).'/'.$path_to_root.'/config.php');

require_once($GLOBALS['where_config'].'/config.php');
/*
if ($GLOBALS["where_cms_relative"] != false)
	$GLOBALS["where_cms_relative"]=$path_to_root.'/'.$GLOBALS["where_cms_relative"];

if ($GLOBALS["where_kms_relative"] != false)
	$GLOBALS["where_kms_relative"]=$path_to_root.'/'.$GLOBALS["where_kms_relative"];

if ($GLOBALS["where_lms_relative"] != false)
	$GLOBALS["where_lms_relative"]=$path_to_root.'/'.$GLOBALS["where_lms_relative"];

if ($GLOBALS["where_framework_relative"] != false)
	$GLOBALS["where_framework_relative"]=$path_to_root.'/'.$GLOBALS["where_framework_relative"];

*/
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

// load lms setting ------------------------------------------------------------------
require_once($GLOBALS['where_framework'].'/setting.php');
require_once($GLOBALS['where_lms'].'/setting.php');

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

// security check --------------------------------------------------------------------

chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

$GLOBALS['operation_result'] = '';
function docebo_cout($string) { $GLOBALS['operation_result'] .= $string; }

// here all the specific code ==========================================================

$op = importVar('op');

switch($op) {

	case "save_new_conf" : {

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance("company", "framework");

		$corecompany_man = new CoreCompanyManager();

		$field_assigned = $corecompany_man->getFieldInfoAssignedToCompany();

		if(function_exists("mysql_real_escape_string"))	$new_conf = mysql_real_escape_string( importVar('new_conf') );
		else $new_conf = mysql_escape_string( importVar('new_conf') );

		switch($new_conf) {
			case "code" 		: {
				$new_conf_name = $lang->def('_IDREF_DEFAULT_CODE');
			};break;
			case "vat_number" 	: {
				$new_conf_name= $lang->def('_IDREF_VAT_NUMBER');
			};break;
			default : {
				$new_conf_name = str_replace('[field_name]', $field_assigned[$new_conf][2], $lang->def('_IDREF_EXTRA_FIELD'));
			}
		}

		if($corecompany_man->setIdrefCode( $new_conf )) {

			$value = array(
				'result' => true,
				'result_phrase' => $lang->def('_OK_MOD_COMPANY_CODE'),
				'new_conf_name' => $new_conf_name );
		} else {

			$value = array(
				'result' => false,
				'result_phrase' => $lang->def('_ERR_MOD_COMPANY_CODE'),
				'new_conf_name' => '' );
		}

		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	};break;
}

// =====================================================================================

// close database connection

mysql_close($GLOBALS['dbConn']);

ob_clean();
echo $GLOBALS['operation_result'];
ob_end_flush();

?>
