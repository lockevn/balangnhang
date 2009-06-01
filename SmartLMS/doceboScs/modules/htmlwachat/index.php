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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');


require_once(dirname(__FILE__)."/header.php");
// -------------------------------------------------------------------

$script ="<script type=\"text/javascript\">\n";
$script.="<!--\n";
$script.="function addEmo(code)\n";
$script.="{\n";
$script.="	document.forms[1].msgtxt.value=document.forms[1].msgtxt.value+' '+code;\n";
$script.="	document.forms[1].msgtxt.focus();\n";
$script.="}\n";
$script.="//-->\n";
$script.="</script>\n";

$out->add($script, "page_head");

$op=importVar('op');
if (empty($op))
	$op="";

switch ($op) {

	case "send": {
		if (isset($_POST["savechat"]))
			saveChatMsg();
		else
			sendChatMsg();
	} break;
	
	case "setroom": {
		setRoom($out, $lang);
	} break;	
	
}
	
	
if (!isset($_SESSION["refreshrate"]))
	$_SESSION["refreshrate"]=0;
	

checkLogin(false); // Auto-reload is off in accessibility mode

//--debug:--// echo("<pre>"); print_r($_SESSION); echo("</pre>");


	
$out->add("\n<div class=\"chatText\">");
$out->add(getMsgBuffer($lang, 25));
$out->add("</div>\n");

$out->add(listUsers($out, $lang));
$out->add(listRooms($out, $lang));
$out->add("\n<div class=\"no_float\">&nbsp;</div>\n");

$out->add(getWriteBox($out, $lang));

$backurl=getBackUrl();
if (!empty($backurl)) {
	$out->add("\n<noscript>\n");	
	$out->add("<a href=\"".$backurl."\">");
	$out->add($lang->def("_BACK")."</a>\n");
	$out->add("\n</noscript>\n");	
}

// -------------------------------------------------------------------
require_once(dirname(__FILE__)."/footer.php");
// -------------------------------------------------------------------




?>