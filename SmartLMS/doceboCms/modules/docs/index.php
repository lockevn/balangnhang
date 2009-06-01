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
check_docs_perm($GLOBALS["pb"]);
// ---------------------------------------------------------------------------

define("_DOCS_FPATH_INTERNAL", "/doceboCms/docs/");
define("_DOCS_FPATH", $GLOBALS["where_files_relative"]._DOCS_FPATH_INTERNAL);

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

if (isset($_GET["pos"]))
	$pos=(int)$_GET["pos"];
else
	$pos=0;


switch ($_GET["op"]) {

		case "docs" : {
			if ((isset($_GET["sel"])) && ($_GET["sel"]))
				show_doc_list($GLOBALS["pb"], "sel", "normal", $pos);
			else
				show_doc_list($GLOBALS["pb"], "dir", "normal", $pos);
		};break;

		case "showdoc": {
			if ((isset($_GET["sel"])) && ($_GET["sel"]))
				doc_details($GLOBALS["pb"], "sel");
			else
				doc_details($GLOBALS["pb"]);
		} break;

		case "download": {
			docs_download($GLOBALS["pb"]);
		} break;

}


$GLOBALS["page"]->add("<div style=\"text-align: right;\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");

$GLOBALS["page"]->add("</div>\n", "content");




?>