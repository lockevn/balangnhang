<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

$css=getModuleCss($GLOBALS["pb"]);
$GLOBALS["page"]->add("<div class=\"".$css."\">\n", "content");
$GLOBALS["page"]->add(getModuleBlockTitle($GLOBALS["pb"]), "content");


if($GLOBALS["cms"]["use_mod_rewrite"] == 'on')
{
	list($title, $mr_title) = mysql_fetch_row(mysql_query(	"SELECT title, mr_title"
															." FROM ".$GLOBALS["prefix_cms"]."_area"
															." WHERE idArea = '".$GLOBALS["area_id"]."'"));
	
	if ($mr_title != "")
		$page_title = format_mod_rewrite_title($mr_title);
	else
		$page_title = format_mod_rewrite_title($title);
	
	$backurl = 'page/'.$GLOBALS["area_id"].'/'.$page_title.'.html';
}
else
	$backurl = "index.php?special=changearea&amp;newArea=".$GLOBALS["area_id"];

$GLOBALS["page"]->add("<div style=\"text-align: right\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");

$GLOBALS["page"]->add("<div class=\"body_block\">\n", "content");

// ----------------------------------------------------------------------------

if ((isset($_GET["op"])) && (!empty($_GET["op"])))
	$op=$_GET["op"];
else
	$op="";



$teleskill_op=array("teleskill", "quickroom", "createroom", "bookroom", "managesub", "userbooking",
                    "savebooking", "delbooking", "setroomviewperm");


if ((isset($_GET["type"])) && (!empty($_GET["type"]))) {

	switch ($_GET["type"]) {

		case "teleskill": {
			require_once($GLOBALS["where_cms"]."/modules/chat/teleskill.php");
			teleskillDispatch($op);
		} break;

		case "dimdim": {
			require_once($GLOBALS["where_cms"]."/modules/chat/dimdim.php");
			dimdimDispatch($op);
		} break;
		
		case "openconf":
		{
			require_once($GLOBALS["where_cms"]."/modules/chat/openconference.php");
			openconferenceDispatch($op);
		}
		
		case "intelligere":
		{
			require_once($GLOBALS["where_cms"]."/modules/chat/intelligere.php");
			intelligereDispatch($op);
		}
		break;
	}
}
else if (in_array($op, $teleskill_op)) {
	require_once($GLOBALS["where_cms"]."/modules/chat/teleskill.php");

/*	if ($GLOBALS['current_user']->isAnonymous())
		$GLOBALS["page"]->add($lang->def("_LOGIN_REQUIRED"), "content");
	else */
	teleskillDispatch($op);
}



// ----------------------------------------------------------------------------

$GLOBALS["page"]->add("</div>\n", "content"); // body_block

$GLOBALS["page"]->add("<div style=\"text-align: right\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");
$GLOBALS["page"]->add("</div>\n", "content");






?>