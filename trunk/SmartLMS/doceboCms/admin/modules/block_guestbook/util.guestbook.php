<?php

/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2002 by abio Pirovano (gishell@tiscali.it)              */
/* http://www.spaghettilearning.com                                      */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

function guestbook_options( $block ) {
	global $prefixCms;

	$backurl = $block->getBackurl();
	echo '<form method="POST" action="'.page_url(array()).'">' //&amp;insert=1
		.'<div class="stdBlock">';
	$block->loadParamAsInput();

	$idBlock=$block->getIdBlock();
	$q=mysql_query("SELECT title FROM ".$prefixCms."_area_block WHERE idBlock='$idBlock';");
	list($title)=mysql_fetch_row($q);

	$opt=loadBlockOption($idBlock); //--debug-//print_r($opt);
	echo "<br />";


	echo("<br /><b>"._BLOCK_TITLE.":</b>\n");
	echo("<input type=\"text\" id=\"title\" name=\"title\" size=\"25\" value=\"".$title."\" /><br />\n");


	echo("<br />\n");
	$db_group=db_block_groups($idBlock);
	sel_block_groups($idBlock, $db_group);

	echo("<br /><b>"._BLOCK_STYLE.":</b>\n");
	block_css_list($opt["css"]);

	echo("<br /><br />\n");
	show_pubexp_table($opt["pubdate"], $opt["expdate"]);

	echo("<br /><br /><b>"._GUESTBOOK_NUMBER.":</b>\n");
	echo("<input type=\"text\" id=\"number\" name=\"number\" size=\"3\" value=\"".$opt["number"]."\" /><br />\n");


	echo '<br /><input class="button" type="submit" id="save" name="save" value="'._GUESTBOOK_INSERT.'" />'
		.'</div>'
		.'</form>';

	// back option
	echo '<form method="post" action="'.$backurl['backurl'].'">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();
	echo '<input class="button" type="submit" value="'._GUESTBOOK_BACK.'" />'
		.'</div>'
		.'</form>';

}



function save_options( $block ) {
	global $prefixCms;

	//--debug--// print_r($_POST);

	$backurl = $block->getBackurl();
	$idBlock=$block->getIdBlock();

	if ((int)$_POST["folder_id"] == 0)
		$path="/";
	else {
		$q=mysql_query("SELECT * FROM ".$prefixCms."_guestbook_dir WHERE id='".$_POST["folder_id"]."';");
		$row=mysql_fetch_array($q);

		$path=$row["path"];
	}

	$q=mysql_query("UPDATE ".$prefixCms."_area_block SET title='".$_POST["title"]."' WHERE idBlock='$idBlock';");

	$err="";
	saveParam($idBlock, "number", (int)$_POST["number"]);
	saveParam($idBlock, "css", (int)$_POST["css"]);
	if (check_period($ts_pub, $ts_exp)) {
		saveParam($idBlock, "pubdate", (int)$ts_pub);
		saveParam($idBlock, "expdate", (int)$ts_exp);
	}
	else
		$err=_INVALID_PERIOD;
	save_block_groups($idBlock, $_POST["idGroups"]);

	// back option
	echo '<form method="post" action="'.$backurl['backurl'].'">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();

	if ($err == "") {
		echo("<b>"._GUESTBOOK_ALLOK."</b><br />");
		echo '<input class="button" type="submit" value="'._GUESTBOOK_BACK.'" />';
	}
	else {
		echo("<b><font color=\"#FF0000\">"._WARN."</font>:</b> $err<br />"._SAVEDREMAIN."<br /><br />\n");
		echo '<input class="button" type="submit" value="'._GUESTBOOK_BACK.'" />';
	}


	echo '</div></form>';
}

?>