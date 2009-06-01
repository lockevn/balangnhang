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

class Module_Faq extends LmsModule {

	function hideLateralMenu() {
		
		if(isset($_SESSION['test_assessment'])) return true;
		if(isset($_SESSION['direct_play'])) return true;
		return false;
	}
	
	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		
		switch($GLOBALS['op']) {
			case "insfaqcat" :
			case "newfaq" :
			case "insfaq" : 
			
			case "modfaqcat" :
			case "upfaqcat" :
			case "modfaq" :
			case "upfaq" : {
				loadHeaderHTMLEditor();
			};break;
		}
		return;
	}
	
	function useExtraMenu() {
		return false;
	}
	
	function loadExtraMenu() {
		
	}
	
	function loadBody() {
		//EFFECTS: include module language and module main file
		
		switch($GLOBALS['op']) {
			case "play" : {
				$idCategory = importVar('idCategory', true, 0);
				$id_param = importVar('id_param', true, 0);
				$back_url = importVar('back_url');
				
				$object_faq = createLO( 'faq', $idCategory );
				$object_faq->play( $idCategory, $id_param, urldecode( $back_url ) );
			};break;
			default : {
				parent::loadBody();
			}
		}
	}
}

?>