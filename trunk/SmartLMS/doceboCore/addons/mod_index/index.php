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

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');
if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);

if (!defined("POPUP_MOD_NAME"))
	die();


$path_to_root = "../..";

require_once(dirname(__FILE__).'/'.$path_to_root.'/config.php');
require_once($GLOBALS['where_config'].'/config.php');

if ($GLOBALS["where_cms_relative"] != false) {
	$GLOBALS["base_where_cms_relative"]=$GLOBALS["where_cms_relative"];
	$GLOBALS["where_cms_relative"]=$path_to_root.'/'.$GLOBALS["where_cms_relative"];
}

if ($GLOBALS["where_kms_relative"] != false) {
	$GLOBALS["base_where_kms_relative"]=$GLOBALS["where_kms_relative"];
	$GLOBALS["where_kms_relative"]=$path_to_root.'/'.$GLOBALS["where_kms_relative"];
}

if ($GLOBALS["where_lms_relative"] != false) {
	$GLOBALS["base_where_lms_relative"]=$GLOBALS["where_lms_relative"];
	$GLOBALS["where_lms_relative"]=$path_to_root.'/'.$GLOBALS["where_lms_relative"];
}

if ($GLOBALS["where_framework_relative"] != false) {
	$GLOBALS["base_where_framework_relative"]=$GLOBALS["where_framework_relative"];
	$GLOBALS["where_framework_relative"]=$path_to_root.'/'.$GLOBALS["where_framework_relative"];
}

if ($GLOBALS["where_ecom_relative"] != false) {
	$GLOBALS["base_where_ecom_relative"]=$GLOBALS["where_ecom_relative"];
	$GLOBALS["where_ecom_relative"]=$path_to_root.'/'.$GLOBALS["where_ecom_relative"];
}

if ($GLOBALS["where_files_relative"] != false) {
	$GLOBALS["base_where_files_relative"]=$GLOBALS["where_files_relative"];
	$GLOBALS["where_files_relative"]=$path_to_root.'/'.$GLOBALS["where_files_relative"];
}

if ((isset($_GET["sn"])) && ($_GET["sn"] != ""))
	$GLOBALS['platform']=$_GET["sn"];
else
	$GLOBALS['platform']='lms';

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
require_once($GLOBALS['where_framework'].'/lib/lib.template.php');

// create instance of StdPageWriter
StdPageWriter::createInstance();

require_once($GLOBALS['where_framework'].'/lib/lib.preoperation.php');


// ------------------------------
$GLOBALS['page']->add(
		'<link href="'.$GLOBALS["where_framework_relative"].'/templates/'.getTemplate().'/style/style.css" rel="stylesheet" type="text/css" />'."\n"
		.'<link href="'.$GLOBALS["where_framework_relative"].'/templates/'.getTemplate().'/style/style_form.css" rel="stylesheet" type="text/css" />'."\n",
		'page_head');

addCss("style_table");


$abs_style_mod_index =$GLOBALS["where_framework"].'/templates/'.getTemplate().'/style/style_mod_index.css';

if (file_exists($abs_style_mod_index)) {
	$style_mod_index =$GLOBALS["where_framework_relative"].'/templates/'.getTemplate().'/style/style_mod_index.css';
}
else {
	$style_mod_index =$GLOBALS["where_framework_relative"].'/templates/'.getTemplate('framework').'/style/style_mod_index.css';
}// echo $GLOBALS["where_framework_relative"]." - ".$style_mod_index; die();

$GLOBALS['page']->add(
		'<link href="'.$style_mod_index.'" rel="stylesheet" type="text/css" />'."\n",
		'page_head');

// ------------------------------


/*Site composition********************************************************/
/* if($GLOBALS['modname'] != '') {
	if($module_cfg->useStdHeader()) {
		include(dirname(__FILE__).'/templates/header.php');					//header
	}
} else {
	include(dirname(__FILE__).'/templates/header.php');						//header
}

require(dirname(__FILE__).'/menu/menu_over.php');							//general menu
require(dirname(__FILE__).'/menu/menu_lat.php');							//lateral menu

$GLOBALS['page']->setWorkingZone('content');
if($GLOBALS['modname'] != '') $module_cfg->loadBody();

include(dirname(__FILE__).'/templates/footer.php');	//footer */

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance('popup_'.POPUP_MOD_NAME, 'framework');
	$out->add(getTitleArea($lang->def("_AREA_".strtoupper(POPUP_MOD_NAME)), strtolower(POPUP_MOD_NAME)));

	$out->add("<div class=\"std_block\">\n");
	require_once("../".POPUP_MOD_NAME."/body.php");
	$out->add("</div>\n");



/*Save user info*/
if( $GLOBALS['current_user']->isLoggedIn() )
	$GLOBALS['current_user']->SaveInSession();

/*End database connection*************************************************/

mysql_close();

/*Flush buffer************************************************************/



/* output all */
$GLOBALS['page']->add(ob_get_contents(), 'debug');
ob_clean();

echo $GLOBALS['page']->getContent();
ob_end_flush();










// Page functions:


function drawMenu($menu_label, $menu_url, $sel="") {

	if (is_array($menu_label)) {

		$GLOBALS['page']->add("<div class=\"popup_menu\"><ul>\n", "content");

		foreach($menu_label as $key=>$val) {

			if ($sel == $key)
				$class="class=\"selected\" ";
			else
				$class="";

			$GLOBALS['page']->add("<li><a ".$class."href=\"".$menu_url[$key]."\">".$val."</a></li>\n", "content");
		}

		$GLOBALS['page']->add("</ul></div>\n", "content");
	}
}


function getPopupBaseUrl() {

	return basename($_SERVER["SCRIPT_NAME"])."?sn=".$GLOBALS['platform'];

}




?>