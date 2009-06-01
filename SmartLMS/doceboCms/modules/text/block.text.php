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

function text_ShowMain( $idBlock, $title, $block_op ) {
	//REQUIRES :areaFunction.php
	//EFFECTS  :display the navigator bar

	$qtxt ="SELECT textof FROM ".$GLOBALS["prefix_cms"]."_text ";
	$qtxt.="WHERE idBlock = '".$idBlock."' AND language = '".getCmsLang()."'";

	list($textof) = mysql_fetch_row(mysql_query($qtxt));

	if( $title != '')
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");
	$GLOBALS["page"]->add('<div class="body_block">'.fixAnchors($textof).'</div>', "content");
}

?>
