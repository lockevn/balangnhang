<?php

/****************************************************************************/
/* DOCEBO LMS - Learning Managment System										*/
/* ========================================================================	*/
/*																				*/
/* This program is free software. You can redistribute it and/or modify  	*/
/* it under the terms of the GNU General Public License as published by  	*/
/* the Free Software Foundation; either version 2 of the License.        	*/
/****************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Module_Poll extends LmsModule {
	
	function hideLateralMenu() {
		
		if(isset($_SESSION['test_assessment'])) return true;
		if(isset($_SESSION['direct_play'])) return true;
		return false;
	}
	
	function loadBody() {
		//EFFECTS: include module language and module main file
		
		switch($GLOBALS['op']) {
			case "play" : {
				$id_poll = importVar('id_poll', true, 0);
				$id_param = importVar('id_param', true, 0);
				$back_url = importVar('back_url');
				
				$object_poll = createLO( 'poll', $id_poll );
				$object_poll->play( $id_poll, $id_param, unserialize(urldecode($back_url)) );
			};break;
			default : {
				parent::loadBody();
			}
		}
	}
}

?>