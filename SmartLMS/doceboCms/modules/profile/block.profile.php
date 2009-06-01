<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

function profile_showMain($idBlock, $title, $block_op) {

	require_once($GLOBALS["where_cms"]."/modules/profile/functions.php");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");

	if(!$GLOBALS['current_user']->isAnonymous()) {
		$op="profile";
		profileDispatch($op);
	}
	else {
		$GLOBALS["page"]->add(def("_LOGIN_REQUIRED", "profile", "cms"), "content");
	}

}


?>
