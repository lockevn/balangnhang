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
 * @version 	$Id: header.php 113 2006-03-08 18:08:42Z ema $
 */

$GLOBALS['page']->add(
		'	<link href="'.getPathTemplate('framework').'style/style.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<link href="'.getPathTemplate("crm").'style/style.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<link href="'.getPathTemplate("crm").'style/style_treeview.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<link href="'.getPathTemplate("crm").'style/style_organizations.css" rel="stylesheet" type="text/css" />'."\n"
, 'page_head');

$GLOBALS['page']->add(
		'<!--[if lte IE 5.5]>'."\n"
		.'		<link href="'.getPathTemplate("crm").'style/style_lte_55.css" rel="stylesheet" type="text/css" />'."\n"
		.'	<![endif]-->'."\n"
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
'		<img class="left_logo" src="'.getPathImage().'left_logo.gif" alt="Left logo" />'."\n"
.'		<img class="right_logo" src="'.getPathImage().'right_logo.gif" alt="Right logo" />'."\n"
.'		<div class="no_float"></div>'."\n");

?>