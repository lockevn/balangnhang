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

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS["where_cms"]."/modules/feedback/functions.php");

function feedback_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");

	$GLOBALS["page"]->add('<div class="body_block">', "content");

	$qtxt="SELECT fdesc FROM ".$GLOBALS["prefix_cms"]."_form WHERE idForm='".$opt["form_id"]."'";
	list($form_desc)=mysql_fetch_row(mysql_query($qtxt));

	if (!empty($form_desc))
		$GLOBALS["page"]->add("<div class=\"cms_form_desc\">".$form_desc."</div>\n", "content");

	show_feedback_mask($idBlock, $opt["form_id"]);
	$GLOBALS["page"]->add('</div>', "content");

}


?>