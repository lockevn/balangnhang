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


if ($GLOBALS["cms"]["use_mod_rewrite"] == "on") {

	$base=$GLOBALS["cms"]["url"];
	if (preg_match("/127.0.0.1/", $base)) {
		$base=preg_replace("/127.0.0.1[^\d\\/][:]?([^\\/]*)/", $_SERVER["HTTP_HOST"], $base);
	}
	if (!preg_match("/".$_SERVER["HTTP_HOST"]."/", $base))
		$GLOBALS["cms"]["use_mod_rewrite"]="off";
	else
		$GLOBALS["page"]->addStart("<base href=\"".$base."\" />\n", "page_head");

}

addCss("style");
addCss("style-block");
addCss("style_treeview");
addCss("style_organizations");
addCss("style_menu");
addCss("style_navbar");
addCss("style_table", "framework");

if(getAccessibilityStatus() === false) {
	$GLOBALS['page']->add(
				'	<style type="text/css">'."\n"
				.'		.access-only {'."\n"
				.'			display: none;'."\n"
				.'		}'."\n"
				.'	</style>'."\n",
				'page_head');
}

/** -- $GLOBALS["header_meta"] is set by the setCmsTitle() function in lib.template_lang.php -- **/
$meta =getCmsMeta();
if (isset($meta["header_meta"])) {
	$GLOBALS["page"]->add($meta["header_meta"], "page_head");
}
if (isset($meta["title_page"])) {
	$GLOBALS["page"]->add("<title>".$meta["title_page"]."</title>", "page_head");
}

getPageId();


if (!defined("POPUP_MODE")) {
	$old_ob=ob_get_contents();
	ob_clean();

	ob_start();
	include($GLOBALS["where_cms"]."/templates/".getTemplate()."/header.php");
	$txt=ob_get_contents();
	$date=$GLOBALS["regset"]->databaseToRegional(date("Y-m-d H:i:s"));
	$txt=replaceInTheme($txt, "date", $date);
	$txt=replaceInTheme($txt, "navigation", navigatorArea(getIdArea()));
	$txt=replaceInTheme($txt, "banner", load_banner());
	$txt=replaceInTheme($txt, "languages", getLangFlags());
	$txt=replaceInTheme($txt, "macroarea", loadMenuOver());
	$txt=replaceInTheme($txt, "admin-link", getAdminQuickLink());
	$txt=replaceInTheme($txt, "logout-link", getLogoutQuickLink());

	if (strpos($txt, "{|-TEMPLATE-PATH-|}") !== false)
		$txt=str_replace("{|-TEMPLATE-PATH-|}", getPathTemplate(), $txt);

	ob_clean();

	ob_start();
	echo($old_ob);
	$GLOBALS["page"]->add($txt, "header");
}



// --------------------------------------------------------------------------


function replaceInTheme($txt, $what, $with, $vop_lbl="") {

	if (empty($vop_lbl))
		$vop_lbl="vop_show_".$what;

	$what="{|-".strtoupper($what)."-|}";

	if (strpos($txt, $what) !== false) {
		if ((isset($GLOBALS["cms"][$vop_lbl])) && (!$GLOBALS["cms"][$vop_lbl]))
			$txt=str_replace($what, "", $txt);
		else
			$txt=str_replace($what, $with, $txt);
	}

	return $txt;
}


?>