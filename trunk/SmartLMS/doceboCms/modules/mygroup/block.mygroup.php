<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Emanuele Sandri, Fabio Pirovano, Giovanni Derks */
/*                      http://www.docebocms.com                         */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

function mygroup_showMain($idBlock, $title, $block_op) {

	require_once($GLOBALS["where_cms"]."/modules/mygroup/functions.php");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");

	if(!$GLOBALS['current_user']->isAnonymous()) {
		$op="group";
		mygroupDispatch($op);
	}

		/* $GLOBALS["op"]="user_display";
		require_once($GLOBALS['where_framework'].'/modules/event_manager/event_manager.php');
		eventDispatch($GLOBALS['op']); */

}


?>
