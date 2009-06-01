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

function news_selBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	include_once($GLOBALS["where_cms"]."/admin/modules/news/news_class.php");


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

	/* if ((isset($opt["ov_comments"])) && ($opt["ov_comments"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_OVERRIDE_COM"), "ov_comments", "ov_comments", "1", $chk)); */



	$out->add($form->getTextfield($lang->def("_NEWS_NUMBER"), "number", "number", 3, $opt["number"]));

	if ((isset($opt["show_newsdate"])) && ($opt["show_newsdate"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_NEWSDATE"), "show_newsdate", "show_newsdate", "1", $chk));

	if ((isset($opt["show_newslink"])) && ($opt["show_newslink"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_NEWSLINK"), "show_newslink", "show_newslink", "1", $chk));

	if ((isset($opt["show_commentslink"])) && ($opt["show_commentslink"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_COMMENTSLINK"), "show_commentslink", "show_commentslink", "1", $chk));

	/* if ((isset($opt["show_catlink"])) && ($opt["show_catlink"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_CATLINK"), "show_catlink", "show_catlink", "1", $chk)); */

	if ((isset($opt["show_topiclink"])) && ($opt["show_topiclink"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_TOPICLINK"), "show_topiclink", "show_topiclink", "1", $chk));

	if ((isset($opt["show_search"])) && ($opt["show_search"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_SEARCH"), "show_search", "show_search", "1", $chk));

	if ((isset($opt["show_attach_inline"])) && ($opt["show_attach_inline"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_ATTACH_INLINE"), "show_attach_inline", "show_attach_inline", "1", $chk));


	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function news_selBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "show_newsdate", (int)$_POST["show_newsdate"]);
	saveParam($block_id, "show_newslink", (int)$_POST["show_newslink"]);
	saveParam($block_id, "show_commentslink", (int)$_POST["show_commentslink"]);
	saveParam($block_id, "show_topiclink", (int)$_POST["show_topiclink"]);
	saveParam($block_id, "show_search", (int)$_POST["show_search"]);
	saveParam($block_id, "number", (int)$_POST["number"]);
	saveParam($block_id, "use_comments", (int)$_POST["use_comments"]);
	saveParam($block_id, "show_attach_inline", (int)$_POST["show_attach_inline"]);


	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function news_selBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "show_newsdate", 1);
	saveParam($block_id, "show_newslink", 1);
	saveParam($block_id, "show_commentslink", 0);
	saveParam($block_id, "show_search", 0);
	saveParam($block_id, "show_topiclink", 0);
	saveParam($block_id, "number", 10);
	saveParam($block_id, "use_comments", 1);

}




function news_selBlockOption(& $out, & $lang, & $form, $block_id, $sub_id, $blk_op) {

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
			del_item(& $out, & $lang, & $form, $block_id, $sub_id);
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

	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');

	$sel_lang=get_area_lang(get_block_idArea($block_id));

	$t1=$GLOBALS["prefix_cms"]."_area_block_items";
	//$t3=$GLOBALS["prefix_cms"]."_news_info";
	$qtxt="";
	$qtxt.="SELECT t1.id, t2.publish_date, t2.title, t2.short_desc  FROM $t1 as t1 ";
	$qtxt.="INNER JOIN ".$GLOBALS["prefix_cms"]."_news as t2 ON (t2.idNews=t1.item_id) ";
	//$qtxt.="LEFT JOIN $t3 ON ($t3.idn=item_id AND $t3.lang='$sel_lang') ";
	$qtxt.="WHERE t1.idBlock='$block_id' AND t1.type='news' ORDER BY t1.id;";
	//echo $qtxt;

	$head = array($lang->def("_PUBDATE"), $lang->def("_TITLE"), $lang->def("_SHORTDESC"),
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
	$head_type = array('', '', '', 'img');

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

			$line=array($pubdate, $row["title"], $row["short_desc"], $rem);
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

	include_once($GLOBALS["where_cms"]."/admin/modules/news/news_class.php");

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
	//$res.=$form->getButton('save', 'save', $lang->def("_SAVE"));
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

		$qtxt ="SELECT t2.title FROM ".$GLOBALS["prefix_cms"]."_area_block_items as t1, ";
		$qtxt.=$GLOBALS["prefix_cms"]."_news as t2 ";
		$qtxt.="WHERE t1.id='".$id."' AND t2.idNews=t1.item_id";
		list($title) = mysql_fetch_row(mysql_query($qtxt));

		$out->add("<div class=\"std_block\">\n");

		$out->add($form->openForm("news_form", $url."&amp;blk_op=delitem"));

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




// OLD / TEMP:

require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

function news_options( $block ) {

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

	if ($opt["show_newslink"]) $chk=" checked=\"checked\""; else $chk="";
	echo("<br /><br />\n<input type=\"checkbox\" id=\"show_newslink\" name=\"show_newslink\" value=\"1\"$chk />\n");
	echo("<b>"._SHOW_NEWSLINK."</b>\n");

	if ($opt["show_commentslink"]) $chk=" checked=\"checked\""; else $chk="";
	echo("<br /><br />\n<input type=\"checkbox\" id=\"show_commentslink\" name=\"show_commentslink\" value=\"1\"$chk />\n");
	echo("<b>"._SHOW_COMMENTSLINK."</b>\n");

	if ($opt["show_catlink"]) $chk=" checked=\"checked\""; else $chk="";
	echo("<br /><br />\n<input type=\"checkbox\" id=\"show_catlink\" name=\"show_catlink\" value=\"1\"$chk />\n");
	echo("<b>"._SHOW_CATLINK."</b>\n");

	if ($opt["show_topiclink"]) $chk=" checked=\"checked\""; else $chk="";
	echo("<br /><br />\n<input type=\"checkbox\" id=\"show_topiclink\" name=\"show_topiclink\" value=\"1\"$chk />\n");
	echo("<b>"._SHOW_TOPICLINK."</b>\n");


	echo '<br /><br /><input class="button" type="submit" id="save" name="save" value="'._NEWS_INSERT.'" />'
		.'</div>'
		.'</form>';

	// back option
	echo '<form method="post" action="'.$backurl['backurl'].'">'
		.'<div class="stdBlock">';
	$block->loadParamAsInput();
	echo '<input class="button" type="submit" value="'._NEWS_BACK.'" />'
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
		$q=mysql_query("SELECT * FROM ".$prefixCms."_news_dir WHERE id='".$_POST["folder_id"]."';");
		$row=mysql_fetch_array($q);

		$path=$row["path"];
	}

	$q=mysql_query("UPDATE ".$prefixCms."_area_block SET title='".$_POST["title"]."' WHERE idBlock='$idBlock';");

	$err="";
	saveParam($idBlock, "css", (int)$_POST["css"]);
	saveParam($idBlock, "ov_comments", (int)$_POST["ov_comments"]);
	saveParam($idBlock, "ov_pub", (int)$_POST["ov_pub"]);
	saveParam($idBlock, "ov_exp", (int)$_POST["ov_exp"]);
	saveParam($idBlock, "show_newslink", (int)$_POST["show_newslink"]);
	saveParam($idBlock, "show_commentslink", (int)$_POST["show_commentslink"]);
	saveParam($idBlock, "show_catlink", (int)$_POST["show_catlink"]);
	saveParam($idBlock, "show_topiclink", (int)$_POST["show_topiclink"]);
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
		echo("<b>"._NEWS_ALLOK."</b><br />");
		echo '<input class="button" type="submit" value="'._NEWS_BACK.'" />';
	}
	else {
		echo("<b><font color=\"#FF0000\">"._WARN."</font>:</b> $err<br />"._SAVEDREMAIN."<br /><br />\n");
		echo '<input class="button" type="submit" value="'._NEWS_BACK.'" />';
	}


	echo '</div></form>';
}


function show_items_table_old($block) {
	global $prefixCms;
	require_once('core/class/class.typeone.php');

	$idBlock=$block->getIdBlock();

	$table=new typeOne(0);
	$table->OpenTable("");

	$qtxt="";
	$qtxt.="SELECT * FROM ".$prefixCms."_area_block_items as t1, ";
	$qtxt.=$prefixCms."_news as t2 ";
	$qtxt.="WHERE t1.idBlock='$idBlock' AND t2.idNews=t1.item_id AND t1.type='news' ORDER BY t1.id;";

	$head = array(_PUBDATE, _TITLE, _SHORTDESC,
		'<img src="'.getPathImage().'standard/rem.gif" alt="'._REM.'" title="'._REM.'" />');
	$head_type = array('', '', '', 'img');

	$table->WriteHeader($head, $head_type);

	$q=mysql_query($qtxt);

	$backurl=$block->getBackurl();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$pdate=conv_datetime($row["publish_date"], 0, _TIMEOFFSET);
			$url =$backurl["address"]."&amp;write=1&amp;idBlock=".$backurl["param"]["idBlock"]."&amp;idSubdivision=";
			$url.=$backurl["param"]["idSubdivision"]."&amp;act_op=delitem&amp;id=".$row["id"];
			$rem ="<a href=\"$url\">";
			$rem.="<img src=\"".getPathImage()."standard/rem.gif\" alt=\""._REM."\" title=\""._REM."\" /></a>";
			$line=array($pdate, $row["title"], $row["short_desc"], $rem);
			$table->WriteRow($line);
		}
	}

	$url="";
	$url.=$backurl["address"]."&amp;write=1&amp;idBlock=".$backurl["param"]["idBlock"]."&amp;idSubdivision=";
	$url.=$backurl["param"]["idSubdivision"]."&amp;blk_op=selitem";
	$table->WriteAddRow('<a href="'.$url.'">'
									.'<img src="'.getPathImage().'standard/add.gif" alt="'._ADD_ITEM.'" /> '._ADD_ITEM.'</a>');

	$table->CloseTable();
}


function sel_item_old($block) {
	global $prefixCms;

	$backurl=$block->getBackurl();
	$url="";
	$url.=$backurl["address"]."&amp;write=1&amp;idBlock=".$backurl["param"]["idBlock"]."&amp;idSubdivision=";
	$url.=$backurl["param"]["idSubdivision"]."&amp;blk_op=selitem";

	echo("<form action=\"$url\" method=\"POST\">\n");
	echo("<div class=\"stdBlock\">\n");

	// ------- mostro albero ----------------------------------------------\

	include_once("admin/modules/news/news_class_sel.php");

	$idBlock=$block->getIdBlock();
	$lang=get_area_lang(get_block_idArea($idBlock));
	define("_BLOCK_LANG", $lang);
	$tree=createTreeView();

	if( isset($_POST[$tree->_getFolderNameId()]) ) { // Al cambio cartella:
		$folderid = $_POST[$tree->_getFolderNameId()];
	}
	else { // La prima volta che carica la pagina con l'albero:
		$folderid = $tree->getSelectedFolderId();
	}

	$tree->show_icons=0;

	$folder=$tree->tdb->getFolderById( $tree->getSelectedFolderId() );
	echo '<input type="hidden" value="'.$folderid.'" name="'.$tree->_getFolderNameId().'" />';
	echo '<input type="hidden" value="'.$tree->getSelectedFolderId().'" name="folder_id" />';
	echo '<div><b>'._FOLDER.':</b></div>';
	$tree->load();

	echo("<br />\n\n");
	$listView = $tree->getListView();
	$listView->setInsNew( false );
	$listView->printOut();
	// --------------------------------------------------------------------/

	echo("</div></form>\n");

	// back option
	$url="";
	$url.=$backurl["address"]."&amp;write=1&amp;idBlock=".$backurl["param"]["idBlock"]."&amp;idSubdivision=";
	$url.=$backurl["param"]["idSubdivision"];

	echo("<form action=\"$url\" method=\"POST\">\n");
	echo("<div class=\"stdBlock\">\n");
	echo("<input class=\"button\" type=\"submit\" value=\""._NEWS_BACK."\" />\n");
	echo("</div></form>\n");
}



function add_item_old($block) {
	global $prefixCms;

	$idBlock=(int)$block->getIdBlock();

	$qtxt="";
	$qtxt.="INSERT INTO ".$prefixCms."_area_block_items (idBlock, item_id, type) ";
	$qtxt.="VALUES ('$idBlock', '".(int)$_GET["item_id"]."', '".$_GET["type"]."');";

	$q=mysql_query($qtxt);
}


function del_item_old($id) {
	global $prefixCms;

	if ((int)$_GET["conf"] == 0) {
		$url="admin.php?modulename=manpage&amp;op=modblock&amp;write=1&amp;idBlock=".$_GET["idBlock"];
		$url.="&amp;idSubdivision=".$_GET["idSubdivision"];
		addTitleArea('content');
		echo("<div class=\"stdBlock\">\n");
		echo '<b>'._AREYOUSUREITEM.'</b><br /><br />'
			.'<div class="evidenceBlock">'
			.'[ <a href="'.$url."&amp;act_op=delitem&id=".$id
			.'&amp;conf=1">'._YES.'</a> | '
			.'<a href="'.$url.'">'._NO.'</a> ]'
			.'</div>';
		echo("</div><br />\n");
		$stop=1;
	}
	else if ((int)$_GET["conf"] == 1) {
		$qtxt="DELETE FROM ".$prefixCms."_area_block_items WHERE id='$id' LIMIT 1;";
		$q=mysql_query($qtxt);
		$stop=0;
	}

	return $stop;
}

?>