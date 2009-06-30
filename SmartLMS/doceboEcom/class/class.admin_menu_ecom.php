<?php

/************************************************************************/
/* DOCEBO Ecom - E-commerce System										*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');


require_once(dirname(__FILE__).'/../setting.php');
require_once($GLOBALS['where_framework'].'/class/class.admin_menu.php');
require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');

class Admin_Ecom extends Admin {

	/**
	 * class constructor
	 * @param 	DoceboUser 	$user	the object of the Docebo User, for permission control
	 *
	 * @return nothing
	 * @access public
	 */
	function Admin_Ecom(&$user) {
		$this->user =& $user;
		$this->platform = 'ecom';
		$this->table_level_one = $GLOBALS['prefix_ecom'].'_menu';
		$this->table_level_two = $GLOBALS['prefix_ecom'].'_menu_under';
	}

	/**
	 * @return	mixed	a list of the first level menu
	 *					[id] (	[link]
	 *							[image]
	 *						 	[name]  )
	 * @access public
	 */
	 function getLevelOne() {

		$lang =& DoceboLanguage::createInstance('menu_admin_over', $this->platform);

		$query_under = "
		SELECT tab.idMenu, menu.module_name, menu.associated_token, tab.name, tab.image, tab.collapse, menu.of_platform
		FROM ".$this->table_level_one." AS tab JOIN ".$this->table_level_two." AS menu
		WHERE tab.idMenu = menu.idMenu
		ORDER BY tab.sequence";
		$re_under = mysql_query($query_under);
		echo doDebug($query_under);

		$menu = array();
		while(list($id_main, $module_name, $token, $name, $image, $collapse, $of_platform) = mysql_fetch_row($re_under)) {

			if(!isset($menu[$id_main]) && checkPerm($token, true, $module_name, ( $of_platform === NULL ? $this->platform : $of_platform ) )) {

				$menu[$id_main] = array('link' => 'index.php?op=change_main&new_main='.$id_main.'&of_platform='.( $of_platform === NULL ? $this->platform : $of_platform ),
									'name' => ( $name != '' ? $lang->def($name)  : '' ),
									'image' => 'area_title/'.$image,
									'collapse' => ( $collapse == 'true' ? true : false ),
									'of_platform' => ( $of_platform === NULL ? $this->platform : $of_platform ));
			}
		}
		return $menu;
	 }

	 function getLevelOneIntest($idMenu) {
		$lang =& DoceboLanguage::createInstance('menu_admin_over', $this->platform);

		$query_menu = "
		SELECT name, image
		FROM ".$this->table_level_one."
		WHERE idMenu = '".(int)$idMenu."'";
		$re_menu = mysql_query($query_menu);
		list($name, $image) = mysql_fetch_row($re_menu);

		return array(
			'name' => $lang->def($name),
			'image' => getPathImage('ecom').'area_title/'.$image
		);
	 }

	 /**
	  * @param 	int 	$id_level_one	the id of a level one menu voice
	  *
	  * @return	mixed	a list of the second level menu of a passed first level menu,
	  *					if not passed return all the voice of the second level
	  *					[id] (	[link]
	  *						 	[name]  )
	  * @access public
	  */
	 function getLevelTwo($id_level_one = false) {

		 $lang =& DoceboLanguage::createInstance('menu_admin', $this->platform);

		 $query_menu = "
		 SELECT idUnder, module_name, default_name, default_op, associated_token, of_platform
		 FROM ".$this->table_level_two."
		 ".( $id_level_one !== false  ? " WHERE idMenu = '".$id_level_one."'" : "")."
		 ORDER BY sequence";
		 $re_menu = mysql_query($query_menu);
		 $GLOBALS['page']->add( doDebug($query_menu), 'debug' );

		 $menu = array();
		 while(list($id, $modname, $name, $op, $token, $of_platform) = mysql_fetch_row($re_menu)) {

			 if($this->user->matchUserRole('/'.( $of_platform === NULL ? $this->platform : $of_platform ).'/admin/'.$modname.'/'.$token)) {
				 $menu[$id] = array('modname' => $modname,
				 					'op' => $op,
				 					'link' => 'index.php?modname='.$modname.'&op='.$op.'&of_platform='.( $of_platform === NULL ? $this->platform : $of_platform ),
									'name' => ( $name != '' ? $lang->def($name) : $lang->def('_'.strtoupper($modname)) ),
									'of_platform' => ( $of_platform === NULL ? $this->platform : $of_platform ) );
			 }
		 }
		 return $menu;
	 }
}

class Admin_Managment_Ecom extends Admin_Managment {

	/**
	 * class constructor
	 * @return	nothing
	 * @access public
	 */
	function Admin_Managment_Ecom() {
		$this->platform = 'ecom';
		$this->table_level_one = $GLOBALS['prefix_ecom'].'_menu';
		$this->table_level_two = $GLOBALS['prefix_ecom'].'_menu_under';

		$this->lang_over 		=& DoceboLanguage::createInstance('menu_admin_over', 'ecom');
		$this->lang 			=& DoceboLanguage::createInstance('menu_admin', 'ecom');
		$this->lang_perm 		=& DoceboLanguage::createInstance('permission');
	}

}

?>