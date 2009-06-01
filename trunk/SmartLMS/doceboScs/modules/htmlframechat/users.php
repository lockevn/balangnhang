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
// check for remote file inclusion attempt -------------------------------
$list = array('GLOBALS', '_POST', '_GET', '_COOKIE', '_SESSION'); 
while(list(, $elem) = each($list)) {
		
	if(isset($_REQUEST[$elem])) die('Request overwrite attempt detected');
}

// -------------------------------------------------------------------
require_once(dirname(__FILE__)."/header.php");

/*$last_msg_id=(int)importVar("lmi");
$getnew=haveNewMsg($last_msg_id); */

//$script.="parent.chatText.setTimeout('refreshPage()',1000);\n";
//$script.="parent.chatText.document.write('refreshPage');\n";

/*if (($last_msg_id > 0) && (count($txt_arr) > 0)) {
	foreach ($txt_arr as $key=>$val) {
		$script.="parent.chatText.appendMsg('".addslashes($val["text"])."');\n";
	}
}*/
$script ="
	<script type=\"text/javascript\">
	<!--

		function refreshPage() {".
/*			document.location.href='".getPopupBaseUrl()."'; */			
		"window.location.reload( false );\n".
		"}

		window.setTimeout('refreshPage()',10000);

	//-->
	</script>";

$out->add($script, "page_head");
//$out->add(date("H:i:s", time()), "content");
$out->add(listUsers($out, $lang), 'content');

require_once(dirname(__FILE__)."/footer.php");
// -------------------------------------------------------------------


?>