<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ==============================================						*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package  admin-library
 * @subpackage user
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id:$
 */

/**
 * this class is minded as an abstract level for manage users preferences
 * 
 * @author	Fabio Pirovano <fabio (@) docebo (.) com>
 */
class UserPreferencesDb {
	
	var $_db_conn;
	
	/**
	 * @return string the name of the table with all the possible preference
	 * @access private
	 */
	function _getTablePreference() {
		
		return $GLOBALS['prefix_fw'].'_setting_list';
	}
	
	/**
	 * @return string the name of the table with the users preferences
	 * @access private
	 */
	function _getTableUser() {
		
		return $GLOBALS['prefix_fw'].'_setting_user';
	}
	
	function _executeQuery($query) {
		
		if($this->_db_conn == false) $result = mysql_query( $query );
		else $result = mysql_query( $query, $this->_db_conn );
		
		if($GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page'])) {
			$GLOBALS['page']->add('<!-- debug : '.$query.( $result === false ? ' with error: '.mysql_error() : '-->' ), 'debug');
		}
		return $result;
	}
	
	/**
	 * class construtor
	 */
	function UserPreferencesDb($_db_conn = NULL) {
		
		$this->_db_conn = $_db_conn;
	}
	
	/**
	 * return info of all the preferences
	 * @param	string	$base_path	if is passed only the preferences that is based on this path will be returned
	 * 
	 * @return 	mixed	an array with the info of the preferences founded
	 *					[path_name] => (	[path_name] 
	 *										[label]
	 *										[default_value]
	 *										[type]
	 *										[visible]
	 *										[load_at_startup] ), ...
	 */
	function getAllPreference($base_path = false) {
		
		$query_all_preferences = "
		SELECT path_name, label, default_value, type, visible, load_at_startup 
		FROM ".$this->_getTablePreference()."
		WHERE 1 
		ORDER BY sequence";
		if($base_path == false) $base_path = " AND path_name LIKE '".$base_path."%' ";
		$re_all_preferences = $this->_executeQuery($query_all_preferences);
		
		$all_preferences = array();
		while(list($path_name, $label, $default_value, $type, $visible, $load_at_startup) = mysql_fetch_row($re_all_preferences)) {
			
			$all_preferences[$path_name] = array(
				'path_name'			=> $path_name, 
				'label'				=> $label, 
				'default_value'		=> $default_value, 
				'type'				=> $type, 
				'visible'			=> $visible, 
				'load_at_startup'	=> $load_at_startup );
		}
		return $all_preferences;
	}
	
	/**
	 * return info of a preference
	 * @param	string	$path	the preference
	 * 
	 * @return 	mixed	an array with the info of the preferencs if founded or FALSE
	 *					array(	[path_name] 
	 *							[label]
	 *							[default_value]
	 *							[type]
	 *							[visible]
	 *							[load_at_startup] )
	 */
	function getPreference($path) {
		
		$query_preference = "
		SELECT path_name, label, default_value, type, visible, load_at_startup 
		FROM ".$this->_getTablePreference()."
		WHERE path_name = '".$path."' 
		ORDER BY sequence";
		$re_preference = $this->_executeQuery($query_preference);
		
		$preference = array();
		if( mysql_num_rows($re_preference) > 0) {
			
			list($path_name, $label, $default_value, $type, $visible, $load_at_startup) = mysql_fetch_row($re_preference);
			$preference = array(
				'path_name'			=> $path_name, 
				'label'				=> $label, 
				'default_value'		=> $default_value, 
				'type'				=> $type, 
				'visible'			=> $visible, 
				'load_at_startup'	=> $load_at_startup );
		} else {
			
			return false;
		}
		return $preference;
	}
	
	/**
	 * return the default value for the preference
	 * @param	string	$path	the preference
	 * 
	 * @return 	mixed	the default_value for the preference if exists or FALSE
	 */
	function getDefaultValue($path) {
		
		$query_preference = "
		SELECT default_value, type 
		FROM ".$this->_getTablePreference()."
		WHERE path_name = '".$path."' 
		ORDER BY sequence";
		$re_preference = $this->_executeQuery($query_preference);
		
		$preference = array();
		if( mysql_num_rows($re_preference) > 0) {
			
			list($default_value) = mysql_fetch_row($re_preference);
			return $default_value;
		} else {
			
			return false;
		}
	}
	
	/**
	 * create a new preference
	 * @param 	string 	$path				the path of the preference
	 * @param 	string 	$label				the label for the name
	 * @param 	string 	$default_value		the default value
	 * @param 	string 	$type				an identifier for the type of the preference
	 * @param 	bool 	$visible			if the field is visible
	 * @param 	bool 	$load_at_startup	if it must loaded at class instanciation or only at request
	 *
	 * @return true if success false otherwise
	 */
	function createPreference($path, $label, $default_value, $type, $visible, $load_at_startup) {
		
		$query_ins_preferences = "
		INSERT INTO ".$this->_getTablePreference()." 
		( path_name, label, default_value, type, visible, load_at_startup )
		VALUES 
		( 	'".$path."', 
			'".$label."', 
			'".$default_value."', 
			'".$type."', 
			'".( $visible ? 1 : 0 )."',
			'".( $load_at_startup ? 1 : 0 )."' )";
		
		return $this->_executeQuery($query_ins_preferences);
	}
	
	/**
	 * update an existing preference
	 * @param 	string 	$path				the path of the preference
	 * @param 	string 	$label				the label for the name
	 * @param 	string 	$default_value		the default value
	 * @param 	string 	$type				an identifier for the type of the preference
	 * @param 	bool 	$visible			if the field is visible
	 * @param 	bool 	$load_at_startup	if it must loaded at class instanciation or only at request
	 *
	 * @return true if success false otherwise
	 */
	function updatePreference($path, $label, $default_value, $type, $visible, $load_at_startup) {
		
		$query_update_preferences = "
		UPDATE ".$this->_getTablePreference()."
		SET label = '".$label."', 
			default_value = '".$default_value."', 
			type = '".$type."', 
			visible = '".( $visible ? 1 : 0 )."',
			load_at_startup = '".( $load_at_startup ? 1 : 0 )."'
		WHERE path_name = '".$path."'";
		
		return $this->_executeQuery($query_update_preferences);
	}
	
	/**
	 * delete an existing preference, and the user value for it
	 * @param 	string 	$path				the path of the preference
	 *
	 * @return bool true if success false otherwise
	 */
	function deletePreference($path) {
		
		$query_delete_preferences = "
		DELETE FROM ".$this->_getTableUser()."
		WHERE path_name = '".$path."'";
		if(!$this->_executeQuery($query_delete_preferences)) return false;
		
		$query_delete_preferences = "
		DELETE FROM ".$this->_getTablePreference()."
		WHERE path_name = '".$path."'";
		return $this->_executeQuery($query_delete_preferences);
	}
	
	/**
	 * @param 	int		$id_user	the id of the user 
	 * @param 	string	$path		the path of the preference
	 *
	 * @return	string	the value of the user for this preference or the default value of the preference
	 */
	function getUserValue($id_user, $path) {
		
		$query_preference = "
		SELECT value 
		FROM ".$this->_getTableUser()."
		WHERE path_name = '".$path."' AND id_user = '".$id_user."'";
		$re_preference = $this->_executeQuery($query_preference);
		
		if( mysql_num_rows($re_preference) > 0) {
			
			list($value) = mysql_fetch_row($re_preference);
			return $value;
		} else {
			
			return $this->getDefaultValue($path);
		}
	}
	
	
	/**
	 * get all user preferences
	 * @param 	int		$id_user	the id of the user 
	 * @param 	string	$base_path	the base_path of the preference
	 *
	 * @return	array	the value of the user for the various preferences
	 */
	function getAllUserValue($id_user, $base_path = false) {
		
		$query_preferences = "
		SELECT prdata.path_name, prdata.default_value, udata.value 
		FROM ".$this->_getTablePreference()." AS prdata LEFT JOIN ".$this->_getTableUser()." AS udata
			ON ( prdata.path_name = udata.path_name AND id_user = '".$id_user."' )
		WHERE 1";
		if($base_path !== false) {
			$query_preferences .= " AND prdata.path_name LIKE '".$base_path."%'";
		}
		$query_preferences .= "ORDER BY sequence";
		$re_preferences = $this->_executeQuery($query_preferences);
		$pref = array();
		while(list($path, $default_value, $user_value) = mysql_fetch_row($re_preferences)) {
			
			if($user_value === NULL) $pref[$path] = $default_value;
			else $pref[$path] = $user_value;
		}
		return $pref;
	}
	
	/**
	 * get all user preferences
	 * @param 	int		$id_user			the id of the user 
	 * @param 	string	$visible			if true only the visible is returned
	 * @param 	string	$load_at_startup	if true only the load at startup is returned
	 * @param 	string	$base_path			the base_path of the preference
	 *
	 * @return	array	the value of the user for the various preferences [path] => value(user or default)
	 */
	function getFilteredUserValue($id_user, $visible = false, $load_at_startup = false, $base_path = false) {
		
		$query_preferences = "
		SELECT prdata.path_name, prdata.default_value, udata.value 
		FROM ".$this->_getTablePreference()." AS prdata LEFT JOIN ".$this->_getTableUser()." AS udata
			ON ( prdata.path_name = udata.path_name AND id_user = '".$id_user."')
		WHERE 1 ";
		if($visible !== false) {
			$query_preferences .= " AND prdata.visible = '1'";
		}
		if($load_at_startup !== false) {
			$query_preferences .= " AND prdata.load_at_startup = '1'";
		}
		if($base_path !== false) {
			$query_preferences .= " AND prdata.path_name LIKE '".$base_path."%'";
		}
		$query_preferences .= "ORDER BY sequence";
		$re_preferences = $this->_executeQuery($query_preferences);
		$pref = array();
		while(list($path, $default_value, $user_value) = mysql_fetch_row($re_preferences)) {
			
			if($user_value === NULL) $pref[$path] = $default_value;
			else $pref[$path] = $user_value;
		}
		return $pref;
	}
	
	/**
	 * get all user preferences and the value of a specific user for it, and in respect with passed filter
	 * @param 	int		$id_user			the id of the user 
	 * @param 	int		$visible			filter preferences that is visible
	 * @param 	int		$load_at_startup	filter preferences that is loaded at startup
	 * @param 	string	$base_path			if you need to load the user preferences limited to a specific group of path
	 *
	 * @return	string	the value of the user for the various preferences
	 */
	function getFullPreferences($id_user, $visible = false, $load_at_startup =false, $base_path = false) {
		
		$query_all_preferences = "
		SELECT 	prdata.path_name, 
				prdata.label, 
				prdata.default_value, 
				prdata.type, 
				prdata.visible, 
				prdata.load_at_startup, 
				udata.value 
		FROM ".$this->_getTablePreference()." AS prdata LEFT JOIN ".$this->_getTableUser()." AS udata
			ON ( prdata.path_name = udata.path_name AND id_user = '".$id_user."' ) 
		WHERE 1 ";
		if($visible !== false) {
			$query_all_preferences .= " AND prdata.visible = 1";
		}
		if($load_at_startup !== false) {
			$query_all_preferences .= " AND prdata.load_at_startup = 1";
		}
		if($base_path !== false) {
			$query_all_preferences .= " AND prdata.path_name LIKE '".$base_path."%'";
		}
		$query_all_preferences .= " ORDER BY prdata.sequence";
		$re_all_preferences = $this->_executeQuery($query_all_preferences);
		$pref = array();
		$all_preferences = array();
		while(list($path_name, $label, $default_value, $type, $visible, $load_at_startup, $user_value) 
			= mysql_fetch_row($re_all_preferences)) {
			
			$all_preferences[$path_name] = array(
				'path_name'			=> $path_name, 
				'label'				=> $label, 
				'default_value'		=> $default_value, 
				'type'				=> $type, 
				'visible'			=> $visible, 
				'load_at_startup'	=> $load_at_startup, 
				'user_value' 		=> ($user_value === NULL ? $default_value : $user_value ) );
		}
		return $all_preferences;
	}
	
	/**
	 * assign to a user a value for a preferences
	 * @param 	int		$id_user	the id of the user
	 * @param 	int		$path		the path of the preference
	 * @param 	string	$new_value	the new value
	 *
	 * @return	bool true if success false otherwise
	 */
	function assignUserValue($id_user, $path, $new_value) {
		
		$query_preference = "
		SELECT value 
		FROM ".$this->_getTableUser()."
		WHERE path_name = '".$path."' AND id_user = '".$id_user."'";
		$re_preference = $this->_executeQuery($query_preference);
		if( !mysql_num_rows($re_preference) ) {
			
			// Insert new entry
			return $this->_executeQuery("
			INSERT INTO ".$this->_getTableUser()."
			( path_name, id_user, value ) 
			VALUES 
			( '".$path."', '".$id_user."', '".$new_value."' )");
		} else {
			
			// Update existent entry
			return $this->_executeQuery("
			UPDATE ".$this->_getTableUser()."
			SET value = '".$new_value."'
			WHERE path_name = '".$path."'
				AND id_user = '".$id_user."'");
		}
	}
	
	/**
	 * delete all the preference value stored for a user or a specific one
	 * @param 	int		$id_user	the id of the user
	 * @param 	int		$path		(optional) the path of the preference
	 *
	 * @return	bool true if success false otherwise
	 */
	function removeUserValue($id_user, $path = false) {
		
		// Delete existent entry
		$delete_user_preferences = "
		DELETE FROM ".$this->_getTableUser()."
		WHERE id_user = '".$id_user."'";
		if($path !== false) {
			$delete_user_preferences .= " AND path_name = '".$path."'";
		}
		return $this->_executeQuery($delete_user_preferences);
	}
	
	/**
	 * delete all the preference value stored for a user from a base path
	 * @param 	int 	$id_user	the id of the user
	 * @param 	int		$base_path	the path of the preference
	 *
	 * @return	bool true if success false otherwise
	 */
	function removeUserValueOfPath($id_user, $base_path) {
		
		// Delete existent entry
		$delete_user_preferences = "
		DELETE FROM ".$this->_getTableUser()."
		WHERE id_user = '".$id_user."'
			AND path_name LIKE '".$base_path."%'";
		return $this->_executeQuery($delete_user_preferences);
	}
}

/**
 * this class is minded for manage the preferences of a specific user
 *
 * @uses 	class UserPreferencesDb 
 * @author	Fabio Pirovano <fabio (@) docebo (.) com>
 */
class UserPreferences {
	
	var $id_user;
	var $is_anonymous;
	var $_up_db;
	var $_preferences;
	var $base_name;
	
	/**
	 * class constructor
	 * @param int	$id_user the id of the user 
	 */
	 function UserPreferences($id_user, $db_conn = NULL) {
		
		$acl_man = new DoceboACLManager();
		
		$this->id_user 		= $id_user;
		
		if($acl_man->getAnonymousId() == $id_user) $this->is_anonymous = true;
		else $this->is_anonymous = false;
		
		$this->_up_db 		= new UserPreferencesDb($db_conn);
		$this->base_name 	= 'user_preference';
		// Load startup
		$this->_preferences = $this->_up_db->getFilteredUserValue($id_user, false, true, false);
	}
	
	/**
	 * @param string	$preference the preference that must by find
	 *
	 * @return mixed	the value of the preference for the user if preference exist else FALSE
	 */
	function getPreference($preference) {
		
		if(isset($this->_preferences[$preference])) {
			
			// Return loaded value
			return $this->_preferences[$preference];
		} else {
			
			// If the value is not present in the pool of preference loaded at startup try to load it from db
			$loaded_pref = $this->_up_db->getUserValue($this->id_user, $preference);
			if($loaded_pref !== false) {
				
				$this->_preferences[$preference] = $loaded_pref;
				return $loaded_pref;
			} else {
				
				return false;
			}
		}
	}
	
	/**
	 * save a specific value for the preference
	 * @param string	$preference the preference that must by find
	 * @param mixed		$new_value 	the new value to assign
	 *
	 * @return mixed	true if success false otherwise
	 */
	function setPreference($preference, $new_value) {
		
		if($this->is_anonymous) return true;
		
		if(!$this->_up_db->assignUserValue($this->id_user, $preference, $new_value)) {
			
			return false;
		} else {
		
			$this->_preferences[$preference] = $new_value;
			return true;
		}
	}
	
	/**
	 * @return string	the value of the preference 'ui.template'
	 */
	function getTemplate() {
		
		$value = $this->getPreference('ui.template');
		if($value == '' || $value == false) return false;
		return $value;
	}
	
	/**
	 * @param string	the value to assign at 'ui.template'
	 */
	function setTemplate($new_template) {
		
		$this->setPreference('ui.template', $new_template);
		if($this->id_user == getLogUserId() || $GLOBALS['framework']['templ_use_field'] == 0) setTemplate($new_template);
		return true;
	}
	
	/**
	 * @return string	the value of the preference 'ui.language'
	 */
	function getLanguage() {
		
		$value = $this->getPreference('ui.language');
		if($value == '') {

			if(isset($_SESSION['custom_lang'])) {

				$this->setPreference('ui.language', $_SESSION['custom_lang']);
				return $_SESSION['custom_lang'];
			}
			$value = getLanguage();
			if($value) {

				$this->setPreference('ui.language', $value);
				return $value;
			}
			require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
			$plt_man = PlatformManager::createInstance();
			$value = $plt_man->getLanguageForPlatform();
			$this->setPreference('ui.language', $value);
		}
		return $value;
	}
	
	/**
	 * @param string	the value to assign at 'ui.language'
	 */
	function setLanguage($new_language) {
		
		$this->setPreference('ui.language', $new_language);
		if($this->id_user == getLogUserId()) setLanguage($new_language);
		return true;
	}
	
	
	/**
	 * @param string	$base_path 		if specified load only preference form this base_path
	 * @param bool		$only_visible 	if true only the visible 
	 *
	 * @return string	the code for show the actual preference of the user
	 */
	function getFreezeMask($base_path = false, $only_visible = true) {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$lang =& DoceboLanguage::createInstance('preferences', 'framework');
		
		$preferences = $this->_up_db->getFullPreferences($this->id_user, $only_visible, false, $base_path);
		
		$html = '';
		while(list(, $pref) = each($preferences)) {
			
			// Navigation trought the preferences 
			// array( 'path_name', 'label', 'default_value', 'type', 'visible', 'load_at_startup', 'user_value' )
			switch( $pref['type'] ) {
				case "language" : {
					//drop down language
					$lang_sel = $this->getLanguage();
					$html .= Form::getLineBox( $lang->def($pref['label']), 
												$lang_sel );
					
				};break;
				case "template" : {
					//drop down template
					$templ_sel = getTemplate();
					$html .= Form::getLineBox( $lang->def($pref['label']), 
												$templ_sel );
				};break;
				case "hteditor" : {
					//drop down hteditor
					$ht_edit = getHTMLEditorList();
					$value = ( $pref['user_value'] ? $pref['user_value'] : $pref['default_value'] );
					$html .= Form::getLineBox( $lang->def($pref['label']), 
												$ht_edit[$value] );
				};break;
				case "layout_chooser" : {
					//drop down hteditor
					$layout = array(
						'left' => def('_LAYOUT_LEFT'), 
						'over' => def('_LAYOUT_OVER'), 
						'right' => def('_LAYOUT_RIGHT'));
					$value = ( $pref['user_value'] ? $pref['user_value'] : $pref['default_value'] );
					$html .= Form::getLineBox( $lang->def($pref['label']), 
												$layout[$value] );
				};break;
				case "enum" : {
					//on off
					$value = ( $pref['user_value'] ? $pref['user_value'] : $pref['default_value'] );
					$html .= Form::getLineBox( $lang->def($pref['label']), 
												( $value == 'on' ? 
													$lang->def('_ACTIVE') : 
													$lang->def('_OFF') ) );
				};break;
				//string or int
				default : {
					$html .= Form::getLineBox( $lang->def($pref['label']),
												( $pref['user_value'] ? 
														$pref['user_value'] : 
														$pref['default_value'] ) );
				}
			}
		}
		return $html.'<div class="no_float"></div>';
	}
	
	/**
	 * @param string	$base_path 		if specified load only preference form this base_path
	 * @param bool		$only_visible 	if true only the visible 
	 *
	 * @return string	the code for the mod mask
	 */
	function getModifyMask($base_path = false, $only_visible = true) {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$lang =& DoceboLanguage::createInstance('preferences', 'framework');
		
		$preferences = $this->_up_db->getFullPreferences($this->id_user, $only_visible, false, $base_path);
		
		$html = '';
		while(list(, $pref) = each($preferences)) {
			
			// Navigation trought the preferences 
			// array( 'path_name', 'label', 'default_value', 'type', 'visible', 'load_at_startup', 'user_value' )
			switch( $pref['type'] ) {
				case "language" : {
					//drop down language
					$lang_sel = $this->getLanguage();
					
					$langs_var = $GLOBALS['globLangManager']->getAllLangCode();
					$langs = array();
					foreach($langs_var as $k => $v) {
					
						$langs[$k] = $v;
					}
					/* XXX: remove when alll lang ready*/
					$html .= Form::getDropdown( $lang->def($pref['label']), 
												$this->base_name.'_'.$pref['path_name'], 
												$this->base_name.'['.$pref['path_name'].']', 
												$langs, 
												array_search($lang_sel, $langs));
					
				};break;
				case "template" : {
					//drop down template
					$templ_sel = $this->getTemplate();
					$templ = getTemplateList();
					
					$html .= Form::getDropdown( $lang->def($pref['label']), 
												$this->base_name.'_'.$pref['path_name'], 
												$this->base_name.'['.$pref['path_name'].']', 
												$templ, 
												array_search($templ_sel, $templ));
				};break;
				case "hteditor" : {
					//drop down hteditor
					$ht_edit = getHTMLEditorList();
					$html .= Form::getDropdown( $lang->def($pref['label']), 
												$this->base_name.'_'.$pref['path_name'], 
												$this->base_name.'['.$pref['path_name'].']', 
												$ht_edit, 
												( $pref['user_value'] ? $pref['user_value'] : $pref['default_value'] ) );
				};break;
				case "layout_chooser" : {
					//drop down hteditor
					$layout = array(
						'left' => def('_LAYOUT_LEFT'), 
						'over' => def('_LAYOUT_OVER'), 
						'right' => def('_LAYOUT_RIGHT'));
					$html .= Form::getDropdown( $lang->def($pref['label']), 
												$this->base_name.'_'.$pref['path_name'], 
												$this->base_name.'['.$pref['path_name'].']',  
												$layout, 
												( $pref['user_value'] ? $pref['user_value'] : $pref['default_value'] ) );
				};break;
				case "enum" : {
					//on off
					$value = ( $pref['user_value'] ? $pref['user_value'] : $pref['default_value'] );
					$html .= Form::openFormLine()
							.Form::getInputCheckbox( $this->base_name.'_'.$pref['path_name'].'_on', 
											$this->base_name.'['.$pref['path_name'].']', 
											'on', 
											($value == 'on'), '' )		
							.' '
							.Form::getLabel($this->base_name.'_'.$pref['path_name'].'_on', $lang->def($pref['label']) )
							.Form::closeFormLine();
					
					
				};break;
				//string or int
				default : {
					$html .= Form::getTextfield( $lang->def($pref['label']), 
												$this->base_name.'_'.$pref['path_name'], 
												$this->base_name.'['.$pref['path_name'].']', 
												'65535', 
												( $pref['user_value'] ? $pref['user_value'] : $pref['default_value'] ) );
				}
			}
		}
		return $html.'<div class="no_float"></div>';
	}
	
	/**
	 * @param array		$array_source 	save the preferences of a user
	 * @param string	$base_path 		if specified load only preference form this base_path
	 *
	 * @return nothing
	 */
	function savePreferences( $array_source, $base_path = false) {
		
		$info_pref = $this->_up_db->getFullPreferences($this->id_user, true, false, $base_path);
		
		if(!isset($array_source[$this->base_name])) return true;
		if(!is_array($array_source[$this->base_name])) return true;
		
		$re = true;
		while(list(, $pref) = each($info_pref)) {
			
			if(isset($array_source[$this->base_name][$pref['path_name']])) {
				$new_value = $array_source[$this->base_name][$pref['path_name']];
			} else $new_value = NULL;
			switch($pref['type']) {
				case "language" : {
					
					$langs = $GLOBALS['globLangManager']->getAllLangCode();
					$re &= $this->setLanguage($langs[$new_value]);
				};break;
				case "template" : {
					
					$templ = getTemplateList();
					$re &= $this->setTemplate($templ[$new_value]);
				};break;
				case "enum" : {
					if($new_value == NULL) $re &= $this->setPreference($pref['path_name'], 'off');
					else $re &= $this->setPreference($pref['path_name'], 'on');
				};break;
				
				default : {
					
					$re &= $this->setPreference($pref['path_name'], $new_value);
				}
			}
		}
		return $re;
	}
}

?>