<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once(dirname(__FILE__).'/certificate.base.php');

class CertificateSubs_User extends CertificateSubstitution {

	function getSubstitutionTags() {
		
		$lang =& DoceboLanguage::createInstance('admin_certificate', 'lms');
		
		$subs = array();
		$subs['[display_name]'] = $lang->def('_DISPLAY_NAME');
		$subs['[username]'] 	= $lang->def('_USERNAME');
		$subs['[firstname]'] 	= $lang->def('_FIRSTNAME');
		$subs['[lastname]'] 	= $lang->def('_LASTNAME');
		
		//variable fields
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		$temp = new FieldList();
    $fields = $temp->getFlatAllFields();
		foreach ($fields as $key=>$value) {
      $subs['[userfield_'.$key.']'] = $lang->def('_USERFIELD').' "'.$value.'"';
    }
		
		return $subs;
	}
	
	function getSubstitution() {
		
		$subs = array();
		
		$aclman =& $GLOBALS['current_user']->getAclManager();
		$user = $aclman->getUser($this->id_user, false);
		
		$subs['[display_name]'] =  ( $user[ACL_INFO_LASTNAME].$user[ACL_INFO_FIRSTNAME]
			? $user[ACL_INFO_LASTNAME].' '.$user[ACL_INFO_FIRSTNAME]
			: $aclman->relativeId($user[ACL_INFO_USERID]) );
		
		$subs['[username]'] 	= $aclman->relativeId($user[ACL_INFO_USERID]);
		$subs['[firstname]'] 	= $user[ACL_INFO_FIRSTNAME];
		$subs['[lastname]'] 	= $user[ACL_INFO_LASTNAME];
		
		//variable fields
	    require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		
		$temp = new FieldList();
	    $fields = $temp->getFlatAllFields();
		foreach ($fields as $key=>$value)
	    	$subs['[userfield_'.$key.']'] = $temp->showFieldForUser(getLogUserId(), $key);
	    
		return $subs;
	}
}

?>