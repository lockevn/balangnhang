<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2003-2008                                              */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License or, at */
/* your opinion, any later version. 									*/
/*                                                                      */
/************************************************************************/

//framework position
$GLOBALS['where_framework_relative'] = './doceboCore';
$GLOBALS['where_framework'] = dirname(__FILE__).'/doceboCore';

//lms position
$GLOBALS['where_lms_relative'] = './doceboLms';
$GLOBALS['where_lms'] = dirname(__FILE__).'/doceboLms';

//cms position
$GLOBALS['where_cms_relative'] = './doceboCms';
$GLOBALS['where_cms'] = dirname(__FILE__).'/doceboCms';

//crm position
$GLOBALS['where_crm_relative'] = './doceboKms';
$GLOBALS['where_crm'] = dirname(__FILE__).'/doceboCrm';

//ecom position
$GLOBALS['where_ecom_relative'] = './doceboEcom';
$GLOBALS['where_ecom'] = dirname(__FILE__).'/doceboEcom';

//scs position
$GLOBALS['where_scs_relative'] = './doceboScs';
$GLOBALS['where_scs'] = dirname(__FILE__).'/doceboScs';

// file save position
$GLOBALS['where_files_relative'] = './files';

// config with db info position
$GLOBALS['where_config'] = dirname(__FILE__);

/*Information needed for database access**********************************/

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');

define("IN_DOCEBO", true);

$GLOBALS['platform'] = 'lms';
$GLOBALS['base_url'] = 'http' . ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '' ).'://' .$_SERVER['HTTP_HOST'];
if($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) $GLOBALS['base_url'] = '/'.$dir;

//get config with position of the others application
require(dirname(__FILE__).'/config.php');


/*Start buffer************************************************************/

ob_start();

/*Start database connection***********************************************/

$GLOBALS['dbConn'] = @mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] ) {
	
	if(file_exists(dirname(__FILE__).'/install/index.php')) {
		
		Header('Location: http://'.$_SERVER['HTTP_HOST']
    		.( strlen(dirname($_SERVER['SCRIPT_NAME'])) != 1 ? dirname($_SERVER['SCRIPT_NAME']) : '' )
			.'/install/');
	}
	die( "Can't connect to db. Check configurations" );
}

if( !@mysql_select_db($dbname, $GLOBALS['dbConn']) ) {
	
	if(file_exists(dirname(__FILE__).'/install/index.php')) {
		
		Header('Location: http://'.$_SERVER['HTTP_HOST']
    		.( strlen(dirname($_SERVER['SCRIPT_NAME'])) != 1 ? dirname($_SERVER['SCRIPT_NAME']) : '' )
			.'/install/');
	}
	die( "Database not found. Check configurations" );
}

require_once($GLOBALS['where_framework'].'/setting.php');
require_once($GLOBALS['where_lms'].'/setting.php');

if($GLOBALS['framework']['do_debug'] == 'on') {
	@error_reporting(E_ALL);
	@ini_set('display_errors', 1);
} else {
	@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
}

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);

$query_platform = "
SELECT platform  
FROM ".$GLOBALS['prefix_fw']."_platform 
WHERE main = 'true'
LIMIT 0, 1";
list($sel) = mysql_fetch_row(mysql_query($query_platform));

if($sel == 'cms') {
	Header('Location: http://'.$_SERVER['HTTP_HOST']
	    	.( strlen(dirname($_SERVER['SCRIPT_NAME'])) != 1 ? dirname($_SERVER['SCRIPT_NAME']) : '' )
			.'/doceboCms/');
    exit();
}
/*Start session***********************************************************/

//cookie lifetime
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

if($GLOBALS['current_user']->isLoggedIn()) {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.utils.php');
	jumpTo('./doceboLms/');
}

// Utils and so on
require($GLOBALS['where_lms'].'/lib/lib.php');

// special operation
if(isset($_GET['special'])) {
	
	switch($_GET['special']) {
		case "changelang" : {
			setLanguage(importVar('new_lang'));
			$_SESSION['changed_lang'] = true;
		};break;
	}
}

//require_once($GLOBALS['where_lms'].'/lib/lib.preoperation.php');

$modname = importVar('modname');
$op = importVar('op');

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();

$module_cfg = false;
if(isset($GLOBALS['modname']) && $GLOBALS['modname'] != '') {

	//create the class for management the called module
	$module_cfg =& createModule($GLOBALS['modname']);
}

// create instance of LmsPageWriter
require_once($GLOBALS['where_framework'].'/lib/lib.pagewriter.php');
emptyPageWriter::createInstance();
//include($GLOBALS['where_lms'].'/templates/header.php');
//include($GLOBALS['where_lms'].'/templates/footer.php');

$template = 'home';
if($op == '') $op = 'login';
switch ($op) {
	
	case 'login': {
		
		$template = 'home_login';
		
	};break;
	default: {
				
		$module_cfg->loadBody();
	};break;
}

/*Save user info*/
if( $GLOBALS['current_user']->isLoggedIn() )
	$GLOBALS['current_user']->SaveInSession();

/*End database connection*************************************************/

/*Add google stat code if used*********************************************/

if (($GLOBALS['google_stat_in_lms'] == 1) && (!empty($GLOBALS['google_stat_code']))) {
	$GLOBALS['page']->addEnd($GLOBALS['google_stat_code'], 'footer');
}

/*Flush buffer************************************************************/

/* output all */

$GLOBALS['page']->add(ob_get_contents(), 'debug');
ob_clean();
//echo $GLOBALS['page']->getContent();

require_once($GLOBALS['where_framework'].'/lib/lib.layout.php');

$tmpl = parseTemplateDomain($_SERVER['HTTP_HOST']);
echo Layout::parse_template(dirname(__FILE__).'/template'.( $tmpl ? '_'.$tmpl : '' ).'/'.$template.'.html');

mysql_close($GLOBALS['dbConn']);

ob_end_flush();

?>
