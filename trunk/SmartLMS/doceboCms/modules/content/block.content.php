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

function content_showMain($idBlock, $title, $block_op) {

	require_once($GLOBALS["where_cms"]."/modules/content/functions.php");

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");

	show_single_content($idBlock);

}

?>