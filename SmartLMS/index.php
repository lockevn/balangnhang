<?php

// INFO: LockeVN: thiết lập các thông số về đường dẫn và file cho các module
// phần này dần dần chỉnh thành hằng số trong Class là yên tâm, hoặc dùng kỹ thuật ABSPATH như qAPI

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

$GLOBALS['dbConn'] = @mysql_pconnect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] ) {
	// INFO: không tìm thấy DB, chuyển sang phần Install, mình sẽ gỡ bỏ phần này ra cho đỡ chậm. Install này là ko cần thiết.
	
	// HACKED LockeVN comment
//	if(file_exists(dirname(__FILE__).'/install/index.php')) {
//		
//		Header('Location: http://'.$_SERVER['HTTP_HOST']
//			.( strlen(dirname($_SERVER['SCRIPT_NAME'])) != 1 ? dirname($_SERVER['SCRIPT_NAME']) : '' )
//			.'/install/');
//	}
	// END HACKED
	
	die( "Can't connect to dbhost [" .$GLOBALS['dbhost']. "]. Check configurations" );
}

if( !@mysql_select_db($dbname, $GLOBALS['dbConn']) ) {
	die( "Database [$dbname] not found. Check configurations" );
}

// INFO: LockeVN: load setting, file setting này nạp các dữ liệu trong bảng _setting của từng module
require_once($GLOBALS['where_framework'].'/setting.php');
require_once($GLOBALS['where_lms'].'/setting.php');



if($GLOBALS['framework']['do_debug'] == 'on') {
	@error_reporting(E_ALL);
	@ini_set('display_errors', 1);
} else {
	@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
}


// INFO: LockeVN: chuyển charset của Mysql sang dạng theo như cấu hình, tuy nhiên phần này có thể bỏ quả để tăng tốc độ
// trong my.ini có thể setup sẵn để hỗ trợ tốt tiếng Việt
@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);


// INFO: LockeVN: tìm module chính, nếu là CMS, redirect sang
$query_platform = "SELECT platform  FROM ".$GLOBALS['prefix_fw']."_platform WHERE main = 'true' LIMIT 0, 1";
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
// INFO: LockeVN: nạp lớp quản lý các thiết lập hiển thị theo vùng miền, kiểu hiển thị ngày giờ, ...
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();



// INFO: AUTHENTICATION SECURITY
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


// INFO: Dựng trang, mã dựng trang khá lằng nhằng.

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
	};
	break;
	
	default: {				
		$module_cfg->loadBody();
	};
	break;
}

/*Save user info*/
if( $GLOBALS['current_user']->isLoggedIn() )
{
	$GLOBALS['current_user']->SaveInSession();
}

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