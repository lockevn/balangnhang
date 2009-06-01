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


$lang=& DoceboLanguage::createInstance('blind_navigation', 'cms');
$blind_link="<li><a href=\"#footer_box\">".$lang->def("_FOOTER")."</a></li>";
$GLOBALS["page"]->add($blind_link, "blind_navigation");


$fn=getPathTemplate()."/footer.php";
if (file_exists($fn)) {
	$handle=fopen($fn, "r");
	$size=filesize($fn);
	$footer="";
	if ($size > 0) {
		$footer=fread($handle, $size);
	}
	fclose($handle);
	$footer=str_replace("{|-TEMPLATE-PATH-|}", getPathTemplate(), $footer);
	$GLOBALS["page"]->add($footer, "footer");
}

?>