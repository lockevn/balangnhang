<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Emanuele Sandri, Fabio Pirovano, Giovanni Derks */
/*                      http://www.docebocms.com                         */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS["where_cms"]."/modules/links/functions.php");

function links_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	if($title != '')
		$out->add('<div class="titleBlock">'.$title.'</div>');

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_links_dir WHERE path='".$opt["path"]."';";
	$q=mysql_query($qtxt); 	//echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);

		show_links($idBlock, $row["id"]);
	}
	else if ($opt["path"] == "/")
		show_links($idBlock, 0);

}


?>
