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

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

define("_MEDIA_FPATH_INTERNAL", "/doceboCms/media/");
define("_MEDIA_FPATH", $GLOBALS["where_files_relative"]._MEDIA_FPATH_INTERNAL);
define("_MEDIA_PPATH_INTERNAL", "/doceboCms/media/preview/");
define("_MEDIA_PPATH", $GLOBALS["where_files_relative"]._MEDIA_PPATH_INTERNAL);
require_once($GLOBALS["where_cms"]."/lib/lib.manModules.php");
require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");


function check_media_perm($pb, $id=0) {

	// --- Checking block permissions:

	$pb=(int)$pb;

	if ($pb == 0)
		return false;

	if ((int)$GLOBALS["area_id"] == 0)
		return false;

	$b_info=getBlockInfo($pb);

	if (!$b_info["view"])
		return false;

	if (!isset($b_info["name"]))
		return false;


	// --- Checking item permissions:

	if (($b_info["name"] == "media") && ($id > 0)) {

		$opt=loadBlockOption($pb);
		$path=$opt["path"];

		if ($opt["recurse"])
			$path_q="t2.path LIKE '$path%'";
		else
			$path_q="t2.path='$path'";

		$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_media as t1 ";
		$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_media_dir as t2 ";
		$qtxt.="ON $path_q AND t1.idFolder=t2.id ";
		if ($path == "/")
			$qtxt.=" OR t1.idFolder='0'";
		$qtxt.=" WHERE t1.idMedia='$id' AND t1.publish='1'";

		$q=mysql_query($qtxt);
		if ((!$q) || (mysql_num_rows($q) == 0)) {
			return false;
		}
	}


	if (($b_info["name"] == "media_sel") && ($id > 0)) {
		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE idBlock='$pb' AND item_id='$id';";
		$q=mysql_query($qtxt);

		if ((!$q) || (mysql_num_rows($q) == 0)) {
			return false;
		}
	}


	// --- All test passed
	return true;
}


function count_media_hit($id) {

	require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

	$id=(int)$id;

	$key=array("media_item", $id, "hit");

	if ((!isBot()) && (!getItemValue($key))) {
		$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_media SET click=click+1 WHERE idMedia='".$id."' LIMIT 1";
		$q=mysql_query($qtxt);
		setItemValue($key, true);
	}

}


function can_see_media_old ($pb, $id) {
	// Controllo che l'utente possa visualizzare il file..


	return true;  //-TP//

	$can_see=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE idBlock='$pb' AND item_id='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$valid_item=1;
	}


	$opt=loadBlockOption($pb);

	$path_q=get_path_q("media", $opt);
	$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_media ";
	$qtxt.="WHERE $path_q AND publish='1' AND idMedia='$id';";
	$q=mysql_query($qtxt); //echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) {
		$valid_folder=1;
	}


	if (($valid_item) || ($valid_folder)) {
		$user_grp=getUserGroup();
		$allowed_grp=db_block_groups($pb);
		$can_see=can_see_block($user_grp, $allowed_grp);
	}

	return $can_see;
}


function show_slide_start($preview, $txt, $idBlock, $ext="") {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');


	$url="index.php?mn=media&amp;pi=".getPI()."&amp;op=slide$ext";

	$out->add("<table>\n");
	$out->add("<tr><td class=\"slidetxt\">$txt\n");
	$out->add("</td><td><img src=\""._MEDIA_PPATH."$preview\" alt=\"\" title=\"\" class=\"slidelink\" />\n");
	$out->add("</td></tr>\n");
	$out->add("<tr><td colspan=\"2\" class=\"startslide\">\n");
	$out->add("<a href=\"$url\">"._START_SLIDE."</a> &raquo;\n");
	$out->add("</td></tr>\n");
	$out->add("</table>\n");

}


function show_gallery_pag($pb, $type="dir", $pos=0) {

	// ---------------------      ---------------------
	if (!check_media_perm($pb)) die("You can't access!");
	// ---------------------      ---------------------

	require_once($GLOBALS["where_framework"]."/lib/lib.multimedia.php");

	$opt=loadBlockOption($pb);

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$lang=DoceboLanguage::createInstance("media", "cms");

	$sel_lang=getLanguage();

	if ($type == "dir") {
		$cf_path=$opt["path"];
	}


	$out->add("<div class=\"show_gallery\">\n");

	// --------------------- Finding folder id --------------------------

	if ((isset($_GET["folder"])) && ($_GET["folder"] > 0)) {

		$folder=(int)$_GET["folder"];
		$qtxt="SELECT id, path, lev FROM ".$GLOBALS["prefix_cms"]."_media_dir WHERE id='".$folder."'";

	}
	else {

		$folder=0;
		$qtxt="SELECT id, lev FROM ".$GLOBALS["prefix_cms"]."_media_dir WHERE path='".$opt["path"]."'";

	}
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);

		if (isset($row["path"])) {
			$cf_path=$row["path"];

			if (substr($cf_path, 0, strlen($opt["path"])) != $opt["path"])
				$cf_path=$opt["path"]; // Anti "smart guy" code ;)
		}

		$cf_id=$row["id"];
		$lev=$row["lev"]+1;

	}
	else if (($q) && (mysql_num_rows($q) == 0)) {
		$lev=1; // ..assuming that the path == root
		$cf_id=0;
	}

	// --------------------- Categories Box ----------------------------

	if (($type == "dir") && ($opt["recurse"])) {

		$fields="t1.id as folder_id, t2.title as folder_title, COUNT(DISTINCT t3.idMedia) as item_tot";
		$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_media_dir as t1 ";
		$qtxt.="INNER JOIN ".$GLOBALS["prefix_cms"]."_media_titles as t2 ON (t2.iddir=t1.id) ";
		$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_media as t3 ON (t3.idFolder=t1.id) ";
		$qtxt.="WHERE t1.lev='".$lev."' AND t1.path LIKE '".$cf_path."%' ";
		$qtxt.="AND t2.lang='".getLanguage()."' AND t3.publish=1 ";
		$qtxt.="GROUP BY t1.id ";

		$out->add('<div class="gallery_title_top">');
		folders_nav_bar($folder, $opt["path"], "media");
		$out->add('</div>');

		drawCategoriesBox($out, $lang, $qtxt, "media");

	}

	// -----------------------------------------------------------------




	$sp=$pos*$opt["number"];

	if ($type == "dir") {

		// [TODO] ?
		// spostare il pezzo che carica cf_id fuori dall'if (($type == "dir") && ($opt["recurse"])) {
		// in modo che venga sempre calcolato il cf_id anche se poi non verra' mostrata la lista delle sotto cat.
		$path_q="t1.idFolder='".$cf_id."'";

		$t1=$GLOBALS["prefix_cms"]."_media";
		$t2=$GLOBALS["prefix_cms"]."_media_info";

		$sel_q="t1.idMedia, t1.idFolder, t1.fname, t1.real_fname, t1.fpreview, t1.media_url, t2.title, t2.sdesc, t2.ldesc, t2.lang";

		$qtxt ="SELECT $sel_q FROM $t1 as t1 ";
		$qtxt.="LEFT JOIN $t2 as t2 ON (t2.idm=t1.idMedia AND t2.lang='$sel_lang') ";
		$qtxt.="WHERE $path_q AND t1.publish='1' ORDER BY t1.idMedia";

	}
	else if ($type == "sel") {

		$t1=$GLOBALS["prefix_cms"]."_area_block_items";
		$t3=$GLOBALS["prefix_cms"]."_media_info";

		$qtxt="";
		$qtxt.="SELECT t1.id, t2.idMedia, t2.fname, t2.real_fname, t2.fpreview, t2.media_url, title, sdesc  FROM $t1 as t1, ";
		$qtxt.=$GLOBALS["prefix_cms"]."_media as t2 ";
		$qtxt.="LEFT JOIN $t3 ON ($t3.idm=item_id AND $t3.lang='$sel_lang') ";
		$qtxt.="WHERE t1.idBlock='$pb' AND t2.idMedia=t1.item_id AND t1.type='media' ";
		$qtxt.="AND t2.publish=1 ORDER BY t1.id";

	}


	$q=mysql_query($qtxt); //echo $qtxt;

	if ($q) {

		$tot=mysql_num_rows($q);

		if ($tot > 0) {

			if ((!isset($opt["onerandom"])) || (!$opt["onerandom"])) {
				$qtxt.=" LIMIT $sp,".$opt["number"];
			}
			else {
				$num=rand(0, ($tot-1));
				$tot=1;
				$qtxt.=" LIMIT $num,1";
			}

			$q=mysql_query($qtxt);  //echo $qtxt;
		}
	}


	$txt=loadTextof($pb);
	if (($txt != "<br _moz_editor_bogus_node=\"TRUE\"/>") && ($txt != "")) {
		$out->add($txt."<br />\n");
	}

	if ((isset($opt["cols"])) && ($opt["cols"] > 0))
		$cols=(int)$opt["cols"];
	else
		$cols=2;

	$x=($cols > 2 ? 0.6 : 1);
	$col_w=(int)((94-($cols)*$x)/$cols);

	if (($q) && (mysql_num_rows($q) > 0)) {

		$out->add("<div class=\"images_frame\">\n");
		$out->add("<div class=\"images_box\">\n");

		$i=1;
		while($row=mysql_fetch_array($q)) {

			if ((isset($opt["showtitle"])) && ($opt["showtitle"])) $title=$row["title"];
			if ((isset($opt["showdesc"])) && ($opt["showdesc"])) $sdesc=$row["sdesc"];

			$out->add("<div style=\"width: ".$col_w."%;\" class=\"image_col\" >\n");


			$file=_MEDIA_PPATH.$row["fpreview"];
			if ($row["fpreview"] != "")
				$img="<img class=\"image_thumb\" src=\"".$file."\" alt=\"".$row["fname"]."\" title=\"".$row["fname"]."\" />\n";
			else {
				if (!empty($row["media_url"])) {
					$media_type ="streaming";
				}
				else {
					$media_type =getMediaType($row["fname"]);
				}
				$img="<img class=\"image_thumb\" src=\"".getPathImage('fw')."media/".$media_type.".png\" alt=\"".$row["fname"]."\" title=\"".$row["fname"]."\" />\n";
			}

			if ($type == "dir") {
				$url="index.php?mn=media&amp;pi=".getPI()."&amp;op=file&amp;folder=".$row["idFolder"]."&amp;id=".$row["idMedia"];
			}
			else if ($type == "sel") {
				$url="index.php?mn=media&amp;pi=".getPI()."&amp;op=file&amp;sel=1&amp;id=".$row["idMedia"];
			}

			if ((!isset($opt["onlypreview"])) || (!$opt["onlypreview"])) {
				$out->add("<a href=\"$url\">$img</a>");
			}
			else {
				$out->add($img);
			}

			if ((isset($title)) && ($title != ""))
				$out->add("<b>".$title."</b>\n");

			if ((isset($sdesc)) && ($sdesc != ""))
				$out->add("<div>".$sdesc."</div>\n");


			$out->add("</div>\n"); // image_col

			if ($i % $cols == 0)
				$out->add("<div class=\"no_float\"></div>\n");

			$i++;
		}

		if (($i-1) % $cols != 0)
			$out->add("<div class=\"no_float\"></div>\n");
		$out->add("</div>\n"); // images_box
		$out->add("</div>\n"); // images_frame


	}

	//---------------------------------------- navigation table:

	if ($type == "dir") $op="gallery";
	else if ($type == "sel") $op="gallery_sel";

	$url="index.php?mn=media&amp;pi=".getPI()."&amp;op=$op&amp;pos=";

	show_nav_bar($out, $lang, $url, $pos, $opt["number"], $tot);


	$out->add("</div>\n"); // show_gallery
}




function show_file($pb, $type="dir", $id=FALSE, $opt=FALSE, $slide=FALSE) {

	if ($id === false)
		$id=(int)$_GET["id"];

	if ($opt === false)
		$opt=loadBlockOption($pb);

	if (isset($opt["showtitle"]))
		$opt_showtitle=$opt["showtitle"];
	else
		$opt_showtitle="";

	if (isset($opt["showdesc"]))
		$opt_showdesc=$opt["showdesc"];
	else
		$opt_showdesc="";

	if (isset($opt["path"]))
		$opt_path=$opt["path"];
	else
		$opt_path="/";

	$use_comments=((isset($opt["use_comments"]) && ($opt["use_comments"] == 1)) ? true : false);
	$open_in_player=((isset($opt["openinplayer"]) && ($opt["openinplayer"] == 1)) ? true : false);

	load_media($id, $type, $slide, $opt_showtitle, $opt_showdesc, $opt_path, $pb, $use_comments, $open_in_player);

}



function load_media($id, $type="dir", $slide=false, $showtitle, $showdesc, $path, $pb, $show_comments=false, $open_in_player=FALSE) {
	$res ="";
	// ---------------------                ---------------------
	if (!check_media_perm($pb, (int)$id)) die("You can't access!");
	// ---------------------                ---------------------

	require_once($GLOBALS["where_framework"]."/lib/lib.multimedia.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");

	count_media_hit($id);


	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance("media", "cms");

	$sel_lang=getLanguage();

	if ($type == "dir") {

		$t1=$GLOBALS["prefix_cms"]."_media";
		$t2=$GLOBALS["prefix_cms"]."_media_info";

		$qtxt ="SELECT * FROM $t1 LEFT JOIN $t2 ON ($t2.idm=idMedia AND $t2.lang='$sel_lang') ";
		$qtxt.="WHERE idMedia='$id' AND publish=1 ORDER BY idMedia";

	}
	else if ($type == "sel") {

		$t1=$GLOBALS["prefix_cms"]."_area_block_items";
		$t2=$GLOBALS["prefix_cms"]."_media";
		$t3=$GLOBALS["prefix_cms"]."_media_info";

		$qtxt ="SELECT t1.id, t2.idMedia, t2.fname, t2.real_fname, t2.fpreview, t2.click, title, sdesc  FROM $t1 as t1, ";
		$qtxt.=$GLOBALS["prefix_cms"]."_media as t2 ";
		$qtxt.="LEFT JOIN $t3 as t3 ON (t3.idm=t1.item_id AND t3.lang='$sel_lang') ";
		$qtxt.="WHERE t1.idBlock='".$GLOBALS["pb"]."' AND t2.idMedia=t1.item_id AND t1.type='media' ";
		$qtxt.="AND t2.publish='1' AND t1.item_id='$id' ORDER BY t1.id";

	}

	$q=mysql_query($qtxt); //echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
	}

	$file=_MEDIA_FPATH.$row["real_fname"];
	$mime=mimetype(end(explode(".", $row["real_fname"])));


	if (isset($_GET["folder"]))
		$folder=$_GET["folder"];
	else if (isset($row["idFolder"]))
		$folder=$row["idFolder"];
	else
		$folder=0;


	$out->add("<div class=\"show_gallery\">\n");

	$out->add('<div class="load_media">');
	if (($type == "dir") && (!$slide)) {
		$out->add('<div class="gallery_title_top">');
		folders_nav_bar($folder, $path, "media");
		$out->add(' </div>');
	}


	$out->add('<div class="gallery_box">');
	$out->add('<div class="gallery_text">');

	if (($showtitle) && ($row["title"] != "")) {
		$out->add('<div class="gallery_title">'.$row["title"].'</div>');
	}

	if (!empty($row["media_url"])) {

		if (isYouTube($row["media_url"])) {
			$video_id =getYouTubeId($row["media_url"]);
			$res.=getYouTubeCode($video_id);
		}
		else {
			$res.=getDoceboFlashPlayer($row["media_url"]);
		}

	}
	else if (!empty($row["fname"])) {

		if (!empty($row["media_url"])) {
			$media_type ="streaming";
		}
		else {
			$media_type =getMediaType($row["fname"]);
		}

		$res="";
		if ($media_type == "image") {
			$res.="<img class=\"image_frame\" src=\"".$file."\" alt=\"".$row["fname"]."\" title=\"".$row["fname"]."\" />\n";
		}
		else {

			if (($open_in_player) && (isPossibleEmbedPlay("", $row["fname"]))) {
				$res.=getEmbedPlay(_MEDIA_FPATH, $row["real_fname"]);
			}
			else {
				$alt =$row["fname"];
				$img ='<img src="'.getPathImage('fw').mimeDetect($row["fname"]).'" ';
				$img.='alt="'.$alt.'" title="'.$alt.'" />';
				$res.='<a href="'.$file.'">'.$img." ".$row["fname"]."</a>\n";
			}

		}
	}


	$out->add($res);

	if (($showdesc) && ($row["ldesc"] != "")) {
		$out->add("".$row["ldesc"]."");
	}


	$out->add("<div class=\"gallery_click\">");
	$out->add($lang->def("_CLICK")." <span>(".$row["click"].")</span>");
	$out->add("</div>");


	$out->add('<div class="gallery_line">&nbsp;</div>');
	if (!$slide)
		image_slide_nav_bar($id, $type, $folder);
	else
		slideshow_nav_bar($type, $pb);


	if ($show_comments) {
		load_media_comments($id, $pb, $slide);
	}

	$out->add('</div>'); // gallery_text
	$out->add('<div class="no_float"></div>');

	$out->add('</div>'); // gallery_box
	$out->add('</div>'); // load_media
	$out->add("</div>\n"); // show_gallery
}



function show_nav_bar(& $out, & $lang, $url, $pos, $ipp, $tot, $maxpl=10) {
	// $pos=current page
	// $ipp=items per page; used to find out how many page we'll have.
	// $tot=total items
	// $maxpl=max pages linked

	$pagcount=(int)($tot/$ipp);
	if ($tot % $ipp != 0) $pagcount++;

	if ($pagcount <= 1) return 1;

	$empty_img="<img class=\"fakesmallbtn\" src=\"".getPathImage()."standard/pixel.gif\" alt=\" \" title=\" \" />";
	$prev_img="<img src=\"".getPathImage()."navbar/prev.gif\" alt=\"".$lang->def("_PREV")."\" title=\"".$lang->def("_PREV")."\" />";
	$next_img="<img src=\"".getPathImage()."navbar/next.gif\" alt=\"".$lang->def("_NEXT")."\" title=\"".$lang->def("_NEXT")."\" />";
	$end_img="<img src=\"".getPathImage()."navbar/end.gif\" alt=\"".$lang->def("_END")."\" title=\"".$lang->def("_END")."\" />";
	$start_img="<img src=\"".getPathImage()."navbar/start.gif\" alt=\"".$lang->def("_START")."\" title=\"".$lang->def("_START")."\" />";

	$out->add("<div class=\"media_nav_bar\">\n");
	if ($pos > 0) {	 // prev
		$out->add("<span class=\"media_prev\"><a href=\"$url".(0)."\">$start_img</a></span>\n");
		$out->add("<span class=\"media_prev\"><a href=\"$url".($pos-1)."\">$prev_img</a></span>\n");
	}
	else {
		$out->add("<span class=\"media_prev\">$empty_img</span>\n");
		$out->add("<span class=\"media_prev\">$empty_img</span>\n");
	}

	if ($pagcount > $maxpl) {
		$start=(int)(($pos+1)-$maxpl/2);
		$end=(int)(($pos+1)+$maxpl/2)-1;
		if ($start < 1) {
			$diff=1-$start;
			$start=$start+$diff;
			$end=$end+$diff;
		}
		if ($end > $pagcount) {
			$diff=$end-$pagcount;
			$start=$start-$diff;
			$end=$end-$diff;
		}
	}
	else {
		$start=1;
		$end=$pagcount;
	}

	if (($pagcount > $maxpl) && ($start > 1)) $out->add("...");
	for ($i=$start; $i<=$end; $i++) {
//		if (($pos >= ($i-1)*$opt["number"]) && ($pos < ($i)*$opt["number"])) {
		if ($pos == ($i-1)) {
			$sel="_sel";
		}
		else {
			$sel="";
		}
		$out->add("<a class=\"media_pag_num$sel\" href=\"$url".(int)($i-1)."\">$i</a>\n");
	}
	if (($pagcount > $maxpl) && ($end < $pagcount)) $out->add("...");


	$sp=$pos*$ipp;
	if ($sp < $tot-$ipp) { // next
		$out->add("<span class=\"media_next\"><a href=\"$url".($pos+1)."\">$next_img</a></span>\n");
		$out->add("<span class=\"media_next\"><a href=\"$url".($pagcount-1)."\">$end_img</a></span>\n");
	}
	else {
		$out->add("<span class=\"media_next\">$empty_img</span>\n");
		$out->add("<span class=\"media_next\">$empty_img</span>\n");
	}

	$out->add("</div>\n");

}



function load_media_comments($id, $pb, $slide=false) {

	require_once($GLOBALS["where_framework"]."/lib/lib.sysforum.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance("sysforum", "cms");

	$anonymous_comment=$GLOBALS["cms"]["anonymous_comment"];

	$force_comment=0;
	$can_comment=0;
	$can_post=(bool)(($GLOBALS['current_user']->isLoggedIn()) || ($anonymous_comment == "on"));
	$is_admin=isCmsAdmin();

	$opt=loadBlockOption($GLOBALS["pb"]);
	if (isset($opt["ov_comments"]))
		$force_comment=(int)$opt["ov_comments"];
	else
		$force_comment=0;

	$can_comment=true;

	if (($can_comment) || ($force_comment)) {

		$out->add("<div class=\"commentsHead\">".$lang->def("_COMMENTS")."</div>\n");

		$sf=new sys_forum("cms", "media_comment", $id);
		$sf->setPrefix("cms");
		$sf->can_write=$can_post;
		$sf->can_moderate=$is_admin;

		$folder=(int)importVar("folder");
		if (!$slide) {
			$sf->url="index.php?mn=media&amp;pi=".getPI()."&amp;op=file&amp;folder=".$folder."&amp;id=".$id;
		}
		else {
			$cur_pos=(int)importVar("pos");
			$sf->url="index.php?mn=media&amp;pi=".getPI()."&amp;op=slideshow&amp;pos=".$cur_pos;//."&amp;id=".$id;
		}
		$out->add($sf->show());
	}

}




function image_slide_nav_bar($id, $type="dir", $folder) {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance("media", "cms");


	if ($type == "dir") {
		$fields="idMedia";
		$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_media ";
		$qtxt.="WHERE idFolder='".$folder."' AND publish='1' ORDER BY idMedia";

		$extra="";
	}
	else if ($type == "sel") {
		$fields="item_id as idMedia";
		$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_area_block_items ";
		$qtxt.="WHERE idBlock='".$GLOBALS["pb"]."' AND type='media' ORDER BY id";
		$extra="&amp;sel=1";
	}

	$q=mysql_query($qtxt);

	$pos=0;
	$cur_pos=0;
	$found=false;
	if ($q) {
		$tot=mysql_num_rows($q);

		if ($tot > 0) {
			while(($row=mysql_fetch_array($q)) && (!$found)) {

				if ($row["idMedia"] == $id) {
					$cur_pos=$pos;
					$found=true;
				}

				$pos++;
			}
		}
	}

	$url="index.php?mn=media&amp;pi=".getPI().$extra."&amp;op=gallery_setpos&amp;folder=".$folder."&amp;pos=";
	show_nav_bar($out, $lang, $url, $cur_pos, 1, $tot);

}


function slideshow_nav_bar($type="dir", $pb) {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance("media", "cms");

	$opt=loadBlockOption($pb);

	if ($type == "dir") {

		$path=$opt["path"];
		$recurse=$opt["recurse"];

		if ($recurse)
			$path_q="t2.path LIKE '".$path."%'";
		else
			$path_q="t2.path='".$path."'";

		$qtxt = "
		SELECT idMedia
		FROM ".$GLOBALS["prefix_cms"]."_media as t1
			JOIN ".$GLOBALS["prefix_cms"]."_media_dir as t2
		WHERE publish='1'
			AND $path_q
			AND t1.idFolder = t2.id ";
		if($path == "/") $qtxt.=" OR t1.idFolder='0'";
		$qtxt .= "GROUP BY idMedia
		ORDER BY idMedia";

		$extra="";
	}
	else if ($type == "sel") {

		$fields="item_id as idMedia";
		$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_area_block_items ";
		$qtxt.="WHERE idBlock='".$pb."' AND type='media' ORDER BY id";

		$extra="&amp;sel=1";
	}

	$q=mysql_query($qtxt);

	if ((isset($_GET["pos"])))
		$cur_pos=(int)$_GET["pos"];
	else
		$cur_pos=0;

	if ($q) {
		$tot=mysql_num_rows($q);
	}

	$url="index.php?mn=media&amp;pi=".getPI().$extra."&amp;op=slideshow&amp;pos=";
	show_nav_bar($out, $lang, $url, $cur_pos, 1, $tot);

}


function goto_selpos($type="dir") {

	$folder=(int)importVar("folder");
	$pos=(int)importVar("pos");

	if ($type == "dir") {
		$fields="idMedia";
		$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_media ";
		$qtxt.="WHERE idFolder='".$folder."' AND publish='1' ORDER BY idMedia LIMIT ".$pos.",1";
		$extra="";
	}
	else if ($type == "sel") {
		$fields="item_id as idMedia";
		$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_area_block_items ";
		$qtxt.="WHERE idBlock='".$GLOBALS["pb"]."' AND type='media' ORDER BY id LIMIT ".$pos.",1";
		$extra="&amp;sel=1";
	}

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$id=$row["idMedia"];
	}

	$url="index.php?mn=media&amp;pi=".getPI().$extra."&amp;op=file&amp;folder=".$folder."&amp;id=".$id;
	jumpTo($url);

}



function show_slide($pb, $type="dir") {

	$pos=(int)importVar("pos");
	$opt=loadBlockOption($pb);

	if ($type == "dir") {

		$path=$opt["path"];
		$recurse=$opt["recurse"];

		if ($recurse)
			$path_q="t2.path LIKE '".$path."%'";
		else
			$path_q="t2.path='".$path."'";

		$qtxt = "
		SELECT idMedia
		FROM ".$GLOBALS["prefix_cms"]."_media as t1
			JOIN ".$GLOBALS["prefix_cms"]."_media_dir as t2
		WHERE publish='1'
			AND $path_q
			AND t1.idFolder = t2.id ";
		if($path == "/") $qtxt.=" OR t1.idFolder='0'";
		$qtxt .= "GROUP BY idMedia
		ORDER BY idMedia
		LIMIT ".$pos.",1";

		$extra="";
	}
	else if ($type == "sel") {
		$fields="item_id as idMedia";
		$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_area_block_items ";
		$qtxt.="WHERE idBlock='".$pb."' AND type='media' ORDER BY id LIMIT ".$pos.",1";
	}

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$id=$row["idMedia"];
	}

	show_file($pb, $type, $id, $opt, true);

}


function downloadMediaItem() {
	require_once($GLOBALS["where_framework"]."/lib/lib.download.php");

	if (isset($_GET["id"]))
		$id=(int)$_GET["id"];
	else
		return FALSE;

	// ---------------------                ---------------------
	if (!check_media_perm($GLOBALS["pb"], (int)$id)) die("You can't access!");
	// ---------------------                ---------------------


	$qtxt ="SELECT fname, real_fname FROM ".$GLOBALS["prefix_cms"]."_media ";
	$qtxt.="WHERE idMedia='".$id."' AND publish='1'";

	$q=mysql_query($qtxt); //echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);

		$fn=$row["real_fname"];
		$ext=end(explode(".", $fn));
		$fname=basename($row["fname"], ".".end(explode(".", $row["fname"])));

		ob_clean();
		sendFile(_MEDIA_FPATH_INTERNAL, $fn, $ext, $fname);
	}
	die();
}


?>
