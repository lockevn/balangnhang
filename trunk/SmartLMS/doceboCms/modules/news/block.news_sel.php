<?php

/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


function news_sel_showMain($idBlock, $title, $block_op) {
	//REQUIRES :areaFunction.php
	//EFFECTS  :display the navigator bar

	require_once($GLOBALS["where_cms"]."/modules/news/functions.php");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	show_news_list($idBlock, $title, "sel");

}

?>
