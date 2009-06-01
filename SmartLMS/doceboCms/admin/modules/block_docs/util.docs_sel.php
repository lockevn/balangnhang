<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <giovanni[AT]docebo-com>         */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

function docs_selBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	include_once($GLOBALS["where_cms"]."/admin/modules/docs/docs_class.php");


	// ------- mostro elementi blocco --------------------------------------\

	$out->add(show_items_table($block_id, $sub_id));

	// --------------------------------------------------------------------/


	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	if ((isset($opt["use_comments"])) && ($opt["use_comments"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_USE_COMMENTS"), "use_comments", "use_comments", "1", $chk));

	$out->add($form->getTextfield($lang->def("_DOCS_NUMBER").":", "number", "number", 3, $opt["number"]));

	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function docs_selBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "number", (int)$_POST["number"]);
	saveParam($block_id, "use_comments", (int)$_POST["use_comments"]);
	saveParam($block_id, "number", (int)$_POST["number"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function docs_selBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "number", 10);
	saveParam($block_id, "use_comments", 0);

}




function docs_selBlockOption(& $out, & $lang, & $form, $block_id, $sub_id, $blk_op) {

	$backurl="index.php?modname=manpage&op=modblock";
	$backurl.="&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=";
	$backurl.=$sub_id;

	switch ($blk_op) { // ------------------------------------------------

		case "selitem" : {
			if (!isset($_POST["undo"])) {
				$out->add(sel_item($lang, $form, $block_id, $sub_id));
			}
			else {
				jumpTo($backurl);
			}
		} break;

		case "additem" : {
			add_item($block_id, $sub_id);
		} break;

		case "delitem" : {
			del_item($out, $lang, $form, $block_id, $sub_id);
		} break;

	}

}





function show_items_table($block_id, $sub_id) {
	require_once($GLOBALS["where_framework"]."/lib/lib.typeone.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");
	require_once($GLOBALS["where_cms"]."/lib/lib.area.php");

	$res="";

	$table=new typeOne(0);
	$res.=$table->OpenTable("");

	$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

	$sel_lang=get_area_lang(get_block_idArea($block_id));

	$t1=$GLOBALS["prefix_cms"]."_area_block_items";
	$t3=$GLOBALS["prefix_cms"]."_docs_info";
	$qtxt="";
	$qtxt.="SELECT t1.id, t2.fname, t2.real_fname, title, sdesc  FROM $t1 as t1 ";
	$qtxt.="INNER JOIN ".$GLOBALS["prefix_cms"]."_docs as t2 ON (t2.idDocs=t1.item_id) ";
	$qtxt.="LEFT JOIN $t3 ON ($t3.idd=item_id AND $t3.lang='$sel_lang') ";
	$qtxt.="WHERE t1.idBlock='$block_id' AND t1.type='docs' ORDER BY t1.id;";
	//echo $qtxt;

	$head = array($lang->def("_TYPE"), $lang->def("_FILENAME"), $lang->def("_TITLE"), $lang->def("_SHORTDESC"),
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
	$head_type = array('image', '', '', '', 'img');

	$res.=$table->WriteHeader($head, $head_type);

	$q=mysql_query($qtxt);

	$backurl="";
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {

			// Tipo:
			$fn=$row["real_fname"];
			$expFileName = explode('.', $fn);
			$totPart = count($expFileName) - 1;
			$mime=mimetype($expFileName[$totPart]);
			$img="<img src=\"".getPathImage().mimeDetect($fn)."\" alt=\"$mime\" title=\"$mime\" />\n";
			$type=$img;

			$url="index.php?modname=manpage&op=modblock";
			$url.="&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=";
			$url.=$sub_id."&amp;blk_op=delitem&amp;id=".$row["id"];
			$rem ="<a href=\"$url\">";
			$rem.="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")."\" /></a>";

			$line=array($type ,$row["fname"], $row["title"], $row["sdesc"], $rem);
			$res.=$table->WriteRow($line);
		}
	}

	$url="index.php?modname=manpage&op=modblock";
	$url.="&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=";
	$url.=$sub_id."&amp;blk_op=selitem";
	$res.=$table->WriteAddRow('<a href="'.$url.'">'
									.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def("_ADD").'" /> '.$lang->def("_ADD").'</a>');

	$res.=$table->CloseTable();

	return $res;
}



function sel_item(& $lang, & $form, $block_id, $sub_id) {

	require_once($GLOBALS["where_cms"]."/lib/lib.area.php");

	$url="index.php?modname=manpage&op=modblock";
	$url.="&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=";
	$url.=$sub_id."&amp;blk_op=selitem";

	$res="";

 	$res.=$form->openForm("block_form", $url);

 	$res.=$form->openElementSpace();

	// ------- mostro albero ----------------------------------------------\

	include_once($GLOBALS["where_cms"]."/admin/modules/docs/docs_class.php");

	$sel_lang=get_area_lang(get_block_idArea($block_id));
	define("_BLOCK_LANG", $sel_lang);
	$tree=createTreeView();

	if( isset($_POST[$tree->_getFolderNameId()]) ) { // Al cambio cartella:
		$folderid = $_POST[$tree->_getFolderNameId()];
	}
	else { // La prima volta che carica la pagina con l'albero:
		$folderid = $tree->getSelectedFolderId();
	}

 	$tree->show_icons=0;
 	$tree->setSelMode(true);

	$folder=$tree->tdb->getFolderById( $tree->getSelectedFolderId() );
	echo '<input type="hidden" value="'.$folderid.'" name="'.$tree->_getFolderNameId().'" />';
	echo '<input type="hidden" value="'.$tree->getSelectedFolderId().'" name="folder_id" />';
	$res.='<div><b>'.$lang->def("_FOLDER").':</b></div>';
	$res.=$tree->load();

	$res.="<br />\n\n";
	$listView = $tree->getListView();
	$listView->setInsNew( false );
	$res.=$listView->printOut();
	// --------------------------------------------------------------------/

 	$res.=$form->closeElementSpace();

	$res.=$form->openButtonSpace();
	$res.=$form->getButton('undo', 'undo', $lang->def('_BACK'));
	$res.=$form->closeButtonSpace();

	$res.=$form->closeForm();

	return $res;
}


function add_item($block_id, $sub_id) {

	$qtxt="";
	$qtxt.="INSERT INTO ".$GLOBALS["prefix_cms"]."_area_block_items (idBlock, item_id, type) ";
	$qtxt.="VALUES ('$block_id', '".(int)$_GET["item_id"]."', '".$_GET["type"]."');";

	$q=mysql_query($qtxt);

	$url="index.php?modname=manpage&op=modblock";
	$url.="&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=";
	$url.=$sub_id;

	jumpTo($url);

}



function del_item(& $out, & $lang, & $form, $block_id, $sub_id) {

	$id=(int)importVar("id");

	$url="index.php?modname=manpage&amp;op=modblock&amp;write=1&amp;block_id=".$block_id;
	$url.="&amp;sub_id=".$sub_id;

	if (isset($_POST["canc_del"])) {
		jumpTo($url);
	}
	else if (isset($_POST["conf_del"])) {
		$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE id='$id' LIMIT 1;";
		$q=mysql_query($qtxt);

		jumpTo($url);
	}
	else {

		$qtxt ="SELECT t2.fname FROM ".$GLOBALS["prefix_cms"]."_area_block_items as t1, ";
		$qtxt.=$GLOBALS["prefix_cms"]."_docs as t2 ";
		$qtxt.="WHERE t1.id='".$id."' AND t2.idDocs=t1.item_id";
		list($fname) = mysql_fetch_row(mysql_query($qtxt));

		$out->add("<div class=\"std_block\">\n");

		$out->add($form->openForm("docs_form", $url."&amp;blk_op=delitem"));

		$out->add($form->getHidden("id", "id", $id));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_FILENAME').' :</span> '.$fname.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}

}




// OLD / TEMP:


require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");


function docs_options( $block ) {

	$stop=0;

	if ($_GET["act_op"] != "") $act_op=$_GET["act_op"];
	if ($_POST["act_op"] != "") $act_op=$_POST["act_op"];
	switch ($act_op) { // ------------------------------------------------

		case "additem" : {
			add_item($block);
		} break;

		case "delitem" : {
			//$stop=del_item((int)$_GET["id"]);
		} break;

	}


	if (!$stop) {

		if ($_GET["blk_op"] == "") $blk_op="edit";
		else $blk_op=$_GET["blk_op"];
		switch ($blk_op) { // ------------------------------------------------

			case "edit" : {
				edit_options($block);
			} break;

			case "selitem" : {
				sel_item($block);
			} break;

		}

	}
}



function edit_options($block) {
	global $prefixCms;

	$backurl = $block->getBackurl();
	echo '<form method="POST" action="'.page_url(array()).'">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();

	$idBlock=$block->getIdBlock();
	$q=mysql_query("SELECT title FROM ".$prefixCms."_area_block WHERE idBlock='$idBlock';");
	list($title)=mysql_fetch_row($q);

	$opt=loadBlockOption($idBlock);
	echo "<br />";

	// ------- mostro elementi blocco --------------------------------------\

	show_items_table($block);

	// --------------------------------------------------------------------/

	echo("<br /><b>"._BLOCK_TITLE.":</b>\n");
	echo("<input type=\"text\" id=\"title\" name=\"title\" size=\"25\" value=\"".$title."\" /><br />\n");

	echo("<br />\n");
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

	if ($opt["showclick"]) $chk=" checked=\"checked\""; else $chk="";
	echo("<br /><br />\n");
	echo("<input type=\"checkbox\" id=\"showclick\" name=\"showclick\" value=\"1\"$chk />\n");
	echo("<b>"._DOCS_SHOWCLICK."</b>\n");

	echo("<br /><br /><b>"._DOCS_NUMBER.":</b>\n");
	echo("<input type=\"text\" id=\"number\" name=\"number\" size=\"3\" value=\"".$opt["number"]."\" /><br />\n");

	echo '<br /><input class="button" type="submit" id="save" name="save" value="'._DOCS_INSERT.'" />'
		.'</div>'
		.'</form>';

	// back option
	echo '<form method="post" action="'.$backurl['backurl'].'">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();
	echo '<input class="button" type="submit" value="'._DOCS_BACK.'" />'
		.'</div>'
		.'</form>';

}


function save_options( $block ) {
	global $prefixCms;

	$backurl = $block->getBackurl();
	$idBlock=$block->getIdBlock();

	if ((int)$_POST["folder_id"] == 0)
		$path="/";
	else {
		$q=mysql_query("SELECT * FROM ".$prefixCms."_docs_dir WHERE id='".$_POST["folder_id"]."';");
		$row=mysql_fetch_array($q);

		$path=$row["path"];
	}

	$q=mysql_query("UPDATE ".$prefixCms."_area_block SET title='".$_POST["title"]."' WHERE idBlock='$idBlock';");

	$err="";
	saveParam($idBlock, "number", (int)$_POST["number"]);
	saveParam($idBlock, "path", $path);
	saveParam($idBlock, "css", (int)$_POST["css"]);
	saveParam($idBlock, "ov_comments", (int)$_POST["ov_comments"]);
	saveParam($idBlock, "showclick", (int)$_POST["showclick"]);
	saveParam($idBlock, "ov_pub", (int)$_POST["ov_pub"]);
	saveParam($idBlock, "ov_exp", (int)$_POST["ov_exp"]);
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
		echo("<b>"._DOCS_ALLOK."</b><br />");
		echo '<input class="button" type="submit" value="'._DOCS_BACK.'" />';
	}
	else {
		echo("<b><font color=\"#FF0000\">"._WARN."</font>:</b> $err<br />"._SAVEDREMAIN."<br /><br />\n");
		echo '<input class="button" type="submit" value="'._DOCS_BACK.'" />';
	}


	echo '</div></form>';
}

?>