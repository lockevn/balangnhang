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

require_once($GLOBALS["where_cms"]."/modules/media/functions.php");

function media_sel_showMain($idBlock, $title, $block_op) {
	global $prefixCms;

	$opt=loadBlockOption($idBlock);

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

/*	if($title != '') echo '<div class="titleBlock">'.$title.'</div>';

	$lang=get_lang();

	$t1=$prefixCms."_area_block_items";
	$t3=$prefixCms."_media_info";
	$qtxt="";
	$qtxt.="SELECT t1.id, t2.fname, t2.real_fname, t2.fpreview, title, sdesc  FROM $t1 as t1, ";
	$qtxt.=$prefixCms."_media as t2 ";
	$qtxt.="LEFT JOIN $t3 ON ($t3.idm=item_id AND $t3.lang='$lang') ";
	$qtxt.="WHERE t1.idBlock='$idBlock' AND t2.idMedia=t1.item_id AND t1.type='media' ";
	$qtxt.="AND t2.publish=1 AND t2.fpreview!='' ORDER BY t1.id;";

	$q=mysql_query($qtxt); //echo $qtxt;
*/
	switch ($opt["vistype"]) {

		// ----------------------------------------------------------------------
		case "slide" : {

			$path_q=get_path_q("media", $opt);
			// Todo: cambiare la query.. mmh

			$qtxt ="SELECT item_id FROM ".$GLOBALS["prefix_cms"]."_area_block_items ";
			$qtxt.="WHERE type='media' AND idBlock='".$idBlock."' ORDER BY id";

			$q=mysql_query($qtxt);

			if (($q) && (mysql_num_rows($q) > 0)) {
				$row=mysql_fetch_array($q);

				show_file($idBlock, "sel", $row["item_id"], $opt, true);

			}

		};break;


		// ----------------------------------------------------------------------
		case "gallery" : {

			show_gallery_pag($idBlock, "sel");

		};break;

	}

}


?>
