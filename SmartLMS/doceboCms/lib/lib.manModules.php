<?php

/*************************************************************************/
/* DOCEBO - Content Management System                                    */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks                                  */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

function get_path_q($type, $opt, $dtp="", $sel_path="") {

	$res="";
	$path="";

	if ($sel_path != "")
		$path=$sel_path;
	else if (isset($opt["path"]))
		$path=$opt["path"];

	if ((isset($opt["recurse"])) && ($opt["recurse"]))
		$path_q="path LIKE '$path%'"; else $path_q="path='$path'";

	if ($dtp != "") // table prefix
		$dtp.=".";

	$qtxt="SELECT id FROM ".$GLOBALS["prefix_cms"]."_".$type."_dir WHERE $path_q;";

	$q=mysql_query($qtxt);
	if ($path == "/") $la=array(0); else $la=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$la[]=$row["id"];
		}
	}

	if ((is_array($la)) && (count($la) > 0)) {
		$list=implode(",", $la);
		$res=$dtp."idFolder IN ($list)";
	}
	else {
		$res="1";
	}

	return $res;
}



function drawCategoriesBox(& $out, & $lang, $qtxt, $type, $lev=FALSE) {

	$img_path=getPathImage()."block/";

	switch ($type) {

		case "media": {
			$url="index.php?mn=media&amp;op=gallery&amp;pi=".getPI()."&amp;folder=";
		} break;

		case "links": {
			$url="index.php?mn=links&amp;op=links&amp;pi=".getPI()."&amp;folder=";
		} break;

		case "docs": {
			$url="index.php?mn=docs&amp;op=docs&amp;pi=".getPI()."&amp;folder=";
		} break;

	}

	$q=mysql_query($qtxt);

	$do_output =FALSE;
	$out_list ="";
	if (($q) && (mysql_num_rows($q) > 0)) {

		while ($row=mysql_fetch_assoc($q)) {

			if ((!isset($row["lev"])) || ($row["lev"] == $lev)) {
				$title=$row["folder_title"];

				$out_list.="<li><a href=\"".$url.$row["folder_id"]."\">".$title."<br />";
                if ($row["item_tot"]>0) {
					$out_list.="<span class=\"cat_item_info\">";
					$out_list.=$lang->def("_ITEMS"). " (".$row["item_tot"].")</span>";
				}
				$out_list.="</a></li>\n";

				$do_output =TRUE;
			}
		}
	}

	if ($do_output) {
		$out->add("<div class=\"cat_list_back\">\n");
		$out->add("<div class=\"cat_list_box\">\n");
		$out->add("<ul class=\"cat_list_box\">");

		$out->add($out_list);

		$out->add("</ul>");
		$out->add("<div class=\"no_float\"></div>\n");
		$out->add("</div>\n"); // cat_list_box
		$out->add("</div>\n"); // cat_list_back
	}
}





function folders_nav_bar($folder, $base_path, $type) {

	switch ($type) {

		case "media": {
				$id_field="idMedia";
				$dir_tab_name="_media_dir";
				$titles_tab_name="_media_titles";
				$url="index.php?mn=media&amp;pi=".getPI()."&amp;op=gallery&amp;folder=";
				$lang_mod="media";
		} break;

		case "links": {
				$id_field="idLinks";
				$dir_tab_name="_links_dir";
				$titles_tab_name="_links_titles";
				$url="index.php?mn=links&amp;pi=".getPI()."&amp;op=links&amp;folder=";
				$lang_mod="links";
		} break;

		case "docs": {
				$id_field="idDocs";
				$dir_tab_name="_docs_dir";
				$titles_tab_name="_docs_titles";
				$url="index.php?mn=docs&amp;pi=".getPI()."&amp;op=docs&amp;folder=";
				$lang_mod="docs";
		} break;

	}


	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance($lang_mod, "cms");

	if ($folder > 0) {

		$qtxt="SELECT path FROM ".$GLOBALS["prefix_cms"].$dir_tab_name." WHERE id='".$folder."'";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$path=$row["path"];
		}

	}
	else {
		$path="/";
	}

	$qtxt="SELECT id FROM ".$GLOBALS["prefix_cms"].$dir_tab_name." WHERE path='".$base_path."'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$base_folder=$row["id"];
	}
	else {
		$base_folder=0;
	}


	$rel_path=substr_replace($path, '', 0, strlen($base_path));

	$path_arr=explode("/", $rel_path);

	$in_path="";
	$in_query=array();
	foreach($path_arr as $key=>$val) {
		$in_path.="/".$val;
		$in_query[]="'".$in_path."'";
	}


	$qtxt ="SELECT t1.id, t2.title FROM ".$GLOBALS["prefix_cms"].$dir_tab_name." as t1 ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"].$titles_tab_name." as t2 ON (t1.id=t2.iddir AND t2.lang='".getLanguage()."') ";
	$qtxt.="WHERE t1.path IN (".implode(",", $in_query).")";

	$q=mysql_query($qtxt);

	$nav_arr=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {
			$nav_arr[$row["id"]]=$row["title"];
		}
	}

	if (count($nav_arr) > 0)
		$out->add("<a href=\"".$url.$base_folder."\">".$lang->def("_MAIN")."</a>\n");
	foreach($nav_arr as $key=>$val) {
		$out->add(" &gt; <a href=\"".$url.$key."\">".$val."</a>\n");
	}


}


function getModuleCss($pb) {

	$b_info=getBlockInfo($pb);

	if (isset($b_info["css"]))
		$css=$b_info["css"];
	else {
		$opt=loadBlockOption($pb);
		if ((isset($opt["css"])) && ((int)$opt["css"] > 0))
			$css=$opt["css"];
		else
			$css=1;
	}

	return "contentblock_".$css;
}


function getModuleBlockTitle($pb, $with_code=TRUE) {
	$res ="";
	$title ="";

	$b_info =getBlockInfo($pb);
	if (isset($b_info["title"])) {
		$title =$b_info["title"];
	}
	else {
		$opt =loadBlockOption($pb);
		if ((isset($opt["title"])) && ((int)$opt["title"] > 0)) {
			$title =$opt["title"];
		}
	}

	if (($with_code) && (!empty($title))) {
		$res ='<div class="titleBlock">'.$title.'</div>';
	}
	else {
		$res =$title;
	}
	
	return $res;
}



function getCmsNavSymbols() {

	$res = array(
		'start' => array(
			'img' => '<img src="'.getPathImage().'navbar/start.gif" alt="'.def('_START').'" title="'.def('_START').'" />',
			'src' => getPathImage().'navbar/start.gif',
			'alt' => def('_START')
		),
		'prev' => array(
			'img' => '<img src="'.getPathImage().'navbar/prev.gif" alt="'.def('_PREV').'" title="'.def('_PREV').'" />',
			'src' => getPathImage().'navbar/prev.gif',
			'alt' => def('_PREV')
		),
		'next' => array(
			'img' => '<img src="'.getPathImage().'navbar/next.gif" alt="'.def('_NEXT').'" title="'.def('_NEXT').'" />',
			'src' => getPathImage().'navbar/next.gif',
			'alt' => def('_NEXT')
		),
		'end' => array(
			'img' => '<img src="'.getPathImage().'navbar/end.gif" alt="'.def('_END').'" title="'.def('_END').'" />',
			'src' => getPathImage().'navbar/end.gif',
			'alt' => def('_END')
		)
	);

	return $res;
}


function cmsUrlManagerSetup($mr_pattern=FALSE, $mr_items=FALSE, $std_title=FALSE, $std_query=FALSE, $query_map=FALSE, $instance_name=FALSE) {

	require_once($GLOBALS['where_framework']."/lib/lib.urlmanager.php");

	if (($instance_name !== FALSE) && (!empty($instance_name))) {
		$um=& UrlManager::getInstance($instance_name);
	}
	else {
		$um=& UrlManager::getInstance();
	}


	if ($std_query !== FALSE)
		$um->setStdQuery($std_query);

	if (($query_map !== FALSE) && (is_array($query_map)))
		$um->setQueryMap($query_map);


	if (($mr_pattern !== FALSE) && ($GLOBALS["cms"]["use_mod_rewrite"] == "on")) {
		$um->setUseModRewrite(TRUE);
		$um->setModRewriteUrlPattern($mr_pattern);
		$um->setModRewriteUrlItems($mr_items);
		if ($std_title !== FALSE)
			$um->setModRewriteTitle($std_title);
	}
	else {
		$um->setUseModRewrite(FALSE);
	}

	if (defined("POPUP_MODE")) {
		$um->setBaseUrl("popup.php");
	}

}


function getAttachList($table, $id_name, $id_val, $attach_type=FALSE) {
	$res=array();

	$qtxt="SELECT * FROM ".$table." WHERE ".$id_name."='".(int)$id_val."'";
	$q=mysql_query($qtxt);

	$attach_list=array();

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {

			$type=$row["type"];

			if ((!isset($attach_list[$type])) || (!in_array($row["idAttach"], $attach_list[$type]))) {
				$attach_list[$type][$row["id"]]=$row["idAttach"];
			}

		}
	}

	if ($attach_type === FALSE)
		$attach_type=array("docs", "media", "links");

	foreach ($attach_type as $type) {

		if ((isset($attach_list[$type])) && (is_array($attach_list[$type]))) {

			switch ($type) {
				case "docs": {

					$fields="t1.idDocs, t1.fname, t1.real_fname, t2.title";
					$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_docs as t1 ";
					$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_docs_info as t2 ON ";
					$qtxt.="(t2.idd=t1.idDocs AND t2.lang='".getLanguage()."') ";
					$qtxt.="WHERE t1.idDocs IN (".implode(",", $attach_list[$type]).")";
					$type_id_name="idDocs";

				} break;
				case "media": {

					$fields="t1.idMedia, t1.fname, t1.real_fname, t2.title";
					$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_media as t1 ";
					$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_media_info as t2 ON ";
					$qtxt.="(t2.idm=t1.idMedia AND t2.lang='".getLanguage()."') ";
					$qtxt.="WHERE t1.idMedia IN (".implode(",", $attach_list[$type]).")";
					$type_id_name="idMedia";

				} break;
				case "links": {

					$fields="t1.idLinks, t1.url, t2.title";
					$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_links as t1 ";
					$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_links_info as t2 ON ";
					$qtxt.="(t2.idl=t1.idLinks AND t2.lang='".getLanguage()."') ";
					$qtxt.="WHERE t1.idLinks IN (".implode(",", $attach_list[$type]).")";
					$type_id_name="idLinks";

				} break;
			}

			$attach_id_arr=array_flip($attach_list[$type]);


			$q=mysql_query($qtxt);

			if (($q) && (mysql_num_rows($q) > 0)) {
				while ($row=mysql_fetch_array($q)) {

					$id=$row[$type_id_name];
					$attach_id=$attach_id_arr[$id];
					$res[$type][$attach_id]=$row;

				}
			}
		}
	}

	return $res;
}


function downloadModuleAttachment($table, $id_name, $id_val, $attach_id, $type) {
	require_once($GLOBALS["where_framework"]."/lib/lib.download.php");

	$type_arr=array($type);
	$attach_list=getAttachList($table, $id_name, $id_val, $type_arr);

	// Download paths setup:
	$path["docs"]="/doceboCms/docs/";
	$path["media"]="/doceboCms/media/";

	$attach_id_arr=array_keys($attach_list[$type]);

	if (in_array($attach_id, $attach_id_arr)) {
		// Seems that the attach_id actually corresponds to an attachment
		// available into the selected module.. so we can download it!

		$attach=$attach_list[$type][$attach_id];

		$fn=$attach["real_fname"];
		$ext=end(explode(".", $fn));
		$fname=basename($attach["fname"], ".".$ext);

		ob_clean();
		sendFile($path[$type], $fn, $ext, $fname);
	}
}


/**
 * Fix anchors in provided text when mod rewrite is enbled
 * -- example: --
 * from: href="#anchor"
 * to: href="page/1/MyPage.html#anchor"
 **/
function fixAnchors($text) {
	$use_mod_rewrite =($GLOBALS["cms"]["use_mod_rewrite"] == 'off' ? FALSE : TRUE);

	if ($use_mod_rewrite) {

		if (!isset($GLOBALS["page_relative_path"])) {
			$req_uri =$_SERVER["REQUEST_URI"];
			$abs_path =preg_replace("/http[s]{0,1}:\\/\\/.*?(\\/.*)/", "$1", $GLOBALS["cms"]["url"]);
			$GLOBALS["page_relative_path"] =str_replace($abs_path, "", $req_uri);
		}

		$text =str_replace('href="#', 'href="'.$GLOBALS["page_relative_path"].'#', $text);
		$text =str_replace('href="./#', 'href="'.$GLOBALS["page_relative_path"].'#', $text);
	}

	return $text;
}


?>