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

require_once(dirname(__FILE__).'/class.definition.php');


class Module_Forum extends CmsAdminModule {

	function loadHeader() {

	}

	function loadBody() {

		require_once($GLOBALS["where_cms"]."/admin/modules/".$this->module_name."/".$this->module_name.".php");
		forumDispatch($GLOBALS['op']);

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