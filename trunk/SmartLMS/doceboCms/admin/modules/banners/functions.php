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

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

function show_filter_dialog() {


	return 0;

}


function show_banner_list($admin=0, $idG=0) {

	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');

	$can_add=checkPerm('add', true);
	$can_mod=checkPerm('mod', true);
	$can_del=checkPerm('del', true);

	$table = new typeOne(0);
	$out->add($table->OpenTable(""));
	$head=array(
		$lang->def("_TITLE"),// $lang->def("_BANNER_GROUP"),
		$lang->def("_TYPE"), $lang->def("_IMPRESSION"), $lang->def("_CLICK"), $lang->def("_CTR"), $lang->def("_EXPIRE"));

	$head_type=array('','','','','','');


	if (($admin) && ($can_mod)) {
		$head[]='<img src="'.getPathImage().'standard/moduser.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />';
		$head[]='<img src="'.getPathImage().'standard/view.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />';
		$head_type[]='img';
		$head_type[]='img';
	}

	$head[]='<img src="'.getPathImage().'/standard/flag.gif" alt="'.$lang->def("_STATUS").'" title="'.$lang->def("_STATUS").'" />';
	$head_type[]='img';

	if (($admin) && ($can_mod)) {
		$head[]='<img src="'.getPathImage().'standard/publish.gif" alt="'.$lang->def("_ACTIVATE").'" title="'.$lang->def("_ACTIVATE").'" />';
		$head[]='<img src="'.getPathImage().'standard/unpublish.gif" alt="'.$lang->def("_DEACTIVATE").'" title="'.$lang->def("_DEACTIVATE").'" />';
		$head[]='<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />';
		$head_type[]='img';
		$head_type[]='img';
		$head_type[]='img';
	}

	if (($admin) && ($can_del)) {
		$head[]='<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />';
		$head_type[]='img';
	}

	$head[]='<img src="'.getPathImage().'standard/details.gif" alt="'.$lang->def("_DETAILS").'" title="'.$lang->def("_DETAILS").'" />';
	$head_type[]='img';

	$out->add($table->WriteHeader($head, $head_type));

	$a_filter=""; $k_filter=""; $g_filter="";
	$fa_arr=array();
	$fk_arr=array();
	$fg_arr=array();
	$farr=array();
	if ((isset($_POST[""])) && ($_POST["show_active"])) $fa_arr[]="t1.status='1'";
	if ((isset($_POST[""])) && ($_POST["show_inactive"])) $fa_arr[]="t1.status='0'";
	if ((isset($_POST[""])) && ($_POST["show_image"])) $fk_arr[]="t1.kind='image'";
	if ((isset($_POST[""])) && ($_POST["show_code"])) $fk_arr[]="t1.kind='code'";
	if ((isset($_POST["show_flash"])) && ($_POST["show_flash"])) $fk_arr[]="t1.kind='flash'";
	if ((isset($_POST[""])) && ($_POST["idGroup"] != "")) $fg_arr[]="t1.idGroup='".$_POST["idGroup"]."'";

	$a_filter.=implode(" OR ", $fa_arr);
	if ($a_filter != "") $farr[]="(".$a_filter.")";
	$k_filter.=implode(" OR ", $fk_arr);
	if ($k_filter != "") $farr[]="(".$k_filter.")";
	$g_filter.=implode(" OR ", $fg_arr);
	if ($g_filter != "") $farr[]="(".$g_filter.")";

	$sel_lang=getLanguage();
	$farr[]="t1.cat_id=t2.cat_id";
	$farr[]="t2.language='$sel_lang'";

	$filter=implode(" AND ", $farr);
	if ($filter != "") $filter="WHERE ".$filter;

	$qtxt ="SELECT t1.*, t2.cat_name ";
	$qtxt.="FROM ".$GLOBALS["prefix_cms"]."_banner as t1, ".$GLOBALS["prefix_cms"]."_banner_cat as t2 $filter ";
	$qtxt.="ORDER BY (impression-expimp) DESC, expdate, title;"; //echo $qtxt;

	$groups=array(); //listGroup();
	//$types=array("block"=>$lang->def("_BLOCK"), "large"=>$lang->def("_LARGE"), "main"=>$lang->def("_MAINBAN"), "context"=>$lang->def("_CONTBAN"));
	$types=array("block"=>"", "large"=>"", "main"=>"", "context"=>"");

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {

		while ($row=mysql_fetch_array($q)) {

			$line=array();
			$line[]=$row["title"];
			//$line[]="&nbsp;"; //$groups[$row["idGroup"]];
			$line[]=$row["cat_name"];
			$line[]=$row["impression"];
			if ($row["kind"] == "image") {
				$line[]=$row["click"];
				$line[]=($row["impression"] > 0 ? number_format(100*$row["click"]/$row["impression"], 2, '.', '') : "0")."%";
			}
			else {
				$line[]="&nbsp;";
				$line[]="&nbsp;";
			}

			$exptxt="";
			if ($row["expimp"] > 0) $exptxt.="-".($row["expimp"]-$row["impression"])."<br />";
			if ($row["expdate"] > 0) {
				$exptxt=$GLOBALS["regset"]->databaseToRegional($row["expdate"]);
			}

			$line[]=$exptxt;

			if (($admin) && ($can_mod)) {

				$btn ="<a href=\"index.php?modname=banners&amp;op=selcustomer&amp;id=".$row["banner_id"]."\">";
				$btn.="<img src=\"".getPathImage()."standard/moduser.gif\" ";
				$btn.="alt=\"".$lang->def("_ALT_SELCUSTOMER")."\" title=\"".$lang->def("_ALT_SELCUSTOMER")."\" />";
				$btn.="</a>\n";
				$line[]=$btn;

				$btn ="<a href=\"index.php?modname=banners&amp;op=visperm&amp;id=".$row["banner_id"]."\">";
				$btn.="<img src=\"".getPathImage()."standard/view.gif\" ";
				$btn.="alt=\"".$lang->def("_ALT_MODPERM")."\" title=\"".$lang->def("_ALT_MODPERM")."\" />";
				$btn.="</a>\n";
				$line[]=$btn;

			}

			if ($row["status"]) {
				$flag="flag.gif";
				$msg=$lang->def("_ACTIVE");
			}
			else {
				$flag="flag_grey.gif";
				$msg=$lang->def("_INACTIVE");
			}
			$img='<img src="'.getPathImage().'standard/'.$flag.'" alt="'.$msg.'" title="'.$msg.'" />';
			$line[]=$img;
			if (($admin) && ($can_mod)) {

				if (($row["status"] == 1) || (!can_publish($row))) {
					$line[]="&nbsp;";
				}
				else {
					$img='<img src="'.getPathImage().'standard/publish.gif" alt="'.$lang->def("_ACTIVATE").'" title="'.$lang->def("_ACTIVATE").'" />';
					$line[]="<a href=\"index.php?modname=banners&amp;op=banners&amp;act_op=activate&amp;id=".$row["banner_id"]."\">$img</a>\n";
				}
				if ($row["status"] == 1) {
					$img='<img src="'.getPathImage().'standard/unpublish.gif" alt="'.$lang->def("_DEACTIVATE").'" title="'.$lang->def("_DEACTIVATE").'" />';
					$line[]="<a href=\"index.php?modname=banners&amp;op=banners&amp;act_op=deactivate&amp;id=".$row["banner_id"]."\">$img</a>\n";
				}
				else {
					$line[]="&nbsp;";
				}
				$img='<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />';
				$line[]="<a href=\"index.php?modname=banners&amp;op=modbanner&amp;id=".$row["banner_id"]."\">$img</a>\n";
			}

			if (($admin) && ($can_del)) {
				$img='<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />';
				$line[]="<a href=\"index.php?modname=banners&amp;op=delbanner&amp;id=".$row["banner_id"]."&amp;conf_del=1\"  title=\"".$lang->def("_DEL")." : ".$row['title']."\">$img</a>\n";
			}

			$img='<img src="'.getPathImage().'standard/details.gif" alt="'.$lang->def("_DETAILS").'" title="'.$lang->def("_DETAILS").'" />';
			$line[]="<a href=\"index.php?modname=banners&amp;op=details&amp;id=".$row["banner_id"]."\">$img</a>\n";

			$out->add($table->writeRow($line));

		}

		if ($can_del) {
			//add confirm pop ups
			require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
			setupHrefDialogBox('a[href*=delbanner]');
		}

	}

	if (($admin) && ($can_add)) {
		$out->add($table->WriteAddRow('<a href="index.php?modname=banners&amp;op=newbanner" title="'.$lang->def("_ADD").'">
									<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def("_ADD").'" /> '.$lang->def("_ADD").'</a>'));
	}


	$out->add($table->CloseTable());

}



function can_publish($row) { // check if the banner can be published.

	$res=1;
	$now=time();

	if ($row["title"] == "") $res=0;
	if (($row["kind"] == "image") && (($row["banfile"] == "") || ($row["banurl"] == "")) ) $res=0;
	if (($row["kind"] == "flash") && ($row["banfile"] == "")) $res=0;
	if (($row["kind"] == "code") && ($row["bancode"] == "")) $res=0;
	if (($row["expdate"] > 0) && ($row["expdate"] < $now)) $res=0;
	if (($row["expimp"] > 0) && ($row["expimp"]-$row["impression"] < 1)) $res=0;

	return $res;
}



function sel_vis(& $form, & $lang, $id, $type) {

	$res="";

	$res.="<div class=\"bannervis\">\n";


	// Find out wich elements has already been selected
	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner_rules WHERE banner_id='$id' AND item_type='$type';";
	$q=mysql_query($qtxt);

	$sel_arr=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			if ($type != "language") $sel_arr[]=$row["item_id"];
			else $sel_arr[]=$row["item_val"];
		}
	}

	// Displays the elements list

	$sel_lang=getLanguage();
	switch ($type) {
		case "macroarea": {
			$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE lev='2' ORDER BY path;";
			$key="idArea"; $val="title";
		} break;
	}


	if ($type == "language") {
		$lang_arr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($lang_arr as $key=>$val) {
			if (in_array($val, $sel_arr)) $chk=true; else $chk=false;

			$res.=$form->getCheckbox($val, "vis_".$type."_".$key."_", "vis_".$type."[".$key."]", $val, $chk);
		}
	}
	else {

		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_array($q)) {
				if (in_array($row[$key], $sel_arr)) $chk=true; else $chk=false;
				$name="vis_".$type."[".$row[$key]."]";
				$name_id=preg_replace("/(\[|\])/", "_", $name);

				$res.=$form->getCheckbox($row[$val], $name_id, $name, $row[$key], $chk);
			}
		}

	}


	$res.="</div>\n";

	return $res;
}


function getCategoryDropdownArray() {
	
	$cat_arr=array();
	$sel_lang=getLanguage();

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner_cat WHERE language='$sel_lang' ORDER BY cat_name";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$cat_arr[$row["cat_id"]]=$row["cat_name"];
		}
	}	
	
	return $cat_arr;
}



function getCategoryDropdown(& $form, & $lang, $sel) {

	$res="";

	$cat_arr=getCategoryDropdownArray();

	$res.=$form->getDropdown($lang->def("_BANNER_CAT"), "cat_id", "cat_id", $cat_arr , $sel);

	return $res;
}


?>