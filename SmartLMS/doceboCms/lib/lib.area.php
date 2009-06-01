<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/



function get_lang_parent() {

	$lang=getLanguage();
	$res=-1;

	$q=mysql_query("SELECT idArea FROM ".$GLOBALS["prefix_cms"]."_area WHERE title='$lang' AND langdef='1'");

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$res=$row["idArea"];
	}

	return $res;

}


function get_lang_root() {
	// ritorna una stringa contenente la radice della lingua.

	$lang=getLanguage();
	$res="-1";

	$q=mysql_query("SELECT path FROM ".$GLOBALS["prefix_cms"]."_area WHERE title='$lang' AND langdef='1'");

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$res=$row["path"];
	}

	if ($res == -1) {
	}
	return $res;

}

function get_lang_list() {
	// ritorna un array con la lista delle lingue disponibili in pagine.

	$q=mysql_query("SELECT title FROM ".$GLOBALS["prefix_cms"]."_area WHERE langdef='1' ORDER BY path;");

	$larr=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$larr[]=$row["title"];
		}
	}

	return $larr;
}



function get_area_lang($idArea, $use_default=true) {
	// ritorna la lingua usata da una pagina

	if ($use_default)
		$res=getLanguage();
	else
		$res="";

	$qtxt="SELECT path FROM ".$GLOBALS["prefix_cms"]."_area WHERE idArea='$idArea';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$path=$row["path"];
		$parr=explode("/", substr($path, 1));
		$root="";
		$i=0;
		if (preg_match("/^\\/root/", $path)) {
			$root="/root";
			$i=1;
		}
		$root.="/".$parr[$i];

		$qtxt="SELECT title FROM ".$GLOBALS["prefix_cms"]."_area WHERE path='$root' AND langdef='1';";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row["title"];
		}
	}
	else {
		$res =getLanguage();
	}

	return $res;
}


function get_area_parent_macroarea($idArea, $res_type="id") {
	// returns the parent macroarea id (lev == 2) of the current area

	$res=getLanguage();

	$qtxt="SELECT path, lev FROM ".$GLOBALS["prefix_cms"]."_area WHERE idArea='".$idArea."';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$path=$row["path"];
		if ($row["lev"] == 2) { // we don't want to waste our time ;)
			if ($res_type == "id")
				return getIdArea();
			else if ($res_type == "path")
				return $path;
		}
		$root=(preg_match("/^\/root/", $path) ? "/root/":"/");
		$parr=explode("/", substr($path, strlen($root)));
		$path=$root.$parr[0]."/".$parr[1];
	}

	if ($res_type == "id") { // returns the id of the parent area
		$qtxt="SELECT idArea FROM ".$GLOBALS["prefix_cms"]."_area WHERE path='".$path."' AND lev='2';";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row["idArea"];
		}

		return $res;
	}
	else if ($res_type == "path") { // returns the path of the parent macroarea
		return $path;
	}

}


function get_area_parent($idArea, $res_type="id") {
	// returns the parent area id (lev == lev-1) of the current area

	$res="";

	$qtxt="SELECT path, lev FROM ".$GLOBALS["prefix_cms"]."_area WHERE idArea='$idArea';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$lev=$row["lev"];
		$path=$row["path"];
		$parr=explode("/", substr($path, 1));
		$path="";
		for ($i=0; $i<count($parr); $i++) {
			$path.="/".$parr[$i];
		}
	}

	if ($res_type == "id") { // returns the id of the parent area
		$qtxt="SELECT idArea FROM ".$GLOBALS["prefix_cms"]."_area WHERE path='$path' AND lev='".($lev-1)."';";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row["idArea"];
		}

		return $res;
	}
	else if ($res_type == "path") { // returns the path of the parent area
		return $path;
	}

}


function lang_is_used($lang) {
	// ritorna true se la lingua e' stata usata (l'albero ha figli)

	$res=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE title='$lang';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$path=$row["path"];

		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE path LIKE '$path%';";
		$pq=mysql_query($qtxt);

		if (($pq) && (mysql_num_rows($pq) > 0)) $res=1;
	}

	return $res;
}


function getIdArea() {
	//REQUIRES : true
	//EFFECTS  : return the id of this area , if inexistent return 1 -> Main Page


	if ((isset($_GET["newArea"])) && ((int)$_GET["newArea"] > 0) && ($_GET["special"] == "changearea")) {
		return $_GET["newArea"];
	}
	else if ((isset($GLOBALS["area_id"])) && (!empty($GLOBALS["area_id"]))) {
		return $GLOBALS["area_id"];
	}
	else {
		if(!isset($_SESSION['sesCurrentArea'])) return getMainArea();
		else return $_SESSION['sesCurrentArea'];
	}

}

function setIdArea($area_id) {
	$GLOBALS["area_id"]=$area_id;
}

function getMainArea($set_lang=FALSE) {

	if ($set_lang !== FALSE) {
		setLanguage($set_lang);
	}

	$avail_lang=get_lang_list();
	$sel_lang=getLanguage();
	if ((!in_array($sel_lang, $avail_lang)) && (count($avail_lang) > 0)) {
		// Default language not found in cms pages tree..
		setLanguage($avail_lang[0]);
	}


	$lang_parent=get_lang_parent();
	$idArea=-1;

	if ($lang_parent >= 0) {

		$qtxt="SELECT idArea
		FROM ".$GLOBALS["prefix_cms"]."_area
		WHERE home = '1' AND publish = '1' AND idParent='$lang_parent'";

		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			list($idArea) = mysql_fetch_row($q);
		}
	}

	return $idArea;
}




/*function for portal construction***********************************/


function loadArea( $idArea = false) {

	if($idArea === false)
		$idArea = $GLOBALS["area_id"];

	if (checkRoleForItem("page", $idArea)) {
		doSubdivision($idArea, 0, 0);
	}
	else {
		$GLOBALS["page"]->add(def("_CANT_VIEW_PAGE", "standard", "cms"), "content");
	}
}

function doSubdivision($idArea, $idSubdivision, $rip) {
	//EFFECTS : create the page required

	//needest limit
	if($rip > '10') return;
	if($rip == '') $rip = 0;



	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE idArea='".$idArea."' AND publish='1'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {

		//-------------------- COUNTER ---------------------------
		$row=mysql_fetch_array($q);
		$areatitle=$row["title"];
		if (!defined("_BBCLONE_DIR"))
			define("_BBCLONE_DIR", $GLOBALS["where_cms"]."/addons/bbclone/");
		if (!defined("COUNTER"))
			define("COUNTER", _BBCLONE_DIR."cms_mark_page.php");
		//--------------------------------------------------------

		//finding area tree
		$reSubdivision = mysql_query("
		SELECT idSubdivision, areaWidth, areaType, margin
		FROM ".$GLOBALS["prefix_cms"]."_area_subdivision
		WHERE idParentSub = '$idSubdivision' AND idArea='$idArea'
		ORDER BY sequence ASC");


		//subdive page and display content
		while(list($idS, $width, $type, $margin) = mysql_fetch_row($reSubdivision)) {
			$GLOBALS["page"]->add('<div class="contentBox'.$width.'">', "content");

			if (isset($_GET["mn"]))
				$mn=$_GET["mn"];
			else
				$mn="";

			if($type == 'content' && $mn == "") {
				//load_banner();
				doSubdivision($idArea, $idS, ++$rip);
				$GLOBALS["page"]->add('<div class="no_float"></div>', "content");
			}
			if (($type != "content") || ($mn == ""))
				listBlocks($idS, $idArea);
			else {
				//load_banner();
				if ((isset($_GET["sb"])) && ((int)$_GET["sb"] != 0))
					load_block($_GET["sb"]);
				load_module($mn);
			}

			$GLOBALS["page"]->add('</div>'."\n\n", "content");
		}
	}
}

function load_banner() {

	require_once($GLOBALS["where_cms"]."/modules/banners/functions.php");

	$res="";

	if ((isset($GLOBALS["cms"]["default_banner_cat"])) && (!empty($GLOBALS["cms"]["default_banner_cat"]))) {

		$default_banner_cat=(int)$GLOBALS["cms"]["default_banner_cat"];
		$res=show_banner($default_banner_cat);

	}

	return $res;
}


function navigatorArea($idArea, $first=TRUE) {
	//REQUIRES : idArea valid
	//EFFECTS  : return a linked navigator

	$use_mod_rewrite=$GLOBALS["cms"]["use_mod_rewrite"];
	$lang_root=get_lang_root();

	$res="";

	if ($first) {
		$lang=& DoceboLanguage::createInstance('blind_navigation', 'cms');
		$blind_link="<li><a href=\"#navigator_box\">".$lang->def("_NAVIGATION_AREA")."</a></li>";
		$GLOBALS["page"]->add($blind_link, "blind_navigation");
	}

	//finding area tree
	list($idA, $idParent, $title, $alias, $mr_title, $level, $home) = mysql_fetch_row(mysql_query("
	SELECT idArea, idParent, title, alias, mr_title, lev, home
	FROM ".$GLOBALS["prefix_cms"]."_area
	WHERE path LIKE '$lang_root%' AND lev > '1' AND idArea = '$idArea'"));

	$class=($idA == getIdArea() ? "class=\"selected\" ":"");

	if (!empty($alias))
		$title=$alias;

	if ((int)$level == 2) {

		if (!$home) {
			// Home info:
			list($home_idA, $home_title, $home_alias) = mysql_fetch_row(mysql_query("
			SELECT idArea, title, alias
			FROM ".$GLOBALS["prefix_cms"]."_area
			WHERE path LIKE '$lang_root%' AND lev > '1' AND home='1'"));

			if (!empty($home_alias))
				$home_title=$home_alias;

			$res.='<a '.$class.'href="'.$GLOBALS["where_cms_relative"].'">'.$home_title.'</a>';
			$res.=' &gt; ';

			if ($use_mod_rewrite == "off")
				$res.='&nbsp;<a '.$class.'href="index.php?special=changearea&amp;newArea='.$idA.'">'.$title.'</a>';
			else
				$res.='&nbsp;'.getMrOpenLink($idA, $title, $mr_title, $class).$title."</a>";
		}
		else {
			$res.='&nbsp;<a '.$class.'href="'.$GLOBALS["where_cms_relative"].'">'.$title.'</a>';
		}

	}
	else if ($level > 2) {
		$res.=navigatorArea($idParent, false);
		if ($use_mod_rewrite == "off")
			$res.=' &gt; <a '.$class.'href="index.php?special=changearea&amp;newArea='.$idA.'">'.$title.'</a>';
		else
			$res.=' &gt; '.getMrOpenLink($idA, $title, $mr_title, $class).$title."</a>";
	}

	return $res;
}



function listBlocks($idSubdivision, $idArea) {
	require_once($GLOBALS["where_cms"]."/lib/lib.cms_common.php");

	$fields="t1.idBlock, t1.block_name, t1.title, t2.folder";
	$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_area_block as t1, ";
	$qtxt.=$GLOBALS["prefix_cms"]."_blocktype as t2 ";
	$qtxt.="WHERE t1.block_name=t2.name AND ";
	$qtxt.="t1.idSubdivision='".$idSubdivision."' ORDER BY t1.sequence ASC";

	$reBlock = mysql_query($qtxt);


	while(list($idBlock, $block_name, $title, $folder) = mysql_fetch_row($reBlock)) {
		$opt=loadBlockOption($idBlock);
		if ((isset($opt["css"])) && ($opt["css"] != ""))
			$css=$opt["css"]; else $css=1;

		$published=true;
		if ((isset($opt["pubdate"])) && ($opt["pubdate"] > date("Y-m-d H:i:s")))
			$published=false;
		if ((isset($opt["expdate"])) && ($opt["expdate"] < date("Y-m-d H:i:s")))
			$published=false;

		if ($published) {
			$b_info=getBlockInfo($idBlock, $block_name, $title, $css);

			if ($b_info["view"]) {
				// -- Blind navigation ---------------------------
				if ((isset($opt["blindnavdesc"])) && (!empty($opt["blindnavdesc"])))
					$label=$opt["blindnavdesc"];
				else
					$label=(!empty($title) ? $title : $block_name);
				$GLOBALS["page"]->add("<li><a href=\"#block_".$idBlock."\">".$label."</a></li>", "blind_navigation");
				// -----------------------------------------------
				if ((isset($opt["gmonitoring"])) && (!empty($opt["gmonitoring"])))
					$GLOBALS["page"]->add($opt["gmonitoring"], "content");
				// -----------------------------------------------
				$GLOBALS["page"]->add('<div id="block_'.$idBlock.'" class="contentblock_'.$css.'">', "content");
				displayBlock($idBlock, $block_name, $title, $folder);
				$GLOBALS["page"]->add('</div>', "content");
			}
		}
	}
}



function displayBlock($idBlock, $block_name, $title, $folder="") {
	if($block_name != '') {
		if (empty($folder))
			$folder=$block_name;

		$function_name=$block_name."_showMain";
		include_once('modules/'.$folder.'/block.'.$block_name.'.php' );

		call_user_func_array($function_name, array($idBlock, $title, $function_name) );
	}
}


function getPageId() {

	$GLOBALS["area_id"]=getIdArea();

	if (isset($GET["pb"]))
		$GLOBALS["pb"]=(int)$_GET["pb"];
	else
		$GLOBALS["pb"]=0;

	if ((isset($_GET["pi"]))) {

		$pi_arr=explode("_", $_GET["pi"]);

		if ((is_array($pi_arr)) && (count($pi_arr) == 2)) {

			$got_pi=true;
			$GLOBALS["area_id"]=(int)$pi_arr[0];
			$GLOBALS["pb"]=(int)$pi_arr[1];

		}
	}

}

function setPageId($area_id=0, $pb=0) {

	if ($area_id == 0)
		$GLOBALS["area_id"]=getIdArea();
	else
		$GLOBALS["area_id"]=$area_id;

	$GLOBALS["pb"]=$pb;

}


/**
	@return string PageId tocken
*/
function getPI() {

	$res=$GLOBALS["area_id"]."_".$GLOBALS["pb"];

	return $res;
}


function load_module($mn) {

	require_once($GLOBALS["where_cms"]."/lib/lib.cms_common.php");

	// --- simple anti-hacking[XSS] protection --- //
	$mn=strtok($mn, " \r\n\t<>");
	//---------------------------------------------//

	$mod_i=$GLOBALS["where_cms"]."/modules/$mn/index.php";
	$mod_f=$GLOBALS["where_cms"]."/modules/$mn/functions.php";
	if (file_exists($mod_i)) {

		getPageId();

		$lang=& DoceboLanguage::createInstance('module_title', 'cms');
		$module_title="_".strtoupper($mn)."_TITLE";

		require_once($GLOBALS["where_cms"]."/lib/lib.manModules.php");
		
		if (file_exists($mod_f))
			require_once($mod_f);
		require_once($mod_i);
	}

}



function checkRoleForItem($type, $id, $action="view") {

	require_once($GLOBALS["where_framework"]."/lib/lib.acl.php");

	$res=false;
	$role_id="";
	$user=& $GLOBALS['current_user'];
	$acl=new DoceboACL();

	switch ($type) {
		case "block": {
			$role_id="/cms/modules/block/".$id."/".$action;
		} break;
		case "page": {
			$role_id="/cms/page/".$id."/".$action;
		} break;
		case "banner": {
			$role_id="/cms/banner/".$id."/".$action;
			//echo "<br /><br />".$acl->getRoleST($role_id)." ---- "; print_r($user->arrst); echo "<br /><br />";
		} break;
		case "forum": {
			$role_id="/cms/forum/".$id."/".$action;
		} break;
	}

	if (($role_id != "") && ($acl->getRoleST($role_id) != false))
		$res=$user->matchUserRole($role_id);

	return $res;
}

function getBlockInfo($pb, $block_name=FALSE, $title=FALSE, $css=1) {

	if (isset($_SESSION["block_info"])) {
		if ((isset($_SESSION["block_info_time"])) && (time()-$_SESSION["block_info_time"] > 3600*24)) {
			$saved_info=array();
		}
		else {
			$saved_info=unserialize($_SESSION["block_info"]);
		}
	}
	else
		$saved_info=array();

	if (isset($saved_info[$pb])) {
		return $saved_info[$pb];
	}
	else {

		if (($block_name === FALSE) || ($title === FALSE)) {

			$qtxt="SELECT block_name,title FROM ".$GLOBALS["prefix_cms"]."_area_block WHERE idBlock='$pb'";
			$q=mysql_query($qtxt);

			if (($q) && (mysql_num_rows($q))) {
				$row=mysql_fetch_array($q);
				$saved_info[$pb]["name"]=$row["block_name"];
				$saved_info[$pb]["title"]=$row["title"];
			}
			else {
				$saved_info[$pb]["view"]=FALSE;
				return $saved_info[$pb];
			}
		}
		else {
			$saved_info[$pb]["name"]=$block_name;
			$saved_info[$pb]["title"]=$title;
		}

		$saved_info[$pb]["css"]=$css;
		$saved_info[$pb]["view"]=checkRoleForItem("block", $pb, "view");

		$_SESSION["block_info"]=serialize($saved_info);
		if (!isset($_SESSION["block_info_time"])) {
			$_SESSION["block_info_time"]=time();
		}

		return $saved_info[$pb];
	}
}

function getItemValue($key) {

	if (isset($_SESSION["item_info"])) {
		if ((isset($_SESSION["item_info_time"])) && (time()-$_SESSION["item_info_time"] > 3600*24)) {
			$saved_info=array();
		}
		else {
			$saved_info=unserialize($_SESSION["item_info"]);
		}
	}
	else
		$saved_info=array();

	foreach($key as $k=>$v) {
		$key[$k]=str_replace("_", "\\_", $v);
	}
	$key_code=implode("_", $key);

	if (isset($saved_info[$key_code])) {
		return $saved_info[$key_code];
	}
	else
		return false;
}

function setItemValue($key, $value, $unset=FALSE) {

	if (isset($_SESSION["item_info"])) {
		$saved_info=unserialize($_SESSION["item_info"]);
	}

	foreach($key as $k=>$v) {
		$key[$k]=str_replace("_", "\\_", $v);
	}
	$key_code=implode("_", $key);


	if (!$unset)
		$saved_info[$key_code]=$value;
	else if (isset($saved_info[$key_code]))
		unset($saved_info[$key_code]);


	$_SESSION["item_info"]=serialize($saved_info);
	if (!isset($_SESSION["item_info_time"])) {
		$_SESSION["item_info_time"]=time();
	}
}

// -------------------------------------------------------------------

function format_mod_rewrite_title($title) {
	require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

	$res=getCleanTitle($title);

	return $res;
}

/**
 * function open_ext_link
 * returns the open tag of the "a" tag and makes the links open in a new
 * window. This is used for replace the target="_blank" attribute that
 * is no longer supported on XHTML 1.1.
 *
 * @param string $link the url of the link
 * @param string $class the css class of the link
 * @param string $title the title of the link
 *
 * @return the open of the "a" tag.
 * @author Giovanni Derks
 **/
function open_ext_link($link, $class="", $title="") {

	$res="";

	$res.="<a href=\"".$link."\" onclick=\"window.open('".$link."'); return false;\"";
	if ($class != "") $res.=" class=\"".$class."\"";
	if ($title != "") $res.=" title=\"".$title."\"";
	$res.=" >";

	return $res;
}

/**
 * @param string $name the name of the constant to be defined
 * @param string $val  the value of the constant to be defined
 *
 * @author Giovanni Derks
 **/
function langDefine($name, $val) {

	if (!defined($name)) {
		define($name, $val);
	}

}


?>
