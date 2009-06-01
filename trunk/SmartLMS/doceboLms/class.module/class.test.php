<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System						 */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*																		 */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Module_Test extends LmsModule {
	
	function hideLateralMenu() {
		
		if(isset($_SESSION['test_assessment'])) return true;
		if(isset($_SESSION['direct_play'])) return true;
		return false;
	}
	
	function loadBody() {
		//EFFECTS: include module language and module main file
		
		switch($GLOBALS['op']) {
			case "play" : {
				$idTest = importVar('id_test', true, 0);
				$id_param = importVar('id_param', true, 0);
				$back_url = importVar('back_url');
				
				$object_poll = createLO( 'test', $idTest );
				$object_poll->play( $idTest, $id_param, unserialize(urldecode($back_url)) );
			};break;
			default : {
				parent::loadBody();
			}
		}
	}
}

?>