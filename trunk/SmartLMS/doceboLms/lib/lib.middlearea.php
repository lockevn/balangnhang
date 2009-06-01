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

class Man_MiddleArea {

	var $_cache = NULL;

	function Man_MiddleArea() {}
	
	function _tableMA() { return $GLOBALS['prefix_lms'].'_middlearea'; }
	
	function _query($query) {
		
		$re = mysql_query($query);
		if($GLOBALS['framework']['do_debug'] == 'on') {
			echo '<!-- debug :: Man_MiddleArea query: '.$query.' '.( !$re ? '@with_error: '.mysql_error() : '' ).' -->';
		}
		return $re;
	}
		
	function getObjIdstList($obj_index) {
	
		$query = "SELECT idst_list 
		FROM ".$this->_tableMA()." 
		WHERE obj_index = '".$obj_index."' ";
		$re_query = $this->_query($query);
		if(!$re_query) return false;
		
		list($idst_list) = mysql_fetch_row($re_query);
		
		if($idst_list && is_string($idst_list)) return unserialize($idst_list);
		return array();
	} 	
	
	function isDisabled($obj_index) {
	
		$query = "SELECT disabled 
		FROM ".$this->_tableMA()." 
		WHERE obj_index = '".$obj_index."' ";
		$re_query = $this->_query($query);
		if(!$re_query) return false;
		
		list($disabled) = mysql_fetch_row($re_query);
		
		return $disabled;
	}
	
	function changeDisableStatus($obj_index) {
		
		$c_status = $this->isDisabled($obj_index);
		
		if($c_status == 1) $c_status = 0;
		else $c_status = 1;
		
		$query = "UPDATE ".$this->_tableMA()." 
		SET disabled = '".$c_status."' 
		WHERE obj_index = '".$obj_index."' ";
		$re_query = $this->_query($query);
		
		if(!$re_query) return false;
		return true;
	}
	
	function setObjIdstList($obj_index, $idst_list) {
		
		$idst_list = serialize($idst_list);
		
		$query = "SELECT obj_index 
		FROM ".$this->_tableMA()." 
		WHERE obj_index = '".$obj_index."' ";
		$exists = mysql_num_rows($this->_query($query));
		
		if(!$exists) {
			
			$query = "INSERT INTO ".$this->_tableMA()."
			( idst_list, obj_index ) VALUES ( '".$idst_list."', '".$obj_index."' ) ";
		} else {
		
			$query = "UPDATE ".$this->_tableMA()."
			SET idst_list = '".$idst_list."'
			WHERE obj_index = '".$obj_index."' ";
		}
		$this->_cache[$obj_index] = $idst_list;
		return $this->_query($query);
	}
	
	function getDisabledList() {
	
		$disabled = array();
				
		$query = "SELECT obj_index 
		FROM ".$this->_tableMA()." as t
		WHERE t.disabled = '1' ";
		$re_query = $this->_query($query);
		
		while(list($obj_i) = mysql_fetch_row($re_query)) {
			
			$disabled[$obj_i] = $obj_i;
		}
		
		return $disabled;
	}
	
	function currentCanAccessObj($obj_index) {
		if($this->_cache === NULL) {
				
			$query = "SELECT obj_index, disabled, idst_list 
			FROM ".$this->_tableMA()." ";
			$re_query = $this->_query($query);
			
			while(list($obj_i, $disabled, $idst_list) = mysql_fetch_row($re_query)) {
				
				$this->_cache[$obj_i]['list'] = unserialize($idst_list);
				$this->_cache[$obj_i]['disabled'] =$disabled;
			}
		}
		if(isset($this->_cache[$obj_index]) && ($this->_cache[$obj_index]['disabled'] == 1)) {
			return false;	
		}
		$user_level = $GLOBALS['current_user']->getUserLevelId();
		if($user_level == ADMIN_GROUP_GODADMIN) return true;
		
		$user_assigned = $GLOBALS['current_user']->getArrSt();
		if(isset($this->_cache[$obj_index])) {
			if($this->_cache[$obj_index]['list'] == '' || empty($this->_cache[$obj_index]['list'])) return true;
			
			$intersect = array_intersect($user_assigned, $this->_cache[$obj_index]['list']);
		} else {
			return true;
		}
		
		return !empty($intersect);
	}
	
}

?>