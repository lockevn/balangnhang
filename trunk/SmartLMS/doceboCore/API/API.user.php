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

require_once($GLOBALS['where_framework'].'/API/class.rest.php');

require_once($GLOBALS['where_framework'].'/lib/lib.acl.php');
require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');

class RestAPI_User extends RestAPI {

	public function __construct() {
		//...
	}

	public function dispatch($method, $function, &$params) {
	
		switch ($method) {
			
			//GET requests are used for data retrieving
			case 'GET': {
				switch ($function) {
				
					case 'userslist': {
						$this->getUsersList();
					} break;
					
					case 'userdetails': {
						if (count($params)>0) { //params[0] should contain user id
							if (is_numeric($params[0])) {
								$this->getUserDetails($params[0]);
							} else {
								$this->getError('Invalid passed parameter.');
							}
						} else {
							$this->getError('No parameter provided.');
						}
					} break;
					
					case 'customfields':{
						$tmp_lang = false; //if not specified, use default language
						if (isset($params[0])) { $tmp_lang = $params[0]; } //check if a language has been specified
						$this->getCustomFields($tmp_lang); 
					} break;
					
					default: {
						$this->invalidMethodCall($function);
					} break;
									
				}
			} break;
			
			
			//POST requests are used for data inserting
			case 'POST': {
				switch ($function) {
					case 'createuser': {
						//create a new user using postdata parameters
						$idst=false;
						if (count($params)>0 && is_numeric($params[0])) { $idst=$params[0]; }
						$this->createUser($_POST, $idst);
					} break;
					
					default: {
						$this->invalidMethodCall($function);
					} break;
				}				
			} break;
			
			
			//PUT requests are used for data updating
			case 'PUT': {
				switch ($function) {
					case 'updateuser': {
						if (count($params)>0) { //params[0] should contain user id
							$this->updateUser($params[0], $_POST);
						} elseif (isset($_POST['idst'])) {
							$this->updateUser($_POST['idst'], $_POST);
						} else {
							$error=array('success'=>false, 'message'=>'Error: user id to update has not been specified.');
							$this->write( $this->convertArray($error) );
						}
					} break;
					
					default: {
						$this->invalidMethodCall($function);
					} break;
				}				
			} break;
			
			
			//DELETE requests are used for data removing
			case 'DELETE': {			
				switch ($function) {
					case 'deleteuser': {
						if (count($params)>0) { //params[0] should contain user id
							$this->deleteUser($params[0]);
						} elseif (isset($_POST['idst'])) {
							$this->deleteUser($_POST['idst']);
						} else {
							$error=array('success'=>false, 'message'=>'Error: user id to delete has not been specified.');
							$this->write( $this->convertArray($error) );
						}
					} break;
					
					default: {
						$this->invalidMethodCall($function);
					} break;
				}
			} break;
			
			default: {
				//...
			}
		}

	}

	//internal data management functions
	private function getCustomFields($lang_code=false) {
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		$output=array();
		$fl = new FieldList();
		$fields = $fl->getFlatAllFields(false, false, $lang_code);
		foreach ($fields as $key=>$val) {
			$output[]=array('id'=>$key, 'name'=>$val);
		}
		$this->write( $this->convertArray($output) );
	}

	private function getUserDetails($id_user) {
		$acl_man = new DoceboACLManager();
		$user_data = $acl_man->getUser($id_user, false);
		
		$user_details=array();
		if (!$user_data) {
			$user_details['error'] = 'Invalid user ID: '.$id_user.'.';
		} else {
		
			$user_details = array(
				'idst' => $user_data[ACL_INFO_IDST],
				'userid' => $acl_man->relativeId($user_data[ACL_INFO_USERID]),
				'firstname' => $user_data[ACL_INFO_FIRSTNAME],
				'lastname' => $user_data[ACL_INFO_LASTNAME],
				//'password' => $user_data[ACL_INFO_PASS],
				'email' => $user_data[ACL_INFO_EMAIL],
				//'photo' => $user_data[ACL_INFO_PHOTO],
				//'avatar' => $user_data[ACL_INFO_AVATAR],
				'signature' => $user_data[ACL_INFO_SIGNATURE],
				'valid' => $user_data[ACL_INFO_VALID],
				'pwd_expire_at' => $user_data[ACL_INFO_PWD_EXPIRE_AT],
				'register_date' => $user_data[ACL_INFO_REGISTER_DATE],
				'last_enter' => $user_data[ACL_INFO_LASTENTER]
			);
		
			require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
			$field_man = new FieldList();
			$field_data = $field_man->getFieldsAndValueFromUser($id_user, false, true);

			$fields=array();
			foreach($field_data as $field_id => $value) {
				$fields[] = array('name'=>$value[0], 'value'=>$value[1]);
			}

			//$profile = array_merge($user_details, $fields);
			$user_details['custom_fields'] = $fields;
		}
		
		$this->write( //'TYPE: '.$this->getOutputType().'<br />'.
			$this->convertArray($user_details)
		);
	}
	
	private function getUsersList() {
		$acl_man = new DoceboACLManager();
		
		$output = array();
		$query = "SELECT idst, userid, firstname, lastname FROM ".$GLOBALS['prefix_fw']."_user ORDER BY userid";
		$res = mysql_query($query);
		while ($row=mysql_fetch_assoc($res)) {
			$output[]=array(
				'userid'=>$acl_man->relativeId($row['userid']),
				'idst'=>$row['idst'],
				'firstname'=>$row['firstname'],
				'lastname'=>$row['lastname']
			);
		}
		$this->write( //'TYPE: '.$this->getOutputType().'<br />'.
			$this->convertArray($output)
		);
	}

	private function createUser(&$postdata, $set_idst=false) {
		$acl_man = new DoceboACLManager();
		$output = array();
		//check postdata validity
		$is_valid = true;
		if (!isset($postdata['userid'])) {
			$is_valid=false;
			$output=array('success'=>false, 'message'=>'Error: you must specify an userId for the new user.');
		}		
		if ($is_valid) { 
			$temp = $acl_man->registerUser(
				$postdata['userid'],
				(isset($postdata['firstname']) ? $postdata['firstname'] : '' ),
				(isset($postdata['lastname']) ? $postdata['lastname'] : ''),
				(isset($postdata['password']) ? $postdata['password'] : ''),
				(isset($postdata['email']) ? $postdata['email'] : ''),
				'',//$photo,
				'',//$avatar,
				(isset($postdata['signature']) ? $postdata['signature'] : ''),
				false,//$alredy_encripted = false,
				$set_idst,//$idst = false,
				(isset($postdata['pwd_expire_at']) ? $postdata['pwd_expire_at'] : '')//$pwd_expire_at = '' );
			);
			
			//check if some additional fields have been set
			$okcustom = true;
			if (isset($postdata['_customfields']) && $temp) {
				require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
				/*$fields=array();
				foreach ($postdata['_customfields'] as $key=>$val) {
					$fields[$key]=>$val;
				}*/ $fields =& $postdata['_customfields'];
				if (count($fields)>0) {
					$fl = new FieldList();
					$okcustom = $fl->storeDirectFieldsForUser($temp, $fields);
				}
			}
			
			if ($temp) {
				$custom_msg = '';
				if (!$okcustom) $custom_msg=' Warning: unable to set custom fields.';
				$output = array('success'=>true, 'message'=>'User has been created with id #'.$temp.'.'.$custom_msg);
			} else {
				$output = array('success'=>false, 'message'=>'Error: unable to create user.');
			}
		}
		$this->write( $this->convertArray($output) );
	}

	private function updateUser($id_user, &$postdata) {
		$acl_man = new DoceboACLManager();
		$output = array();
		$temp = $acl_man->updateUser(
			$id_user, 
			(isset($postdata['userid']) ? $postdata['userid'] :  false),
			(isset($postdata['firstname']) ? $postdata['firstname'] :  false),//$firstname = FALSE, 
			(isset($postdata['lastname']) ? $postdata['lastname'] :  false),//$lastname = FALSE,
			(isset($postdata['password']) ? $postdata['password'] :  false),//$pass = FALSE, 
			(isset($postdata['email']) ? $postdata['email'] :  false),//$email = FALSE, 
			false,//(isset($postdata['photo']) ? $postdata['photo'] :  false),//$photo = FALSE, 
			false,//(isset($postdata['avatar']) ? $postdata['avatar'] :  false),//$avatar = FALSE,
			(isset($postdata['signature']) ? $postdata['signature'] :  false),//$signature = FALSE, 
			(isset($postdata['lastenter']) ? $postdata['lastenter'] :  false),//$lastenter = FALSE, 
			(isset($postdata['valid']) ? $postdata['valid'] :  false)//$resume = FALSE
		);
		
		//additional fields
		$okcustom = true;
		if (isset($postdata['_customfields']) && $temp) {
			require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
				/*$fields=array();
				foreach ($postdata['_customfields'] as $key=>$val) {
					$fields[$key]=>$val;
				}*/ $fields =& $postdata['_customfields'];
			if (count($fields)>0) {
				$fl = new FieldList();
				$okcustom = $fl->storeDirectFieldsForUser($id_user, $fields);
			}
		}
		
		//$arr_fields_toset[$field_id] = $valore del campo
		
		$result = $fl->storeDirectFieldsForUser($idst, $arr_fields_toset);
		
		if ($temp) {
			$custom_msg = '';
			if (!$okcustom) $custom_msg=' Warning: unable to update custom fields.';
			$output = array('success'=>true, 'message'=>'User #'.$id_user.' has been updated.');
		} else {
			$output = array('success'=>false, 'message'=>'Error: unable to update user #'.$id_user.'.');
		}
		$this->write( $this->convertArray($output) );
	}

	private function deleteUser($id_user) {
		$acl_man = new DoceboACLManager();
		$output = array();
		if ($acl_man->deleteUser($id_user)) {
			$output = array('success'=>true, 'message'=>'User #'.$id_user.' has been deleted.');
		} else {
			$output = array('success'=>false, 'message'=>'Error: unable to delete user #'.$id_user.'.');
		}
		$this->write( $this->convertArray($output) );
	}

}

?>