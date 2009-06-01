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


function newsBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	include_once($GLOBALS["where_cms"]."/admin/modules/news/news_class.php");

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

	//$out->add('<div><b>'._FOLDER.':</b></div>');
	$out->add($tree->load());


	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));


	$lang_arr=array();
	$tmp_array=$GLOBALS['globLangManager']->getAllLangCode();
	$lang_arr[]=$lang->def("_DEFAULT");
	foreach($tmp_array as $key=>$val) {
		$lang_arr[$val]=$val;
	}
	$force_lang=((isset($opt["force_lang"]) && (!empty($opt["force_lang"]))) ? $opt["force_lang"] : FALSE);
	$out->add($form->getDropdown($lang->def("_LANGUAGE"), "force_lang", "force_lang", $lang_arr, $force_lang));


	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	if ((isset($opt["use_comments"])) && ($opt["use_comments"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_USE_COMMENTS"), "use_comments", "use_comments", "1", $chk));

	if ((isset($opt["recurse"])) && ($opt["recurse"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_NEWS_RECOURSE"), "recurse", "recurse", "1", $chk));

	$out->add($form->getTextfield($lang->def("_NEWS_NUMBER"), "number", "number", 3, $opt["number"]));

	if ((isset($opt["show_newsdate"])) && ($opt["show_newsdate"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_NEWSDATE"), "show_newsdate", "show_newsdate", "1", $chk));

	if ((isset($opt["show_newslink"])) && ($opt["show_newslink"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_NEWSLINK"), "show_newslink", "show_newslink", "1", $chk));

	if ((isset($opt["show_commentslink"])) && ($opt["show_commentslink"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_COMMENTSLINK"), "show_commentslink", "show_commentslink", "1", $chk));

	if ((isset($opt["show_topiclink"])) && ($opt["show_topiclink"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_TOPICLINK"), "show_topiclink", "show_topiclink", "1", $chk));

	if ((isset($opt["show_search"])) && ($opt["show_search"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_SEARCH"), "show_search", "show_search", "1", $chk));

	if ((isset($opt["show_attach_inline"])) && ($opt["show_attach_inline"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_ATTACH_INLINE"), "show_attach_inline", "show_attach_inline", "1", $chk));


	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function newsBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "force_lang", $_POST["force_lang"]);
	saveParam($block_id, "recurse", (int)$_POST["recurse"]);
	saveParam($block_id, "show_newsdate", (int)$_POST["show_newsdate"]);
	saveParam($block_id, "show_newslink", (int)$_POST["show_newslink"]);
	saveParam($block_id, "show_commentslink", (int)$_POST["show_commentslink"]);
	// saveParam($block_id, "show_catlink", (int)$_POST["show_catlink"]);
	saveParam($block_id, "show_topiclink", (int)$_POST["show_topiclink"]);
	saveParam($block_id, "show_search", (int)$_POST["show_search"]);
	saveParam($block_id, "number", (int)$_POST["number"]);
	saveParam($block_id, "use_comments", (int)$_POST["use_comments"]);
	saveParam($block_id, "show_attach_inline", (int)$_POST["show_attach_inline"]);

	saveBlockPath($block_id, (int)$_POST["folder_id"], "_news_dir");

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function newsBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "path", "/");
	saveParam($block_id, "recurse", 1);
	saveParam($block_id, "show_newsdate", 1);
	saveParam($block_id, "show_newslink", 1);
	saveParam($block_id, "show_commentslink", 0);
	saveParam($block_id, "show_search", 0);
	saveParam($block_id, "show_topiclink", 0);
	saveParam($block_id, "number", 10);
	saveParam($block_id, "use_comments", 1);

}
?>