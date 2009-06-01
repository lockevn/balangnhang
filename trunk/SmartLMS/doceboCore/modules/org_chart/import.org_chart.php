<?php
/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2005 by Emanuele Sandri (esandri@tiscali.it)            */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @package		Docebo
 * @subpackage	ImportExport
 * @version 	$Id: import.org_chart.php 977 2007-02-23 10:40:19Z fabio $
 * @author		Emanuele Sandri <emanuele (@) docebo (.) com>
**/
require_once($GLOBALS['where_framework'].'/lib/lib.import.php');
class ImportUser extends DoceboImport_Destination {

	var $last_error = NULL;
	var $mandatory_cols = array('userid');
	var $default_cols = array(	'firstname'=>'','lastname'=>'','pass'=>'',
								'email'=>'','photo'=>'','avatar'=>'',
								'signature'=>'');
	var $ignore_cols = array( 'idst','photo', 'avatar', 'lastenter', 'valid', 'pwd_expire_at', 'level', 'register_date' );
	var $valid_filed_type = array( 'textfield','date','dropdown','yesno', 'freetext','country',
									'gmail', 'icq', 'msn', 'skype', 'yahoo');
	var $cols_descriptor = NULL;
	var $dbconn = NULL;
	var $tree = 0;
	var $charset = '';

	/**
	 * constructor for docebo users destination connection
	 * @param array $params
	 *			- 'dbconn' => connection to database (required)
	 *			- 'tree' => The id of the destination folder on tree (required)
	**/
	function ImportUser( $params ) {
		$this->dbconn = $params['dbconn'];
		$this->tree =& $params['tree'];
	}

	function connect() {
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		// load language for fields names
		$lang_dir =& DoceboLanguage::createInstance('admin_directory', 'framework');
		$acl =& $GLOBALS['current_user']->getACL();
		$fl = new FieldList();
		$idst_group = $this->tree->tdb->getGroupST($this->tree->getSelectedFolderId());
		$idst_desc = $this->tree->tdb->getGroupDescendantsST($this->tree->getSelectedFolderId());
		$arr_idst_all = $acl->getArrSTGroupsST(array($idst_group,$idst_desc));
		$arr_fields = $fl->getFieldsFromIdst($arr_idst_all);

		$this->cols_descriptor = NULL;
		if( $this->dbconn === NULL ) {
			$this->last_error = $this->tree->lang->def('_ORG_IMPORT_ERR_DBCONNISNULL');
			return FALSE;
		}
		$query = "SHOW FIELDS FROM ".$GLOBALS['prefix_fw']."_user";
		$rs = mysql_query( $query, $this->dbconn );
		if( $rs === FALSE ) {
			$this->last_error = $this->tree->lang->def('_ORG_IMPORT_ERR_ERRORONQUERY').$query.' ['.mysql_error($this->dbconn).']';
			return FALSE;
		}
		$this->cols_descriptor = array();
		while( $field_info = mysql_fetch_array($rs) ) {
			if( !in_array($field_info['Field'],$this->ignore_cols) ) {
				$mandatory = in_array($field_info['Field'],$this->mandatory_cols);
				if( isset($this->default_cols[$field_info['Field']])) {
					$this->cols_descriptor[] =
								array(  DOCEBOIMPORT_COLNAME => $lang_dir->def('_DIRECTORY_FILTER_'.$field_info['Field']),
										DOCEBOIMPORT_COLID => $field_info['Field'],
										DOCEBOIMPORT_COLMANDATORY => $mandatory,
										DOCEBOIMPORT_DATATYPE => $field_info['Type'],
										DOCEBOIMPORT_DEFAULT => $this->default_cols[$field_info['Field']]
										);
				} else {
					$this->cols_descriptor[] =
								array(  DOCEBOIMPORT_COLNAME => $lang_dir->def('_DIRECTORY_FILTER_'.$field_info['Field']),
										DOCEBOIMPORT_COLID => $field_info['Field'],
										DOCEBOIMPORT_COLMANDATORY => $mandatory,
										DOCEBOIMPORT_DATATYPE => $field_info['Type']
										);
				}
			}
		}

		mysql_free_result( $rs );

		foreach($arr_fields as $field_id => $field_info) {
			if( in_array($field_info[FIELD_INFO_TYPE],$this->valid_filed_type) ) {
				$this->cols_descriptor[] =
							array(  DOCEBOIMPORT_COLNAME => $field_info[FIELD_INFO_TRANSLATION],
									DOCEBOIMPORT_COLID => $field_id,
									DOCEBOIMPORT_COLMANDATORY => FALSE,
									DOCEBOIMPORT_DATATYPE => 'text',
									);
			}
		}
		return TRUE;

	}

	function close() {}

	function get_tot_cols(){
		return count( $this->cols_descriptor );
	}

	function get_cols_descripor() {
		return $this->cols_descriptor;
	}

	/**
	 * @return integer the number of mandatory columns to import
	**/
	function get_tot_mandatory_cols() {
		$result = array();
		foreach( $this->cols_descriptor as $col ) {
			if( $col[DOCEBOIMPORT_COLMANDATORY] )
				$result[] = $col;
		}
		return count($result);
	}

	function _convert_char( $text ) {
		if( function_exists('mb_convert_encoding') ) {
			return mb_convert_encoding($text, 'UTF-8', $this->charset);
		} else {
			return utf8_encode($text);
		}
	}

	/**
	 * @param array data to insert; is an array with keys the names of cols and
	 *				values the data
	 * @return TRUE if the row was succesfully inserted, FALSE otherwise
	**/
	function add_row( $row ) {
		$acl =& $GLOBALS['current_user']->getACL();
		$idst_group = $this->tree->tdb->getGroupST($this->tree->getSelectedFolderId());
		$idst_desc = $this->tree->tdb->getGroupDescendantsST($this->tree->getSelectedFolderId());
		$userid = addslashes($this->_convert_char($row['userid']));
		
		$firstname = addslashes($this->_convert_char($row['firstname']));
		$lastname = addslashes($this->_convert_char($row['lastname'])); 

		$pass = addslashes($this->_convert_char($row['pass']));
		$email = addslashes($this->_convert_char($row['email']));
		$idst = $this->tree->aclManager->registerUser( 	$userid, $firstname, $lastname,
														$pass, $email, '', '',
														'');
		if($idst !== false) {

			// |- Sending alert ----------------------------------------------------
			//  - 07 mar 2006 - Giovanni Derks' edit/quick fix ---------------------
			require_once($GLOBALS['where_framework'] . "/lib/lib.eventmanager.php");
			$pl_man =& PlatformManager::createInstance();

			$array_subst = array(	'[url]' => $GLOBALS[$pl_man->getHomePlatform()]['url'],
									'[userid]' => $userid,
									'[password]' => $pass );
			// message to user that is inserted
			$msg_composer = new EventMessageComposer('admin_directory', 'framework');

			$msg_composer->setSubjectLangText('email', '_REGISTERED_USER_SBJ', false);
			$msg_composer->setBodyLangText('email', '_REGISTERED_USER_TEXT', $array_subst);

			$msg_composer->setSubjectLangText('sms', '_REGISTERED_USER_SBJ_SMS', false);
			$msg_composer->setBodyLangText('sms', '_REGISTERED_USER_TEXT_SMS', $array_subst);

			createNewAlert(	'UserNew', 'directory', 'edit', '1', 'User '.$userid.' created',
						array($userid), $msg_composer  );

			//  -- Add user to registered users group if not importing into root ---

			$idst_oc 			= $this->tree->aclManager->getGroup(false, '/oc_0');
			$idst_oc 			= $idst_oc[ACL_INFO_IDST];

			$idst_ocd 			= $this->tree->aclManager->getGroup(false, '/ocd_0');
			$idst_ocd 			= $idst_ocd[ACL_INFO_IDST];

			if ($idst_group != $idst_oc)
				$this->tree->aclManager->addToGroup($idst_oc, $idst);

			if ($idst_desc != $idst_ocd)
				$this->tree->aclManager->addToGroup($idst_ocd, $idst);

			//  -------------------------------------------------------------------|

			$result = TRUE;
			$this->tree->aclManager->addToGroup($idst_group,$idst );
			$this->tree->aclManager->addToGroup($idst_desc,$idst );

			// add to group level
			$userlevel = $this->tree->aclManager->getGroupST(ADMIN_GROUP_USER);
			$this->tree->aclManager->addToGroup($userlevel,$idst );

			//-save extra field------------------------------------------
			require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
			$fl = new FieldList();
			$arr_idst_all = $acl->getArrSTGroupsST(array($idst_group,$idst_desc));
			$arr_fields = $fl->getFieldsFromIdst($arr_idst_all);
			$arr_fields_toset = array();
			foreach( $arr_fields as $field_id => $field_info) {
				if( isset($row[$field_id]) ) {
					$arr_fields_toset[$field_id] = $this->_convert_char($row[$field_id]);
				}
			}
			if( count($arr_fields_toset) > 0 )
				$result = $fl->storeDirectFieldsForUser($idst, $arr_fields_toset);
			//-----------------------------------------------------------
			if( !$result ) {
				$this->last_error = $this->tree->lang->def('_ORG_IMPORT_ERR_STORECUSTOMFIELDS');
			}
			return $result;
		} else {
			$this->last_error = $this->tree->lang->def('_ORG_IMPORT_ERR_REGUSER');
			return FALSE;
		}
	}

	function set_charset( $charset ) { $this->charset = $charset; }


	function get_error() {
		return $this->last_error;
	}
}

class ImportGroupUser extends DoceboImport_Destination {

	var $last_error = NULL;
	var $cols_id			= array('userid', 'groupid');
	var $cols_default		= array();
	var $cols_mandatory		= array('userid', 'groupid');
	var $cols_type			= array('userid' => 'text', 'groupid' => 'text');
	var $cols_descriptor 	= array();
	var $dbconn = NULL;
	var $charset = '';
	
	var $acl_man;
	
	var $group_cache = array();
	var $user_cache = array();

	/**
	 * constructor for docebo users destination connection
	 * @param array $params
	 *			- 'dbconn' => connection to database (required)
	 *			- 'tree' => The id of the destination folder on tree (required)
	**/
	function ImportGroupUser( $params ) {
		$this->dbconn = $params['dbconn'];
		$this->acl_man 	=& $GLOBALS['current_user']->getAclManager();
	}

	function connect() {
		
		$this->cols_descriptor = array();
		foreach($this->cols_id as $k => $field_id) {

			$mandatory = in_array($field_id, $this->cols_mandatory);
				
			if( in_array($field_id, $this->cols_default)) {
				
				$this->cols_descriptor[] = array(  
					DOCEBOIMPORT_COLNAME 		=> def('_GROUPUSER_'.$field_id, 'organization_chart', 'framework'),
					DOCEBOIMPORT_COLID 			=> $field_id,
					DOCEBOIMPORT_COLMANDATORY 	=> in_array($field_id, $this->cols_mandatory),
					DOCEBOIMPORT_DATATYPE 		=> $this->cols_type[$field_id],
					DOCEBOIMPORT_DEFAULT => $this->default_cols[$field_id]
				);
			} else {
				
				$this->cols_descriptor[] = array(  
					DOCEBOIMPORT_COLNAME 		=> def('_GROUPUSER_'.$field_id, 'organization_chart', 'framework'),
					DOCEBOIMPORT_COLID 			=> $field_id,
					DOCEBOIMPORT_COLMANDATORY 	=> in_array($field_id, $this->cols_mandatory),
					DOCEBOIMPORT_DATATYPE 		=> $this->cols_type[$field_id]
				);
			}
		}
		return TRUE;
	}

	function close() {}

	function get_tot_cols(){
		return count( $this->cols_descriptor );
	}

	function get_cols_descripor() {
		return $this->cols_descriptor;
	}

	/**
	 * @return integer the number of mandatory columns to import
	**/
	function get_tot_mandatory_cols() {
		
		return count( $this->cols_mandatory );
	}

	function _convert_char( $text ) {
		if( function_exists('mb_convert_encoding') ) {
			return mb_convert_encoding($text, 'UTF-8', $this->charset);
		} else {
			return utf8_encode($text);
		}
	}

	/**
	 * @param array data to insert; is an array with keys the names of cols and
	 *				values the data
	 * @return TRUE if the row was succesfully inserted, FALSE otherwise
	**/
	function add_row( $row ) {
		
		chkInput($row);
		// find the group idst
		$group_idst = array_search($row['groupid'], $this->group_cache);
		if($group_idst === NULL || $group_idst === false) {
			
			$group = $this->acl_man->getGroup(false, $row['groupid']);
			$this->group_cache[$group[ACL_INFO_IDST]] = $row['groupid'];
			$group_idst = $group[ACL_INFO_IDST];
		}
		if($group_idst == false) {
			// the group doesn't exist
			$this->last_error = def('_GROUP_IMPORT_ERR_GROUP_DOESNT_EXIST', 'org_chart', 'framework');
			return false;
		}
		// find the user idst
		$user_idst = array_search($row['userid'], $this->user_cache);
		if($user_idst === NULL || $user_idst === false) {
			
			$user = $this->acl_man->getUser(false, $row['userid']);
			$this->user_cache[$user[ACL_INFO_IDST]] = $row['userid'];
			$user_idst = $user[ACL_INFO_IDST];
		}
		if($user_idst == false) {
			// the user doesn't exist
			$this->last_error = def('_GROUP_IMPORT_ERR_USER_DOESNT_EXIST', 'org_chart', 'framework');
			return false;
		}
		
		$result = $this->acl_man->addToGroup( $group_idst, $user_idst );
		if( !$result ) {
			$this->last_error = def('_GROUP_IMPORT_ERR_SUBSCRIPTION', 'org_chart', 'framework');
		}
		return true;
	}

	function set_charset( $charset ) { $this->charset = $charset; }


	function get_error() {
		return $this->last_error;
	}
}

?>