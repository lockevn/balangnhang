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
check_content_perm($pb, (int)$_GET["id"]);
// ---------------------------------------------------------------------------


echo("<div class=\"moduleBlock\">\n");

$df=df_str(_DATEFORMAT, _DATESEP, "%");

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

$qtxt="SELECT publish_date , title, long_desc FROM ".$prefixCms."_content WHERE idContent='".(int)$_GET["id"]."';";
$q=mysql_query($qtxt);
if (($q) && (mysql_num_rows($q) > 0)) {
	$row=mysql_fetch_array($q);
	$dt=conv_datetime($row["publish_date"], 0, _TIMEOFFSET);
	echo("<div style=\"text-align: right;\">\n");
	echo("<a href=\"$backurl\">&lt;&lt; "._BACK."</a></div>\n");
	echo("<div class=\"contentListBox\">\n");
	/*echo "<span class=\"content_title\">".$dt; // data
	echo "&nbsp;&nbsp;&nbsp;".$row["title"]."</span><br />";*/
	echo "<span class=\"content_title\">".$row["title"]."</span><br /><br />";
	echo $row["long_desc"]."<br /></div><br />\n\n";

	show_content_attach((int)$_GET["id"]);

	load_comments((int)$_GET["id"], $pb);

	echo("<div style=\"text-align: right;\">\n");
	echo("<a href=\"$backurl\">&lt;&lt; "._BACK."</a></div>\n");
}

echo("</div>\n");


?>