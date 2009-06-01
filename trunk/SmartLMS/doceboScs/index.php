<?php

/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2007													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

define("IN_DOCEBO", true);

ob_start();

session_name("docebo_video_conference");
session_start();

// check for remote file inclusion attempt -------------------------------
$list = array('GLOBALS', '_POST', '_GET', '_COOKIE', '_SESSION'); 
while(list(, $elem) = each($list)) {
		
	if(isset($_REQUEST[$elem])) die('Request overwrite attempt detected');
}

//get config with position of the others application
require(dirname(__FILE__).'/config.php');
require($GLOBALS['where_config'].'/config.php');

require_once($GLOBALS['where_scs'].'/lib/lib.php');

adapt_input_data($_GET);
adapt_input_data($_POST);
adapt_input_data($_COOKIE);

// first db connection
$db 	=& DbConn::getInstance();
$html 	=& DbPurifier::getInstance();

	
echo Layout::parse_template(getLayoutForRoom($_SESSION['id_room']));

function _getRoomTable() { return $GLOBALS['prefix_scs'].'_vc_room'; }

function getLayoutForRoom($id_room) {
	$query = "SELECT layout" .
			" FROM "._getRoomTable()."" .
			" WHERE id_room = '".$id_room."'";
	
	$result = mysql_query($query);
	$num_rows = mysql_num_rows($result);
	
	if ($num_rows) {
		list($layout) = mysql_fetch_row($result);
		return $layout;
	}
	return 'second_scheme';
}

$db->close();

ob_end_flush();

?>