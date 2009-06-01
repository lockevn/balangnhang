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

require_once($GLOBALS["where_cms"]."/modules/media/functions.php");

function media_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	switch ($opt["vistype"]) {

		// ----------------------------------------------------------------------
		case "slide" : {
			
			$path_q=get_path_q("media", $opt);

			$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_media ";
			$qtxt.="WHERE $path_q AND publish=1 AND fpreview!='' ORDER BY idFolder;";

			$q=mysql_query($qtxt); //echo $qtxt;

			if (($q) && (mysql_num_rows($q) > 0)) {
				$row=mysql_fetch_array($q);

				show_file($idBlock, "dir", $row["idMedia"], $opt, true);

				/*$preview=$row["fpreview"];
				$txt=loadTextof($idBlock);

				show_slide_start($preview, $txt, $idBlock); */
			}

		};break;


		// ----------------------------------------------------------------------
		case "gallery" : {

			show_gallery_pag($idBlock);

		};break;

	}

}


?>
