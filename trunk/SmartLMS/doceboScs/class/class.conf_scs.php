<?php

/************************************************************************/
/* DOCEBO SCS - Syncronous Collaborative System							*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**                   
 * @package Configuration
 * @author 	Pirovano Fabio (fabio@docebo.com)
 * @version $Id: class.conf_scs.php 236 2006-04-10 21:06:34Z fabio $
 **/

class Config_Scs extends Config {
	
	var $table_root;
	
	/**
	 * class constructor
	 */
	function Config_Scs($table = false) {
		
		parent::Config($table);
		
		if($table === false) $this->table = $GLOBALS['prefix_scs'].'_setting';
		else $this->table = $table;
		
		$this->table_root = $GLOBALS['prefix_scs'].'_rules_root';
	}
	
	function setTableroot($table) {
		
		$this->table_root = $table;
	}
	
	/**
	 * @return 	array 	this array contains association trought id and name of the regroup units
	 *
	 * @access 	public
	 */
	function getRegroupUnit($with_invisible = false) {
		
		$lang =& DoceboLanguage::createInstance('admin_config', 'scs');
		
		//$group['root'] = $lang->def('_RG_FW_ROOT');
		
		$query_regroup = "
		SELECT DISTINCT regroup 
		FROM ".$this->table." "
		.( $with_invisible == false ? " WHERE hide_in_modify = '0' " : '' ) 
		."ORDER BY regroup ";
		$re_regroup = mysql_query($query_regroup);
		$GLOBALS['page']->add(doDebug($query_regroup), 'debug');
		
		while(list($id_regroup) = mysql_fetch_row($re_regroup))  {
			
			$group[$id_regroup] = $lang->def('_RG_FW_'.$id_regroup);
		}
		return $group;
	}
	
	function _getRoot() {
		
		$lang =& DoceboLanguage::createInstance('admin_config', 'scs');
		
		$reSetting = mysql_query("
		DESCRIBE ".$this->table_root."");
		
		$reSettingValue = mysql_query("
		SELECT  system_type, server_ip, server_port, server_path, 
				max_user_at_time, max_room_at_time, max_subroom_for_room, 
				enable_drawboard, enable_livestream, enable_remote_desktop, enable_webcam, enable_audio 
		FROM ".$this->table_root."");
		$values = mysql_fetch_array( $reSettingValue );
		
		$html = '';
		while($res = mysql_fetch_row( $reSetting ) ) {
			
			$var_name 		= $res[0]; 
			$value_type 	= $res[1];
			$default_value 	= $res[4];
			
			switch( $value_type ) {
				case "enum('p2p','server')" : {
					//radio button
					$html .= Form::getOpenCombo($lang->def('_'.strtoupper($var_name)) )
							.Form::getRadio($lang->def('_P2P'), 
											'option_'.$var_name.'_p2p', 
											'option['.$var_name.']', 
											'p2p', 
											($values[$var_name] == 'p2p'))
							.Form::getRadio($lang->def('_SERVER'), 
											'option_'.$var_name.'_server', 
											'option['.$var_name.']', 
											'server', 
											($values[$var_name] == 'server'))
							.Form::getCloseCombo();
				};break;
				case "varchar(255)" : {
					
					$html .= Form::getTextfield($lang->def('_'.strtoupper($var_name)), 
											'option_'.$var_name.'', 
											'option['.$var_name.']', 
											255, 
											$values[$var_name] );
				};break;
				case "int(5) unsigned" : {
										
					$html .= Form::getTextfield($lang->def('_'.strtoupper($var_name)), 
											'option_'.$var_name.'', 
											'option['.$var_name.']', 
											5, 
											$values[$var_name] );
				};break;
				case "int(11) unsigned" : {
					
										
					$html .= Form::getTextfield($lang->def('_'.strtoupper($var_name)), 
											'option_'.$var_name.'', 
											'option['.$var_name.']', 
											11, 
											$values[$var_name] );
				};break;
				case "enum('yes','no')" : {
					
					$html .= Form::getOpenCombo( $lang->def('_'.strtoupper($var_name)) )
							.Form::getRadio($lang->def('_YES'), $var_name.'_yes', 'option['.$var_name.']', 'yes', 
								($values[$var_name] == 'yes'))
							.Form::getRadio($lang->def('_NO'), $var_name.'_no', 'option['.$var_name.']', 'no', 
								($values[$var_name] == 'no'))
							.Form::getCloseCombo();
				};break;
			}
		}
		return $html;
	}
	
	/**
	 * @return 	string 	contains the displayable information for a selected group
	 *
	 * @access 	public
	 */
	function getPageWithElement($regroup) {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		
		$lang =& DoceboLanguage::createInstance('admin_config', 'scs');
		
		
		if($regroup == 'root') {
			
			return $this->_getRoot();
		} 
		
		$reSetting = mysql_query("
		SELECT param_name, param_value, value_type, max_size 
		FROM ".$this->table." 
		WHERE regroup = '".$regroup."' AND 
			hide_in_modify = '0'
		ORDER BY sequence");
		
		$html = '';
		while(list( $var_name, $var_value, $value_type, $max_size ) = mysql_fetch_row( $reSetting ) ) {
			
			switch( $value_type ) {
				case "template" : {
					//drop down template
					$templ = getTemplateList();
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)), 
												$var_name, 
												'option['.$var_name.']', 
												$templ, 
												array_search($var_value, $templ));
				};break;
				case "enum" : {
					//on off 
					$html .= Form::openFormLine()
							.Form::getLabel($var_name.'_on', $lang->def('_'.strtoupper($var_name)) )
							.Form::getInputCheckbox($var_name.'_on', 
											'option['.$var_name.']', 
											'on', 
											($var_value == 'on'), '' )
							.Form::closeFormLine();
				};break;
				case "check" : {
					//on off
					
					$html .= Form::getCheckbox( $lang->def('_'.strtoupper($var_name)) , $var_name, 'option['.$var_name.']', 1, ($var_value == 1));
				};break;
				//string or int
				default : {
					$html .= Form::getTextfield( $lang->def('_'.strtoupper($var_name)), 
												$var_name, 
												'option['.$var_name.']', 
												$max_size, 
												$var_value );
				}
			}
		}
		return $html;
	}
	
	function _setRoot() {
		
		$query_update = "UPDATE ".$this->table_root." SET "; 
		$re = true;
		while( list($var_name, $new_value) = each($_POST['option']) ) {
			
			$query_update .= "".$var_name." = '".$new_value."' ,";
		}
		$query_update = substr($query_update, 0, -1);
		if(!mysql_query($query_update)) $re = false;
		return $re;
	}
	
	/**
	 * @return 	bool 	true if the operation was successfull false otherwise
	 *
	 * @access 	public
	 */
	function saveElement($regroup) {
		
		if($regroup == 'root') {
			
			return $this->_setRoot();
		}
		
		$reSetting = mysql_query("
		SELECT param_name, value_type, extra_info 
		FROM ".$this->table." 
		WHERE regroup = '".$regroup."' AND 
			hide_in_modify = '0'");
		
		$re = true;
		while( list( $var_name, $value_type, $extra_info ) = mysql_fetch_row( $reSetting ) ) {
			
			switch( $value_type ) {
				//if is int cast it
				case "template" : {
					$templ = getTemplateList();
					$new_value = $templ[$_POST['option'][$var_name]];
				};break;
				case "int" : {
					$new_value = (int)$_POST['option'][$var_name];
				};break;
				//if is enum switch value to on or off
				case "enum" : {
					if( isset($_POST['option'][$var_name]) ) $new_value = 'on';
					else $new_value = 'off';
				};break;
				case "check" : {
					if( isset($_POST['option'][$var_name]) && $_POST['option'][$var_name] == 1) $new_value = 1;
					else $new_value = 0;
				};break;
				//else simple assignament
				default : {
					$new_value = $_POST['option'][$var_name];
				}
			}
			
			if(!mysql_query("
			UPDATE ".$this->table." 
			SET param_value = '$new_value' 
			WHERE param_name = '$var_name' AND regroup = '".$regroup."'")) {
				$re = false;
			}
			
		}
		
		return $re;
	}
}

?>