<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2008													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');


define('_REST_OUTPUT_XML', 'xml');
define('_REST_OUTPUT_JSON', 'json');


define('_REST_AUTH_UCODE', 0); //use single user-code authentication
define('_REST_AUTH_TOKEN', 1); //use generated token authentication

define('_REST_AUTH_UCODE_DESC', 'SINGLE_CODE');
define('_REST_AUTH_TOKEN_DESC', 'GENERATED_TOKEN');

class RestAPI {

	//internal variables
	protected $buffer = '';
	protected $outputType = _REST_OUTPUT_XML; //default type = xml
	
	protected $needAuthentication = true;
	protected $authenticated = false;
	
	
	//public methods

	public function __construct() {
		//...
	}

	public function dispatch($method, $function, &$params) {
		//...
	}

	final public function checkAuthentication() {
		//eliminates old token
		$query = "DELETE FROM ".$GLOBALS['prefix_fw']."_rest_authentication WHERE expiry_date<NOW() ";
		$res = mysql_query($query);
	
		if (!$this->needAuthentication) {
			return true; //no authentication needed for this module
		}
	
		//load auth setting
		$auth_method = $GLOBALS['framework']['rest_auth_method'];
		$code = get_req('auth', DOTY_STRING, false); //code provided by the user in the request
		
		$result = false;
		switch ($auth_method) {
	
			//use application's pre-set authentication code
			case _REST_AUTH_UCODE: {
				$result = ($code==$GLOBALS['framework']['rest_auth_code']); 
			} break;
	
			//search the token in  authentications DB table
			case _REST_AUTH_TOKEN: {
				$query = "SELECT * FROM ".$GLOBALS['prefix_fw']."_rest_authentication WHERE token='$code'";
				$res = mysql_query($query);
				if (mysql_num_rows($res)>0) { //$result = (mysql_num_rows($res)>0);
					$result = true;
					//if ($GLOBALS['framework']['rest_auth_update']) {
					$now = time();
					$query = "UPDATE ".$GLOBALS['prefix_fw']."_rest_authentication ".
						" SET last_enter_date='".date("Y-m-d H:i:s", $now)."' ";
					if ($GLOBALS['framework']['rest_auth_update']) {
						$lifetime = $GLOBALS['framework']['rest_auth_lifetime']*60;
						$query .= " , expiry_date='".date("Y-m-d H:i:s", $now + $lifetime)."' ";
					}							
					$query .= " WHERE token='$token'";
					mysql_query($query);
					//}
				} else {
					$result = false;
				} 
			} break;
	
			//...
			default: {
				//...
			}
		}

		return $result;
	}

	//get information about the authetication mode
	final protected function getAuthenticationMethod() {
		$result = '';
		switch ($GLOBALS['framework']['rest_auth_method']) {
			case _REST_AUTH_UCODE: { $result=_REST_AUTH_UCODE_DESC; } break;
			case _REST_AUTH_TOKEN: { $result=_REST_AUTH_TOKEN_DESC; } break;
		}
		$mode = array( 'auth_mode'=>$result );
		$this->write( $this->convertArray($mode) );
	}

	public function setOutputType($type) {
		$success=true;
		switch ($type) {
			case _REST_OUTPUT_XML: $this->outputType=_REST_OUTPUT_XML; break;
			case _REST_OUTPUT_JSON: $this->outputType=_REST_OUTPUT_JSON; break;
			default: $success=false; //invelid type definition has been passed, no setting done
		}
		return $success;
	}

	public function getOutputType() {
		return $this->outputType;
	}

	public function getOutput() {
		return $this->buffer;
	}

	//internal methods
	final protected function write($data) {
		$this->buffer .= $data;
	}

	final protected function convertArray(&$arr) {
		$output = '';
		
		switch ($this->outputType) {
			case _REST_OUTPUT_XML: {
				$output .= getXML($arr);
			} break;
			
			case _REST_OUTPUT_JSON: {
				$json = new Services_JSON();
				$output .= $json->encode($arr);
			} break;
			
			default: {
				$output .= '<error>Invalid type setting.</error>';
			}
		
		}
		
		return $output;
	}

	protected function getError($msg) {
		$output = '';
		
		$arr = array('error'=>$msg);
		switch ($this->outputType) {
			case _REST_OUTPUT_XML: {
				$output .= getXML($arr);
			} break;
			
			case _REST_OUTPUT_JSON: {
				$json = new Services_JSON();
				$output .= $json->encode($arr);
			} break;
			
			default: {
				$output .= '<error>Invalid type setting.</error>';
			}
		
		}
		
		return $output;
	}

	protected function invalidMethodCall($method='') {
		$temp='';
		if ($method!='') { $temp='"'.$method.'" '; }
		$this->getError('Invalid method '.$temp.'for module "'.$this->getModule().'".');
	}

}



//other functions

define('_REST_STANDARD_ERROR', 'Invalid input data.');

//error handling
function restAPI_HandleError($error=_REST_STANDARD_ERROR, $type=_REST_OUTPUT_XML) {
	$output = '';
	$temp = array('error'=>$error);
	switch ($type) {
		
		case _REST_OUTPUT_XML:  {	$output .= getXML($temp); } break;
		
		case _REST_OUTPUT_JSON: {
			$json = new Services_JSON();
			$output .= $json->encode($temp);
		} break;
		
		default: $output .= $error; break; //handler doesn't know how to format the output, so send raw string
	
	}
	return $output;
}


//debug information handling, it's used only in developement context
function restAPI_HandleDebugInfo($message, $type=_REST_OUTPUT_XML) {
	$f_msg = $message; //TO DO : format message if needed
	$output = '';
	$temp = array('debug'=>$f_msg);
	switch ($type) {
		
		case _REST_OUTPUT_XML: { $output .= getXML($temp); } break;
		
		case _REST_OUTPUT_JSON: {
			$json = new Services_JSON();
			$output .= $json->encode($temp);	
		} break;
		
		default: $output .= $message; break; //handler doesn't know how to format the output, so send raw string
	
	}
	return $output;
}


//retrieve an user id by token, if authenticated
function getUserIdByToken($token) {
	$output = false;
	$query = "SELECT * FROM ".$GLOBALS['prefix_fw']."_rest_authentication WHERE token='$token'";
	$res = mysql_query($query);
	if (mysql_num_rows($res)>0) {
		$row = mysql_fetch_assoc($res);
		$output = $row['id_user'];
	}
	return $output;
}


//******************************************************************************
//utils function
define('_XML_VERSION', '1.0');
define('_XML_ENCODING', 'UTF-8');
define('_GENERIC_ELEMENT', 'element');

function getXML($arr) {
	$output='';
	
	function _getopentag($tagkey) {
		$output  = '<';
		if (is_numeric($tagkey)) $output.=_GENERIC_ELEMENT; else $output.=$tagkey;
		$output .= '>';
		return $output;
	}
	
	function _getclosetag($tagkey) {
		$output  = '</';
		if (is_numeric($tagkey)) $output.=_GENERIC_ELEMENT; else $output.=$tagkey;
		$output .= '>';
		return $output;
	}
	
	function _getstringval(&$value) {
		$output='';
		if (is_bool($value)) {
			switch ($value) {
				case true:  $output.='true';  break;
				case false: $output.='false'; break;
			}
		} else {
			$output.=$value;
		}
		return $output;
	}
	
	function _convert(&$out, &$data) {
		if (!is_array($data)) return;
		foreach ($data as $key=>$val) {
			$out.=_getopentag($key);//'<'.$key.'>';
			if (is_array($val))
				_convert($out, $val);
			else
				$out.=_getstringval($val);
			$out.=_getclosetag($key);//'</'.$key.'>';
		}
	}

	if (is_array($arr)) {
		$output.='<?xml version="'._XML_VERSION.'" encoding="'._XML_ENCODING.'"?>';
		$output.='<XMLoutput>';
		_convert($output, $arr);
		$output.='</XMLoutput>';
	}
	return $output;
}
?>