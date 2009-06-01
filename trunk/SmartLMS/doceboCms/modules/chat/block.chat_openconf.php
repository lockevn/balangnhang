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


function chat_openconf_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('chat', 'cms');

	if (!empty($title))
		$out->add("<div class=\"titleBlock\">$title</div>", "content");

	$out->add("<div class=\"body_block\">\n");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	if ($GLOBALS["where_scs"] === FALSE) {

		$out->add($lang->def("_SCS_REQUIRED"));

	}
	else {

		require_once($GLOBALS["where_cms"]."/modules/chat/openconference.php");

/*		if ($GLOBALS['current_user']->isAnonymous())
			$out->add($lang->def("_LOGIN_REQUIRED"));
		else */
		openconferenceDispatch("openconference");

	}

	$out->add("</div>\n"); // body_block

}


?>