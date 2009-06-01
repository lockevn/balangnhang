<?php
/*************************************************************************/
/* DOCEBO CRM - Customer Relationship Management                         */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @package Configuration
 * @author 	Giovanni Derks
 * @version $Id: class.conf_crm.php 543 2006-08-01 09:53:36Z giovanni $
 **/

class Config_Crm extends Config {

	/**
	 * class constructor
	 */
	function Config_Crm($table = false) {

		parent::Config($table);

		if($table === false) $this->table = $GLOBALS['prefix_crm'].'_setting';
		else $this->table = $table;

		return;
	}

	/**
	 * @return 	array 	this array contains association trought id and name of the regroup units
	 *
	 * @access 	public
	 */
	function getRegroupUnit($with_invisible = false) {

		$lang =& DoceboLanguage::createInstance('configuration', 'crm');

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
		return $group;
	}

	/**
	 * @return 	string 	contains the displayable information for a selected group
	 *
	 * @access 	public
	 */
	function getPageWithElement($regroup) {

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		if($regroup == 'suiteman') {

			return $this->_maskSuiteManager();
		}

		$lang =& DoceboLanguage::createInstance('configuration', 'crm');

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
							.Form::getLabel($var_name.'_on', $lang->def('_'.strtoupper($var_name)) )
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
							.Form::getLabel($var_name, $lang->def('_'.strtoupper($var_name)) )
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

		if($regroup == 'suiteman') {

			return $this->_saveSuiteManager();
		}

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
