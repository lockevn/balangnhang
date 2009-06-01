<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

$GLOBALS['page']->add(
		'<title>'.$GLOBALS['title_page'].'</title>'."\n", 
		'page_head');

// standard style ----------------------------------------------------------------------------------------------
$GLOBALS['page']->add(''
		.'<link href="'.getPathTemplate('framework').'style/style.css" rel="stylesheet" type="text/css" />'."\n"
		.'<link href="'.getPathTemplate('framework').'style/style_form.css" rel="stylesheet" type="text/css" />'."\n"
		.'<link href="'.getPathTemplate('framework').'style/style_table.css" rel="stylesheet" type="text/css" />'."\n"
		.'<link href="'.getPathTemplate('framework').'style/style_layout.css" rel="stylesheet" type="text/css" />'."\n"
		.'<link href="'.getPathTemplate('framework').'style/style_menu_dropdown.css" rel="stylesheet" type="text/css" media="screen,print,handheld,projection" />'."\n"  
		
		.'	<!--[if lte IE 6]>'."\n"
		.'		<link href="'.getPathTemplate('framework').'style/style_lte_6.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<![endif]-->'."\n"
		
		.'	<!--[if IE 7]>'."\n"
		.'		<link href="'.getPathTemplate('framework').'style/style_ie_7.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<![endif]-->'."\n"
, 'page_head');

if(getAccessibilityStatus() === false) {
	
	$GLOBALS['page']->add(	'<style type="text/css">'."\n"
							.'	.access-only {'."\n"
							.'		display: none;'."\n"
							.'}'."\n"
							.'</style>'."\n", 
				'page_head');
}

$GLOBALS['page']->setWorkingZone('header');

// header content --------------------------------------------------------------------------------------
$GLOBALS['page']->add(
'		<img class="left_logo" src="'.getPathImage('framework').'left_logo.png" alt="Left logo" />'."\n");

?>
