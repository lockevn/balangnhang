<?php

class Upgrade {
	
	var $platform;
	
	var $mname;
	
	var $db_man;
	
	var $last_error = false;
	
	var $end_version = false;
	
	/**
	 * class constructor
	 **/
	function Upgrade() {}
	
	/**
	 * @param DoceboSql 	$db_man 	the database connection
	 **/
	function setDbMan(&$db_man) {
		
		$this->db_man =& $db_man;
	}
	
	/**
	 * @return mixed 	false if unsetted, else the version reached by the last version upgrade performed
	 **/
	function _getEndVersion() {
		
		return $this->end_version;
	}
	
	/**
	 * return a meangfull error code
	 * @param string 	$start_version 		the version from which the step is stated
	 * @param int 		$index 				an index of the operation performed in the step
	 *
	 * @return string 	the error code
	 **/
	function _getErrCode($start_version, $index) {
		
		return $this->platfom.'_'.$this->mname.'_'.$start_version.'_'.$index;
	}
	
	/**
	 * return a meangfull array with error code and message
	 * @param string 	$start_version 		the version from which the step is stated
	 * @param int 		$index 				an index of the operation performed in the step
	 * @param bool 		$is_critical 		if true the flag is_critical will be a true
	 *
	 * @return array 	with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function _getErrArray($start_version, $index, $message = false, $is_critical = false) {
		
		$this->last_error = array(
			'error_code' => $this->_getErrCode($start_version, $index), 
			'error_msg' => ( $message === false ? $this->db_man->error() : $message ),
			'is_critical' => $is_critical
		);
		return $this->last_error;
	}
	
	/**
	 * return a meangfull array with error code and message if an error was occured
	 *
	 * @return mixed 	an arraywith an error code and an error message
	 * 					array( 'error_code', 'error_msg' ) if error exists, else false
	 **/
	function getLastError() {
		
		return $this->last_error;
	}
	
	/**
	 * check version of module is reached
	 * @param string 	$version 	a version name to check
	 *
	 * @return bool true if the version of the module is compatible with the version passed (equal or greater)
	 **/
	function isReached($version) {
		
		return true;
	}
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		return true;
	}
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version
	 * @param string 	$end_version 	the end version
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function actionUpgrade($start_version, $end_version) {
		
		return true;
	}
}

?>