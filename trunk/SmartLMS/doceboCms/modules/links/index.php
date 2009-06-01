<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks                                  */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

define("_IN_LINK_MOD", true);
require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");

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

$GLOBALS["page"]->add("<div style=\"text-align: right;\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");


if (isset($_GET["op"]))
	$op=$_GET["op"];
else
	$op="";

if (isset($_GET["pid"]))
	$pid=(int)$_GET["pid"];
else
	$pid=0;

if (isset($_GET["pos"]))
	$pos=(int)$_GET["pos"];
else
	$pos=0;


switch ($op) {

		default : {
			show_links($GLOBALS["pb"], $pid, $pos);
		}; break;

		case "showlink": {
			link_details($GLOBALS["pb"]);
		}; break;

		case "go": {
			open_link_url($GLOBALS["pb"]);
		} break;

}


$GLOBALS["page"]->add("<div style=\"text-align: right;\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");

$GLOBALS["page"]->add("</div>\n", "content");




?>