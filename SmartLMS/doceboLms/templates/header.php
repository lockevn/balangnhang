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

/**
 * @package 	DoceboLMS
 * @category 	Layout
 * @author 		Fabio Pirovano <fabio@docebo.com>
 */

$GLOBALS['page']->add('<title>'.$GLOBALS['title_page'].'</title>'."\n", 'page_head');

$GLOBALS['page']->add(
		'<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">'."\n"
		.'	<link href="'.getPathTemplate('framework').'style/style.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<link href="'.getPathTemplate().'style/style.css" rel="stylesheet" type="text/css" />'."\n"
		
		.'	<link href="'.getPathTemplate('framework').'style/style_form.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<link href="'.getPathTemplate('framework').'style/style_table.css" rel="stylesheet" type="text/css" />'."\n"
		
		.'	<link href="'.getPathTemplate().'style/style_treeview.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<link href="'.getPathTemplate().'style/style_organizations.css" rel="stylesheet" type="text/css" />'."\n"
		
		.'	<link href="'.getPathTemplate().'style/style_print.css" rel="stylesheet" type="text/css" media="print" />'."\n"
		
		.'	<!--[if lte IE 6]>'."\n"
		.'		<link href="'.getPathTemplate().'style/style_lte_6.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<![endif]-->'."\n"
		
		.'	<!--[if IE 7]>'."\n"
		.'		<link href="'.getPathTemplate().'style/style_ie_7.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<![endif]-->'."\n"
		.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>'
		
		
, 'page_head');

if(getAccessibilityStatus() === false) {
	
	$GLOBALS['page']->add(	
				'	<style type="text/css">'."\n"
				.'		.access-only {'."\n"
				.'			display: none;'."\n"
				.'		}'."\n"
				.'	</style>'."\n", 
				'page_head');
}

$GLOBALS['page']->setWorkingZone('header');
$GLOBALS['page']->add(
'		<img class="left_logo" src="'.getPathImage().'logo_docebo.jpg" alt="Left logo" />'."\n"
.'		<div class="no_float"></div>'."\n");

?>