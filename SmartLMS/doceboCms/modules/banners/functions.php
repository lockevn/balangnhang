<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

define("_BANNER_FPATH", $GLOBALS["where_files_relative"]."/doceboCms/banners/");

require_once($GLOBALS["where_cms"]."/lib/lib.manModules.php");
require_once($GLOBALS["where_cms"]."/lib/lib.cms_common.php");

function show_banner($cat_id=1) {
	$macroarea=get_area_parent_macroarea(getIdArea());
	$lang=getLanguage();

	$vis_q ="(";
	$vis_q.="(t2.item_type='macroarea' AND t2.item_id='$macroarea') OR ";
	$vis_q.="(t2.item_type='language' AND t2.item_val='$lang'))";

	$fields="t1.banner_id, t1.cat_id, t1.bdesc, t1.banfile, t1.bancode, t1.ban_w, t1.ban_h, t1.ban_bg, t1.kind, t1.banurl, t2.item_type, t2.item_id, t2.item_val";
	$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_banner as t1, ".$GLOBALS["prefix_cms"]."_banner_rules as t2 ";
	$qtxt.="WHERE t1.banner_id=t2.banner_id AND $vis_q AND t1.status='1' AND t1.cat_id='".$cat_id."' ORDER BY t1.banner_id;";

	$q=mysql_query($qtxt); //echo $qtxt;

	$res="";

	$banners=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		$i=1;
		$old_id="";
		while ($row=mysql_fetch_array($q)) {

			if ($row["banner_id"] != $old_id)
				$ok=reset_flags();

			$ok["group"]=(checkRoleForItem("banner", $row["banner_id"]) ? 1:0);
			if (($row["item_type"] == "macroarea") && ($row["item_id"] == $macroarea)) $ok["macroarea"]=1;
			if (($row["item_type"] == "language") && ($row["item_val"] == $lang)) $ok["language"]=1;

			if (check_flags($ok)) {
				$banners[$i]=$row["banner_id"];
				$banrow[$i]=$row;
				$i++;
			}
			$old_id=$row["banner_id"];
		}
	}

	if (!isset($banrow)) // No results found
		return "";

	$show_ban=rand(1,count($banners));
	$bdesc=$banrow[$show_ban]["bdesc"];

	switch ($banrow[$show_ban]["kind"]) {
		case "image": {
			$host=$_SERVER['HTTP_HOST'];
			$link=fillSiteBaseUrlTag($banrow[$show_ban]["banurl"]);
			if (!strstr($link, $host)) { // External link
				$res.=open_ext_link("banner.php?id=".$banners[$show_ban]);
			}
			else { // Internal link
				$res.="<a href=\"banner.php?id=".$banners[$show_ban]."\">";
			}
			$res.="<img src=\""._BANNER_FPATH.$banrow[$show_ban]["banfile"]."\" alt=\"$bdesc\" title=\"$bdesc\" /></a>\n";
		} break;
		case "code": {
			$res.=$banrow[$show_ban]["bancode"];
		} break;
		case "flash": {
			$bg=$banrow[$show_ban]["ban_bg"];
			$w=$banrow[$show_ban]["ban_w"];
			$h=$banrow[$show_ban]["ban_h"];
			$res.="<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\" width=\"$w\" height=\"$h\" />\n";
			$res.="<param name=\"movie\" value=\"./"._BANNER_FPATH.$banrow[$show_ban]["banfile"]."\" />\n";
			$res.="<param name=\"menu\" value=\"false\" />\n";
			$res.="<param name=\"quality\" value=\"high\" />\n";
			$res.="<param name=\"bgcolor\" value=\"$bg\" />\n";
			$res.="<embed src=\"./"._BANNER_FPATH.$banrow[$show_ban]["banfile"]."\" menu=\"false\" quality=\"high\" bgcolor=\"$bg\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_P rod_Version=ShockwaveFlash\">\n";
			$res.="</embed>\n";
			$res.="</object>\n";
		} break;
	}

	if ($show_ban > 0) {
		$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_banner SET impression=impression+1 WHERE banner_id='".$banners[$show_ban]."'";
		$q=mysql_query($qtxt);
	}

	check_expired($banners[$show_ban]);

	addToBannerStats($banrow[$show_ban], "impression");

	return $res;
}


function reset_flags() {
	$res=array();

	$res["group"]=0;
	$res["macroarea"]=0;
	$res["language"]=0;

	return $res;
}


function check_flags($arr) {
	$res=1;

	foreach ($arr as $key=>$val) {
		if (!$val) $res=0;
	}

	return $res;
}



function check_expired($id) {


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner WHERE banner_id='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);

		$exp=0;

		$now=time();
		$impression=$row["impression"];
		$expimp=$row["expimp"];
		$expdate=$row["expdate"];

		if (($expimp > 0) && ($expimp-$impression < 1)) $exp=1;
		if (($expdate > 0) && ($expdate < $now)) $exp=1;

		if ($exp)
			mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_banner SET status='0' WHERE banner_id='$id' LIMIT 1;");
	}

}



function addToBannerStats($banner, $rec_type) {

	$browser=getBrowserInfo();

	$rec_from=($GLOBALS['current_user']->isAnonymous() ? "anonymous" : "registered");

	if ((int)$banner["banner_id"] > 0) {

		$qtxt ="INSERT INTO ".$GLOBALS["prefix_cms"]."_banner_raw_stat ";
		$qtxt.="(rec_time, rec_type, banner_id, idArea, cat_id, kind, rec_from, b_name, b_os, b_country, language) ";
		$qtxt.="VALUES (NOW(), '".$rec_type."', '".$banner["banner_id"]."', '".getIdArea()."', '".$banner["cat_id"]."', ";
		$qtxt.="'".$banner["kind"]."', '".$rec_from."', '".$browser["os"]."', '".$browser["browser"]."', ";
		$qtxt.="'".$browser["main_lang"]."', '".getLanguage()."')";

		$q=mysql_query($qtxt);

		return $q;
	}
	else {
		return false;
	}
}


?>
