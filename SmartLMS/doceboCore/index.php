<?php

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');

if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);
define("IN_CORE", true);

/*Start buffer************************************************************/
ob_start();

require_once(dirname(__FILE__).'/config.php');
require_once($GLOBALS['where_config'].'/config.php');

/*Start database connection***********************************************/
$GLOBALS['dbConn'] = mysql_pconnect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] ) 
	die( "Can't connect to db. Check configurations" );

if( !mysql_select_db($dbname, $GLOBALS['dbConn']) ) 
	die( "Database not found. Check configurations" );

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);

require_once(dirname(__FILE__).'/setting.php');

if($GLOBALS['framework']['do_debug'] == 'on') {
	@error_reporting(E_ALL);
	@ini_set('display_errors', 1);
} else {
	@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
}

/*Start session***********************************************************/

//cookie lifetime ( valid until browser closed )
session_set_cookie_params( 0 );
//session lifetime ( max inactivity time )
ini_set('session.gc_maxlifetime', $GLOBALS['framework']['ttlSession']);

session_name( ( ($GLOBALS['framework']['common_admin_session'] == 'on') ? "docebo_session" : "docebo_core" ) );
session_start();

require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');

// load regional setting
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession( ( ($GLOBALS['framework']['common_admin_session'] == 'on') ? "public_area" : "admin_area" ) );

// Utils and so on
require_once($GLOBALS['where_framework'].'/lib/lib.php');

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();

require_once($GLOBALS['where_framework'].'/lib/lib.preoperation.php');

if($GLOBALS['modname'] == '') {
	$GLOBALS['modname'] = 'dashboard';
	$GLOBALS['op'] = 'dashboard';
	$_SESSION['current_action_platform'] = 'framework';
}

// create instance of StdPageWriter
StdPageWriter::createInstance();

if($GLOBALS['modname'] != '') {
	require_once($GLOBALS['where_framework'].'/lib/lib.istance.php');
	$module_cfg =& createModule($GLOBALS['modname']);
}

/*Site composition********************************************************/

if($GLOBALS['modname'] != '') {
	if($module_cfg->useStdHeader()) {
		
		if(is_file(getAbsolutePathTemplate('framework').'header.php')) {
			include(getAbsolutePathTemplate('framework').'header.php');
		} else include($GLOBALS['where_framework'].'/templates/header.php');		//header
	}
} else {
	if(is_file(getAbsolutePathTemplate('framework').'header.php')) {
		include(getAbsolutePathTemplate('framework').'header.php');
	} else include($GLOBALS['where_framework'].'/templates/header.php');			//header
}

require($GLOBALS['where_framework'].'/menu/menu_over.php');							//general menu

$GLOBALS['page']->setWorkingZone('content');
if($GLOBALS['modname'] != '') $module_cfg->loadBody();

if(is_file(getAbsolutePathTemplate('framework').'footer.php')) {
	include(getAbsolutePathTemplate('framework').'footer.php');
} else include($GLOBALS['where_framework'].'/templates/footer.php');

/*Save user info*/
if( $GLOBALS['current_user']->isLoggedIn() )
	$GLOBALS['current_user']->SaveInSession();

/*End database connection*************************************************/

mysql_close();

/*Flush buffer************************************************************/

//$time_end = getmicrotime();
//$GLOBALS['page']->add('time : '.($time_end - $time_start), 'footer');

$php_conf = ini_get_all();

$problem = false;

if($php_conf['register_globals']['local_value'])
	$problem = true;

if (version_compare(phpversion(), "5.2.0", ">"))
	if($php_conf['allow_url_include']['local_value'])
		$problem = true;

if($problem && $GLOBALS['current_user']->getUSerLevelId() == '/framework/level/godadmin')
	$GLOBALS['page']->addStart(getInfoUi('Configuration problem - <a href="./index.php?modname=configuration&op=config&of_platform=framework&tab=global&group_sel=server#">Check diagnostic for more information</a>'), 'content');

/* output all */
$GLOBALS['page']->add(ob_get_contents(), 'debug');
ob_clean();

echo $GLOBALS['page']->getContent();
ob_end_flush();

?>