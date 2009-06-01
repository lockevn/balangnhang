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

function news_small_showMain($idBlock, $title, $block_op) {

	require_once($GLOBALS["where_cms"]."/modules/news/functions.php");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	show_news_list($idBlock, $title, "dir", "small");

}

?>
