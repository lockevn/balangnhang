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


function linksBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	include_once($GLOBALS["where_cms"]."/admin/modules/links/links_class.php");

	$tree=createTreeView();

	if( isset($_POST[$tree->_getFolderNameId()]) ) { // Al cambio cartella:
		$folderid = $_POST[$tree->_getFolderNameId()];
	}
	else { // La prima volta che carica la pagina con l'albero:
		unset($tree);
		$tree=createTreeView(true, false, false, $opt["path"]);
		$folderid = $tree->getSelectedFolderId();

		//set_folder_from_path("/hlkjl2/sub", $tree);
		//set_folder_from_path($opt["path"], $tree);
		//$tree->expandPath($opt["path"]);
		//--debug-// print_r($tree->expandList);
	}

	$tree->show_icons=0;

	$folder=$tree->tdb->getFolderById( $tree->getSelectedFolderId() );

	$name=$tree->_getFolderNameId();
	$value=$folderid;
	$out->add($form->getHidden($name, $name, $value));

	$name="folder_id";
	$value=$tree->getSelectedFolderId();
	$out->add($form->getHidden($name, $name, $value));

	$out->add('<div><b>'._FOLDER.':</b></div>');
	$out->add($tree->load());


	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	if ((isset($opt["use_comments"])) && ($opt["use_comments"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_USE_COMMENTS"), "use_comments", "use_comments", "1", $chk));	
	
	/* if ((isset($opt["ov_comments"])) && ($opt["ov_comments"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_OVERRIDE_COM"), "ov_comments", "ov_comments", "1", $chk)); */



	if ((isset($opt["recurse"])) && ($opt["recurse"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_LINKS_RECOURSE"), "recurse", "recurse", "1", $chk));

	$out->add($form->getTextfield($lang->def("_LINKS_NUMBER"), "number", "number", 3, $opt["number"]));


	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function linksBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "recurse", (int)$_POST["recurse"]);
	saveParam($block_id, "number", (int)$_POST["number"]);
	saveParam($block_id, "use_comments", (int)$_POST["use_comments"]);

	saveBlockPath($block_id, (int)$_POST["folder_id"], "_links_dir");

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function linksBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "path", "/");
	saveParam($block_id, "recurse", 1);
	saveParam($block_id, "number", 10);
	saveParam($block_id, "use_comments", 0);

}

?>