<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');

if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);
define("IN_CORE", true);

/*Start buffer************************************************************/

ob_start();

require_once(dirname(__FILE__).'/config.php');
require_once($GLOBALS['where_config'].'/config.php');

/*Start database connection***********************************************/

$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
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


//don't use session variable with rest
//...


// load current user from session -----------------------------------------------------
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');

require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
require_once($GLOBALS['where_framework'].'/lib/lib.utils.php');
require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

//******************************************************************************

$GLOBALS['output'] = '';
function rest_cout($string) { $GLOBALS['output'] .= $string; }

require_once($GLOBALS['where_framework'].'/API/class.rest.php');


//parameters managing
define('_REST_PARAM_NAME', 'q');
define('_REST_VALIDATOR_PARAM', 'restAPI');
define('_REST_MINIMUM_PARAMS', 3);
define('_REST_APINAME_INDEX', 2);
define('_REST_APIMETHOD_INDEX', 3);

if (!isset($_GET[_REST_PARAM_NAME])) { die('Error: no input parameters.'); }

$rest_method = $_SERVER['REQUEST_METHOD'];
$rest_params = explode('/', $_GET[_REST_PARAM_NAME]);



$numparams = count( $rest_params );
if ($numparams<_REST_MINIMUM_PARAMS) { /* passed url has wrong format */ }
$last_index = $numparams-1;


//check if this is a valid call
if ($rest_params[0]!='' || $rest_params[1]!=_REST_VALIDATOR_PARAM) {
	die('Invalid request.');
}


//you may force a different REQUEST_METHOD    
$matches = array();
if (preg_match('/^(.*)!(DELETE|PUT|GET|POST|OPTIONS|HEAD|TRACE|CONNECT)$/', $rest_params[$last_index], $matches)) {
	//if ($rest_method == 'POST' && preg_match('/^(.*)!(DELETE|PUT|GET|POST|OPTIONS|HEAD|TRACE|CONNECT)$/', $rest_params[$last_index], $matches)) {
	$rest_params[$last_index] = $matches[1];
	if ($rest_method == 'POST') {
		//$rest_params[$last_index] = $matches[1];	
		$rest_method = $matches[2];
	}
}

// set the output data type (XML or JSON at the moment)
$GLOBALS['REST_API_ACCEPT']='';
$matches = array();
if (preg_match('/^(.*)\.(xml|json)$/', $rest_params[$last_index], $matches)) {
	$rest_params[$last_index] = $matches[1];
	$GLOBALS['REST_API_ACCEPT'] = $matches[2];
} else {
	$GLOBALS['REST_API_ACCEPT'] = 'xml';//$_SERVER['HTTP_ACCEPT'];
}


//set MIME type
$content_type = '';
switch ($GLOBALS['REST_API_ACCEPT']) {
	case _REST_OUTPUT_JSON: { $content_type = 'application/json'; } break;
	case _REST_OUTPUT_XML:  { $content_type = 'application/xml';  } break;
	default: {
		$GLOBALS['REST_API_ACCEPT'] = _REST_OUTPUT_XML;
		$content_type = 'application/xml';
	} break;
}
header('Content-type:'.$content_type.'; charset=utf-8');

//rest_cout('<div>'.$GLOBALS['REST_API_ACCEPT'].'</div>'); //debug ...
//******************************************************************************



$rest_obj = false;
$rest_module = $rest_params[_REST_APINAME_INDEX]; //the module specification
$rest_function = $rest_params[_REST_APIMETHOD_INDEX]; //the name of module's method to call

$api_path = $GLOBALS['where_framework'].'/API/API.'.$rest_module.'.php';  //rest_cout($api_path);
if (file_exists($api_path)) {
	require_once($api_path);
	$class_name = 'RestAPI_'.$rest_module;
	$rest_obj = new $class_name();
} else {
	//error: no API exists with the name passed
	$err_msg = 'Error: module "'.$rest_module.'" does not exists.';
	rest_cout(restAPI_HandleError($err_msg,$GLOBALS['REST_API_ACCEPT']));
}

//extract additional parameters from GET string, void and outputtype parameter should be already avoided
$i = _REST_APIMETHOD_INDEX+1;
$rest_subparams = array();
while ($i<count($rest_params)) {//$numparams) {
	$rest_subparams[] = $rest_params[$i];
	$i++;
}

//dispatch command and produce output data on $GLOBALS['output'];
if ($rest_obj !== false) {
	$rest_obj->setOutputType($GLOBALS['REST_API_ACCEPT']);
	if ($rest_obj->checkAuthentication()) {
		$rest_obj->dispatch($rest_method, $rest_function, $rest_subparams);
		rest_cout($rest_obj->getOutput());
	} else {
		$err_msg = 'Error: invalid authentication.';
		rest_cout(restAPI_HandleError($err_msg,$GLOBALS['REST_API_ACCEPT']));
	}
	
} else {
	//no api has been loaded ... output some error
	$err_msg = _REST_STANDARD_ERROR;
	rest_cout(restAPI_HandleError($err_msg,$GLOBALS['REST_API_ACCEPT']));
}

//******************************************************************************

mysql_close();

/*Flush buffer************************************************************/

//clear debug messages and clean buffer for output
$GLOBALS['debug'] = ob_get_contents(); //at the moment, debug informations are discarded
ob_clean();

echo $GLOBALS['output'];
$GLOBALS['show_debug_info']=false;//true;
if ($GLOBALS['show_debug_info']) echo restAPI_HandleDebugInfo($GLOBALS['debug']);
ob_end_flush();

?>