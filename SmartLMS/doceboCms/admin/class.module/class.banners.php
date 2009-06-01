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

require_once(dirname(__FILE__).'/class.definition.php');


class Module_Banners extends CmsAdminModule {

	function loadBody() {

		require_once($GLOBALS["where_cms"].'/admin/modules/banners/banners.php');

	}

	// Function for permission managment
	function getAllToken($op) {
		return array(
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif'),
			'add' => array( 	'code' => 'add',
								'name' => '_ADD',
								'image' => 'standard/add.gif'),
			'mod' => array( 	'code' => 'mod',
								'name' => '_MOD',
								'image' => 'standard/mod.gif'),
			'del' => array( 'code' => 'del',
								'name' => '_DEL',
								'image' => 'standard/rem.gif')
		);
	}

}

?>