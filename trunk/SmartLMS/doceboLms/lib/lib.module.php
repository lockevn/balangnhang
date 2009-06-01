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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package 	DoceboLMS
 * @category 	function for class istance
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id: lib.module.php 573 2006-08-23 09:38:54Z fabio $
 */

/**
 * create a istance of a specified class of a module
 * automaticaly include the file that contains the class of the module
 *
 * @param string	$module_name 	the name og the module to istance
 * @param string 	$class_name 	the name of the class relative to the module, if not passed is 
 *									extracted from the $module_name
 * 
 * @return mixed 	the class istance
 */
function &createModule($module_name, $class_name = NULL) {
	
	require_once(dirname(__FILE__).'/../class.module/class.definition.php');
	
	$query_class = "
	SELECT file_name, class_name 
	FROM ".$GLOBALS['prefix_lms']."_module 
	WHERE module_name = '".$module_name."'";
	list($class_file, $class_name) = mysql_fetch_row(mysql_query($query_class));
	
	if(file_exists(dirname(__FILE__).'/../class.module/'.$class_file) && $class_file != '') {
		
		include(dirname(__FILE__).'/../class.module/'.$class_file);
		if($class_name === NULL) $class_name = 'Module_'.ucfirst($module_name);
		
	} elseif(file_exists(dirname(__FILE__).'/../class.module/class.'.$module_name.'.php')) {
		
		include(dirname(__FILE__).'/../class.module/class.'.$module_name.'.php');
		if( $class_name === NULL ) $class_name = 'Module_'.ucfirst($module_name);
		
	} else {
		
		$class_name = 'LmsModule';
	}
	$module_cfg = eval( "return new $class_name ();" );
	return $module_cfg;
}

function &createLmsModule($file_name, $class_name) {
	
	$file_path = $GLOBALS['where_lms'].'/class.module/'.$file_name;
	
	if(file_exists($file_path) && !is_dir($file_path)) {
		
		require_once($GLOBALS['where_lms'].'/class.module/class.definition.php');
		require_once($file_path);
	} else {
		
		require_once($GLOBALS['where_lms'].'/class.module/class.definition.php');
		$class_name = 'LmsModule';
	}
	$module_cfg = eval( "return new $class_name();" );
	return $module_cfg;
}

function createLO( $objectType, $idResource = NULL ) {
	
	$query = "SELECT className, fileName FROM ".$GLOBALS['prefix_lms']."_lo_types WHERE objectType='".$objectType."'";
	$rs = mysql_query( $query );
	list( $className, $fileName ) = mysql_fetch_row( $rs );
	require_once(dirname(__FILE__).'/../class.module/learning.object.php' );
	/*
	if($objectType == '') return eval("return new Learning_Object( $idResource );");
	*/
	require_once(dirname(__FILE__).'/../class.module/'.$fileName );
	$lo = eval( "return new $className ( $idResource );" );
	return $lo;
}

function createLOTrack( $idTrack, $objectType, $idResource, $idParams, $back_url ) {
	
	$query = "SELECT classNameTrack, fileNameTrack FROM ".$GLOBALS['prefix_lms']."_lo_types WHERE objectType='".$objectType."'";
	$rs = mysql_query( $query );
	list( $className, $fileName ) = mysql_fetch_row( $rs );
	if( $fileName == '' ) return false;
	require_once(dirname(__FILE__).'/../class.module/learning.object.php' );
	require_once(dirname(__FILE__).'/../class.module/'.$fileName );
	$lo = eval( "return new $className ( '$idTrack', '$idResource', '$idParams', \$back_url );" );
	return $lo;
}

function createLOTrackShort( $idReference, $idUser, $back_url ) {
	
	$query = "SELECT o.idParam, o.objectType, o.idResource,"
			." ct.idTrack, lt.classNameTrack, lt.fileNameTrack"
 			." FROM ".$GLOBALS['prefix_lms']."_organization o"
			." JOIN ".$GLOBALS['prefix_lms']."_commontrack ct"
			." JOIN ".$GLOBALS['prefix_lms']."_lo_types lt"
			." WHERE (o.objectType = lt.objectType)"
			."   AND (o.idOrg = ct.idReference)"
 			."   AND (o.idOrg = '".(int)$idReference."')"
 			."   AND (ct.idUser = '".(int)$idUser."')";
	$rs = mysql_query( $query );
	echo "\n\n<!-- $query -->\n\n";
	list( $idParams, $objectType, $idResource, $idTrack, $className, $fileName ) = mysql_fetch_row( $rs );
	if( $fileName == '' ) return false;
	require_once( dirname(__FILE__).'/../class.module/learning.object.php' );
	require_once( dirname(__FILE__).'/../class.module/'.$fileName );
	$lo = eval( "return new $className ( '$idTrack', '$idResource', '$idParams', \$back_url );" );
	return $lo;
}

?>
