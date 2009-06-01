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



function contentBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	include_once($GLOBALS["where_cms"]."/admin/modules/content/content_class.php");


	// ------- mostro elementi blocco --------------------------------------\

	$out->add(show_items_table($block_id, $sub_id));

	// --------------------------------------------------------------------/


	$out->add(getBlockTitleField($form, $lang, $block_id));
//EZ
	if ((isset($opt["ov_hide_title"])) && ($opt["ov_hide_title"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_HIDE_CONTENT_TITLE"), "ov_hide_title", "ov_hide_title", "1", $chk));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

 	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	if ((isset($opt["ov_comments"])) && ($opt["ov_comments"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_OVERRIDE_COM"), "ov_comments", "ov_comments", "1", $chk));


	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function contentBlockSave($block_id, $sub_id) {
	
	if ((int)$_POST["ov_hide_title"]) {
		$title='';
		$_POST['title']='';
	}

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "ov_comments", (int)$_POST["ov_comments"]);
	saveParam($block_id, "ov_hide_title", (int)$_POST["ov_hide_title"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function contentBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "ov_comments", 0);
	saveParam($block_id, "ov_hide_title", 0);

}

function contentBlockOption(& $out, & $lang, & $form, $block_id, $sub_id, $blk_op) {

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

	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

	$sel_lang=get_area_lang(get_block_idArea($block_id));

	$t1=$GLOBALS["prefix_cms"]."_area_block_items";
	$qtxt="";
	$qtxt.="SELECT t1.id, t2.publish_date, t2.title FROM $t1 as t1, ";
	$qtxt.=$GLOBALS["prefix_cms"]."_content as t2 ";
	$qtxt.="WHERE t1.idBlock='$block_id' AND t2.idContent=t1.item_id AND t1.type='content' ORDER BY t1.id;";

	$head = array($lang->def("_PUBDATE"), $lang->def("_TITLE"),
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
	$head_type = array('', '', 'img');

	$res.=$table->WriteHeader($head, $head_type);

	$q=mysql_query($qtxt);

	$backurl="";
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {

			$pubdate=$GLOBALS["regset"]->databaseToRegional($row["publish_date"]);;

			$url="index.php?modname=manpage&op=modblock";
			$url.="&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=";
			$url.=$sub_id."&amp;blk_op=delitem&amp;id=".$row["id"];
			$rem ="<a href=\"$url\">";
			$rem.="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")."\" /></a>";

			$line=array($pubdate, $row["title"], $rem);
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

	include_once($GLOBALS["where_cms"]."/admin/modules/content/content_class.php");

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
	$res.=$form->getHidden($tree->_getFolderNameId(), $tree->_getFolderNameId(), $folderid);
	$res.=$form->getHidden("folder_id", "folder_id", $tree->getSelectedFolderId());
	$res.='<div><b>'.$lang->def("_FOLDER").':</b></div>';
	$res.=$tree->load();

	$res.="<br />\n\n";
	$listView = $tree->getListView();
	$listView->setInsNew( false );
	$res.=$listView->printOut();
	// --------------------------------------------------------------------/

 	$res.=$form->closeElementSpace();

	$res.=$form->openButtonSpace();
	//$res.=$form->getButton('save', 'save', $lang->def("_SAVE"));
	$res.=$form->getButton('undo', 'undo', $lang->def('_BACK'));
	$res.=$form->closeButtonSpace();

	$res.=$form->closeForm();

	return $res;
}


function add_item($block_id, $sub_id) {

	$qtxt="";
	$qtxt.="DELETE FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE idBlock='$block_id' AND type='".$_GET["type"]."';";
	$q=mysql_query($qtxt);
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

		$qtxt ="SELECT t2.title FROM ".$GLOBALS["prefix_cms"]."_area_block_items as t1, ";
		$qtxt.=$GLOBALS["prefix_cms"]."_content as t2 ";
		$qtxt.="WHERE t1.id='".$id."' AND t2.idContent=t1.item_id";
		list($title) = mysql_fetch_row(mysql_query($qtxt));

		$out->add("<div class=\"std_block\">\n");

		$out->add($form->openForm("content_form", $url."&amp;blk_op=delitem"));

		$out->add($form->getHidden("id", "id", $id));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}

}




?>