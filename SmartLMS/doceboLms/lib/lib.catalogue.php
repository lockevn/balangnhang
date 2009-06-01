<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system									*/
/* ============================================							*/
/*																			*/
/* Copyright (c) 2005														*/
/* http://www.docebo.com													*/
/*																			*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @version  $Id: lib.catalogue.php 635 2006-09-15 07:28:40Z fabio $
 * @author	 Fabio Pirovano <fabio [at] docebo-com>
 */

class Selector_Catalogue {
	
	var $show_filter = true;
	
	var $filter = array();
	
	var $current_page = '';
	
	var $current_selection = array();
	
	/**
	 * Class constructor
	 */
	function Selector_Catalogue() {
		
		$this->show_filter = true;
	}
	
	function enableFilter() {
		
		$this->show_filter = true;
	}
	
	function disableFilter() {
		
		$this->show_filter = false;
	}
	
	/**
	 * return the current status in a pratic format
	 * @return string a string with the data used for reloading the current status
	 */
	function getStatus() {
		
		$status = array(
			'page' 					=> $this->current_page,
			'filter' 				=> serialize($this->filter), 
			'show_filter' 			=> $this->show_filter, 
			'current_selection' 	=> serialize($this->current_selection) );
		return serialize($status);
	}
	
	/**
	 * reset the current status to te given one
	 * @param string	$status_serialized a valid status saved using getStatus
	 */
	function loadStatus(&$status_serialized) {
		
		if($status_serialized == '') return ;
		$status = unserialize($status_serialized);
		
		$this->current_page			= $status['page'];
		$this->filter				= unserialize($status['filter']);
		$this->show_filter			= $status['show_filter'];
		$this->current_selection	= unserialize($status['current_selection']);
		
	}
	
	function parseForAction($array_action) {
	
	}
	
	function parseForState($array_state) {
		
		// older selection
		if(isset($array_state['catalogue_selected'])) {
			
			$this->current_selection = unserialize(urldecode($array_state['catalogue_selected']));
		}
		// add last selection
		if(isset($array_state['new_catalogue_selected'])) {
			while(list($id_c) = each($_POST['new_catalogue_selected'])) {
				
				$this->current_selection[$id_c] = $id_c;
			}
		}
	}
	
	function stateSelection() {
		
		return Form::getHidden('catalogue_selected', 'catalogue_selected', urlencode(serialize($this->current_selection)) );
	}
	
	function getSelection() {
		
		return $this->current_selection;
	}
	
	function resetSelection($new_selection) {
		
		$this->current_selection = $new_selection;
	}
	
	function loadCatalogueSelector() {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		
		$lang =& DoceboLanguage::createInstance('catalogue', 'lms');
		
		// Filter
		$this->filter['catalogue_name'] = ( isset($_POST['cat_filter_name']) ? $_POST['cat_filter_name'] : '' );
		if($this->show_filter === true) {
			$form = new Form();
			$GLOBALS['page']->add(
				$form->getOpenFieldset($lang->def('_CATALOGUEFILTER'))
				.Form::getTextfield($lang->def('_NAME'), 'cat_filter_name', 'cat_filter_name', '255', 
					( isset($_POST['cat_filter_name']) ? $_POST['cat_filter_name'] : '' ))
				.$form->openButtonSpace() 
				.$form->getButton('catalogue_filter', 'catalogue_filter', $lang->def('_SEARCH'))
				.$form->closeButtonSpace()
				.$form->getCloseFieldset()
			, 'content');
		}
		// End Filter
		
		$tb = new TypeOne($GLOBALS['visuItem'], $lang->def('_CATALOGUE_CAPTION'), $lang->def('_CATALOGUE_SUMMARY'));
		
		$tb->initNavBar('ini_cat', 'button');
		$ini = $tb->getSelectedElement();
		
		$select = "
		SELECT c.idCatalogue, c.name, c.description"; 
		$query_catalogue = "
		FROM ".$GLOBALS['prefix_lms']."_catalogue AS c
		WHERE 1";
		// Retriving data
		if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
			
			require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');
			
			$course_man = new AdminCourseManagment();
			$catalogues =& $course_man->getUserCatalogues( getLogUserId() );
			
			if(empty($catalogues)) {
				
				$query_catalogue .= " AND 0 ";
			} else {
				
				$query_catalogue .= " AND idCatalogue IN (".implode(',', $catalogues).") ";
			}
		}
		if($this->filter['catalogue_name'] != '') {
			$query_catalogue .= " AND c.name LIKE '%".$this->filter['catalogue_name']."%'";
		}
		list($tot_catalogue) = mysql_fetch_row(mysql_query("SELECT COUNT(*) ".$query_catalogue));
		$query_catalogue .= " ORDER BY c.name 
							LIMIT ".$ini.",".(int)$GLOBALS['visuItem'];
		
		$re_catalogue = mysql_query($select.$query_catalogue);
		
		$type_h = array('image', '', '', '');
		$cont_h = array(
			'<span class="access-only">'.$lang->def('_CATALOGUE_SELECTION').'</span>', 
			$lang->def('_CATALOGUE_NAME'), 
			$lang->def('_DESCRIPTION')
		);
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);
		while(list($id_catalogue, $name, $descr) = mysql_fetch_row($re_catalogue)) {
			
			$tb_content = array(
				Form::getInputCheckbox('new_catalogue_selected_'.$id_catalogue, 'new_catalogue_selected['.$id_catalogue.']', $id_catalogue, 
					isset($this->current_selection[$id_catalogue]), ''), 
				'<label for="new_catalogue_selected_'.$id_catalogue.'">'.$name.'</label>',
				'<label for="new_catalogue_selected_'.$id_catalogue.'">'.$descr.'</label>'
			);
			$tb->addBody($tb_content);
			if(isset($this->current_selection[$id_catalogue])) unset($this->current_selection[$id_catalogue]);
		}
		
		$GLOBALS['page']->add(
			$tb->getTable()
			.$tb->getNavBar($ini, $tot_catalogue)
			.$this->stateSelection(), 'content');
	}
}

class Catalogue_Manager {
	
	/**
	 * @var $acl the acl instance
	 */
	var $acl;
	
	/**
	 * @var $aclManager the aclManager instance
	 */
	var $aclManager;
	
	/**
	 * class constructor
	 */
	function Catalogue_Manager() {
		
		$this->acl = new DoceboACL();
		$this->aclManager =& $this->acl->getAclManager();
	}
	
	/**
	 * exucute querys and do some debug function
	 * @param string	$query	the text of the query
	 *
	 * @return	resource_id		the result of the query
	 */
	function _executeQuery($query) {
		
		$rs = mysql_query($query);
		$GLOBALS['page']->add('<!-- '.$query.' : '.mysql_error().' -->'."\n", 'debug');
		return $rs;
	}
	
	/**
	 * @return	string 	the name of the catalogue main table
	 */
	function _getCataTable() {
		return $GLOBALS['prefix_lms'].'_catalogue';
	}
	
	/**
	 * @return	string 	the name of the catalogue contained element
	 */
	function _getCataEntryTable() {
		return $GLOBALS['prefix_lms'].'_catalogue_entry';
	}
	
	/**
	 * @return	string 	the name of the catalogue association with groups
	 */
	function _getCataMemberTable() {
		return $GLOBALS['prefix_lms'].'_catalogue_member';
	}
	
	/**
	 * @param	int	$id_user 	the idst of a user 
	 *
	 * @return	string 	the id of the catalogues assigned to the assed user
	 */
	function &getUserAllCatalogueId($id_user) {
		
		$catalogues 	= array();
		$user_groups 	= $this->acl->getSTGroupsST($id_user);
		
		if(empty($user_groups)) return $catalogues;
		$query = "
		SELECT DISTINCT cm.idCatalogue 
		FROM ".$this->_getCataMemberTable()." AS cm 
		WHERE cm.idst_member IN (".implode(',', $user_groups).") ";
		$re_catalogue = $this->_executeQuery($query);
		while(list($id_cata) = mysql_fetch_row($re_catalogue)) {
			
			$catalogues[$id_cata] = $id_cata;
		}
		return $catalogues;
	}
	
	/**
	 * @param	int	$id_user 	the idst of a user 
	 *
	 * @return	array 	some info about the catalogues associated to the user  array( [id] => array([idCatalogue], [name], [description]), ...)
	 */
	function &getUserAllCatalogueInfo($id_user) {
		
		$catalogues 	= array();
		$user_groups 	= $this->acl->getSTGroupsST($id_user);
		$query = "
		SELECT DISTINCT cm.idCatalogue, m.name, m.description 
		FROM ".$this->_getCataTable()." AS m
				JOIN ".$this->_getCataMemberTable()." AS cm
		WHERE m.idCatalogue = cm.idCatalogue AND cm.idst_member IN (".implode(',', $user_groups).") ";
		$re_catalogue = $this->_executeQuery($query);
		while($cata = mysql_fetch_assoc($re_catalogue)) {
			
			$catalogues[$cata['idCatalogue']] = $cata;
		}
		return $catalogues;
	}
	
	/**
	 * @param	int	$id_user 	the idst of a user 
	 *
	 * @return	array 	the id of all the course associated to the group, not include the course in the associated coursepath
	 */
	function getAllCourseOfUser($id_user) {
		
		$courses 		= array();
		if($id_user == getLogUserId()) $user_groups = $GLOBALS['current_user']->getArrSt();
		else $user_groups 	= $this->acl->getSTGroupsST($id_user);
		$query = "
		SELECT DISTINCT ce.idEntry
		FROM ".$this->_getCataEntryTable()." AS ce 
			JOIN ".$this->_getCataMemberTable()." AS cm
		WHERE ce.type_of_entry = 'course' AND 
			ce.idCatalogue = cm.idCatalogue AND 
			cm.idst_member IN (".implode(',', $user_groups).") ";
		$re_courses = $this->_executeQuery($query);
		while(list($id_course) = mysql_fetch_row($re_courses)) {
			
			$courses[$id_course] = $id_course;
		}
		return $courses;
	}
	
	/**
	 * @param	int	$id_user 	the idst of a user 
	 *
	 * @return	array 	the id of the coursepath associated to the user
	 */
	function getAllCoursepathOfUser($id_user) {
		
		$coursespath 		= array();
		$user_groups 	= $this->acl->getSTGroupsST($id_user);
		$query = "
		SELECT DISTINCT ce.idEntry
		FROM ".$this->_getCataEntryTable()." AS ce JOIN ".$this->_getCataMemberTable()." AS cm
		WHERE ce.type_of_entry = 'coursepath' AND 
			ce.idCatalogue = cm.idCatalogue AND 
			cm.idst_member IN (".implode(',', $user_groups).") ";
		$re_courses = $this->_executeQuery($query);
		while(list($id_path) = mysql_fetch_row($re_courses)) {
			
			$coursespath[$id_path] = $id_path;
		}
		return $coursespath;
	}
	
}

class AdminCatalogue {
	
	/**
	 * @var $acl the acl instance
	 */
	var $acl;
	
	/**
	 * @var $aclManager the aclManager instance
	 */
	var $aclManager;
	
	/**
	 * class constructor
	 */
	function AdminCatalogue() {
		
	}
	
	/**
	 * exucute querys and do some debug function
	 * @param string	$query	the text of the query
	 *
	 * @return	resource_id		the result of the query
	 */
	function _executeQuery($query) {
		
		$rs = mysql_query($query);
		$GLOBALS['page']->add('<!-- '.$query.' : '.mysql_error().' -->'."\n", 'debug');
		return $rs;
	}
	
	/**
	 * @return	string 	the name of the catalogue main table
	 */
	function _getCataTable() {
		return $GLOBALS['prefix_lms'].'_catalogue';
	}
	
	/**
	 * @return	string 	the name of the catalogue contained element
	 */
	function _getCataEntryTable() {
		return $GLOBALS['prefix_lms'].'_catalogue_entry';
	}
	
	/**
	 * @param	array	$catalogues 	the id of the catalogues
	 *
	 * @return	array 	the id of all the course associated to the catalogues
	 */
	function getAllCourses($catalogues) {
		
		$courses = array();
		if(empty($catalogues)) return array();
		$query = "
		SELECT DISTINCT ce.idEntry
		FROM ".$this->_getCataEntryTable()." AS ce 
		WHERE ce.type_of_entry = 'course' AND 
			ce.idCatalogue  IN (".implode(',', $catalogues).") ";
		$re_courses = $this->_executeQuery($query);
		while(list($id) = mysql_fetch_row($re_courses)) {
			
			$courses[$id] = $id;
		}
		return $courses;
	}
	
	/**
	 * @param	array	$catalogues 	the id of the catalogues
	 *
	 * @return	array 	the id of all the coursepath associated to the catalogues
	 */
	function getAllCoursePaths($catalogues) {
		
		$coursepaths = array();
		if(empty($catalogues)) return array();
		$query = "
		SELECT DISTINCT ce.idEntry
		FROM ".$this->_getCataEntryTable()." AS ce 
		WHERE ce.type_of_entry = 'coursepath' AND 
			ce.idCatalogue  IN (".implode(',', $catalogues).") ";
		$re_coursepaths = $this->_executeQuery($query);
		while(list($id) = mysql_fetch_row($re_coursepaths)) {
			
			$coursepaths[$id] = $id;
		}
		return $coursepaths;
	}
}

?>