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


function write_open_link($id_area, $page_title, $mr_title, $home) {

	$use_mod_rewrite=$GLOBALS["cms"]["use_mod_rewrite"];

	if ($mr_title != "") {
		$title=format_mod_rewrite_title($mr_title);
	}
	else {
		$title=format_mod_rewrite_title($page_title);
	}

	if ($home) {
		$GLOBALS["page"]->add('<a class="voicemenu" href="'.$GLOBALS["where_cms_relative"].'/">', "content");
	}
	else {
		if (strtolower($use_mod_rewrite) == "off") {
			$GLOBALS["page"]->add('<a class="voicemenu" href="index.php?special=changearea&amp;newArea='.$id_area.'">', "content");
		}
		else {
			$GLOBALS["page"]->add("<a class=\"voicemenu\" href=\"page/".$id_area."/".$title.".html\">", "content");
		}
	}

}


function show_menu_link($link, $id_area, $page_title, $mr_title, $home, $is_macroarea=FALSE) {

	if ($is_macroarea) {
		$mline ="macro_menuline_".(int)$GLOBALS['macro_index'];
		$GLOBALS["page"]->add("<li class=\"macroarea ".$mline."\">", "content");
		$GLOBALS['macro_index']=($GLOBALS['macro_index'] > 0 ? 0 : 1);
		$GLOBALS['menu_index']=0;
	}
	else {
		$mline ="menuline_".(int)$GLOBALS['menu_index'];
		$GLOBALS["page"]->add("<li class=\"".$mline."\">", "content");
		$GLOBALS['menu_index']=($GLOBALS['menu_index'] > 0 ? 0 : 1);
	}

	if ($link != "") {
		$link =fillSiteBaseUrlTag($link);
		
		if(!stristr($link, 'www') && !stristr($link, 'http://') && !stristr($link, 'https://') || stristr($link, $GLOBALS['_SERVER']['HTTP_REFERER']))
			$new_win = false;
		else
			$new_win = true;

		if (!$new_win)
			$GLOBALS["page"]->add("<a class=\"voicemenu\" href=\"".$link."\">", "content");
		else
			$GLOBALS["page"]->add(open_ext_link($link, "voicemenu"), "content");
	}
	else {
		write_open_link($id_area, $page_title, $mr_title, $home);
	}

	$GLOBALS["page"]->add($page_title."</a></li>", "content");

}


function menu_showMain($idBlock, $title, $block_op) {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$option = loadBlockOption($idBlock);
	$out->add('<div class="menu_block">');

	if($title != '')
		$out->add('<div class="title_menu">'.$title.'</div>');

	$GLOBALS['menu_index']=0;
	$GLOBALS['macro_index']=0;


	$cur_area=getIdArea();

	$default_where ="AND publish='1'";
	$macroarea=get_area_parent_macroarea($cur_area, "id");
	$fields="idArea, idParent, title, path, lev, mr_title, home, show_in_menu";
	$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_area ";
	$qtxt.="WHERE idArea='".$macroarea."' ".$default_where;
	$q=mysql_query($qtxt);
	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$idArea=$row["idArea"];
		$title=$row["title"];
		$mr_title=$row["mr_title"];
		$home=$row["home"];
		$main_path=$row["path"];
		$main_parent=$row["idParent"];
		$main_lev=$row["lev"];
		$main_show_in_menu=$row["show_in_menu"];
	}


	$needs_current_info=array("onlyparent", "onlycurrent", "onlychild");
	if (in_array($option['type'], $needs_current_info)) {

		$fields="path, lev";
		$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_area ";
		$qtxt.="WHERE idArea='".$cur_area."' ".$default_where;
		$q=mysql_query($qtxt);
		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$current_path=$row["path"];
			$current_lev=$row["lev"];
		}

	}


	$fields="t1.idArea, t1.title, t1.path, t1.lev, t1.mr_title, t1.home, t1.link, t1.show_in_menu";
	$default_where ="AND t1.publish='1'";

	$out->add("\n\n<ul class=\"menu_lateral\">\n\n");

	if(isset($option['type'])) {

		switch($option['type']) {

			case "under" : { // -------------------------------------------| under |-

				if ($main_show_in_menu == 1) {
					$out->add("<li>");
					write_open_link($idArea, $title, $mr_title, $home);
					$out->add($title."</a></li>");
				}

				$qtxt ="";
				$qtxt.="SELECT ".$fields." ";
				$qtxt.="FROM ".$GLOBALS["prefix_cms"]."_area as t1 ";
				$qtxt.="WHERE t1.path LIKE '".$main_path."/%' ".$default_where." ";
				$qtxt.="ORDER BY t1.path";

			};break;

			case "complete" : { // -------------------------------------| complete |-

				$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_area as t1 ";
				$qtxt.="WHERE t1.lev>1 ".$default_where." ";
				$qtxt.="AND (t1.path LIKE '".$main_path."%' OR t1.idParent='".$main_parent."' ) ";
				$qtxt.="ORDER BY t1.path";
				// this way we get the list of all the macroarea of the current area plus the sub-areas
				// of the selected macroarea.

			} break;

			case "macroarea": { // ------------------------------------| macroarea |-

				$qtxt ="";
				$qtxt.="SELECT ".$fields." ";
				$qtxt.="FROM ".$GLOBALS["prefix_cms"]."_area as t1 ";
				$qtxt.="WHERE t1.idParent='".$main_parent."' ".$default_where." ";
				$qtxt.="ORDER BY t1.path";

			} break;

			case "onlyparent": { // ----------------------------------| onlyparent |-

				// bug fix  by: lambedue

				$parent_lev=($current_lev > 2 ? $current_lev-1 : $current_lev);

				$current_path_arr = explode("/", trim($current_path, "/")); // -----conversione della stringa indicante il percorso corrente in array dei singoli livelli
				$parent_path_arr = array_slice($current_path_arr,0,-2); // ------ eliminazione degli ultimi due elementi della matrice
				$parent_path1 = "/".implode("/", $parent_path_arr); // riconversione in stringa del path reso monco

				$qtxt ="";
				$qtxt.="SELECT ".$fields." ";
				$qtxt.="FROM ".$GLOBALS["prefix_cms"]."_area as t1 ";
				$qtxt.="WHERE t1.publish='1' AND t1.path LIKE '".$parent_path1."/%' AND t1.lev='".$parent_lev."' "; //il LIKE seleziona le path che contengono il percorso reso monco e l'ultimo AND garantisce che sia selezionato il livello immediatamente precedente a quello corrente
				$qtxt.="ORDER BY t1.path";

			} break;

			case "onlycurrent": { // --------------------------------| onlycurrent |-

				$current_path_arr=explode("/", trim($current_path, "/"));
				array_pop($current_path_arr);

				$parent_path="/".implode("/", $current_path_arr);

				$qtxt ="";
				$qtxt.="SELECT ".$fields." ";
				$qtxt.="FROM ".$GLOBALS["prefix_cms"]."_area as t1 ";
				$qtxt.="WHERE t1.path LIKE '".$parent_path."/%' AND t1.lev='".$current_lev."' ".$default_where." ";
				$qtxt.="ORDER BY t1.path";

			} break;

			case "onlychild": { // ------------------------------------| onlychild |-

				$child_lev=$current_lev+1;

				$qtxt ="";
				$qtxt.="SELECT ".$fields." ";
				$qtxt.="FROM ".$GLOBALS["prefix_cms"]."_area as t1 ";
				$qtxt.="WHERE t1.path LIKE '".$current_path."/%' AND t1.lev='".$child_lev."' ".$default_where." ";
				$qtxt.="ORDER BY t1.path";

			} break;

		}
	}

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {

			$id_area=$row["idArea"];
			if ((checkRoleForItem("page", $id_area)) && ($row["show_in_menu"] == 1)) {

				$is_macroarea=($row["lev"] == 2 ? TRUE : FALSE);
				show_menu_link($row["link"], $id_area, $row["title"], $row["mr_title"], $row["home"], $is_macroarea);

			
			}
		}
	}

	unset($GLOBALS['menu_index']);
	unset($GLOBALS['macro_index']);

	$out->add('</ul>');
	$out->add('</div>'."\n\n");
}


?>
