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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly!');

require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

function mediaBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	include_once($GLOBALS["where_cms"]."/admin/modules/media/media_class.php");

	$tree=createTreeView();

	if( isset($_POST[$tree->_getFolderNameId()]) ) { // Al cambio cartella:
		$folderid = $_POST[$tree->_getFolderNameId()];
	}
	else { // La prima volta che carica la pagina con l'albero:
		unset($tree);
		$tree=createTreeView(true, false, false, $opt["path"]);
		$folderid = $tree->getSelectedFolderId();
	}

	$tree->show_icons=0;

	$folder=$tree->tdb->getFolderById( $tree->getSelectedFolderId() );

	$name=$tree->_getFolderNameId();
	$value=$folderid;
	$out->add($form->getHidden($name, $name, $value));

	$name="folder_id";
	$value=$tree->getSelectedFolderId();
	$out->add($form->getHidden($name, $name, $value));

	$out->add($tree->load());


	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	if ((isset($opt["use_comments"])) && ($opt["use_comments"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_USE_COMMENTS"), "use_comments", "use_comments", "1", $chk));

	if ((isset($opt["recurse"])) && ($opt["recurse"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_MEDIA_RECOURSE"), "recurse", "recurse", "1", $chk));

	$out->add($form->getTextfield($lang->def("_MEDIA_NUMBER").":", "number", "number", 3, $opt["number"]));
	$out->add($form->getTextfield($lang->def("_MEDIA_COLS").":", "cols", "cols", 3, $opt["cols"]));


	if ((isset($opt["showtitle"])) && ($opt["showtitle"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_MEDIA_SHOWTITLE"), "showtitle", "showtitle", "1", $chk));

	if ((isset($opt["showdesc"])) && ($opt["showdesc"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_MEDIA_SHOWDESC"), "showdesc", "showdesc", "1", $chk));

	if ((isset($opt["openinplayer"])) && ($opt["openinplayer"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_MEDIA_OPENINPLAYER"), "openinplayer", "openinplayer", "1", $chk));

	$vt_list=array();
	$vt_list["gallery"]=$lang->def("_VIS_GALLERY");
	$vt_list["slide"]=$lang->def("_VIS_SLIDE");
	$out->add($form->getDropdown($lang->def("_MEDIA_VISTYPE").":", "vistype", "vistype", $vt_list, $opt["vistype"]));

	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}

function mediaBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "recurse", (int)$_POST["recurse"]);
	saveParam($block_id, "cols", (int)$_POST["cols"]);
	saveParam($block_id, "number", (int)$_POST["number"]);
	saveParam($block_id, "use_comments", (int)$_POST["use_comments"]);
	saveParam($block_id, "vistype", $_POST["vistype"]);
	saveParam($block_id, "showtitle", (int)$_POST["showtitle"]);
	saveParam($block_id, "showdesc", (int)$_POST["showdesc"]);
	saveParam($block_id, "openinplayer", (int)$_POST["openinplayer"]);

	saveBlockPath($block_id, (int)$_POST["folder_id"], "_media_dir");

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}

function mediaBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "path", "/");
	saveParam($block_id, "recurse", 1);
	saveParam($block_id, "showtitle", 1);
	saveParam($block_id, "showdesc", 0);
	saveParam($block_id, "number", 10);
	saveParam($block_id, "cols", 2);
	saveParam($block_id, "use_comments", 0);
	saveParam($block_id, "vistype", "gallery");
	saveParam($block_id, "openinplayer", 0);

}
?>
