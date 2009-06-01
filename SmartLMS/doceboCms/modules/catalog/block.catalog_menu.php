<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.org                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


function catalog_menu_showMain($idBlock, $title, $block_op) {

	//$opt=loadBlockOption($idBlock);

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");


	$GLOBALS["page"]->add("<div class=\"body_block\">\n", "content");

	require_once($GLOBALS["where_framework"]."/lib/lib.platform.php");
	if (canUsePlatform("ecom")) {
		getPageId();
		setPageId($GLOBALS["area_id"], $idBlock);
		require_once($GLOBALS["where_cms"]."/modules/catalog/functions.php");
		$GLOBALS["page"]->add(getCategoriesMenu($idBlock), "content");
	}
	else {
		$lang =& DoceboLanguage::createInstance('catalog');
		$GLOBALS["page"]->add($lang->def("_ECOM_NOT_FOUND", "content"));
	}

	$GLOBALS["page"]->add("</div>\n", "content"); // body_block
}

?>
