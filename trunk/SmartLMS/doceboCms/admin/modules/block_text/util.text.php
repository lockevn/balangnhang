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

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

function textBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);
	$textof=loadTextof($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));

	$fck_opt["ImageBrowserURL"]="../filemanager/browser/cms_browser/default/browser.html?Type=Image&Connector=connectors/php/connector.php";
	$out->add($form->getTextarea($lang->def("_DESCRIPTION"), "textof", "textof", $textof, $fck_opt));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add(getBlindNavDescField($form, $lang, $opt));
 	$out->add(getGMonitoringField($form, $lang, $opt));

}



function textBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);
	saveTextof($block_id, $_POST["textof"], true);

	saveParam($block_id, "css", (int)$_POST["css"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function textBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}


function textBlockDel($block_id, $sub_id) {
}





// OLD / TEMP:


require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

function text_listinsert( $block ) {
	global $prefixCms;
	$backurl = $block->getBackurl();

	include_once( 'core/class/class.typeone.php' );

	$reText = mysql_query("
	SELECT language, LEFT(textof, 50)
	FROM ".$prefixCms."_text
	WHERE idBlock = '".$block->getIdBlock()."'");
	while( list($language, $txt) = mysql_fetch_row($reText) ) {
		$txt_in[$language] = $txt;
	}


	$lang = giveLanguageList();
	echo '<form method="post" action="'.$backurl['address'].'&amp;write=1">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();

	$table = new typeOne(0);
	$table->OpenTable(_TEXT_LIST);
	$table->WriteHeader(array(_TEXT_LANGUAGE, _TEXT_TXT,''), array('','','img'));

	while( list(,$lang_name) = each($lang) ) {
		$table->WriteRow( array(
			$lang_name,
			(isset($txt_in[$lang_name]) ? $txt_in[$lang_name].'...' : '---' ),
			'<input class="textModSubmit" type="submit" name="button['.$lang_name.']" value=" " />' ));

	}
	$table->CloseTable();
	echo '</div>'
		.'</form>';

	// back option
	echo '<form method="post" action="'.$backurl['backurl'].'">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();
	echo '<input class="button" type="submit" value="'._TEXT_BACK.'" />'
		.'</div>'
		.'</form>';
}


function get_block_lang($idBlock) {
// temp: doppia in util.text.php e util.news.php
// todo: mettere in un file "condiviso"
	global $prefixCms;

	list($idSub) = mysql_fetch_row(mysql_query(
		"SELECT idSubdivision FROM ".
		$prefixCms."_area_block WHERE idBlock = '$idBlock'"));

	list($idArea) = mysql_fetch_row(mysql_query(
		"SELECT idArea FROM ".
		$prefixCms."_area_subdivision WHERE idSubdivision = '$idSub'"));

	list($path) = mysql_fetch_row(mysql_query(
		"SELECT path FROM ".
		$prefixCms."_area WHERE idArea = '$idArea'"));


	$path_arr=explode("/", $path);
	$lang_path="/".$path_arr[1];

	list($title) = mysql_fetch_row(mysql_query(
		"SELECT title FROM ".
		$prefixCms."_area WHERE path = '$lang_path'"));


	return $title;

}


function text_write( $block ) {
	global $prefixCms;

	$backurl = $block->getBackurl();
	echo '<form id="text_write" method="post" action="'.$backurl['address'].'&amp;insert=1">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();

	$idBlock=$block->getIdBlock();
	$q=mysql_query("SELECT title FROM ".$prefixCms."_area_block WHERE idBlock='$idBlock';");
	list($title)=mysql_fetch_row($q);

	echo("<br /><b>"._BLOCK_TITLE.":</b>\n");
	echo("<input type=\"text\" id=\"title\" name=\"title\" size=\"25\" value=\"".$title."\" /><br /><br />\n");

	$lang=get_block_lang($block->getIdBlock());

	echo _TEXT_LANGUAGE.' :<b> '.$lang.'<br />';

	list($textof) = mysql_fetch_row(mysql_query("
	SELECT textof
	FROM ".$prefixCms."_text
	WHERE idBlock = '".$block->getIdBlock()."' AND language = '$lang'"));

	echo '<input type="hidden" name="text_lang" value="'.$lang.'" />';

	loadHTMLEditor('text_write','textof',$textof);


	$idBlock=$block->getIdBlock();
	$opt=loadBlockOption($idBlock);

	echo("<br /><br />\n");
	$db_group=db_block_groups($idBlock);
	sel_block_groups($idBlock, $db_group);

	echo("<br /><b>"._BLOCK_STYLE.":</b>\n");
	block_css_list($opt["css"]);

	echo("<br /><br />\n");
	show_pubexp_table($opt["pubdate"], $opt["expdate"]);

	if ($opt["ov_comments"]) $chk_ov_com=" checked=\"checked\""; else $chk_ov_com="";
	echo("<br /><br />\n<input type=\"checkbox\" id=\"ov_comments\" name=\"ov_comments\" value=\"1\"$chk_ov_com />\n");
	echo("<b>"._OVERRIDE_COM."</b>\n");

	if ($opt["ov_pub"]) $chk_ov_pub=" checked=\"checked\""; else $chk_ov_pub="";
	echo("<br /><br />\n<input type=\"checkbox\" id=\"ov_pub\" name=\"ov_pub\" value=\"1\"$chk_ov_pub />\n");
	echo("<b>"._OVERRIDE_PUB."</b>\n");

	if ($opt["ov_exp"]) $chk_ov_exp=" checked=\"checked\""; else $chk_ov_exp="";
	echo("<br /><br />\n<input type=\"checkbox\" id=\"ov_exp\" name=\"ov_exp\" value=\"1\"$chk_ov_exp />\n");
	echo("<b>"._OVERRIDE_EXP."</b>\n");

	echo '<br /><br /><input class="button" type="submit" value="'._TEXT_INSERT.'" />'
		.'</div>'
		.'</form>';


	// back option
	echo '<form method="post" action="'.$backurl['backurl'].'">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();
	echo '<input class="button" type="submit" value="'._TEXT_BACK.'" />'
		.'</div>'
		.'</form>';
}

function text_insert( $block ) {
	global $prefixCms;
	// back option

	$backurl = $block->getBackurl();
	echo '<form method="post" action="'.$backurl['backurl'].'">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();

	$re = mysql_query("
	SELECT idBlock
	FROM ".$prefixCms."_text
	WHERE idBlock = '".$block->getIdBlock()."' AND language = '".$_POST['text_lang']."'");

	if(mysql_num_rows($re)) {
		if(!mysql_query("
		UPDATE ".$prefixCms."_text
		SET textof = '".$_POST['textof']."'
		WHERE idBlock = '".$block->getIdBlock()."' AND language = '".$_POST['text_lang']."'")) errorCommunication(_TEXT_ERR);
		else echo _TEXT_ALLOK;

		$idBlock=$block->getIdBlock();

		$q=mysql_query("UPDATE ".$prefixCms."_area_block SET title='".$_POST["title"]."' WHERE idBlock='$idBlock';");

		saveParam($idBlock, "css", (int)$_POST["css"]);
		saveParam($idBlock, "ov_comments", (int)$_POST["ov_comments"]);
		saveParam($idBlock, "ov_pub", (int)$_POST["ov_pub"]);
		saveParam($idBlock, "ov_exp", (int)$_POST["ov_exp"]);
		if (check_period($ts_pub, $ts_exp)) {
			saveParam($idBlock, "pubdate", (int)$ts_pub);
			saveParam($idBlock, "expdate", (int)$ts_exp);
		}
		else
			$err=_INVALID_PERIOD;
		save_block_groups($idBlock, $_POST["idGroups"]);

	}
	else {
		if(!mysql_query("
		INSERT INTO  ".$prefixCms."_text
		SET idBlock = '".$block->getIdBlock()."',
			language = '".$_POST['text_lang']."',
			textof = '".$_POST['textof']."'")) errorCommunication(_TEXT_ERR);
		else echo _TEXT_ALLOK;
	}

	echo '<br /><br /><input class="button" type="submit" value="'._TEXT_BACK.'" />'
		.'</div>'
		.'</form>';

}

?>
