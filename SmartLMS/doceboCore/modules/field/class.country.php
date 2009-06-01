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
 * @package  DoceboCore
 * @version  $Id: class.dropdown.php 987 2007-02-28 17:25:05Z giovanni $
 * @category Field
 * @author   Fabio Pirovano <fabio@docebo.com>
 */

require_once(dirname(__FILE__).'/class.field.php');

class Field_Country extends Field {

	var $back;
	var $back_coded;
	
	var $_options = array();

	/**
	 * class constructor
	 */
	function Field_Country($id_common) {

		parent::Field($id_common);
		
		$query_tax_country = "
		SELECT id_country, name_country
		FROM ".$this->getCountryTable()."
		ORDER BY name_country";
		$re_field_element = mysql_query($query_tax_country);

		$this->_options[0] = '';
		while(list($id_common_son, $element) = mysql_fetch_row($re_field_element)) {

			$this->_options[$id_common_son] = $this->convert_name($element);
		}
		
	}

	/**
	 * this function is useful for field recognize
	 *
	 * @return string	return the identifier of the field
	 *
	 * @access public
	 */
	function getFieldType() {
		return 'country';
	}

	function getCountryTable() {
		return $GLOBALS['prefix_fw'].'_country';
	}
	
	/**
	 * this function create a new field for future use
	 *
	 * @param  string	$back	indicates the return url
	 * @return nothing
	 *
	 * @access public
	 */
	function create($back) {

		$back_coded = htmlentities(urlencode($back));

		$array_lang = array();
		$std_lang 		=& DoceboLanguage::createInstance('standard');
		$lang 			=& DoceboLanguage::createInstance('field');
		$array_lang 	= $GLOBALS['globLangManager']->getAllLangCode();
		$out 			=& $GLOBALS['page'];

		if(isset($_POST['undo'])) {
			//undo action
			jumpTo($back.'&result=undo');
		}
		if(isset($_POST['save_field_'.$this->getFieldType()])) {

			//insert mandatory translation
			$use_multilang = 0;
			$mand_lang = getLanguage();
			$show_on = '';
			if(isset($_POST['show_on_platform'])) {
				while(list($code, ) = each($_POST['show_on_platform']))
					$show_on .= $code.',';
			}
			//control if all is ok
			if(!isset($_POST['new_dropdown'][$mand_lang])) {
				$out->add(
					getErrorUi($lang->def('_ERR_MUST_DEF_MANADATORY_TRANSLATION'))
					.getBackUi($this->getUrl().'&amp;type_field='
						.$this->getFieldType().'&amp;back='.$back_coded, $std_lang->def('_BACK')),
					'content'
				);
				return;
			}
			if($_POST['new_dropdown'][$mand_lang] == $lang->def('_NEW_FIELD') || trim($_POST['new_dropdown'][$mand_lang]) == '') {
				$out->add(
					getErrorUi($lang->def('_ERR_MUST_DEF_MANADATORY_TRANSLATION'))
					.getBackUi($this->getUrl().'&amp;type_field='
						.$this->getFieldType().'&amp;back='.$back_coded, $std_lang->def('_BACK')),
					'content'
				);
				return;
			}

			//insert mandatory field
			if(!mysql_query("
			INSERT INTO ".$this->_getMainTable()."
			(type_field, lang_code, translation, show_on_platform, use_multilang) VALUES
			('".$this->getFieldType()."', '".$mand_lang."', '".$_POST['new_dropdown'][$mand_lang]."', '".$show_on."', '".$use_multilang."') ")) {
				jumpTo($back.'&result=fail');
			}
			list($id_common) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
			if(!mysql_query("
			UPDATE ".$this->_getMainTable()."
			SET id_common = '".(int)$id_common."'
			WHERE idField = '".(int)$id_common."'")) {
				jumpTo($back.'&result=fail');
			}
			$re = true;
			//insert other field
			foreach($_POST['new_dropdown'] as $lang_code => $translation) {

				if($mand_lang != $lang_code && $translation != $lang->def('_NEW_FIELD') && trim($translation) != '') {
					$re_ins = mysql_query("
					INSERT INTO ".$this->_getMainTable()."
					(type_field, id_common, lang_code, translation, show_on_platform, use_multilang) VALUES
					('".$this->getFieldType()."', '".(int)$id_common."', '".$lang_code."', '".$translation."', '".$show_on."', '".$use_multilang."') ");
					$re = $re && $re_ins;
				}
			}
			jumpTo($back.'&result='.( $re ? 'success' : 'fail'));
		}

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		$form = new Form();

		$out->setWorkingZone('content');
		$out->add('<div class="std_block">');
		$out->add(
			$form->getFormHeader($lang->def('_NEW_DROPDOWN'))
			.$form->openForm('create_'.$this->getFieldType(), $this->getUrl())
			.$form->openElementSpace()
			.$form->getHidden('type_field', 'type_field', $this->getFieldType())
			.$form->getHidden('back', 'back', $back_coded)
		);
		$mand_lang = getLanguage();
		foreach($array_lang as $k => $lang_code ) {

			$out->add(
				$form->getTextfield(( ($mand_lang == $lang_code) ? '<span class="mandatory">*</span>' : '' ).$lang_code,
									'new_dropdown_'.$lang_code,
									'new_dropdown['.$lang_code.']',
									255,
									'',
									$lang_code.' '.$lang->def('_NEW_FIELD') )
			);
		}

		$GLOBALS['page']->add($this->getMultiLangCheck(), 'content');
		$GLOBALS['page']->add($this->getShowOnPlatformFieldset(), 'content');

		$out->add(
			$form->closeElementSpace()
			.$form->openButtonSpace()
			.$form->getButton('save_field', 'save_field_'.$this->getFieldType(), $std_lang->def('_CREATE', 'standard'))
			.$form->getButton('undo', 'undo', $std_lang->def('_UNDO', 'standard'))
			.$form->closeButtonSpace()
			.$form->closeForm()
		);
		$out->add('</div>');
	}

	/**
	 * this function manage a field
	 *
	 * @param  string	$back	indicates the return url
	 * @return nothing
	 *
	 * @access public
	 */
	function edit($back) {
		$back_coded = htmlentities(urlencode($back));

		$array_lang = array();
		$std_lang 		=& DoceboLanguage::createInstance('standard');
		$lang 			=& DoceboLanguage::createInstance('field');
		$array_lang 	= $GLOBALS['globLangManager']->getAllLangCode();
		$out 			=& $GLOBALS['page'];

		if(isset($_POST['undo'])) {
			//undo action
			jumpTo($back.'&result=undo');
		}
		if(isset($_POST['save_field_'.$this->getFieldType()])) {

			//insert mandatory translation
			$mand_lang = getLanguage();
			$show_on = '';
			if(isset($_POST['show_on_platform'])) {
				while(list($code, ) = each($_POST['show_on_platform']))
					$show_on .= $code.',';
			}
			//control if all is ok
			if(!isset($_POST['new_country'][$mand_lang])) {
				$out->add(
					getErrorUi($lang->def('_ERR_MUST_DEF_MANADATORY_TRANSLATION'))
					.getBackUi($this->getUrl().'&amp;type_field='
						.$this->getFieldType().'&amp;back='.$back_coded, $std_lang->def('_BACK')),
					'content'
				);
				return;
			}
			if($_POST['new_country'][$mand_lang] == $lang->def('_NEW_FIELD') || trim($_POST['new_country'][$mand_lang]) == '') {
				$out->add(
					getErrorUi($lang->def('_ERR_MUST_DEF_MANADATORY_TRANSLATION'))
					.getBackUi($this->getUrl().'&amp;type_field='
						.$this->getFieldType().'&amp;back='.$back_coded, $std_lang->def('_BACK')),
					'content'
				);
				return;
			}

			$existsing_translation = array();
			$re_trans = mysql_query("
			SELECT lang_code
			FROM ".$this->_getMainTable()."
			WHERE id_common = '".$this->id_common."'");
			while(list($l_code) = mysql_fetch_row($re_trans)) {
				$existsing_translation[$l_code] = 1;
			}

			$use_multilang =(isset($_POST['use_multi_lang']) ? 1 : 0);

			$re = true;
			//insert other field
			foreach($_POST['new_country'] as $lang_code => $translation) {

				if(isset($existsing_translation[$lang_code])) {

					if(!mysql_query("
					UPDATE ".$this->_getMainTable()."
					SET translation = '".$translation."',
						show_on_platform = '".$show_on."',
						use_multilang = '".$use_multilang."'
					WHERE id_common = '".(int)$this->id_common."' AND lang_code = '".$lang_code."'")) $re = false;
				} else {

					if(!mysql_query("
					INSERT INTO ".$this->_getMainTable()."
					(type_field, id_common, lang_code, translation, show_on_platform, use_multilang) VALUES
					('".$this->getFieldType()."', '".(int)$this->id_common."', '".$lang_code."', '".$translation."', '".$show_on."', '".$use_multilang."') ")) $re= false;
				}
			}
			jumpTo($back.'&result='.( $re ? 'success' : 'fail'));
		}

		//load value form database
		$re_trans = mysql_query("
		SELECT lang_code, translation, show_on_platform, use_multilang
		FROM ".$this->_getMainTable()."
		WHERE id_common = '".$this->id_common."'");
		while(list($l_code, $trans, $show_on, $db_use_multilang) = mysql_fetch_row($re_trans)) {
		
			$translation[$l_code] = $trans;
			if(!isset($show_on_platform)) $show_on_platform = array_flip(explode(',', $show_on));
			if(!isset($use_multilang)) $use_multilang = $db_use_multilang;
		}

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		$form = new Form();

		$out->setWorkingZone('content');
		$out->add('<div class="std_block">');
		$out->add(
			$form->getFormHeader($lang->def('_MODIFY_COUNTRY'))
			.$form->openForm('create_'.$this->getFieldType(), $this->getUrl())
			.$form->openElementSpace()
			.$form->getHidden('type_field', 'type_field', $this->getFieldType())
			.$form->getHidden('id_common', 'id_common', $this->id_common)
			.$form->getHidden('back', 'back', $back_coded)
		);
		$mand_lang = getLanguage();
		foreach($array_lang as $k => $lang_code ) {

			$out->add(
				$form->getTextfield(( ($mand_lang == $lang_code) ? '<span class="mandatory">*</span>' : '' ).$lang_code,
									'new_country_'.$lang_code,
									'new_country['.$lang_code.']',
									255,
									( isset($translation[$lang_code]) ? $translation[$lang_code] : '' ),
									$lang_code.' '.$lang->def('_NEW_FIELD') )
			);
		}

		$GLOBALS['page']->add($this->getMultiLangCheck($use_multilang), 'content');
		$GLOBALS['page']->add($this->getShowOnPlatformFieldset($show_on_platform), 'content');

		$out->add(
			$form->closeElementSpace()
			.$form->openButtonSpace()
			.$form->getButton('save_field', 'save_field_'.$this->getFieldType(), $std_lang->def('_SAVE', 'standard'))
			.$form->getButton('undo', 'undo', $std_lang->def('_UNDO', 'standard'))
			.$form->closeButtonSpace()
			.$form->closeForm()
		);
		$out->add('</div>');
	}

	/**
	 * this function completely remove a field
	 *
	 * @param  string	$back	indicates the return url
	 * @return nothing
	 *
	 * @access public
	 */
	function del($back) {

		$query_del = "
		DELETE FROM ".$this->_getUserEntryTable()."
		WHERE id_common = '".(int)$this->id_common."'";
		$re = mysql_query($query_del);
		if(!$re) jumpTo($back.'&result=fail');

		$query_del = "
		DELETE FROM ".$this->_getMainTable()."
		WHERE id_common = '".(int)$this->id_common."'";
		$re = mysql_query($query_del);

		jumpTo($back.'&result='.( $re ? 'success' : 'fail'));
	}

	function convert_name($name) {
		/*
		$exp_name = explode(' ', $name);
		while(list($i, $word) = each($exp_name)) {
			$exp_name[$i] = ucfirst(strtolower($word));
		}
		return implode(' ', $exp_name);*/
		
		return mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
	}

	/**
	 * display the entry of this field for the passed user
	 *
	 * @param 	int		$id_user 			if alredy exists a enty for the user load it
	 *
	 * @return string 	of field xhtml code
	 *
	 * @access public
	 */
	function show( $id_user ) {

		list($user_entry) = mysql_fetch_row(mysql_query("
		SELECT user_entry
		FROM ".$this->_getUserEntryTable()."
		WHERE id_user = '".(int)$id_user."' AND
			id_common = '".(int)$this->id_common."' AND
			id_common_son = '0'"));

		$user_entry = (int)$user_entry;
		
		/*
		$query_tax_country = "
		SELECT id_country, name_country
		FROM ".$this->getCountryTable()." 
		ORDER BY name_country";
		$re_field_element = mysql_query($query_tax_country);
		
		$option = array();
		$option[0] = '';
		while(list($id_common_son, $element) = mysql_fetch_row($re_field_element)) {
			$option[$id_common_son] = $this->convert_name($element);
		}*/
		$user_entry = (int)$user_entry;
		return $this->_options[$user_entry];

		//$option[$user_entry];
	}

	function getTranslation() {

		$re_field = mysql_query("
		SELECT translation
		FROM ".$this->_getMainTable()."
		WHERE id_common = '".(int)$this->id_common."' AND
			type_field = '".$this->getFieldType()."' AND
			lang_code = '".getLanguage()."'");
		list($translation) = mysql_fetch_row($re_field);

		return $translation;
	}

	/**
	 * display the field for interaction
	 *
	 * @param 	int		$id_user			if alredy exists a entry for the user load as default value
	 * @param 	bool	$freeze				if true, disable the user interaction
	 * @param 	bool	$mandatory			if true, the field is considered mandatory
	 * @param 	bool	$do_not_show_label	if true, do not show the label in freeze mode
	 *
	 * @return string 	of field xhtml code
	 *
	 * @access public
	 */
	function play( $id_user, $freeze, $mandatory = false, $do_not_show_label = false) {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		if( 	isset( $_POST['field_'.$this->getFieldType()] )
			&& 	isset( $_POST['field_'.$this->getFieldType()][$this->id_common] ) ) {
			$user_entry = $_POST['field_'.$this->getFieldType()][$this->id_common];
		} else {
			list($user_entry) = mysql_fetch_row(mysql_query("
			SELECT user_entry
			FROM ".$this->_getUserEntryTable()."
			WHERE id_user = '".(int)$id_user."' AND
				id_common = '".(int)$this->id_common."' AND
				id_common_son = '0'"));
		}
		$user_entry = (int)$user_entry;

		if(!isset($this->_tr_field[$this->id_common])) {
			
			$re_field = mysql_query("
			SELECT translation
			FROM ".$this->_getMainTable()."
			WHERE id_common = '".(int)$this->id_common."' AND
				type_field = '".$this->getFieldType()."' AND
				lang_code = '".getLanguage()."'");
			list($translation) = mysql_fetch_row($re_field);
			
			$this->_tr_field[$this->id_common] = $translation;
		} else {
			$translation = $this->_tr_field[$this->id_common];
		}
		
		/*
		$query_tax_country = "
		SELECT id_country, name_country
		FROM ".$this->getCountryTable()." 
		ORDER BY name_country";
		$re_field_element = mysql_query($query_tax_country);
		
		$option = array();
		$option[0] = def('_DROPDOWN_NOVALUE', 'field', 'framework');
		while(list($id_common_son, $element) = mysql_fetch_row($re_field_element)) {
			$option[$id_common_son] = $this->convert_name($element);
		}*/
		$this->_options[0] = def('_DROPDOWN_NOVALUE', 'field', 'framework');

		if($freeze) return Form::getLineBox($translation.' : ', $this->_options[$user_entry]);

		return Form::getDropdown($translation.( $mandatory ? ' <span class="mandatory">*</span>' : '' ),
								'field_'.$this->getFieldType().'_'.$this->id_common,
								'field_'.$this->getFieldType().'['.$this->id_common.']',
								$this->_options,
								(int)$user_entry,
								'',
								'');
	}

	/**
	 * display the field for filters
	 *
	 * @param	string	$field_id		the id of the field used for id/name
	 * @param 	mixed 	$value 			(optional) the value to put in the field
	 *										retrieved from $_POST if not given
	 * @param	string	$label			(optional) the label to use if not given the
	 *									value will be retrieved from custom field
	 *									$id_field
	 * @param	string	$field_prefix 	(optional) the prefix to give to
	 *									the field id/name
	 * @param 	string 	$other_after 	optional html code added after the input element
	 * @param	string 	$other_before 	optional html code added before the label element
	 * @param   mixed 	$field_special	(optional) if is an array the elements are
	 *									the options of dropdown, if is numeric is trated
	 *									as a field id and used to retrieve options
	 *									if not given the elements will be retrieved from
	 *									custom field $id_field
	 *
	 * @return string 	of field xhtml code
	 *
	 * @access public
	 */
	function play_filter( $id_field, $value = FALSE, $label = FALSE, $field_prefix = FALSE, $other_after = '', $other_before = '', $field_special = FALSE ) {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		if( $value === FALSE ) {
			$value = Field::getFieldValue_Filter( $_POST, $id_field, $field_prefix, '0' );
		}

		$option = array();
		$option[0] = def('_DROPDOWN_NOVALUE', 'field');
		if( is_array( $field_special ) ) {
			foreach( $field_special as $key_opt => $label_opt ) {
				$option[$key_opt] = $label_opt;
			}
		} else {

		
			$query_tax_country = "
			SELECT id_country, name_country
			FROM ".Field_Country::getCountryTable()." 
			ORDER BY name_country";
			$re_field_element = mysql_query($query_tax_country);
			
			
			while(list($id_common_son, $element) = mysql_fetch_row($re_field_element)) {
				$option[$id_common_son] = $this->convert_name($element);
			}
		}

		if( $label === FALSE ) {
			$re_field = mysql_query("
			SELECT translation
			FROM ".Field::_getMainTable()."
			WHERE id_common = '".(int)$id_field."'
				AND type_field = '".Field_Country::getFieldType()."'");
			list($label) = mysql_fetch_row($re_field);
		}

		return Form::getDropdown($label,
								Field::getFieldId_Filter($id_field, $field_prefix),
								Field::getFieldName_Filter($id_field, $field_prefix),
								$option,
								$value,
								$other_after,
								$other_before);
	}

	/**
	 * check if the user as selected a valid value for the field
	 *
	 * @return 	bool 	true if operation success false otherwise
	 *
	 * @access public
	 */
	function isFilled( $id_user ) {

		if(!isset($_POST['field_'.$this->getFieldType()][$this->id_common])) return false;
		elseif($_POST['field_'.$this->getFieldType()][$this->id_common] == '0') return false;
		else return true;
	}

	/**
	 * return the filled value of the selected field
	 *
	 * @param 	mixed 	$grab_from 			(optional) the array to retrieve the value from
	 *	($_POST will be used as default)
	 * @param bool $dropdown_val (optional). If true will get the value of a dropdown item instead of its id.
	 *
	 * @return 	bool 	true if operation success false otherwise
	 *
	 * @access public
	 */
	function getFilledVal($grab_from=FALSE, $dropdown_val=FALSE) {

		if ($grab_from === FALSE)
			$grab_from=$_POST;

		if ((!$dropdown_val) && (isset($grab_from['field_'.$this->getFieldType()][$this->id_common])))
			return $grab_from['field_'.$this->getFieldType()][$this->id_common];
		else if (($dropdown_val) && (isset($grab_from['field_'.$this->getFieldType()][$this->id_common]))) {

			
			$query_tax_country = "
			SELECT name_country
			FROM ".Field_Country::getCountryTable()." 
			WHERE id_country = '".$grab_from['field_'.$this->getFieldType()][$this->id_common]."'";
			$re_field_element = mysql_query($query_tax_country);
			
			list($translation) = mysql_fetch_row($query_tax_country);

			return $this->convert_name($translation);
		}
		else
			return NULL;
	}

	/**
	 * store the value inserted by a user into the database, if a entry exists it will be overwrite
	 *
	 * @param	int		$id_user 		the user
	 * @param	int		$no_overwrite 	if a entry exists do not overwrite it
	 *
	 * @return 	bool 	true if operation success false otherwise
	 *
	 * @access public
	 */
	function store( $id_user, $no_overwrite, $int_userid=TRUE ) {

		if (($int_userid) || (empty($id_user)))
			$id_user=(int)$id_user;

		if(!isset($_POST['field_'.$this->getFieldType()][$this->id_common])) return true;
		$re_entry = mysql_query("
		SELECT user_entry
		FROM ".$this->_getUserEntryTable()."
		WHERE id_user = '".$id_user."' AND
			id_common = '".(int)$this->id_common."' AND
			id_common_son = '0'");
		$some_entry = mysql_num_rows($re_entry);
		if($some_entry) {
			if($no_overwrite) return true;
			if(!mysql_query("
			UPDATE ".$this->_getUserEntryTable()."
			SET user_entry = '".$_POST['field_'.$this->getFieldType()][$this->id_common]."'
			WHERE id_user = '".$id_user."' AND
			id_common = '".(int)$this->id_common."' AND
			id_common_son = '0'")) return false;
		} else {

			if(!mysql_query("
			INSERT INTO ".$this->_getUserEntryTable()."
			( id_user, id_common, id_common_son, user_entry ) VALUES
			(	'".$id_user."',
				'".(int)$this->id_common."',
				'0',
				'".$_POST['field_'.$this->getFieldType()][$this->id_common]."')")) return false;
		}

		return true;
	}


	/**
	 * store the value passed into the database, if a entry exists it will be overwrite
	 *
	 * @param	int		$id_user 		the user
	 * @param	int		$value 			the value of the field
	 * @param	bool	$is_id 			if false the param must be reconverted
	 * @param	int		$no_overwrite 	if a entry exists do not overwrite it
	 *
	 * @return 	bool 	true if success false otherwise
	 *
	 * @access public
	 */
	function storeDirect( $id_user, $value, $is_id, $no_overwrite, $int_userid=TRUE ) {

		if (($int_userid) || (empty($id_user)))
			$id_user=(int)$id_user;

		$re_entry = mysql_query("
		SELECT user_entry
		FROM ".$this->_getUserEntryTable()."
		WHERE id_user = '".$id_user."' AND
			id_common = '".(int)$this->id_common."' AND
			id_common_son = '0'");
		$some_entry = mysql_num_rows($re_entry);
		if($some_entry && $no_overwrite) return true;

		$id_value = 0;
		if($is_id === false) {

			if(isset($GLOBALS['temp']['dropdown_value_'.$this->id_common])) {

				// alredy read form database, search in the array
				$index = array_search($value, $GLOBALS['temp']['dropdown_value_'.$this->id_common]);
				if($index === false || $index === NULL) $id_value = 0;
				else {
					$id_value = end(explode('_', $index));
				}
			} else {

				$query_tax_country = "
				SELECT id_country, name_country
				FROM ".Field_Country::getCountryTable()." 
				ORDER BY name_country";
				$re_values = mysql_query($query_tax_country);
				while(list($id_son, $id_common_son, $lang_code, $value_com) = mysql_fetch_row($re_values)) {

					$GLOBALS['temp']['dropdown_value_'.$this->id_common][$lang_code.'_'.$id_common_son] = $value_com;
					if($value_com == $value) $id_value = $id_common_son;
				}
			}
		} else {
			// tha value is the id

			$id_value = $value;
		}

		if($some_entry) {
			if(!mysql_query("
			UPDATE ".$this->_getUserEntryTable()."
			SET user_entry = '".$id_value."'
			WHERE id_user = '".$id_user."' AND
				id_common = '".(int)$this->id_common."' AND
				id_common_son = '0'")) return false;
		} else {

			if(!mysql_query("
			INSERT INTO ".$this->_getUserEntryTable()."
			( id_user, id_common, id_common_son, user_entry ) VALUES
			(	'".$id_user."',
				'".(int)$this->id_common."',
				'0',
				'".$id_value."')")) return false;
		}
		return true;
	}

	// NOTE: special function ---------------------------------------

	function getAllSon() {

		$lang 			=& DoceboLanguage::createInstance('field');

		$sons = array();
		//find available son
		
		$query_tax_country = "
		SELECT id_country, name_country
		FROM ".Field_Country::getCountryTable()." 
		ORDER BY name_country";
		$re_field = mysql_query($query_tax_country);
		
		if(!$re_field) return $sons;
		while(list($id_son, $elem) = mysql_fetch_row($re_field)) {

			$sons[$id_son] = ucfirst(strtolower($elem));
		}
		return $sons;
	}
}

?>