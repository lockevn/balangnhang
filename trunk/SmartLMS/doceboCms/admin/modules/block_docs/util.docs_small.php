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


function docs_smallBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	include_once($GLOBALS["where_cms"]."/admin/modules/docs/docs_class.php");

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

	$out->add('<div><b>'.$lang->def("_FOLDER").':</b></div>');
	$out->add($tree->load());


	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add($form->getTextfield($lang->def("_DOCS_NUMBER").":", "number", "number", 3, $opt["number"]));

	if ((isset($opt["order_by"])) && ($opt["order_by"]=='order_by_date'))  {
		$selected='order_by_date' ; 
	} else { // default sorting by title
		$selected= 'order_by_title';	
	}
	 
	$out->add($form->getRadioSet($lang->def("_DOCS_ORDER_BY"), "order_by", "order_by", 
		array( $lang->def("_DOCS_ORDER_BY_DATE") => 'order_by_date',$lang->def("_DOCS_ORDER_BY_TITLE")  => 'order_by_title'), $selected ));
	if ((isset($opt["order_descending"])) && ($opt["order_descending"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_DOCS_ORDER_DESC"), "order_descending", "order_descending", "1", $chk));
	
	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));
}

function docs_smallBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "number", (int)$_POST["number"]);
	saveParam($block_id, "order_by", $_POST["order_by"]);
	saveParam($block_id, "order_descending", (int)$_POST["order_descending"]);
	saveBlockPath($block_id, (int)$_POST["folder_id"], "_docs_dir");

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);
}


function docs_smallBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "path", "/");
	saveParam($block_id, "number", 10);
	saveParam($block_id, "order_by", 'order_by_title');
	saveParam($block_id, "order_descending", 0);

}


?>