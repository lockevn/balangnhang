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
error_reporting(E_ALL ^ E_NOTICE); 
if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);
require_once(dirname(__FILE__)."/header.php");
// check for remote file inclusion attempt -------------------------------
$list = array('GLOBALS', '_POST', '_GET', '_COOKIE', '_SESSION'); 
while(list(, $elem) = each($list)) {
		
	if(isset($_REQUEST[$elem])) die('Request overwrite attempt detected');
}

// ------------------------------------------------------------------------

$script = "
<script type=\"text/javascript\">
	<!--
	function refreshPage() {
		
		window.location.reload( false );
	}
	window.setTimeout('refreshPage()',30000);
	// -->
</script>";
$out->add($script, "page_head");

$op = importVar('op');
if(empty($op)) $op = "rooms";

switch ($op) {
	case "setroom": {
		setRoom($out, $lang);
	} break;
	case "rooms": 
	default: {
		$out->add(listRooms($out, $lang));
	} break;
}

// -------------------------------------------------------------------
require_once(dirname(__FILE__)."/footer.php");
// -------------------------------------------------------------------

?>