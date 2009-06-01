<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2008 by Enrico Zamprogno <ezamprogno[AT]gmail-com>      */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


function scanmenu_write_open_link($id_area, $page_title, $mr_title, $home)
{
	if ($home) {
		$GLOBALS["page"]->add('<a href="'.$GLOBALS["where_cms_relative"].'/">', "content");
	}
	else {
		$use_mod_rewrite=strtolower($GLOBALS["cms"]["use_mod_rewrite"]);
		if ($use_mod_rewrite == "off") {
			$GLOBALS["page"]->add('<a href="index.php?special=changearea&amp;newArea='.$id_area.'">', "content");
		} else {
			if ($mr_title <= ' ') { 
				$title=format_mod_rewrite_title($page_title);
			} else {
				$title= format_mod_rewrite_title($mr_title);
			}
			$GLOBALS["page"]->add("<a href=\"page/".$id_area."/".$title.".html\">", "content");
		}
	}
}


function scanmenu_show_menu_link($link, $id_area, $page_title, $mr_title, $home, $is_macroarea=FALSE)
{
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

	if ($link > " ") {
		$link =fillSiteBaseUrlTag($link);
		
		if(!stristr($link, 'www') && !stristr($link, 'http://') && !stristr($link, 'https://') || stristr($link, $_SERVER['HTTP_HOST']))	
			$new_win = false;
		else
			$new_win = true;
		
		if (!$new_win)
			$GLOBALS["page"]->add("<a href=\"".$link."\">", "content");
		else
			$GLOBALS["page"]->add(open_ext_link($link, ""), "content");
	}
	else {
		scanmenu_write_open_link($id_area, $page_title, $mr_title, $home);
	}

	$GLOBALS["page"]->add(trim(str_replace(' ','&nbsp;',$page_title))."</a>, </li>", "content");

}


function scanmenu_showMain($idBlock, $title, $block_op) {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$option = loadBlockOption($idBlock);
	$menu_type=$option["type"];

	$description=($option["type"] == "description" ? TRUE : FALSE);
 	$explode=($option["type"] == "explode" ? TRUE : FALSE);
 	$simple=($option["type"] == "simple" ? TRUE : FALSE);
	$from_current=($option["base"] == "current" ? TRUE : FALSE);
 	$from_macroarea=($option["base"] == "macroarea" ? TRUE : FALSE);
 
 	apri_blocco();
	
	if($title != '')
		$out->add('<h2>'.$title.'</h2>');

	$GLOBALS['menu_index']=0;
	$GLOBALS['macro_index']=0;

	if ($from_macroarea) {
		$current_lev=1;
		$current_path='/root';
	}
	else
	{
		$cur_area=getIdArea();
		$default_where ="AND publish='1'";
		$macroarea=get_area_parent_macroarea($cur_area, "id");
		$fields="idArea, idParent, title, path, lev, mr_title, alias, sitedesc, home, show_in_menu";
		$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_area ";
		$qtxt.="WHERE idArea='".$macroarea."' ".$default_where;
		$q=mysql_query($qtxt);
		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$idArea=$row["idArea"];
			$title=$row["title"];
			$alias=$row["alias"];
			$mr_title=$row["mr_title"];
			if ($alias >' ') {
				$title=$alias;
			} else if ($mr_title > ' ') {
				$title=$mr_title;
			}
			$sitedesc=$row['sitedesc'];
			$home=$row["home"];
			$main_path=$row["path"];
			$main_parent=$row["idParent"];
			$main_lev=$row["lev"];
			$main_show_in_menu=$row["show_in_menu"];
		}
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
	 
	$child_lev=$current_lev+1; $grandson_lev=$child_lev+1;
	$fields="t1.idArea, t1.title, t1.path, t1.lev, t1.mr_title, t1.alias, t1.sitedesc, t1.home, t1.link, t1.show_in_menu";
	$default_where =" AND t1.publish='1'";
	
	$qtxt="SELECT ".$fields." ";
	$qtxt.="FROM ".$GLOBALS["prefix_cms"]."_area as t1 ";

	switch ($menu_type) { 
	case 'explode': {
		$qtxt.=" WHERE t1.path LIKE '".$current_path."/%' AND " .
				"t1.lev>='".$child_lev."' AND t1.lev<='".$grandson_lev."'".$default_where." ";
		$qtxt.=" ORDER BY t1.path";
	
		$q=mysql_query($qtxt);
		
		if (($q) && (mysql_num_rows($q) > 0)) {
			$count=0; $menu=0;$nchild=0;
			while ($row=mysql_fetch_array($q)) {
				$id_area=$row["idArea"];
				if ((checkRoleForItem("page", $id_area)) && ($row["show_in_menu"] == 1)) {
					$lev=$row['lev'];
					$sitedesc=$row['sitedesc'];
					$link=$row["link"]; 
					$title=$row["title"]; 
					$mr_title=$row["mr_title"]; 
					$alias=$row['alias']; 
					$home=$row["home"];

					if ($alias >' ') 
						$title=$alias;
				
					$is_macroarea=($lev == 2 ? TRUE : FALSE);
					if ($lev == $child_lev) {
						if ($count>0) {
							if ($nchild==0){
								$out->add($menu_sitedesc);
							}
							else
							{
								$GLOBALS["page"]->add("<li class=\"menuline\">... </li> ", "content");
							}
							chiudi_menu($menu);
						}
						$menu++;
						apri_menu($menu,$link, $id_area, $title, $mr_title, $home);
						$menu_sitedesc=$sitedesc;
						$nchild=0;
					} else {
						scanmenu_show_menu_link($link, $id_area, $title, $mr_title, $home, $is_macroarea);
						$nchild++;
					}
						
					$count++;	
				}
			}
		}
		if ($nchild==0 && $menu_sitedesc>' ') {
			$out->add($menu_sitedesc);
		}
		else
		{
			$GLOBALS["page"]->add("<li class=\"menuline\">... </li> ", "content");
		}
		chiudi_menu($menu);
		
		unset($GLOBALS['menu_index']);
		unset($GLOBALS['macro_index']);
	}
	break;
	case 'description':
	{
		$qtxt.="WHERE t1.path LIKE '".$current_path."/%' AND " .
				"t1.lev='".$child_lev."' ".$default_where." ";
		$qtxt.="ORDER BY t1.path";

		$q=mysql_query($qtxt);
		if (($q) && (mysql_num_rows($q) > 0)) {
			$count=0; $menu=1;
			while ($row=mysql_fetch_array($q)) {
				$id_area=$row["idArea"];
				if ((checkRoleForItem("page", $id_area)) && ($row["show_in_menu"] == 1)) {
					$lev=$row['lev'];
					$link=$row["link"]; 
					$title=$row["title"]; $mr_title=$row["mr_title"];
					
					$alias=$row['alias'];
					if ($alias >' ') {
						$title=$alias;
					}
/*					 else if ($mr_title > ' ') {
						$title=$mr_title;
					}
*/				
					$home=$row["home"];
					$sitedesc=$row['sitedesc'];
					
//					$is_macroarea=($lev == 2 ? TRUE : FALSE);
//					echo '<br/>menu='.$menu.' link='.$link.' id_area='.$id_area.' title ='.$title.' mr_title='.$mr_title.' home='.$home;
					
					apri_menu($menu,$link, $id_area, $title, $mr_title, $home);
					$out->add($sitedesc);
					chiudi_menu($menu);
					$menu++;
				}
			}
		}
	}
	break;
	case 'simple': {
		$qtxt.="WHERE t1.path LIKE '".$current_path."/%' AND " .
				"t1.lev='".$child_lev."' ".$default_where." ";
		$qtxt.="ORDER BY t1.path";

		$q=mysql_query($qtxt);
		if (($q) && (mysql_num_rows($q) > 0)) {
			$count=0; $menu=1;
			while ($row=mysql_fetch_array($q)) {
				$id_area=$row["idArea"];
				if ((checkRoleForItem("page", $id_area)) && ($row["show_in_menu"] == 1)) {
					$lev=$row['lev'];
					$link=$row["link"]; $title=$row["title"]; $mr_title=$row["mr_title"]; $home=$row["home"];
										$alias=$row['alias'];
/*					if ($alias >' ') {
						$title=$alias;
					} else if ($mr_title > ' ') {
						$title=$mr_title;
					}
*/		
					$sitedesc=$row['sitedesc'];
					$is_macroarea=($lev == 2 ? TRUE : FALSE);
					apri_menu($menu,$link, $id_area, $title, $mr_title, $home);
//					$out->add($sitedesc);
					chiudi_menu($menu);
					$menu++;
				}
			}
		}
	}
	
	break;
	default:
	break; 
	}
	chiudi_blocco();
}

function chiudi_menu($menu) {
	$out=& $GLOBALS['page'];
	$out->add('</ul>'."\n");
	$out->add('</div></div>'."\n\n");
}


function apri_menu($menu, $link, $id_area, $title, $mr_title, $home) {
	$out=& $GLOBALS['page'];

	if ($menu % 2) {
		$out->add("\n\n<div class=\"linksCollection\">\n\n");
	} else {
		$out->add("\n\n<div class=\"evenLinksCollection\">\n\n");
	}
	$out->add("\n\n<div class=\"linksCollectionsRM\">\n\n");
	$out->add("<h3>", "content");
	if ($link > " ") {
		$link =fillSiteBaseUrlTag($link);
		
		if(!stristr($link, 'www') && !stristr($link, 'http://') && !stristr($link, 'https://') || stristr($link, $GLOBALS['_SERVER']['HTTP_REFERER']))
			$new_win = false;
		else
			$new_win = true;

		if (!$new_win)
			$out->add("<a href=\"".$link."\">", "content");
		else
			$out->add(open_ext_link($link, ""), "content");
	}
	else {
		scanmenu_write_open_link($id_area, $title, $mr_title, $home);
	}

	$out->add($title."</a></h3>", "content");
	$out->add('<ul>'."\n");
	
}

function apri_blocco() {
	$out=& $GLOBALS['page'];
//ezamprogno<at>gmaildotcom: disabling sphider indexing for keywords in menu
	$out->add('<!--sphider_noindex-->');
	$out->add('<div class="scanmenu_container">');
	$out->add('<div class="scanmenu_containerRM">');
}

function chiudi_blocco() {
	$out=& $GLOBALS['page'];
	$out->add('</div></div></div>'."\n\n");
//ezamprogno<at>gmaildotcom: disabling sphider indexing for keywords in menu
	$out->add('<!--/sphider_noindex-->');
	$out->add('<div class="no_float"/>');
}

?>
