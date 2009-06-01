<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
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
 * @package admin-core
 * @subpackage configuration
 * @author 	Pirovano Fabio (fabio@docebo.com)
 * @version $Id: class.conf_fw.php 113 2006-03-08 18:08:42Z ema $
 **/

class Config_Framework extends Config {

	/**
	 * class constructor
	 */
	function Config_Framework($table = false) {

		parent::Config($table);

		if($table === false) $this->table = $GLOBALS['prefix_fw'].'_setting';
		else $this->table = $table;

		return;
	}

	/**
	 * @return 	array 	this array contains association trought id and name of the regroup units
	 *
	 * @access 	public
	 */
	function getRegroupUnit($with_invisible = false) {

		$lang =& DoceboLanguage::createInstance('configuration', 'framework');

		$query_regroup = "
		SELECT DISTINCT regroup
		FROM ".$this->table."
		WHERE pack = 'main' "
		.( $with_invisible ? " AND hide_in_modify = '0' " : '' )
		."ORDER BY regroup ";
		$re_regroup = mysql_query($query_regroup);
		$GLOBALS['page']->add(doDebug($query_regroup), 'debug');

		$group = array();
		while(list($id_regroup) = mysql_fetch_row($re_regroup))  {

			$group[$id_regroup] = $lang->def('_RG_FW_'.$id_regroup);
		}
		$group['templ_man'] = $lang->def('_TEMPL_MAN');
		$group['suiteman'] = $lang->def('_SUITE_MANAGEMENT');
		return $group;
	}
	
	function _maskSuiteManager() {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
		
		$lang =& DoceboLanguage::createInstance('configuration', 'framework');
		$plat_man =& PlatformManager::createInstance();
		
		$all_platform 		= $plat_man->getPlatformsInfo();
		$code_list_home 	= array();
		
		$html = Form::getOpenFieldset($lang->def('_LOAD_UNLOAD_PLATFORM'));
		reset($all_platform);
		while(list($code, $info) = each($all_platform)) {
			if($info['hidden_in_config'] != 'true') { 
				
				$code = $info['platform'];
				$html .= Form::getCheckbox(	$info['name'], 
												'activate_platform_'.$code, 
												'activate_platform['.$code.']', 
												1, 
												( $info['is_active'] == 'true' ), 
												( $info['mandatory'] == 'true' ? ' disabled="disabled"' : '' ) );
				
				if($info['is_active'] == 'true') $code_list_home[$code] = $info['name'];
			}
		}
		unset($code_list_home['scs']);
		unset($code_list_home['framework']);
		
		$html .= Form::getCloseFieldset();
		$html .= Form::getDropdown($lang->def('_HOME_PLATFORM'), 
									'platform_in_home',
									'platform_in_home',
									$code_list_home,
									$plat_man->getHomePlatform() );
		return $html;
	}
	
	function _saveSuiteManager() {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
		
		$plat_man =& PlatformManager::createInstance();
		
		$all_platform 		= $plat_man->getPlatformsInfo();
		$re = true;
		
		reset($all_platform);
		while(list($code, $info) = each($all_platform)) {
			if($info['hidden_in_config'] != 'true') { 
				$code = $info['platform'];
				if(isset($_POST['activate_platform'][$code])) {
					
					$re &= $plat_man->activatePlatform($code);
					$code_list_home[$code] = $info['name'];
				} elseif($info['mandatory'] == 'false') $re &= $plat_man->deactivatePlatform($code);
			}
		}
		if(isset($code_list_home[$_POST['platform_in_home']])) $re &= $plat_man->putInHome($_POST['platform_in_home']);
		return $re;
	}
	
	function _maskTemplateManager() {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
		
		$lang =& DoceboLanguage::createInstance('configuration', 'framework');
		$field_man = new FieldList();
		
		$html = '';
		if(isset($_POST['save_and_refresh'])) {
			
			if(!mysql_query("
			UPDATE ".$this->table." 
			SET param_value = '".$_POST['templ_use_field']."' 
			WHERE pack = 'main' AND param_name = 'templ_use_field'")) {
				
				$html .= getErrorUi('_ERROR_WHILE_SAVING_NEW_FIELD');
			} else {
				
				$GLOBALS['framework']['templ_use_field'] = $_POST['templ_use_field'];
			}
		}
		
		$drop_field = array();
		$drop_field = $field_man->getFlatAllFields(false, 'dropdown');
		$drop_field[0] = $lang->def('_NO_USE');
		
		$html .= Form::getDropdown($lang->def('_TEMPL_USE_FIELD'), 
									'templ_use_field',
									'templ_use_field',
									$drop_field,
									$GLOBALS['framework']['templ_use_field'] );
		
		$html .= Form::getButton('save_and_refresh', 'save_and_refresh', $lang->def('_SAVE_AND_REFRESH'));
		
		if($GLOBALS['framework']['templ_use_field'] != 0) {
			
			$field_obj =& $field_man->getFieldInstance($GLOBALS['framework']['templ_use_field']);
			if($field_obj === NULL) return $html.getErrorUi('_ERROR_WITH_THIS_FIELD');
			
			$assignement = array();
			$query_template_assigned = "
			SELECT ref_id, template_code
			FROM ".$GLOBALS['prefix_fw']."_field_template 
			WHERE id_common = '".$GLOBALS['framework']['templ_use_field']."'";
			$re_templ_assigned = mysql_query($query_template_assigned);
			while(list($ref_id, $template_code) = mysql_fetch_row($re_templ_assigned)) {
				$assignement[$ref_id] = $template_code;
			}
			
			$son_value 			= $field_obj->getAllSon();
			$template_list 		= getTemplateList(true);
			$default_template 	= getDefaultTemplate();
			
			$tb_son = new Typeone(	0, 
									$lang->def('_ASSIGN_DROPDOWN_VALUE_TEMPLATE'), 
									$lang->def('_ASSIGN_DROPDOWN_VALUE_TEMPLATE_SUMMARY'));
			
			$cont_h = array($lang->def('_DROPDOWN_VALUE'), $lang->def('_TEMPLATE_VALUE'));
			$type_h = array('','');
			$tb_son->setColsStyle($type_h);
			$tb_son->addHead($cont_h);
			while(list($id_son, $drop_son_name) = each($son_value)) {
	
				$cont = array(
					'<label for="template_selected_'.$id_son.'">'.$drop_son_name.'</label>',
					Form::getInputDropdown(	'dropdown',
											'template_selected_'.$id_son, 
											'template_selected['.$id_son.']', 
											$template_list,
											( isset($assignement[$id_son]) && isset($template_list[$assignement[$id_son]]) 
												? $assignement[$id_son] 
												: $default_template ),
											''
										)
				);
				$tb_son->addBody($cont);
			}
			$html .= $tb_son->getTable();
		}
		
		return $html;
	}
	
	function _saveTemplateManager() {
		
		$re = true;
		if(!isset($_POST['template_selected'])) return true;
		
		$query_template_assigned = "
		SELECT ref_id, template_code
		FROM ".$GLOBALS['prefix_fw']."_field_template 
		WHERE id_common = '".$GLOBALS['framework']['templ_use_field']."'";
		$re_templ_assigned = mysql_query($query_template_assigned);
		while(list($ref_id, $template_code) = mysql_fetch_row($re_templ_assigned)) {
			$assignement[$ref_id] = $template_code;
		}
		
		while(list($ref_id, $template_code) = each($_POST['template_selected'])) {
			
			if(isset($assignement[$ref_id])) {
				
				if(!mysql_query("
				UPDATE ".$GLOBALS['prefix_fw']."_field_template  
				SET template_code = '".$template_code."' 
				WHERE id_common = '".$GLOBALS['framework']['templ_use_field']."' 
					AND ref_id = '".$ref_id."'")) $re = false;
			} else {
				
				if(!mysql_query("
				INSERT INTO ".$GLOBALS['prefix_fw']."_field_template 
				( id_common, ref_id, template_code ) VALUES (
					'".$GLOBALS['framework']['templ_use_field']."',
					'".$ref_id."',
					'".$template_code."' 
				)")) $re = false;
			}
		}
		return $re;
	}
	
	/**
	 * @return 	string 	contains the displayable information for a selected group
	 *
	 * @access 	public
	 */
	function getPageWithElement($regroup) {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		
		if($regroup == 'templ_man') return $this->_maskTemplateManager();
		
		elseif($regroup == 'suiteman') return $this->_maskSuiteManager();
		
		$lang =& DoceboLanguage::createInstance('configuration', 'framework');
		
		$reSetting = mysql_query("
		SELECT param_name, param_value, value_type, max_size
		FROM ".$this->table."
		WHERE  pack = 'main' AND regroup = '".$regroup."' AND
			hide_in_modify = '0'
		ORDER BY sequence");

		$html = '';
		while(list( $var_name, $var_value, $value_type, $max_size ) = mysql_fetch_row( $reSetting ) ) {

			switch( $value_type ) {
				case "language" : {
					//drop down language
					$langs = $GLOBALS['globLangManager']->getAllLangCode();
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$langs,
												array_search($var_value, $langs));

				};break;
				case "template" : {
					//drop down template
					$templ = getTemplateList();
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$templ,
												array_search($var_value, $templ));
				};break;
				case "hteditor" : {
					//drop down hteditor
					$ht_edit = getHTMLEditorList();
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$ht_edit,
												$var_value);
				};break;
				case "layout_chooser" : {
					//drop down hteditor
					$layout = array(
						'left' => def('_LAYOUT_LEFT'),
						'over' => def('_LAYOUT_OVER'),
						'right' => def('_LAYOUT_RIGHT'));
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$layout,
												$var_value);
				};break;
				case "pubflow_method_chooser" : {
					//drop down hteditor
					$options = array(
						'onestate' => def('_PUBFLOW_ONESTATE'),
						'twostate' => def('_PUBFLOW_TWOSTATE'),
						'advanced' => def('_PUBFLOW_ADVANCED'));
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$options,
												$var_value);
				};break;
				case "field_select" : {
					require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

					$fl=new FieldList();
					$all_fields=$fl->getAllFields();
					$fields=array();
					foreach($all_fields as $key=>$val) {
						$fields[$val[FIELD_INFO_ID]]=$val[FIELD_INFO_TRANSLATION];
					}
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$fields,
												$var_value);
				} break;
				case "sel_sms_gateway" : {
					$options = array(
						'0' => def('_SMS_GATEWAY_AUTO'),
						'1' => def('_SMS_GATEWAY_1'),
						'2' => def('_SMS_GATEWAY_2'),
						'3' => def('_SMS_GATEWAY_3'),
						'4' => def('_SMS_GATEWAY_4'));
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$options,
												$var_value);
				} break;
				case "enum" : {
					//on off
					$html .= Form::openFormLine()
							.Form::getInputCheckbox($var_name.'_on',
											'option['.$var_name.']',
											'on',
											($var_value == 'on'), '' )
							.' '
							.Form::getLabel($var_name.'_on', $lang->def('_'.strtoupper($var_name)), 'label_bold' )
							.Form::closeFormLine();
				};break;
				case "menuvoice" :
				case "check" : {
					//on off
					$html .= Form::openFormLine()
							.Form::getInputCheckbox($var_name,
											'option['.$var_name.']',
											1,
											($var_value == 1), '' )
							.' '
							.Form::getLabel($var_name, $lang->def('_'.strtoupper($var_name)), 'label_bold' )
							.Form::closeFormLine();
					
					
					//$html .= Form::getCheckbox( $lang->def('_'.strtoupper($var_name)) , $var_name, 'option['.$var_name.']', 1, ($var_value == 1));
				};break;
				//uncrypted password
				case "password" : {
					$html .= Form::getPassword( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$max_size,
												$var_value );
				} break;
				case "textarea" : {
					
					$html .= Form::getSimpletextarea( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']', 
												$var_value );
				} break;
				case "security_check" : {
					
					$filter_to_use = ( isset($_POST['option']['session_ip_filter']) 
											? $_POST['option']['session_ip_filter'] 
											: false );
					
					if($filter_to_use != false) {
						$filter_to_use = str_replace('\r', "\r", $filter_to_use);
						$filter_to_use = str_replace('\n', "\n", $filter_to_use);
					}
					$html .= Form::getTextfield( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$max_size,
												$var_value,
												'', 
												( internalFirewall($var_value, $filter_to_use) 
													? $lang->def('_IP_ALLOW') 
													: $lang->def('_IP_DENY') ) );
				} break;
				case "rest_auth_sel_method": {
					$value_set = array(
						$lang->def('_REST_AUTH_UCODE')=>0,
						$lang->def('_REST_AUTH_TOKEN')=>1
					);
					$html .= Form::getRadioSet($lang->def('_REST_AUTH_SEL_METHOD'), $var_name, 'option['.$var_name.']', $value_set, $var_value);
				} break;
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

	/**
	 * @return 	bool 	true if the operation was successfull false otherwise
	 *
	 * @access 	public
	 */
	function saveElement($regroup) {
		
		if($regroup == 'templ_man') 	return $this->_saveTemplateManager();
		
		if($regroup == 'suiteman') 		return $this->_saveSuiteManager();
		
		$reSetting = mysql_query("
		SELECT param_name, value_type, extra_info
		FROM ".$this->table."
		WHERE pack = 'main' AND regroup = '".$regroup."' AND
			hide_in_modify = '0'");

		$re = true;
		while( list( $var_name, $value_type, $extra_info ) = mysql_fetch_row( $reSetting ) ) {

			switch( $value_type ) {
				//if is int cast it
				case "language" : {
					$lang = $GLOBALS['globLangManager']->getAllLangCode();
					$new_value = $lang[$_POST['option'][$var_name]];
				};break;
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
				case "menuvoice" : {
					
					require_once($GLOBALS['where_framework'].'/lib/lib.menu.php');
					$menu_man = new MenuManager();
					if( isset($_POST['option'][$var_name]) && $_POST['option'][$var_name] == 1) {

						$menu_man->addPerm(ADMIN_GROUP_GODADMIN, '/framework/admin'.$extra_info);
						$new_value = 1;
					} else {

						$menu_man->removePerm(ADMIN_GROUP_GODADMIN, '/framework/admin'.$extra_info);
						$new_value = 0;
					}
				};break;
				//else simple assignament
				default : {
					$new_value = $_POST['option'][$var_name];
				}
			}
			if(!mysql_query("
			UPDATE ".$this->table."
			SET param_value = '$new_value'
			WHERE pack = 'main' AND param_name = '$var_name' AND regroup = '".$regroup."'")) {
				$re = false;
			}
		}

		return $re;
	}
}

?>