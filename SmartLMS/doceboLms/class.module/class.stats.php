<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2005 by Emanuele Sandri (esandri@tiscali.it)            */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Module_Stats extends LmsModule {
	
	function loadBody() {
		$GLOBALS['page']->setWorkingZone( 'page_head' );
		
		switch($GLOBALS['op']) {
			case "statuser" : {
				$GLOBALS['page']->add( '<link href="'.getPathTemplate().'style/style_treeview.css" rel="stylesheet" type="text/css" />'."\n" );
				//$GLOBALS['page']->add( '<link href="'.getPathTemplate().'style/style_scormplayer.css" rel="stylesheet" type="text/css" />'."\n" );
			};break;
			case "statitem":
			case "statcourse":
			case "statoneuser":
			case "statoneuseroneitem": {
				$GLOBALS['page']->add( '<link href="'.getPathTemplate().'style/style_treeview.css" rel="stylesheet" type="text/css" />'."\n" );
				$GLOBALS['page']->add( '<link href="'.getPathTemplate().'style/report/style_report_general.css" rel="stylesheet" type="text/css" />'."\n" );
				//$GLOBALS['page']->add( '<link href="'.getPathTemplate().'style/style_organizations.css" rel="stylesheet" type="text/css" />'."\n" );				
				//$GLOBALS['page']->add( '<link href="'.getPathTemplate().'style/style_scormplayer.css" rel="stylesheet" type="text/css" />'."\n" );
			}
		}		
		require('modules/stats/stats.php');
	}
	
	function getAllToken($op) {
		
		if($op == 'statuser') {
			return array( 'view_user' => array( 	'code' => 'view_user',
								'name' => '_VIEW',
								'image' => 'standard/view.gif') );
		} else {
			
			return array( 'view_course' => array( 	'code' => 'view_course',
								'name' => '_VIEW',
								'image' => 'standard/view.gif') );
		}
		
	}
	
}



?>
