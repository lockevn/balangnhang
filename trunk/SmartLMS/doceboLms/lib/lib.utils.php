<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package 	DoceboLMS
 * @category 	Utilities
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id: lib.utils.php 793 2006-11-21 15:43:19Z fabio $
 */

/**
 * @param 	int $idMain if passed return the first voice of the relative menu
 *
 * @return 	array 	with three element modulename and op that contains the first accessible menu element 
 *					indicate in idMain  array( [idMain], [modulename], [op] )
 **/
function firstPage( $idMain = false ) {
	
	$query_main = "
	SELECT module.idModule, main.idMain, module.module_name, module.default_op, module.token_associated 
	FROM ( ".$GLOBALS['prefix_lms']."_menucourse_main AS main JOIN
		".$GLOBALS['prefix_lms']."_menucourse_under AS un ) JOIN
		".$GLOBALS['prefix_lms']."_module AS module
	WHERE main.idMain = un.idMain AND un.idModule = module.idModule 
		AND main.idCourse = '".(int)$_SESSION['idCourse']."'
		AND un.idCourse = '".(int)$_SESSION['idCourse']."'
		".( $idMain !== false ? " AND main.idMain='".$idMain."' "  : '' )."
	ORDER BY main.sequence, un.sequence";
	$re_main = mysql_query($query_main);
	
	while(list($id_module, $main, $module_name, $default_op, $token) = mysql_fetch_row($re_main)) {
		
		if(checkPerm($token, true, $module_name)) {
		
			return array('idModule'=> $id_module, 'idMain' => $main, 'modulename' => $module_name, 'op' => $default_op);
		}
	}
}

function getLmsLangFlags() {
	
	$lang=& DoceboLanguage::createInstance('blind_navigation');
	$blind_link="<li><a href=\"#lang_box\">".$lang->def("_LANG_SELECT")."</a></li>";
	$GLOBALS["page"]->add($blind_link, "blind_navigation");

	$all_lang = $GLOBALS['globLangManager']->getAllLangCode();

	if(!is_array($all_lang)) return '';
	$res = '<ul id="lang_box">';
	foreach($all_lang as $k => $lang_code) {
		
		$res.= '<a href="index.php?sop=changelang&amp;new_lang='.$lang_code.'" title="'.$lang_code.'">'
			.'<img src="'.getPathImage('fw').'language/'.$lang_code.'.png" alt="'.$lang_code.'" /></a>';
	}
	$res .= '</ul>';
	return $res;
}

?>
