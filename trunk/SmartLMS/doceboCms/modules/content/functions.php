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


function check_content_perm($pb, $id)
{
	return true;

	$can_see=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE idBlock='$pb' AND item_id='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$valid_item=1;
	}


	$opt=loadBlockOption($pb);
	$path=$opt["path"];

	if ($opt["recurse"]) $path_q="t2.path LIKE '$path%'"; else $path_q="t2.path='$path'";

	if ($path != "/") {
		$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_content as t1, ".$GLOBALS["prefix_cms"]."_content_dir as t2 ";
		$qtxt.="WHERE $path_q AND t1.idFolder=t2.id AND t1.idContent='$id';";
	}
	else {
		$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_content as t1, ".$GLOBALS["prefix_cms"]."_content_dir as t2 ";
		$qtxt.="WHERE (($path_q AND t1.idFolder=t2.id) OR t1.idFolder='0') AND t1.idContent='$id';";
	}
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$valid_folder=1;
	}


	if (($valid_item) || ($valid_folder)) {
		$user_grp=getUserGroup();
		$allowed_grp=db_block_groups($pb);
		$can_see=can_see_block($user_grp, $allowed_grp);
	}

	if (!$can_see) die("You can't access!");
}

function show_single_content($block_id) {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('content', 'cms');
	
	$opt=loadBlockOption($block_id);
	if ((isset($opt["ov_hide_title"])) && ($opt["ov_hide_title"])) $hide_title=true; else $hide_title=false;
	
	$qtxt ="SELECT t1.item_id, t2.title, t2.long_desc, t2.type, t2.key1, t2.idFolder FROM ";
	$qtxt.=$GLOBALS["prefix_cms"]."_area_block_items as t1, ";
	$qtxt.=$GLOBALS["prefix_cms"]."_content as t2 ";
	$qtxt.="WHERE t1.idBlock='".$block_id."' AND t1.type='content' AND t2.idContent=t1.item_id AND t2.publish=1";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);

		$content_id=$row["item_id"];
		
		$folder_id=$row['idFolder'];
		
		$level_id = $GLOBALS['current_user']->getUserLevelId();

		if ($row["title"] != "" && !$hide_title) {
			switch($level_id)
			{
				case ADMIN_GROUP_ADMIN:
					require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");
					$ctp=new CmsTreePermissions("content");
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
						.'index.php?modname=content&op=modcontent&of_platform=cms&id_content='
		 				.$content_id.'&id_folder='.$row['idFolder'].'" target=blank>'.$row['title'].'</a>';
		 			break;
				default:
					$title=$row['title'];
					break;
			}
			$out->add('<div class="titleBlock">'.$title.'</div>');
		}

		switch ($row["type"]) {
			case "normal": {
				$out->add(fixAnchors($row["long_desc"]));
			} break;
			case "block_text": {
				require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");
				$out->add(fixAnchors(loadTextOf($row["key1"])));
			} break;
		}
	}
}




function show_content_attach($id)
{
	show_content_attach_list("docs", $id);
	show_content_attach_list("media", $id);
	show_content_attach_list("links", $id);
}



function show_content_attach_list($type, $id) {

	require_once($GLOBALS["where_framework"].'/lib/lib.typeone.php');
	require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('content', 'cms');

	$sel_lang=getLanguage(); //get_content_lang($id);

	switch ($type) {

		case "docs" : {
			$t1=$GLOBALS["prefix_cms"]."_docs";
			$t2=$GLOBALS["prefix_cms"]."_docs_info";
			$t3=$GLOBALS["prefix_cms"]."_content_attach";
			$row_id="idDocs";
			$qtxt="SELECT $t1.*, $t2.sdesc, $t3.* "
					." FROM $t1, $t3"
					." LEFT JOIN $t2 ON ($t2.idd=$t1.$row_id AND $t2.lang='$sel_lang')"
					." WHERE $t3.type='$type' AND $t3.idContent='$id' AND $t3.idAttach=$t1.$row_id";

			$row_1="fname";
			$row_2="sdesc";
			$row_3="real_fname";

		};break;


		case "media" : {
			$t1=$GLOBALS["prefix_cms"]."_media";
			$t2=$GLOBALS["prefix_cms"]."_media_info";
			$t3=$GLOBALS["prefix_cms"]."_content_attach";
			$row_id="idMedia";
			$qtxt="SELECT $t1.*, $t2.sdesc, $t3.* "
					." FROM $t1, $t3"
					." LEFT JOIN $t2 ON ($t2.idm=$t1.$row_id AND $t2.lang='$sel_lang')"
					." WHERE $t3.type='$type' AND $t3.idContent='$id' AND $t3.idAttach=$t1.$row_id";

			$row_1="fname";
			$row_2="sdesc";
			$row_3="real_fname";

		};break;

		case "links" : {
			$t1=$GLOBALS["prefix_cms"]."_links";
			$t2=$GLOBALS["prefix_cms"]."_links_info";
			$t3=$GLOBALS["prefix_cms"]."_content_attach";
			$row_id="idLinks";
			$qtxt="SELECT $t1.*, $t2.title, $t3.* "
					." FROM $t1, $t3"
					." LEFT JOIN $t2 ON ($t2.idl=$t1.$row_id AND $t2.lang='$sel_lang')"
					." WHERE $t3.type='$type' AND $t3.idContent='$id' AND $t3.idAttach=$t1.$row_id";

			$row_1="title";
			$row_2="url";

		};break;

	}


	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {

			$rowcnt="";
			if (($type == "docs") || ($type == "media")) {
				$fn=$row[$row_3];
				$expFileName = explode('.', $fn);
				$totPart = count($expFileName) - 1;
				$mime=mimetype($expFileName[$totPart]);
				$img="<img src=\"".getPathImage().mimeDetect($fn)."\" alt=\"$mime\" title=\"$mime\" />\n";

				$url="download.php?type=".$type."&amp;pi=".getPI()."&amp;id=".$row[$row_id];

				$rowcnt="<a href=\"".$url."\">".$img;
				$rowcnt.=$row[$row_1]."</a>\n";
			}
			else if ($type == "links") {
				$rowcnt="&middot; <a href=\"".$row[$row_2]."\">".$row[$row_1]."</a>\n";
			}

			$aid="&amp;aid=".$row["id"];

			$out->add($rowcnt."<br />\n");
		}
	}

	$out->add("<br /><br />\n");
}



function load_content_comments($id, $pb) {

	$anonymous_comment=$GLOBALS["cms"]["anonymous_comment"];

	require_once($GLOBALS["where_framework"]."/lib/lib.sysforum.php");

	$force_comment=0;
	$can_comment=0;
	$can_post=(bool)(($_SESSION["sesCmsUser"] > 0) || ($anonymous_comment == "on"));
	$is_admin=user_is_admin($_SESSION["sesCmsUser"]);

	$opt=loadBlockOption($pb);
	$force_comment=(int)$opt["ov_comments"];

	$qtxt="SELECT cancomment FROM ".$GLOBALS["prefix_cms"]."_content WHERE idContent='$id'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$can_comment=(int)$row["cancomment"];
	}

	if (($can_comment) || ($force_comment)) {

		echo("<br /><b>"._COMMENTS."</b><br />\n");
		$sf=new sys_forum("content_comment", $id);
		$sf->can_write=$can_post;
		$sf->can_moderate=$is_admin;

		$idArea=get_block_idArea($pb);
		$sf->url=$sf->get_page_url();
		$sf->show();
		echo("<br />\n");

	}

}


?>