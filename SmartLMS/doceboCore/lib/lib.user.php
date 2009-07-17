<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');


/**
 * Acl user class
 * This class is for manage user login, preferences, etc
 * It store acl's security tockens in user session
 * For a detailed check use DoceboACL
 * To manage ACLs we must use DoceboACLManager
 *
 * @package admin-core
 * @subpackage user
 * @version  $Id: lib.user.php 977 2007-02-23 10:40:19Z fabio $
 * @uses 	 UserPreference
 * @author   Emanuele Sandri <esandri@tiscali.it>
 */

require_once( dirname(__FILE__) .'/lib.acl.php' );

require_once($GLOBALS['where_framework'].'/lib/lib.preference.php');

define("REFRESH_LAST_ENTER", 600);	//refresh the user last action every specified seconds

define("USER_QUOTA_INHERIT", -1);
define("USER_QUOTA_UNLIMIT", 0);

define("_US_EMPTY", 0);
define("_NOT_DELETED", 0);

class DoceboUser {

	var $sprefix = '';
	var $acl = NULL;
	var $userid;
	var $idst;
	var $arrst = array();
	var $preference;
	
	var $user_level = false;

	/**
	 * create a DoceboACLUtil for given user
	 * and load all ST stored in session
	 **/
	function DoceboUser($userid, $sprefix) {
		$this->userid = $userid;
		$this->sprefix = $sprefix;

		$this->acl = new DoceboACL();
		$this->aclManager =& $this->acl->getACLManager();

		
		if( isset( $_SESSION[$sprefix.'_idst'] ) ) {
			$this->idst = $_SESSION[$sprefix.'_idst'];
		} else {
			$this->idst = $this->acl->getUserST( $userid );
		}
		if( isset( $_SESSION[$sprefix.'_stlist'] ) ) {
			
			require_once($GLOBALS['where_framework'].'/lib/lib.json.php');
			$json = new Services_JSON();
			$this->arrst = $json->decode($_SESSION[$sprefix.'_stlist']);
		}
		
		$this->preference = new UserPreferences($this->idst);
		
		$this->load_user_role();
		
		$aclManager =& $this->acl->getACLManager();
		$arr_levels_id = array_flip($aclManager->getAdminLevels());
		$arr_levels_idst = array_keys($arr_levels_id);

		$level_st = array_intersect($arr_levels_idst, $this->arrst);
		if(count($level_st) == 0) $this->user_level = false;
		$lvl = current($level_st);
		
		if(isset($arr_levels_id[$lvl])) $this->user_level = $arr_levels_id[$lvl];
		else $this->user_level = array_search(ADMIN_GROUP_USER, $arr_levels_id);
		
	}
	
	function load_user_role() {
		
		if(!empty($this->arrst)) {
			$temp = $this->aclManager->getRoleFromArraySt($this->arrst);
			$GLOBALS['user_roles'] = array_flip($temp);
		}
	}
	
	function SaveInSession() {
		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');
	    $json = new Services_JSON();
		
		$_SESSION[$this->sprefix.'_idst'] = $this->idst;
		$_SESSION[$this->sprefix.'_username'] = $this->userid;
		$_SESSION[$this->sprefix.'_stlist'] = $json->encode($this->arrst);
		$_SESSION[$this->sprefix.'_log_ip'] = $_SERVER['REMOTE_ADDR'];
	}

	function isAnonymous()	{ return (strcmp($this->userid,'/Anonymous') == 0); }
	function isLoggedIn() 	{ return (strcmp($this->userid,'/Anonymous') != 0); }
	function getLogIp() 	{ return $_SESSION[$this->sprefix.'_log_ip']; }
	function getIdSt()		{ return $this->idst; }
	function getArrSt()		{ return $this->arrst; }
	function getUserId()	{ return $this->userid; }

	/**
	 * static function for load user from session
	 * @param string $prefix optional prefix for session variables
	 * @return mixed DoceboUser instance of logged in user if found user in session
	 *				 FALSE otherwise
	 **/
	static function &createDoceboUserFromSession($prefix = 'base') {
		
		if(!isset($_SESSION['user_enter_time']))
					$_SESSION['user_enter_time'] = date('Y-m-d H:i:s');
		
		if( isset( $_SESSION[$prefix.'_username'] ) ) {
			$du = new DoceboUser( $_SESSION[$prefix.'_username'], $prefix );

			if(isset($_SESSION['user_enter_mark'])) {

				if($_SESSION['user_enter_mark'] < (time() - REFRESH_LAST_ENTER)) {
					$du->setLastEnter(date("Y-m-d H:i:s"));
					$_SESSION['user_enter_mark'] = time();
				}
			} else {
				$du->setLastEnter(date("Y-m-d H:i:s"));
				$_SESSION['user_enter_mark'] = time();
			}

			return $du;
		} else {
			
			// rest auth
			if (isset($GLOBALS['framework']['use_rest_api'])) {
				if ($GLOBALS['framework']['use_rest_api'] != 'off') {

					require_once($GLOBALS['where_framework'].'/API/class.rest.php');
					if(isset($GLOBALS['framework']['rest_auth_method']) && $GLOBALS['framework']['rest_auth_method'] == _REST_AUTH_TOKEN) {//'1') {

						require_once($GLOBALS['where_framework'].'/lib/lib.utils.php');
						$token = get_req('auth', DOTY_ALPHANUM, '');
						if($token) {

							$id_user = getUserIdByToken($token);
							if($id_user) {
								$user_manager = new DoceboACLManager();
								$user_info 	= $user_manager->getUser($id_user, false);
								$username 	= $user_info[ACL_INFO_USERID];
								if($user_info != false) {
									
									$du = new DoceboUser( $username, $prefix );

									$du->setLastEnter(date("Y-m-d H:i:s"));
									$_SESSION['user_enter_mark'] = time();
									$du->loadUserSectionST();
									$du->SaveInSession();
									return $du;
								}
							}
						}
					}
				}
			}

			// kerberos and similar auth
			if(isset($GLOBALS['framework']['auth_kerberos']) && $GLOBALS['framework']['auth_kerberos'] == 'on') {
				
				if(isset($_SERVER['REMOTE_USER'])) {
					// extract username
					$username = addslashes(substr($_SERVER['REMOTE_USER'], 0, strpos($_SERVER['REMOTE_USER'], '@')));
					$user_manager = new DoceboACLManager();
					$user_info = $user_manager->getUser(false, $username);
					if($user_info != false) {
						
						$du = new DoceboUser( $username, $prefix );
				
						$du->setLastEnter(date("Y-m-d H:i:s"));
						$_SESSION['user_enter_mark'] = time();
						$du->loadUserSectionST();
						$du->SaveInSession();
						return $du;
					}
				}
			}
			$du = new DoceboUser( '/Anonymous', $prefix );
			return $du;
		}
	}

	/**
	 * static function for load user from login e password
	 * @param string $login login of the user
	 * @param string $password password of the user in clear text
	 * @param string $prefix optional prefix for session variables
	 * @return mixed DoceboUser instance of logged in user if success in login
	 *				 FALSE otherwise
	 **/
	function &createDoceboUserFromLogin($login, $password, $prefix = 'base', $new_lang = false) {

		if($login == '') {
			$false_var = FALSE;
			return $false_var;
		}

		$user_manager = new DoceboACLManager();
		$user_info = $user_manager->getUser(false, $login);
		if($GLOBALS['framework']['pass_change_first_login'] == 'on')
			if ($user_info[ACL_INFO_LASTENTER] == '0000-00-00 00:00:00' || $user_info[ACL_INFO_LASTENTER] == NULL)
				$_SESSION['user_prev_last_enter'] = 'first_login';

		$ret_value = false;
		if( $user_info === false )
			return $ret_value;

		if( $user_info[ACL_INFO_VALID] != '1' )
			return $ret_value;

		if($GLOBALS['framework']['ldap_used'] == 'on') {

			if($password == '') {
				$false_var = FALSE;
				return $false_var;
			}
			if($GLOBALS['framework']['ldap_port'] == '')
				$GLOBALS['framework']['ldap_port'] = '389';

			//connect to ldap server
			if( !($ldap_conn = @ldap_connect( $GLOBALS['framework']['ldap_server'], $GLOBALS['framework']['ldap_port'] )) ) {
				die("Could not connect to ldap server");
			}

			//bind on server
			$ldap_user = ereg_replace( '\$user', $login, $GLOBALS['framework']['ldap_user_string'] );
			if (!(@ldap_bind($ldap_conn, $ldap_user, $password))) {
				ldap_close($ldap_conn);

				// Edited by Claudio Redaelli
				if ($GLOBALS['framework']['ldap_alternate_check'] == 'on') {
					if($user_info[ACL_INFO_PASS] != $user_manager->encrypt($password))
						return $ret_value;
				} else {
					$false_var = FALSE;
					return $false_var;
				}
				// End edit
			}
			ldap_close($ldap_conn);

		} elseif($user_info[ACL_INFO_PASS] != $user_manager->encrypt($password)) {

			return $ret_value;
		}
		unset($_SESSION[$prefix."_idst"]);
		$du = new DoceboUser( $login, $prefix );

		$du->setLastEnter(date("Y-m-d H:i:s"));
		$_SESSION['user_enter_mark'] = time();

		if($new_lang != false) {

			$du->preference->setLanguage($new_lang);
			$_SESSION['custom_lang'] = $new_lang;
		} elseif($new_lang != false || isset($_SESSION['changed_lang'])) {

			unset($_SESSION['changed_lang']);
			$du->preference->setLanguage($_SESSION['custom_lang']);
		} else {
			$_SESSION['custom_lang'] = $du->preference->getLanguage();
		}

		if(function_exists('session_regenerate_id')) session_regenerate_id();
		return $du;
	}

	function setLastEnter($lastenter) {

		if (!$this->isAnonymous()) {
			return $this->aclManager->updateUser($this->idst,
				FALSE,  FALSE,  FALSE, FALSE,  FALSE,  FALSE,  FALSE, FALSE,
				$lastenter );
		}
		else {
			return TRUE;
		}
	}

	/**
	 * This method load all security tokens associated to a section (course),
	 * test the match with user and save in user session positive ST
	 * @param string $section the section to load
	 **/
	function loadUserSectionST($section) {
		$this->arrst = $this->acl->getUserAllST($this->userid);
	}

	/**
	 * @return 0 if the user password is not elapsed, 1 if the password is elapsed
	 *           and 2 if the user did his first login.
	 */
	function isPasswordElapsed() {
		$res =0;

		if($GLOBALS['framework']['ldap_used'] == 'on') return $res;

		$user_data = $this->aclManager->getUser($this->idst, false);

		if (isset($_SESSION['user_prev_last_enter']) && $_SESSION['user_prev_last_enter'] == 'first_login')
		{
			$res = 2;
			return $res;
		}

		if($GLOBALS['framework']['pass_max_time_valid'] == '0') return $res;

		if($user_data[ACL_INFO_PWD_EXPIRE_AT] == '0000-00-00 00:00:00')
			return $res;

		$pwd_expire = fromDatetimeToTimestamp($user_data[ACL_INFO_PWD_EXPIRE_AT]);
		if(time() > $pwd_expire)
				$res =1;
		return $res;
	}


	/**
	 * This method load all security tokens associated to a section (course),
	 * test the match with user and save in user session positive ST
	 * @param string $section the section to load
	 **/
	function saveUserSectionSTInSession($section) {
		$sprefix=$this->sprefix;

		if (!isset($_SESSION[$sprefix."_stlist"])) {
			$this->loadUserSectionST($section);
			$this->SaveInSession();
		}
	}

	/**
	 * This method test if in user's loaded ST ther'is a given ST
	 * @param int $st the security token to test
	 * @return bool TRUE, FALSE
	 **/
	function matchUserST($st) {
		return in_array( $st, $this->arrst );
	}

	/**
	 * This method test if user has a role
	 * @param string $roleid the role to test
	 * @return bool TRUE, FALSE
	 **/
	function matchUserRole($roleid) {
		
		if(!isset($GLOBALS['user_roles'])) $this->load_user_role();

		return isset($GLOBALS['user_roles'][$roleid]);
	}

	/**
	 * This method test if user has one of given roles
	 * @param array $roles the array of roles to test
	 * @return bool TRUE, FALSE
	 **/
	function matchUserRoles($roles) {
		
		if(!isset($GLOBALS['user_roles'])) $this->load_user_role();
		foreach( $roles as $r ) {
		
			if(isset($GLOBALS['user_roles'][$r])) return true;
		}
		return FALSE;
	}

	/**
	 * This method test if user has all passed roles
	 * @param array $roles the array of roles to test
	 * @return bool TRUE, FALSE
	 **/
	function matchUserAllRoles($roles) {
		
		if(!isset($GLOBALS['user_roles'])) $this->load_user_role();
		foreach( $roles as $r ) {
		
			if(!isset($GLOBALS['user_roles'][$r])) return false;
		}
		return TRUE;
	}

	/**
	 * Get refernce to DoceboACL
	 * @return DoceboACL the DoceboACL object
	 **/
	function &getACL() {
		return $this->acl;
	}

	/**
	 * Get refernce to DoceboACLManager
	 * @return DoceboACLManager the DoceboACLManager object
	 **/
	function &getACLManager() {
		return $this->acl->getACLManager();
	}

	function getUserLevelId() {
 		
 		return $this->user_level;
	}

	function getUserName() {

		$user_info = $this->aclManager->getUser(getLogUserId(), false);
		return ( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
			? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
			: $this->aclManager->relativeId($user_info[ACL_INFO_USERID]) );
	}

	function getQuotaLimit() {

		$user_quota = $this->preference->getPreference('user_rules.user_quota');
		if($user_quota == USER_QUOTA_INHERIT) $user_quota = $GLOBALS['framework']['user_quota'];
		return $user_quota;
	}

	function getUsedQuota() {

		$user_quota = $this->preference->getPreference('user_rules.user_quota_used');
		return $user_quota;
	}

	/**
	 * This function return the myfile table
	 */
	function getMyFilesTable()
	{
		return $GLOBALS['prefix_fw'].'_user_myfiles';
	}

	/**
	 * This function return the setting user table
	 */
	function getSettingUserTable()
	{
		return $GLOBALS['prefix_fw'].'_setting_user';
	}

	/**
	 * This function update the used space of an user
	 * @$id_user --> The idst of the user to update
	 */
	function updateUserUsedSpace($id_user)
	{
		$used_space = _US_EMPTY;

		$query = "SELECT SUM(size)
			FROM ".$this->getMyFilesTable()."
			WHERE owner = '".$id_user."'";

		$myfile_size = mysql_fetch_row(mysql_query($query));

		if ($myfile_size[0])
			$used_space = $myfile_size[0];

		$control_query = "SELECT *" .
				" FROM ".$this->getSettingUserTable()."" .
				" WHERE id_user = '".$id_user."'" .
				" AND path_name = 'user_rules.user_quota_used'";

		$result = mysql_fetch_row(mysql_query($control_query));

		if ($result[0])
		{
			$update_query = "UPDATE ".$this->getSettingUserTable()."" .
					" SET value = '".$used_space."'" .
					" WHERE id_user = '".$id_user."'" .
					" AND path_name = 'user_rules.user_quota_used'";

			if ($result = mysql_query($update_query))
				return true;
			return false;
		}
		else
		{
			$insert_query = "INSERT INTO ".$this->getSettingUserTable()."" .
					" (path_name, id_user, value)" .
					" VALUES ('user_rules.user_quota_used', '".$id_user."', '".$used_space."')";

			if ($result = mysql_query($insert_query))
				return true;
			return false;
		}
	}
}


function getLogUserId() {

	return $GLOBALS['current_user']->getIdSt();
}

?>
