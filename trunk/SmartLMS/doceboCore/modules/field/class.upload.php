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
 * @version  $Id: class.upload.php 1000 2007-03-23 16:03:43Z fabio $
 * @category Field
 * @author   Fabio Pirovano <fabio@docebo.com>
 */

require_once(dirname(__FILE__).'/class.field.php');

class Field_Upload extends Field {

	/**
	 * class constructor
	 */
	function Field_Upload($id_common) {

		parent::Field($id_common);
	}

	/**
	 * this function is useful for field recognize
	 *
	 * @return string	return the identifier of the field
	 *
	 * @access public
	 */
	function getFieldType() {
		return 'upload';
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
			$mand_lang = getLanguage();
			if(isset($_POST['show_on_platform'])) {
				while(list($code, ) = each($_POST['show_on_platform']))
					$show_on .= $code.',';
			}
			//insert mandatory translation
			$mand_lang = getLanguage();

			//control if all is ok
			if(!isset($_POST['new_upload'][$mand_lang])) {
				$out->add(
					getErrorUi($lang->def('_ERR_MUST_DEF_MANADATORY_TRANSLATION'))
					.getBackUi($this->getUrl().'&amp;type_field='
						.$this->getFieldType().'&amp;back='.$back_coded, $std_lang->def('_BACK')),
					'content'
				);
				return;
			}
			if($_POST['new_upload'][$mand_lang] == $lang->def('_NEW_FIELD') || trim($_POST['new_upload'][$mand_lang]) == '') {
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
			('".$this->getFieldType()."', '".$mand_lang."', '".$_POST['new_upload'][$mand_lang]."', '".$show_on."', '".$use_multilang."') ")) {
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
			foreach($_POST['new_upload'] as $lang_code => $translation) {

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
			$form->getFormHeader($lang->def('_NEW_UPLOAD'))
			.$form->openForm('create_'.$this->getFieldType(), $this->getUrl())
			.$form->openElementSpace()
			.$form->getHidden('type_field', 'type_field', $this->getFieldType())
			.$form->getHidden('back', 'back', $back_coded)
		);
		$mand_lang = getLanguage();
		foreach($array_lang as $k => $lang_code ) {

			$out->add(
				$form->getTextfield(( ($mand_lang == $lang_code) ? '<span class="mandatory">*</span>' : '' ).$lang_code,
									'new_upload_'.$lang_code,
									'new_upload['.$lang_code.']',
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
	function edit( $back ) {
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

			//control if all is ok
			if(!isset($_POST['new_upload'][$mand_lang])) {
				$out->add(
					getErrorUi($lang->def('_ERR_MUST_DEF_MANADATORY_TRANSLATION'))
					.getBackUi($this->getUrl().'&amp;type_field='
						.$this->getFieldType().'&amp;back='.$back_coded, $std_lang->def('_BACK')),
					'content'
				);
				return;
			}
			if($_POST['new_upload'][$mand_lang] == $lang->def('_NEW_FIELD') || trim($_POST['new_upload'][$mand_lang]) == '') {
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
			if(isset($_POST['show_on_platform'])) {
				while(list($code, ) = each($_POST['show_on_platform']))
					$show_on .= $code.',';
			}
			//insert other field
			foreach($_POST['new_upload'] as $lang_code => $translation) {

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
			$form->getFormHeader($lang->def('_MODIFY_UPLOAD'))
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
									'new_upload_'.$lang_code,
									'new_upload['.$lang_code.']',
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
	 * display the entry of this field for the passed user
	 *
	 * @param 	int		$id_user 			if alredy exists a enty for the user load it
	 *
	 * @return string 	of field xhtml code
	 *
	 * @access public
	 */
	function show( $id_user ) {
		require_once($GLOBALS['where_framework'].'/lib/lib.mimetype.php');
		
		list($user_entry) = mysql_fetch_row(mysql_query("
		SELECT user_entry
		FROM ".$this->_getUserEntryTable()."
		WHERE id_user = '".(int)$id_user."' AND
			id_common = '".(int)$this->id_common."' AND
			id_common_son = '0'"));

		if($user_entry != '') {
			$entry_link = '<a href="'.$GLOBALS['where_framework_relative'].'/index.php?modname=field&amp;op=manage&amp;fo=special&amp;type_field='.$this->getFieldType().'&amp;id_user='.$id_user.'&amp;id_common='.$this->id_common.'">'
			.'<img src="'.getPathImage().mimeDetect($user_entry).'" alt="'.def('_MIME_TYPE').'" />'
			.'&nbsp;'.def('_DOWNLOAD')
			.'</a>';
		} else {
			$entry_link = def('_NO_FILE_UPLOADED', 'field');
		}

		return $entry_link;
	}

	/**
	 * display the field for interaction
	 *
	 * @param 	int		$id_user	if alredy exists a entry for the user load as default value
	 * @param 	bool	$freeze		if true, disable the user interaction
	 *
	 * @return string 	of field xhtml code
	 *
	 * @access public
	 */
	function play($id_user, $freeze, $mandatory = false) {

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.mimetype.php');

		list($user_entry) = mysql_fetch_row(mysql_query("
		SELECT user_entry
		FROM ".$this->_getUserEntryTable()."
		WHERE id_user = '".(int)$id_user."' AND
			id_common = '".(int)$this->id_common."' AND
			id_common_son = '0'"));

		$re_field = mysql_query("
		SELECT translation
		FROM ".$this->_getMainTable()."
		WHERE lang_code = '".getLanguage()."' AND id_common = '".(int)$this->id_common."' AND type_field = '".$this->getFieldType()."'");
		list($translation) = mysql_fetch_row($re_field);

		if($user_entry != '') {
			$entry_link = '<a href="'.$GLOBALS['where_framework_relative'].'/index.php?modname=field&amp;op=manage&amp;fo=special&amp;type_field='.$this->getFieldType().'&amp;id_user='.$id_user.'&amp;id_common='.$this->id_common.'">'
			.'<img src="'.getPathImage().mimeDetect($user_entry).'" alt="'.def('_MIME_TYPE').'" />'
			.'&nbsp;'.def('_DOWNLOAD')
			.'</a>';
		} else {
			$entry_link = def('_NO_FILE_UPLOADED', 'field', 'framework');
		}

		if($freeze) return Form::getLineBox($translation.' : ', $entry_link);

		return Form::getFilefield($translation.( $mandatory ? ' <span class="mandatory">*</span>' : '' ),
								'field_'.$this->getFieldType().'_'.$this->id_common,
								'field_'.$this->getFieldType().'['.$this->id_common.']',
								'',
								$translation,
								$entry_link );
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
	 * @param   mixed 	$field_special	(optional) not used
	 *
	 * @return string 	of field xhtml code
	 *
	 * @access public
	 */
	function play_filter( $id_field, $value = FALSE, $label = FALSE, $field_prefix = FALSE, $other_after = '', $other_before = '', $field_special = FALSE ) {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$lang =& DoceboLanguage::createInstance('field');

		if( $value === FALSE ) {
			echo "Trallallero: ".Field::getFieldValue_Filter( $_POST, $id_field, $field_prefix, 'false' );
			if( Field::getFieldValue_Filter( $_POST, $id_field, $field_prefix, 'false' ) == 'false' ) {
				$value = 'false';
			} else {
				$value = 'true';
			}
		}

		if( $label === FALSE ) {
			$re_field = mysql_query("
			SELECT translation
			FROM ".Field::_getMainTable()."
			WHERE id_common = '".(int)$id_field."' AND type_field = '".Field_Upload::getFieldType()."'");
			list($label) = mysql_fetch_row($re_field);
		}

		return Form::getRadioSet(	$label,
									Field::getFieldId_Filter($id_field, $field_prefix),
									Field::getFieldName_Filter($id_field, $field_prefix),
									array( 	$lang->def('_YES') => 'true',
											$lang->def('_NO') => 'false') ,
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

		if(!isset($_FILES['field_'.$this->getFieldType()]['tmp_name'][$this->id_common])) return false;
		elseif($_FILES['field_'.$this->getFieldType()]['tmp_name'][$this->id_common] == '') return false;
		else return true;
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
	function store( $id_user, $no_overwrite ) {

		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');

		$path = '/doceboCore/'.$GLOBALS['pathfield'];

		$file = '';
		sl_open_fileoperations();
		if(isset($_FILES['field_'.$this->getFieldType()]['tmp_name'][$this->id_common]) &&
			$_FILES['field_'.$this->getFieldType()]['tmp_name'][$this->id_common] != '') {

			$file = $id_user.'_'.$this->id_common.'_'.time().'_'.$_FILES['field_'.$this->getFieldType()]['name'][$this->id_common];
			if(!sl_upload($_FILES['field_'.$this->getFieldType()]['tmp_name'][$this->id_common], $path.$file)) {
				$error = 1;
				$file = '';
			}
		}
		sl_close_fileoperations();

		if(!isset($_FILES['field_'.$this->getFieldType()]['name'][$this->id_common])) return true;
		$re_entry = mysql_query("
		SELECT user_entry
		FROM ".$this->_getUserEntryTable()."
		WHERE id_user = '".(int)$id_user."' AND
			id_common = '".(int)$this->id_common."' AND
			id_common_son = '0'");
		$some_entry = mysql_num_rows($re_entry);
		list($old_file) = mysql_fetch_row($re_entry);
		if($some_entry) {
			if($no_overwrite) return true;

			sl_unlink($path.$old_file);

			if(!mysql_query("
			UPDATE ".$this->_getUserEntryTable()."
			SET user_entry = '".addslashes($file)."'
			WHERE id_user = '".(int)$id_user."' AND
			id_common = '".(int)$this->id_common."' AND
			id_common_son = '0'")) return false;
		} else {

			if(!mysql_query("
			INSERT INTO ".$this->_getUserEntryTable()."
			( id_user, id_common, id_common_son, user_entry ) VALUES
			(	'".(int)$id_user."',
				'".(int)$this->id_common."',
				'0',
				'".$file."')")) return false;
		}

		return true;
	}


	/**
	 * use only for special operation
	 *
	 * @access public
	 */
	function specialop() {

		require_once($GLOBALS['where_framework'].'/lib/lib.download.php');

		$re_entry = mysql_query("
		SELECT user_entry
		FROM ".$this->_getUserEntryTable()."
		WHERE id_user = '".importVar('id_user', true)."' AND
			id_common = '".(int)$this->id_common."' AND
			id_common_son = '0'");

		list($file) = mysql_fetch_row($re_entry);

		$path = $GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['pathfield'];

		sendFile($path, $file);
		
	}
}

?>