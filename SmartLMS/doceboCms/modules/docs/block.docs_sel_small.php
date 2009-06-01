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


if (!defined("_DOCS_FPATH_INTERNAL")) define("_DOCS_FPATH_INTERNAL", "/doceboCms/docs/");
if (!defined("_DOCS_FPATH")) define("_DOCS_FPATH", $GLOBALS["where_files_relative"]._DOCS_FPATH_INTERNAL);
require_once($GLOBALS["where_cms"]."/modules/docs/functions.php");

function docs_sel_small_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	if($title != '')
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");

	$GLOBALS["page"]->add('<div class="body_block">', "content");
	show_doc_list($idBlock, "sel", "small");
	$GLOBALS["page"]->add('</div>', "content");

}


?>
