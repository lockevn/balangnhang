<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks                                  */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

function listcontent_sel($idBlock, $title, $block_op) {
	//REQUIRES :areaFunction.php
	//EFFECTS  :display the navigator bar
	global $prefixCms;
	include_once("core/manDateTime.php");

	$df=df_str(_DATEFORMAT, _DATESEP, "%");

	$option = loadBlockOption($idBlock);

	$queryF = "
	SELECT id
	FROM ".$prefixCms."_content_dir
	WHERE path LIKE '".$option['path'].( $option['recurse'] ? '%' : '' )."'";
	$reFolder = mysql_query($queryF);

	$idFolderCollection = '';
	while(list($id_f) = mysql_fetch_row($reFolder)) {
		$idFolderCollection .= $id_f.',';
	}
	if($option['path'] == '/' ) $idFolderCollection .= '0,';

	$query = "
	SELECT DATE_FORMAT(t1.publish_date, '$df') , t1.title, t1.short_desc, t1.idContent
	FROM ".$prefixCms."_content as t1, ".$prefixCms."_area_block_items as t2
	WHERE t1.publish = 1 AND
		t1.language = '".getCmsLang()."' AND t2.idBlock='$idBlock' AND t1.idContent=t2.item_id
	ORDER BY t1.publish_date";
	$reContent = mysql_query($query); //echo $query;

	echo '<div class="contentListBox">'
		.'<div class="titleBlock">'.$title.'</div>';
	while(list($date, $title, $desc, $idContent) = mysql_fetch_row($reContent)) {
		$url="index.php?mn=content&amp;pb=$idBlock&amp;id=$idContent";
		echo '<span class="content_title">'.$date.' <a href="'.$url.'">'.$title.'</a></span>';
		echo "<br />".$desc;
		echo "<br /><br />";
	}
	echo '</div>';

}

?>