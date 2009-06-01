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
 * @package Configuration
 * @author 	Giovanni Derks
 * @version $Id: class.conf_cms.php 113 2006-03-08 18:08:42Z ema $
 **/

class Config_Cms extends Config {

	/**
	 * class constructor
	 */
	function Config_Cms($table = false) {

		parent::Config($table);

		if($table === false) $this->table = $GLOBALS['prefix_cms'].'_setting';
		else $this->table = $table;

		return;
	}

	/**
	 * @return 	array 	this array contains association trought id and name of the regroup units
	 *
	 * @access 	public
	 */
	function getRegroupUnit($with_invisible = false) {

		$lang =& DoceboLanguage::createInstance('admin_config', 'cms');

		$query_regroup = "
		SELECT DISTINCT regroup
		FROM ".$this->table." "
		.( $with_invisible ? " WHERE hide_in_modify = '0' " : '' )
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

		$lang =& DoceboLanguage::createInstance('admin_config', 'cms');

		$reSetting = mysql_query("
		SELECT param_name, param_value, value_type, max_size
		FROM ".$this->table."
		WHERE regroup = '".$regroup."' AND
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
				case "check" : {
					$html .= Form::openFormLine()
							.Form::getInputCheckbox($var_name,
											'option['.$var_name.']',
											1,
											($var_value == 1), '' )
							.' '
							.Form::getLabel($var_name, $lang->def('_'.strtoupper($var_name)) )
							.Form::closeFormLine();

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
				case "enum" : {
					//on off

					$html .= Form::getOpenCombo( $lang->def('_'.strtoupper($var_name)) )
							.Form::getRadio($lang->def('_ACTIVE'), $var_name.'_on', 'option['.$var_name.']', 'on', ($var_value == 'on'))
							.Form::getRadio($lang->def('_NO'), $var_name.'_off', 'option['.$var_name.']', 'off', ($var_value == 'off'))
							.Form::getCloseCombo();
				};break;
				case "grpsel_chooser" : {
					$layout = array(
						'group' => $lang->def('_GROUPS'),
						'orgchart' => $lang->def('_ORGCHART'));
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$layout,
												$var_value);
				};break;				
				case "bancat_chooser" : {
					require_once($GLOBALS["where_cms"]."/admin/modules/banners/functions.php");
					$cat_arr=getCategoryDropdownArray();
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$cat_arr,
												$var_value);					
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

	/**
	 * @return 	bool 	true if the operation was successfull false otherwise
	 *
	 * @access 	public
	 */
	function saveElement($regroup) {

		$reSetting = mysql_query("
		SELECT param_name, param_value, value_type
		FROM ".$this->table."
		WHERE regroup = '".$regroup."' AND
			hide_in_modify = '0'");

		$re = true;
		while( list( $var_name, $old_value, $value_type ) = mysql_fetch_row( $reSetting ) ) {

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
				case "check" : {
					if( isset($_POST['option'][$var_name]) && $_POST['option'][$var_name] == 1) $new_value = 1;
					else $new_value = 0;
				};break;					
				case "int" : {
					$new_value = (int)$_POST['option'][$var_name];
				};break;
				//if is enum switch value to on or off
				case "enum" : {
					if((isset($_POST['option'][$var_name])) && ($_POST['option'][$var_name] == "on"))
						$new_value = 'on';
					else if((isset($_POST['option'][$var_name])) && ($_POST['option'][$var_name] == "off"))
						$new_value = 'off';
				};break;
				//else simple assignament
				default : {
					$new_value = $_POST['option'][$var_name];
				} 
			}
			
			if ($new_value != $old_value) {
				if(!mysql_query("
				UPDATE ".$this->table."
				SET param_value = '$new_value'
				WHERE param_name = '$var_name' AND regroup = '".$regroup."'")) {
					$re = false;
				}
			}
			
		}

		return $re;
	}
}

?>