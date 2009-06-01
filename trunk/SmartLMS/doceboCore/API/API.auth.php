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


//manage token authentication
class RestAPI_Auth extends RestAPI {


	public function __construct() {
		//do not request auth code or won't log user at beginning
		$this->needAuthentication=false;
	}
	
	
	public function dispatch($method, $function, &$params) {
	
		switch ($method) {
			
			//GET requests are used for data retrieving
			case 'GET': {
				switch ($function) {
					case 'getauthmethod': {
						$this->getAuthenticationMethod();
					} break;
					
					default: {
						$this->invalidMethodCall($function);
					} break;
				}
				
			} break;
			
			
			//POST requests are used for data inserting
			case 'POST': {
				switch ($function) {
					case 'authenticate': {
						if ($GLOBALS['framework']['rest_auth_method']!=_REST_AUTH_TOKEN) {
							$output = array('success'=>false, 'message'=>'Tokens are not used on this installation.');
							$this->write( $this->convertArray($output) );
							break;
						}
						$username = get_req('username', DOTY_STRING, false);
						$password = get_req('password', DOTY_STRING, false);
			
						if ($username===false || $password===false) {
							//error: no login data provided
							$output = array('success'=>false, 'message'=>'Error: invalid login data provided.');
							$this->write( $this->convertArray($output) );
						} else {
							if ( $res = $this->generateToken($username, $password) ) {
								$output = array(
									'success'=>true,
									'message'=>'You are authenticated.',
									'token'=>$res['token'],
									'expire_at'=>$res['expire_at']
								);
							} else {
								$output = array('success'=>false, 'message'=>'Error: invalid user.');
							}
							$this->write( $this->convertArray($output) );
						}
					} break;
				
					default: {
						$this->invalidMethodCall($function);
					} break;
				}				
			} break;
			
			
			//PUT requests are used for data updating
			case 'PUT': /*{
				$output = array('success'=>false, 'message'=>'Error: invalid request type.');
				$this->write( $this->convertArray($output) );
			} break;*/
			
			
			//DELETE requests are used for data removing
			case 'DELETE': {
				$output = array('success'=>false, 'message'=>'Error: invalid request type.');
				$this->write( $this->convertArray($output) );
			} break;
			
			default: {
				//...
			}
		}

	}
	
	
	//internal methods
	
	//log user and generate a token
	function generateToken($username, $password) {
		require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
		$acl_man = new DoceboACLManager();
		$query="SELECT * FROM ".$GLOBALS['prefix_fw']."_user WHERE userid='".$acl_man->absoluteId($username)."' AND pass='".$acl_man->encrypt($password)."'";
		$res = mysql_query($query);
		$result = false;
		if (mysql_num_rows($res)>0) {
			$row = mysql_fetch_assoc($res);
			
			$level = $acl_man->getUserLevelId($row['idst']);
			$token = md5(uniqid(rand(), true) + $username);
			$timenow=time();
			$now = date("Y-m-d H:i:s", $timenow);
			$lifetime = $GLOBALS['framework']['rest_auth_lifetime']*60;
			$expire = date("Y-m-d H:i:s", $timenow + $lifetime) ;
			
			//check if the user is already authenticate
			$query="SELECT * FROM ".$GLOBALS['prefix_fw']."_rest_authentication WHERE id_user=".$row['idst'];
			$res = mysql_query($query);
			if (mysql_num_rows($res)>0) { //if so, than re-authenticate it
				//update log table
				$query = "UPDATE ".$GLOBALS['prefix_fw']."_rest_authentication ".
					" SET token='$token', generation_date='".$now."', last_enter_date=NULL, expiry_date='".$expire."' ".
					" WHERE id_user=".$row['idst'];
				$res = mysql_query($query);
			} else {
				//set authentication in DB
				$query = "INSERT INTO ".$GLOBALS['prefix_fw']."_rest_authentication ".
					"(id_user,user_level, token, generation_date, last_enter_date, expiry_date) VALUES ".
					"('".$row['idst']."', '".$level."', '".$token."', '".$now."', NULL, '".$expire."')";
				$res = mysql_query($query);
				//TO DO : insert also in auth log table 
				//... 
			}
			//TO DO: handle error: if (!res) { .... }
			$result = array('token'=>$token, 'expire_at'=>$expire);
		}
		return $result;
	}

}


?>