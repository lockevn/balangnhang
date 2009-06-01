<?php

/*************************************************************************/
/* DOCEBO LMS - E-Learning System                                        */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Fabio Pirovano (gishell@tiscali.it)             */
/* http://www.spaghettilearning.com                                      */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once( dirname( __FILE__ ).'/learning.object.php' );

//if( version_compare(phpversion(), "5.0.0") == -1 ) {
	define('_scorm_basepath',$GLOBALS['where_lms'].'/modules/scorm/');
//} else {
//	define('_scorm_basepath',$GLOBALS['where_lms'].'/modules/scorm5/');
//}

class Learning_ScormOrg extends Learning_Object {
	
	var $idParams;
	var $object_type;
	var $back_url;
	
	/** 
	 * object constructor
	 **/
	 
	function Learning_ScormOrg( $id = NULL ) {
		parent::Learning_Object( $id );

		list($title) = mysql_fetch_row(mysql_query("
		SELECT title 
		FROM ".$GLOBALS['prefix_lms']."_scorm_organizations
		WHERE idscorm_organization = '".(int)$id."'"));	
		
		$this->idAuthor = '';
		$this->title = $title;
		$this->object_type = 'scormorg';
	}
	
	function getTitle() {
		return $this->title;
	}
	
	function getObjectType() {
		return $this->object_type;
	}
	
	function getBackUrl() {
		return $this->back_url;
	}
	
	/**
	include il linguaggio
	 * return array of mustent param
	 [0]
	 	['label'] = _DEFINITION;
		['param_name'] = parameter name;
	 [1]
	 	 ['label'] = _DEFINITION;
		['param_name'] = parameter name;
	 **/
	function getParamInfo() {
		return array(	array('label' => 'Autoplay','param_name' => 'autoplay'),
						array('label' => 'Template','param_name' => 'playertemplate'));
		//return FALSE;
	}
	
	function renderCustomSettings( $arrParams, $form, $lang ) {
		
		$autoplay = isset($arrParams['autoplay'])?$arrParams['autoplay']:'1';
		if($arrParams['autoplay'] == '') $autoplay = '1';
		
		$out = $form->getRadioSet( $lang->def( '_AUTOPLAY' ), 
									'autoplay', 
									'autoplay', 
									array( 	$lang->def( '_NO' ) => "0",
											$lang->def( '_YES' ) => "1"),
									$autoplay
								);


		/* ------ dropdown template choiche ----- */
		$arr_templates = array();
		
		$path = $GLOBALS['where_lms'].'/templates/'.getTemplate().'/player_scorm/';
		$templ = dir($path);
		
		while($elem = $templ->read()) {
	
			if((is_dir($path.$elem)) && ($elem != ".") && ($elem != "..") && ($elem != ".svn") && $elem{0} != '_' ) {
	
				$arr_templates[$elem] = $elem;
			}
		}
		closedir($templ->handle);
		
		$template = isset($arrParams['playertemplate'])?$arrParams['playertemplate']:'default';
		$out .= $form->getDropdown(	def( '_PLAYERTEMPLATE', 'menu_admin_over', 'lms'),//$lang->def( '_PLAYERTEMPLATE'), 
									'playertemplate', 
									'playertemplate', 
									$arr_templates, 
									$template 
								);
		/* -------------------------------------- */
		return $out;
	}
	
	/**
	 * function create( $back_url )
	 * @param string $back_url contains the back url
	 * @return bool TRUE if success FALSE if fail
	 * attach the id of the created object at the end of back_url with the name id_lo
	 *
	 * static
	 **/
	function create( $back_url ) {
		$this->back_url = $back_url;
		
		unset($_SESSION['last_error']);
		
		require_once( _scorm_basepath.'scorm.php' );
		additem( $this );
	}	
	
	/**
	 * function edit
	 * @param int $id contains the resource id
	 * @param string $back_url contains the back url
	 * @return nothing
	 * attach in back_url id_lo that is passed $id and attach edit_result with the result of operation in boolean format 
	 **/
	function edit( $id, $back_url ) {
		
		$this->id = $id;
		$this->back_url = $back_url;
		
		unset($_SESSION['last_error']);
		
		require_once( _scorm_basepath.'scorm.php' );
		moditem( $this );
	}
	
	/**
	 * function play( $id, $id_param, $back_url )
	 * @param int $id contains the resource id
	 * @param int $id_param contains the id needed for params retriving
	 * @param string $back_url contain the back url
	 * @return nothing return
	 **/
	function play( $id, $id_param, $back_url ) {
		require_once( $GLOBALS['where_lms'].'/lib/lib.param.php' );
		require_once( _scorm_basepath.'scorm.php' );
		$idReference = getLOParam($id_param, 'idReference');
		$autoplay = getLOParam($id_param, 'autoplay');
		$playertemplate = getLOParam($id_param, 'playertemplate');
		play($id, $idReference, $back_url, $autoplay, $playertemplate);
		//jumpTo( 'index.php?modname=scorm&op=play&idscorm_organization='.$this->idResource
		//		.'&idReference='.$idReference);
	}
	
	/**
	 * function del
	 * @param int $id contains the resource id
	 * @param string $back_url contains the back url (not used yet)
	 * @return false if fail, else return the id lo
	 **/
	function del( $id, $back_url = NULL ) {
		require_once(  _scorm_basepath.'scorm.php' );
		
		list($idscorm_package) = mysql_fetch_row(mysql_query("
		SELECT idscorm_package 
		FROM ".$GLOBALS['prefix_lms']."_scorm_organizations
		WHERE idscorm_organization = '".(int)$id."'"));	

		_scorm_deleteitem( $idscorm_package, (int)$id );
		return $id;
	}
	
	/**
	 * function copy( $id, $back_url )
	 * @param int $id contains the resource id
	 * @param string $back_url contain the back url (not used yet)
	 * @return int $id if success FALSE if fail
	 **/
	function copy( $id, $back_url = NULL ) {
		require_once(  _scorm_basepath.'scorm.php' );
		
		list($idscorm_package) = mysql_fetch_row(mysql_query("
		SELECT idscorm_package 
		FROM ".$GLOBALS['prefix_lms']."_scorm_organizations
		WHERE idscorm_organization = '".(int)$id."'"));	

		return _scorm_copyitem( $idscorm_package, $id );	 
	}
	 
	/** 
	 * function getMultipleResource( $idMultiResource )
	 * @param int $idMultiResource identifier of the multi resource
	 * @return array an array with the ids of all resources
	 **/
	function getMultipleResource( $idMultiResource ) {
		$arrMultiResources = array();
		$rs = mysql_query("SELECT idscorm_organization "
							." FROM ".$GLOBALS['prefix_lms']."_scorm_organizations "
							." WHERE idscorm_package = '".(int)$idMultiResource."'");
		while( list($idscorm_organization) = mysql_fetch_row($rs) ) {
			$arrMultiResources[] = $idscorm_organization;
		}
		return $arrMultiResources;
	}
	 
	function canBeMilestone() {
		return TRUE;
	}	 
}

?>
