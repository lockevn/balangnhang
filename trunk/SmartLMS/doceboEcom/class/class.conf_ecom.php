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
 * @version $Id: class.conf_ecom.php,v 1.1.1.1 2005/10/18 12:57:54 gishell Exp $
 **/

class Config_Ecom extends Config {

	/**
	 * class constructor
	 */
	function Config_Ecom($table = false) {

		parent::Config($table);

		if($table === false) $this->table = $GLOBALS['prefix_ecom'].'_setting';
		else $this->table = $table;

	}

	/**
	 * @return 	array 	this array contains association trought id and name of the regroup units
	 *
	 * @access 	public
	 */
	function getRegroupUnit($with_invisible = false) {

		$lang =& DoceboLanguage::createInstance('admin_config', 'ecom');

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

		$lang =& DoceboLanguage::createInstance('admin_config', 'ecom');

		$reSetting = mysql_query("
		SELECT param_name, param_value, value_type, max_size
		FROM ".$this->table."
		WHERE regroup = '".$regroup."' AND
			hide_in_modify = '0'
		ORDER BY sequence");

		$html = '';
		while(list( $var_name, $var_value, $value_type, $max_size ) = mysql_fetch_row( $reSetting ) ) {
			switch( $value_type ) {

				case "ecommerce_type" : {
					//drop down hteditor
					$layout = array(
						'none' => def('_ECOM_NONE'),
						'standard' => def('_ECOM_STANDARD'),
						'with_buyer' => def('_ECOM_WITH_BUYER'));
					$html .= Form::getDropdown( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$layout,
												$var_value);
				};break;

				case "textarea" : {

					$html .= Form::getSimpletextarea( $lang->def('_'.strtoupper($var_name)),
												$var_name,
												'option['.$var_name.']',
												$var_value );
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

		$reSetting = mysql_query("
		SELECT param_name, value_type, extra_info
		FROM ".$this->table."
		WHERE regroup = '".$regroup."' AND
			hide_in_modify = '0'");

		$re = true;
		while( list( $var_name, $value_type, $extra_info ) = mysql_fetch_row( $reSetting ) ) {


			switch( $value_type ) {

				case "int" : {
					$new_value = (int)$_POST['option'][$var_name];
				};break;
				//if is enum switch value to on or off

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