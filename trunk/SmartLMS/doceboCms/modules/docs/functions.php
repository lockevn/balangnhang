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

require_once($GLOBALS["where_cms"]."/lib/lib.manModules.php");


function check_docs_perm($pb, $id=0) {

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

	if (($b_info["name"] == "docs") && ($id > 0)) {

		$opt=loadBlockOption($pb);
		$path=$opt["path"];

		if ($opt["recurse"]) $path_q="t2.path LIKE '$path%'"; else $path_q="t2.path='$path'";

		$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_docs as t1 ";
		$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_docs_dir as t2 ";
		$qtxt.="ON (($path_q AND t1.idFolder=t2.id)";
		if ($path == "/")
			$qtxt.=" OR t1.idFolder='0'";
		$qtxt.=") WHERE t1.idDocs='$id' AND t1.publish='1'";

		$q=mysql_query($qtxt);
		if ((!$q) || (mysql_num_rows($q) == 0)) {
			return false;
		}
	}


	if (($b_info["name"] == "docs_sel") && ($id > 0)) {
		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE idBlock='$pb' AND item_id='$id';";
		$q=mysql_query($qtxt);

		if ((!$q) || (mysql_num_rows($q) == 0)) {
			return false;
		}
	}


	// --- All test passed
	return true;
}



function count_docs_hit($id) {

	require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

	$id=(int)$id;

	$key=array("docs_item", $id, "hit");

	if ((!isBot()) && (!getItemValue($key))) {
		$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_docs SET click=click+1 WHERE idDocs='".$id."' LIMIT 1";
		$q=mysql_query($qtxt);
		setItemValue($key, true);
	}

}


function check_docs_perm_old($pb) {
	// Controllo che l'utente possa visualizzare i file..

	return true;

	$user_grp=getUserGroup();
	$allowed_grp=db_block_groups($pb);
	$can_see=can_see_block($user_grp, $allowed_grp);

	if (!$can_see) die("You can't access!");
}


function can_see_docs_old ($pb, $id) {
	// Controllo che l'utente possa visualizzare il file..

return true;

	$can_see=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE idBlock='$pb' AND item_id='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$valid_item=1;
	}


	$opt=loadBlockOption($pb);

	$path_q=get_path_q("docs", $opt);
	$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_docs ";
	$qtxt.="WHERE $path_q AND publish='1' AND idDocs='$id';";
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



function show_doc_list($pb, $type="dir", $block="normal", $pos=0) {

	require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");

	// ---------------------     ---------------------
	if (!check_docs_perm($pb)) die("You can't access!");
	// ---------------------     ---------------------

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$lang=DoceboLanguage::createInstance("docs", "cms");


	check_docs_perm($pb);
	$opt=loadBlockOption($pb);

	$sel_lang=getLanguage();


	if ($type == "dir") {
		$cf_path=$opt["path"];

		if ( ((!isset($opt["recurse"])) || (!$opt["recurse"])) && ($opt["path"] == "/"))
			$cf_id=0;
	}

	$out->add("<div class=\"show_docs\">\n");

	if ($block == "normal") {
		$cf_arr=showCategoriesBox($out, $lang, $opt, $type);

		if (isset($cf_arr["cf_path"]))
			$cf_path=$cf_arr["cf_path"];

		if (isset($cf_arr["cf_id"]))
			$cf_id=$cf_arr["cf_id"];
	}

	if (isset($cf_id)) {
		$path_q="t1.idFolder=".$cf_id;
		$t4_join="";
	}
	else if (isset($cf_path)) {
		$path_q="t4.path='".$cf_path."'";
		$t4_join="LEFT JOIN ".$GLOBALS["prefix_cms"]."_docs_dir as t4 ON (t4.id=t1.idFolder) ";
	}

	$ini=(isset($_GET["ini"]) ? (int)$_GET["ini"]-1 : 0)*$opt["number"];

	if ( (isset($opt["order_descending"]) && $opt["order_descending"]))	{
		 $ordering=" DESC";
	}
	else {
		$ordering.=" ASC";
	}
	
	if ($type == "dir") {

		$t1=$GLOBALS["prefix_cms"]."_docs";
		$t2=$GLOBALS["prefix_cms"]."_docs_info";

		$sel_q="t1.idDocs, t1.idFolder, t1.fname, t1.click, t1.real_fname, t2.title, t2.sdesc, t2.ldesc, t2.lang, COUNT(t3.key1) as com_cnt";

		$qtxt ="SELECT $sel_q FROM $t1 as t1 ";
		$qtxt.="LEFT JOIN $t2 as t2 ON (t2.idd=t1.idDocs AND t2.lang='$sel_lang') ";
		$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_sysforum as t3 ON (t3.key1='docs_comment' AND t3.key2=t1.idDocs) ";
		$qtxt.=$t4_join;
		$qtxt.="WHERE $path_q AND t1.publish=1 GROUP BY t1.fname ";
		if ( (isset($opt["order_by"]) && $opt["order_by"]=='order_by_date'))	{
			$qtxt.="ORDER BY t1.publish_date ".$ordering.", t2.title ";
		}
		else {
			$qtxt.="ORDER BY t2.title ".$ordering.", t1.publish_date";
		}
	}
	else if ($type == "sel") {

		$t1=$GLOBALS["prefix_cms"]."_area_block_items";
		$t2=$GLOBALS["prefix_cms"]."_docs";
		$t3=$GLOBALS["prefix_cms"]."_docs_info";

		$fields="t1.id, t2.idDocs, t2.fname, t2.real_fname, t2.click, t3.title, t3.sdesc, COUNT(t4.key1) as com_cnt";

		$qtxt ="SELECT $fields FROM $t1 as t1 ";
		$qtxt.="INNER JOIN ".$t2." as t2 ON (t2.idDocs=t1.item_id) ";
		$qtxt.="LEFT JOIN $t3 as t3 ON (t3.idd=item_id AND t3.lang='$sel_lang') ";
		$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_sysforum as t4 ON (t4.key1='docs_comment' AND t4.key2=t2.idDocs) ";
		$qtxt.="WHERE t1.idBlock='$pb' AND t1.type='docs' ";
		$qtxt.="AND t2.publish=1 GROUP BY t1.id ";
		if ( (isset($opt["order_by"]) && $opt["order_by"]=='order_by_date'))	{
			$qtxt.="ORDER BY t2.publish_date ".$ordering.", t3.title";
		}
		else {
			$qtxt.="ORDER BY t3.title ".$ordering.", t2.publish_date";
		}
		
	}
	
	$q=mysql_query($qtxt); //echo $qtxt;

	if ($q) {

		$tot=mysql_num_rows($q);

		if (($type == "dir") && ($tot > 0)) {
			$qtxt.=" LIMIT $ini,".$opt["number"];
			$q=mysql_query($qtxt);  //echo $qtxt;
		}
	}

	$txt=loadTextof($pb);
	$out->add($txt."\n");

	$use_comments=((isset($opt["use_comments"]) && ($opt["use_comments"] == 1)) ? true : false);

	if ($block == "normal")
		$out->add(show_docs_table_normal($lang, $opt, $type, $tot, $qtxt, $use_comments));
	else if ($block == "small")
		$out->add(show_docs_table_small($lang, $opt, $type, $qtxt));


	$out->add("</div>\n"); // show_docs

return 0;
	// ---------------------- old code

	if (($q) && (mysql_num_rows($q) > 0)) {

		$out->add('<div class="document_list">');
		while($row=mysql_fetch_array($q)) {

			$title = $row["title"];
			$sdesc = $row["sdesc"];
			$file = _DOCS_FPATH.$row["real_fname"];
			$img="<img src=\"".getPathImage().mimeDetect($file)."\" alt=\"".$row["fname"]."\" title=\"".$row["fname"]."\" />\n";
			$url="download.php?type=docs&amp;pb=$pb&amp;id=".$row["idDocs"];

			$out->add('<div class="doc_title">'.$title.'</div>'
				.'<div class="doc_text">'.$sdesc.'</div>'
				.'<div class="doc_download">'
				.'<a href="'.$url.'">'.$img.' '.$row["fname"].'</a>');

			if ((isset($opt["showclick"])) && ($opt["showclick"])) {
				$out->add("\n<div class=\"doc_click\">"._DOCS_CLICK.": ".$row["click"]."</div>\n");
			}

			$out->add('</div>');

		}
		$out->add('</div>');
	}

	//---------------------------------------- navigation table:

	if ($type == "dir") $op="docs";
	else if ($type == "sel") $op="docs_sel";

	$url="index.php?mn=docs&amp;pb=$pb&amp;op=$op&amp;pos=";

	$out->add("<table width=\"100%\">\n");
	$out->add("<tr><td align=\"left\">\n");

	if ($pos > 0) {
		$out->add("<a href=\"$url".($pos-1)."\">"._PREV."</a>\n");
	}

	$out->add("</td><td align=\"right\">\n");

	if ($sp < $tot-$opt["number"]) {
		$out->add("<a href=\"$url".($pos+1)."\">"._NEXT."</a>\n");
	}

	$out->add("</td></tr>\n");
	$out->add("</table>\n");

}



function showCategoriesBox(& $out, & $lang, $opt, $type) {

	$res=array(); // [keys: cf_path, cf_id]

	// --------------------- Categories Box ----------------------------

	if ((isset($opt["recurse"])) && ($opt["recurse"]) && ($type == "dir")) {

		$out->add('<div class="docs_folder_nav">');
		$folder=(int)importVar("folder");
		folders_nav_bar($folder, $opt["path"], "docs");
		$out->add('</div>');


		if ((isset($_GET["folder"])) && ($_GET["folder"] > 0)) {

			$qtxt="SELECT id, path, lev FROM ".$GLOBALS["prefix_cms"]."_docs_dir WHERE id='".(int)$_GET["folder"]."'";

		}
		else {

			$qtxt="SELECT id, lev FROM ".$GLOBALS["prefix_cms"]."_docs_dir WHERE path='".$opt["path"]."'";

		}
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_assoc($q);

			if (isset($row["path"])) {
				$cf_path=$row["path"];

				if (substr($cf_path, 0, strlen($opt["path"])) != $opt["path"])
					$cf_path=$opt["path"]; // Anti "smart guy" code ;)
			}
			else {
				$cf_path =$opt["path"];
			}

			$cf_id=$row["id"];
			$lev=$row["lev"]+1;

		}
		else {
			$lev=1; // ..assuming that the path == root
			$cf_id=0;
			$cf_path="/";
		}


		$fields ="t1.id as folder_id, t2.title as folder_title, COUNT(DISTINCT t3.idDocs) as item_tot, ";
		$fields.="t1.lev";
		$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_docs_dir as t1 ";
		$qtxt.="INNER JOIN ".$GLOBALS["prefix_cms"]."_docs_titles as t2 ON (t2.iddir=t1.id) ";
		$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_docs as t3 ON (t3.idFolder=t1.id) ";
		$qtxt.="WHERE t1.lev>='".$lev."' AND t1.path LIKE '".$cf_path."%' ";
		$qtxt.="AND t2.lang='".getLanguage()."' AND (t3.publish=1 OR t3.publish IS NULL) ";
		$qtxt.="GROUP BY t1.id ";


		drawCategoriesBox($out, $lang, $qtxt, "docs", $lev);

	}

	// -----------------------------------------------------------------

	if ((isset($cf_path)) && (!empty($cf_path)))
		$res["cf_path"]=$cf_path;

	if (isset($cf_id))
		$res["cf_id"]=(int)$cf_id;

	return $res;
}



function show_docs_table_normal(& $lang, $opt, $type, $tot, $qtxt, $use_comments=FALSE) {

	$res="";

	$q=mysql_query($qtxt);

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$number =($opt["number"] > 0 ? $opt["number"] : $GLOBALS["cms"]["visuItem"]);
	$tab=new typeOne($number);
	$tab->setTableStyle("docs_table");

	$head=array($lang->def("_TYPE"), $lang->def("_FILENAME"), $lang->def("_DESCRIPTION"), $lang->def("_CLICK"));
	$head_type = array('type', '', '', 'clicks');

	if ($use_comments) {
		$head[]=$lang->def("_COMMENTS");
		$head_type[]='comments';
	}

	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$folder=(int)importVar("folder");

	$tab->initNavBar('ini', 'link');
	$tab->setLink("index.php?mn=docs&amp;pi=".getPI()."&amp;op=docs&amp;folder=".$folder);
	$tab->nav_bar->setSymbol(getCmsNavSymbols());

	$ini=$tab->getSelectedElement();

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {

			$rowcnt=array();

			$file=_DOCS_FPATH.$row["real_fname"];
			$url="index.php?mn=docs&amp;op=download&amp;pi=".getPI()."&amp;id=".$row["idDocs"];
			$img ="<img src=\"".getPathImage().mimeDetect($file)."\" alt=\"".$row["fname"]."\" title=\"".$lang->def("_DOWNLOAD").": ";
			$img.=$row["fname"]."\" />\n";
			$rowcnt[]="<a class=\"icon_link\" href=\"".$url."\">".$img."</a>\n";

			//$url="index.php?mn=docs&amp;pi=".getPI()."&amp;op=showdoc&amp;folder=".$row["idFolder"]."&amp;id=".$row["idDocs"];
			//$rowcnt[]="<a class=\"details_link\" href=\"".$url."\">".$row["fname"]."</a>\n";
			if ($row["title"] != "")
				$doc_title=$row["title"];
			else
				$doc_title=$row["fname"];

			$rowcnt[]="<a class=\"file_link\" href=\"".$url."\">".$doc_title."</a>";

			if ($type == "dir")
				$showitem_url="index.php?mn=docs&amp;pi=".getPI()."&amp;op=showdoc&amp;folder=".$row["idFolder"]."&amp;id=".$row["idDocs"];
			else if ($type == "sel")
				$showitem_url="index.php?mn=docs&amp;pi=".getPI()."&amp;op=showdoc&amp;sel=1&amp;id=".$row["idDocs"];


			$alt =$lang->def("_READ_DESCRIPTION")." ".$doc_title;
			$img="<img class=\"cat_item_img\" src=\"".getPathImage()."block/description.gif\" alt=\"".$alt."\" title=\"".$alt."\" />\n";
			$img_comment="<img class=\"cat_item_img\" src=\"".getPathImage()."block/comments.gif\" alt=\" \" title=\" \" />\n";
			$link="<a href=\"".$showitem_url."\">".$img."</a>\n";
			$rowcnt[]=$link.$row["sdesc"]; //."<div class=\"details_link\">".$img_comment.$link."</div>\n";

			$img="<img class=\"cat_item_img\" src=\"".getPathImage()."block/clicks.gif\" alt=\" \" title=\" \" />\n";
			$rowcnt[]=$img."( ".$row["click"]." )";

			if ($use_comments) {
				$rowcnt[]="<a href=\"".$showitem_url."\">".$img_comment."</a>( <a href=\"".$showitem_url."\">".$row["com_cnt"]."</a> )\n";
			}

			$tab->addBody($rowcnt);

		}
		$res=$tab->getTable().$tab->getNavBar($ini, $tot);
	}
	return $res;
}


function show_docs_table_small(& $lang, $opt, $type, $qtxt) {

	$res="";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {

			$file=_DOCS_FPATH.$row["real_fname"];
			$url="index.php?mn=docs&amp;op=download&amp;pi=".getPI()."&amp;id=".$row["idDocs"];
			$img ="<img src=\"".getPathImage().mimeDetect($file)."\" alt=\"".$row["fname"]."\" title=\"".$lang->def("_DOWNLOAD").": ";
			$img.=$row["fname"]."\" />";

			$res.="<div class=\"docs_small_line\">";

			if ($row["title"] != "")
				$res.="<a class=\"file_link\" href=\"".$url."\">".$img.$row["title"];
			else
				$res.="<a class=\"file_link\" href=\"".$url."\">".$img.$row["fname"];

			$res.="</a></div>\n";

		}
	}

	return $res;
}



function doc_details($pb, $type="dir") {

	if (isset($_GET["id"]))
		$id=(int)$_GET["id"];
	else
		return FALSE;

	// ---------------------                ---------------------
	if (!check_docs_perm($pb, (int)$id)) die("You can't access!");
	// ---------------------                ---------------------

	require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance("docs", "cms");

	$out->add("<div class=\"show_docs\">\n");

	$opt=loadBlockOption($pb);
	if (isset($opt["path"]))
		$path=$opt["path"];
	else
		$path="";

	$sel_lang=getLanguage();

	$t1=$GLOBALS["prefix_cms"]."_docs";
	$t2=$GLOBALS["prefix_cms"]."_docs_info";

	$qtxt ="SELECT * FROM $t1 LEFT JOIN $t2 ON ($t2.idd=idDocs AND $t2.lang='$sel_lang') ";
	$qtxt.="WHERE idDocs='$id' AND publish=1;";

	$q=mysql_query($qtxt); //echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
	}


	if (isset($_GET["folder"]))
		$folder=$_GET["folder"];
	else
		$folder=$row["idFolder"];


	$out->add('<div class="doc_details">');
	if ($type == "dir") {
		$out->add('<div class="docs_folder_nav">');
		folders_nav_bar($folder, $path, "docs");
		$out->add('</div>');
	}

	$out->add('<div class="docs_box">');
	$out->add('<div class="docs_text">');


	$file=_DOCS_FPATH.$row["real_fname"];
	$url="index.php?mn=docs&amp;op=download&amp;pi=".getPI()."&amp;id=".$row["idDocs"];
	$img ="<img src=\"".getPathImage().mimeDetect($file)."\" alt=\"".$row["fname"]."\" title=\"".$lang->def("_DOWNLOAD").": ";
	$img.=$row["fname"]."\" />\n";


	$out->add("<a href=\"".$url."\" class=\"file_link\">".$img.$row["fname"]."</a>\n");
	$out->add(" (<span class=\"clicks_label\">".$lang->def("_CLICK").":</span> ".$row["click"].")\n");

	if (isset($row["title"])) {
		$out->add('<div class="docs_title">');
		$out->add("<span class=\"title_label\">".$lang->def("_TITLE").":</span>\n");
		$level_id = $GLOBALS['current_user']->getUserLevelId();
		$folder_id=$row['idFolder'];
		switch($level_id)	{
			case ADMIN_GROUP_ADMIN:
				require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");
				$ctp=new CmsTreePermissions("docs");
 				$consent_edit=$ctp->checkNodePerm($GLOBALS["current_user"]->getIdSt(), (int)$folder_id, TRUE);
				if (!$consent_edit) {
					$title=$row['title'];
					break;
				} // else drop down case
			case ADMIN_GROUP_GODADMIN:
				$qr="SELECT param_value AS base_url FROM ".$GLOBALS["prefix_fw"]."_setting WHERE param_name like 'url'";
				$q=mysql_query($qr);
				if (($q) && (mysql_num_rows($q)==1)) {
					list($base_url)=mysql_fetch_array($q);
				} else  // guessing base URL for doceboCore
					$base_url='http://'.$_SERVER['HTTP_HOST'].'/doceboCore/';
				$title='<a href="'.$base_url
					.'index.php?modname=docs&op=editdocs&of_platform=cms&idDocs='
	 				.$id.'&id_folder='.$folder_id.'" target=blank>'.$row['title'].'</a>';
	 			break;
			default:
				$title=$row['title'];
				break;
			}
		$out->add($title);
		$out->add('</div>');
	}

	if (isset($row["ldesc"])) {
		$out->add('<div class="docs_description">');
		$out->add("<span class=\"description_label\">".$lang->def("_DESCRIPTION").":</span>\n");
		$out->add($row["ldesc"]);
		$out->add('</div>');
	}



	$out->add('<div class="docs_line">&nbsp;</div>');

	$use_comments=((isset($opt["use_comments"]) && ($opt["use_comments"] == 1)) ? true : false);

	if ($use_comments) {
		load_docs_comments($id, $pb);
	}

	$out->add('</div>'); // docs_text
	$out->add('<div class="no_float"></div>');

	$out->add('</div>'); // docs_box
	$out->add('</div>'); // doc_details
	$out->add("</div>"); // show_docs

}



function docs_download($pb) {

	require_once($GLOBALS["where_framework"]."/lib/lib.download.php");

	if (isset($_GET["id"]))
		$id=(int)$_GET["id"];
	else
		return FALSE;

	// ---------------------                ---------------------
	if (!check_docs_perm($pb, (int)$id)) die("You can't access!");
	// ---------------------                ---------------------


	$qtxt ="SELECT fname, real_fname FROM ".$GLOBALS["prefix_cms"]."_docs ";
	$qtxt.="WHERE idDocs='".$id."' AND publish='1'";

	$q=mysql_query($qtxt); //echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);

		$fn=$row["real_fname"];
		$ext=end(explode(".", $fn));
		$fname=basename($row["fname"], ".".end(explode(".", $row["fname"])));

		count_docs_hit($id);

		ob_clean();
		sendFile(_DOCS_FPATH_INTERNAL, $fn, $ext, $fname);
	}
	die();
}




function load_docs_comments($id, $pb) {

	require_once($GLOBALS["where_framework"]."/lib/lib.sysforum.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance("sysforum", "cms");

	$anonymous_comment=$GLOBALS["cms"]["anonymous_comment"];

	$force_comment=0;
	$can_comment=0;
	$can_post=(bool)(($GLOBALS['current_user']->isLoggedIn()) || ($anonymous_comment == "on"));
	$is_admin=isCmsAdmin();
	//print_r($_SESSION);

	$opt=loadBlockOption($GLOBALS["pb"]);
	if (isset($opt["ov_comments"]))
		$force_comment=(int)$opt["ov_comments"];
	else
		$force_comment=0;

	$can_comment=true;

	if (($can_comment) || ($force_comment)) {

		$out->add("<div class=\"commentsHead\">".$lang->def("_COMMENTS")."</div>\n");

		$sf=new sys_forum("cms", "docs_comment", $id);
		$sf->setPrefix("cms");
		$sf->can_write=$can_post;
		$sf->can_moderate=$is_admin;

		$folder=(int)importVar("folder");
		$sf->url="index.php?mn=docs&amp;pi=".getPI()."&amp;op=showdoc&amp;folder=".$folder."&amp;id=".$id;
		$out->add($sf->show());

	}

}


?>
