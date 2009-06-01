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
 * @package admin-library
 * @subpackage field
 * @version  $Id:$
 */

define("FIELDTABLE", 			"_field");
define("TYPEFIELDTABLE", 		"_field_type");
define("GROUPFIELDSTABLE", 		"_group_fields");
define("FIELDUSERENTRYTABLE", 		"_field_userentry");

define("FIELD_INFO_ID", 			0);
define("FIELD_INFO_TYPE", 			1);
define("FIELD_INFO_TRANSLATION", 	2);
define("FIELD_INFO_GROUPIDST", 		3);
define("FIELD_INFO_GROUPID", 		4);
define("FIELD_INFO_MANDATORY", 		5);
define("FIELD_INFO_USERACCESS", 	6);

define("FIELD_BASEINFO_FILE", 		0);
define("FIELD_BASEINFO_CLASS", 		1);

class FieldList {

	/** @var string $field_table the main definition field table */
	var $field_table = '';

	/** @var string $type_field_table the fields type definition table */
	var $type_field_table = '';

	/** @var string $group_field_table the fields <-> group relation table */
	var $group_field_table = '';

	/** @var string $field_entry_table the fields value table */
	var $field_entry_table = FALSE;

	/** @var string $use_multi_lang tell to the object if it has to use
   * or not the multi language features
	 */
	var $use_multi_lang = FALSE;

	function getFieldTable() { 			return $this->field_table; }
	function getTypeFieldTable() { 		return $this->type_field_table; }
	function getGroupFieldsTable() { 	return $this->group_field_table; }
	function getFieldEntryTable() { 	return $this->field_entry_table; }

	function setFieldTable( $field_table ) { 				$this->field_table = $field_table; }
	function setTypeFieldTable( $type_field_table ) { 		$this->type_field_table = $type_field_table; }
	function setGroupFieldsTable( $group_field_table ) { 	$this->group_field_table = $group_field_table; }
	function setFieldEntryTable( $field_entry_table ) { 	$this->field_entry_table = $field_entry_table; }

	function FieldList() {
		$this->field_table = $GLOBALS['prefix_fw'].FIELDTABLE;
		$this->type_field_table = $GLOBALS['prefix_fw'].TYPEFIELDTABLE;
		$this->group_field_table = $GLOBALS['prefix_fw'].GROUPFIELDSTABLE;
		$this->field_entry_table = $GLOBALS['prefix_fw'].FIELDUSERENTRYTABLE;
	}

	function &getFieldInstance($id_field, $type_file = false, $type_class = false) {

		if($type_file === false && $type_class === false) {

			$query = "SELECT ft.id_common, tft.type_file, tft.type_class"
					."  FROM ".$this->getFieldTable() ." AS ft"
					."  JOIN ".$this->getTypeFieldTable(). " AS tft"
					." WHERE ft.id_common = '".$id_field."' AND ft.type_field = tft.type_field";
			if(!$rs = mysql_query($query))  {
				$false_var = NULL;
				return $false_var;
			}
			list( $id_common, $type_file, $type_class ) = mysql_fetch_row( $rs );
		} else {

			$id_common = $id_field;
		}
		require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
		$quest_obj = new $type_class( $id_common );

		return $quest_obj;
	}

	function &getFieldInstanceFromString($id_field, $type_file, $type_class) {

		$query = "SELECT ft.id_common, tft.type_file, tft.type_class"
				."  FROM ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft"
				." WHERE ft.id_common = '".$id_field."' AND ft.type_field = tft.type_field";
		if(!$rs = mysql_query($query))  {
			$false_var = NULL;
			return $false_var;
		}

		list( $id_common, $type_file, $type_class ) = mysql_fetch_row( $rs );
		require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
		$quest_obj =  new $type_class( $id_common );

		return $quest_obj;
	}

	/**
	 * @param  string	the content of the field mandatory of the GroupFieldsTable
 	 * @return bool 	true if the field is mandatory
	 **/
	function _mandatoryField($mandatory) {
		return ($mandatory == 'true');
	}


	function getUseMultiLang() {
		return (bool)$this->use_multi_lang;
	}


	function setUseMultiLang($val) {
		$this->use_multi_lang =(bool)$val;
	}


	/**
 	 * @return array array of all fields; index is numeric, value is array with
	 * 				 - idcommon (id_field)
	 *				 -
	 *				 - translation (in current language)
	 **/
	function getAllFields($platform = false, $type_field = false) {

		$query = "SELECT id_common, type_field, translation"
				."  FROM ".$this->getFieldTable()
				." WHERE lang_code = '".getLanguage()."' AND ( 0 ";
		if($platform === false) $platform = array($GLOBALS['platform']);
		foreach($platform as $pl) {
			$query .= " OR show_on_platform LIKE '%".$pl."%'";
		}
		$query .= " ) ";
		if($type_field != false) {
			$query .= " AND type_field = '".$type_field."'";
		}
		$query .= " ORDER BY sequence";
		$rs = mysql_query( $query );
		$result = array();

		while( $arr = mysql_fetch_row($rs) )
			$result[$arr[FIELD_INFO_ID]] = $arr;
		return $result;
	}

	function getFlatAllFields($platform = false, $type_field = false, $lang_code = false) {

		if( $lang_code === false )
			$lang_code = getLanguage();
		$query = "SELECT id_common, type_field, translation"
				."  FROM ".$this->getFieldTable()
				." WHERE lang_code = '".$lang_code."' AND ( 0 ";
		if($platform === false) $platform = array($GLOBALS['platform']);
		foreach($platform as $pl) {
			$query .= " OR show_on_platform LIKE '%".$pl."%'";
		}
		$query .= " ) ";
		if($type_field != false) {
			$query .= " AND type_field = '".$type_field."'";
		}
		$query .= " ORDER BY sequence";
		$rs = mysql_query( $query );
		$result = array();

		while( $arr = mysql_fetch_row($rs) )
			$result[$arr[FIELD_INFO_ID]] = $arr[FIELD_INFO_TRANSLATION];
		return $result;
	}


	/**
 	 * @return array array of fields; index is numeric, value is array with
	 * 				 - idcommon (id_field)
	 *				 -
	 *				 - translation (in current language)
	 **/
	function getFieldsFromArray ($field_list_arr) {

		if ((!is_array($field_list_arr)) || (count($field_list_arr) < 1))
			return FALSE;

		$query = "SELECT id_common, type_field, translation"
				."  FROM ".$this->getFieldTable()
				." WHERE lang_code = '".getLanguage()."' ";

		$query .= "AND id_common IN (".implode(",", $field_list_arr).") ";

		$query .= "ORDER BY sequence";
		$rs = mysql_query( $query );
		$result = array();

		while( $arr = mysql_fetch_row($rs) )
			$result[$arr[FIELD_INFO_ID]] = $arr;
		return $result;
	}


	/**
	 * @param array $arr_idst idst to search
 	 * @return array array of fields that is associated to an idst;
	 *					index is numeric, value is array with
	 * 				 - idcommon (id_field)
	 *				 - translation (in current language)
	 **/
	function getFieldsFromIdst($arr_idst, $use_group = TRUE, $platform = false ) {
		$query = "SELECT ft.id_common, ft.type_field, ft.translation, gft.idst,"
				.($use_group?" g.groupid,":"0,")." gft.mandatory, gft.useraccess"
				."  FROM ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getGroupFieldsTable(). " AS gft"
				.($use_group?("  JOIN ".$GLOBALS['prefix_fw']."_group AS g"):"")
				." WHERE ft.lang_code = '".getLanguage()."'"
				."   AND ft.id_common = gft.id_field"
				.($use_group?("   AND gft.idst = g.idst"):"")
				."   AND gft.idst IN ('".implode("','", $arr_idst)."')"
				." ORDER BY ft.sequence";
		$rs = mysql_query( $query );
		$result = array();
		while( $arr = mysql_fetch_row($rs) )
			$result[$arr[FIELD_INFO_ID]] = $arr;
		return $result;
	}

	/**
	 * return the info and the value of the field assigned to a user
	 * @param int $id_user the idst of the user
	 * @param array $manual_id_field if != false the function filter the field with this and not for the field associated to the user
	 * @param array $filter_category filter for type_category
	 */
	function getFieldsAndValueFromUser($id_user, $manual_id_field = false, $show_invisible_to_user = false, $filter_category = false) {

		$acl = new DoceboACL();
		if($manual_id_field === false)
			$user_groups = $acl->getUserGroupsST($id_user);

		$query = "SELECT ft.id_common, ft.type_field, ftt.type_file, ftt.type_class, ft.translation, gft.mandatory, gft.useraccess "
				."FROM ".$this->getFieldTable()." AS ft "
				."	JOIN ".$this->getGroupFieldsTable()." AS gft "
 				." 	JOIN ".$this->getTypeFieldTable()." AS ftt "
				."WHERE ft.id_common = gft.id_field "
				." 	AND ft.type_field = ftt.type_field "
				." 	AND ft.lang_code = '".getLanguage()."'"
				.( $show_invisible_to_user === false
					? " AND gft.useraccess <> 'readwrite' "
					: "" )
				.( $manual_id_field !== false
					? "  AND ft.id_common IN ('".implode("','", $manual_id_field)."')"
					: "  AND gft.idst IN ('".implode("','", $user_groups)."')" )
				.( $filter_category !== false
					? " AND ftt.type_category IN ( '".implode("','", $filter_category)."' ) "
					: "" )
				."ORDER BY ft.sequence";

		$rs = mysql_query( $query );
		doDebug($query);

		$result = array();
		while(list($id_common, $type_field, $type_file, $type_class, $translation, $mandatory, $useraccess) = mysql_fetch_row($rs)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = new $type_class( $id_common );
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());

			$result[$id_common] = array(
				0 => $translation,
				1 => (!$this->getUseMultiLang() ? $quest_obj->show( $id_user ) : $quest_obj->showInLang( $id_user, getLanguage() )),
				2 => $mandatory,
				3 => $useraccess,
				4 => $type_field,
				5 => $type_file,
				6 => $type_class );
		}

		return $result;
	}

	/**
	 * @param array $arr_idst idst to search
	 * @param int $value_key the required information that has to be filled in the array
	 *        For example FIELD_INFO_ID or FIELD_INFO_TRANSLATION
	 * @return array [field id] => [value required]
	 **/
	function getFieldsArrayFromIdst($arr_idst, $value_key, $use_group = TRUE, $platform = false ) {
		$fields=$this->getFieldsFromIdst($arr_idst, $use_group = TRUE, $platform = false);

		$res=array();
		foreach ($fields as $field) {
			$res[$field[FIELD_INFO_ID]]=$field[$value_key];
		}

		return $res;
	}

	/**
	 * find the value for the fields correlated with all the  user
	 * @param int 	$id_field 		the id of the field
	 *
	 * @return array with the value saved for the users
	 **/
	function getAllFieldEntryData($id_field) {

		$query = "
		SELECT id_user, user_entry
		FROM ".$this->getFieldEntryTable() ."
		WHERE id_common = ".(int)$id_field."";
		$rs = mysql_query( $query );

		$result = array();
		while( list($id, $value) = mysql_fetch_row($rs) )
			$result[$id] = $value;
		return $result;
	}

	/**
	 * find the number of value filled for the field correlated with all the  user
	 * @param int 	$id_field 		the id of the field
	 *
	 * @return array with the value saved for the users
	 **/
	function getNumberOfFieldEntryData($id_field, $exclude_blank = false) {

		$query = "
		SELECT COUNT(*)
		FROM ".$this->getFieldEntryTable() ."
		WHERE id_common = ".(int)$id_field."";
		if($exclude_blank === true) $query .= " AND user_entry <> '' ";
		if(!$rs = mysql_query( $query )) return false;

		list($num) = mysql_fetch_row($rs);
		return $num;
	}
	/**
	 * find the value for the fields correlated with the user
	 * @param int 		$id_user 		the idst f the user
	 * @param array 	$arr_field 		the id of the fields
	 *
	 * @return array with the value saved for the user
	 **/
	function getUserFieldEntryData($id_user, $arr_field) {

		$query = "
		SELECT id_common, user_entry
		FROM ".$this->getFieldEntryTable() ."
		WHERE id_common IN ( ".implode(',', $arr_field)." ) AND id_user = '".$id_user."'";
		$rs = mysql_query( $query );
		
		$result = array();
		while( list($id, $value) = mysql_fetch_row($rs) )
			$result[$id] = $value;
		return $result;
	}

	/**
	 * find the id of the entity that have the given value for the given field
	 * @param int 		$id_field 			the id of the field
	 * @param mixed 	$value_to_check 	the value to check
	 *
	 * @return array with the id of the entity
	 **/
	function getOwnerData($id_field, $value_to_check) {

		$query = "
		SELECT id_user
		FROM ".$this->getFieldEntryTable() ."
		WHERE id_common = '".$id_field."' AND user_entry = '".$value_to_check."'";
		$rs = mysql_query( $query );
		$result = array();
		while( list($owner) = mysql_fetch_row($rs) )
			$result[] = $owner;
		return $result;
	}
	
	/**
	 * find the id of the entity that have the given value for the given field
	 * @param int 		$id_field 			the id of the field
	 * @param mixed 	$value_to_check 	the value to check
	 *
	 * @return array with the id of the entity
	 **/
	function getOwnerDataWithLike($id_field, $value_to_check) {

		$query = "
		SELECT id_user
		FROM ".$this->getFieldEntryTable() ."
		WHERE id_common = '".$id_field."' AND user_entry LIKE '%".$value_to_check."%'";
		$rs = mysql_query( $query );
		$result = array();
		while( list($owner) = mysql_fetch_row($rs) )
			$result[] = $owner;
		return $result;
	}

	/**
	 * return info about a field
	 * @param int $type_field the type of the field
	 * @return array with 0 => type_file 1 => type_class
	 **/
	 function getBaseFieldInfo( $type_field ) {
		 $arr_result = mysql_fetch_row( mysql_query(
		 				"SELECT type_file, type_class "
						." FROM ".$this->getTypeFieldTable()
						." WHERE type_field = '".$type_field."'"));
		return $arr_result;
	 }


	/**
	 * @param int $id_st idst to be associated to the user
	 * @param int $id_field id of the field to get
	 * @param bool $freeze TRUE to get static text, false to get input control
 	 * @return html with the form code for play a set of fields
	 **/
	function showFieldForUser($idst_user, $id_field) {

		$query = "SELECT tft.type_file, tft.type_class"
				."  FROM ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft"
				." WHERE ft.type_field = tft.type_field"
				."   AND ft.id_common = '".$id_field."'"
				." ORDER BY ft.sequence";

		$rs = mysql_query($query);
		doDebug($query);
		if( mysql_num_rows($rs) < 1 )
			return 'NULL';
		list( $type_file, $type_class ) = mysql_fetch_row( $rs );
		require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
		$quest_obj = eval("return new $type_class( $id_field );");
		if( $this->field_entry_table !== FALSE )
			$quest_obj->setFieldEntryTable($this->field_entry_table);

		$quest_obj->setMainTable($this->getFieldTable());
		if (!$this->getUseMultiLang()) {
			return $quest_obj->show( $idst_user );
		}
		else {
			return $quest_obj->showInLang( $idst_user, getLanguage() );
		}
	}


	 /**
	 * @param int 		$idst_user 	idst to be associated to the user
	 * @param array 	$arr_field 	optional you can filter the field to show
 	 * @return html with the info about yhe field for the user passed
	 **/
	 function showAllFieldForUser($idst_user, $arr_field = false) {

		$acl =& $GLOBALS['current_user']->getACL();
		$arr_idst = $acl->getUserGroupsST($idst_user);

		$acl_man =& $acl->getAclManager();
		$tmp = $acl_man->getGroup( false, '/oc_0' );
		$arr_idst[] = $tmp[0];
		$tmp = $acl_man->getGroup( false, '/ocd_0' );
		$arr_idst[] = $tmp[0];

		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class, gft.mandatory"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				."  JOIN ".$this->getGroupFieldsTable(). " AS gft"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common = gft.id_field"
				."   AND gft.idst IN ('".implode("','", $arr_idst)."')"
				.( $arr_field !== false && is_array($arr_field) && !empty($arr_field)
					? " AND ft.id_common IN ('".implode("','", $arr_field)."') "
					: "" )
				." GROUP BY ft.id_common "
				." ORDER BY ft.sequence, gft.id_field";

		$play_txt = '';
		$re_fields = mysql_query($query);
		if(!mysql_num_rows($re_fields)) return '';
		doDebug($query);
		while(list($id_common, $type_field, $type_file, $type_class, $mandatory) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
			if (!$this->getUseMultiLang()) {
				$play_txt .= $quest_obj->show( $idst_user );
			}
			else {
				$play_txt .= $quest_obj->showInLang( $idst_user, getLanguage() );
			}
		}
		return $play_txt;
	 }


	function getAllFieldValue($id_common) {
		$query = "SELECT ft.id_common, tft.type_file, tft.type_class"
				."  FROM ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft"
				." WHERE ft.type_field = tft.type_field"
				." AND ft.id_common = '".$id_common."'"
				." ORDER BY ft.sequence";

		$res=array();
		$rs = mysql_query($query);
		if(!$rs)
			return $res;
		doDebug($query);
		if( mysql_num_rows($rs) < 1 ){
			return $res;
		}
		list( $id_common, $type_file, $type_class ) = mysql_fetch_row( $rs );
			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
		return $quest_obj->getSon();

	}


	/**
	 * @param array $idst_user_arr idst to be associated to the user
	 * @param int $id_field_arr id of the field to get
 	 * @return array with values for the specified fields for each user
 	 * 	array[user_idst][field_idcommon]=field_value
 	 * 	you can find an usage example in /lib/lib.usernotifier.php
	 **/
	function showFieldForUserArr($idst_user_arr, $id_field_arr) {

		$query = "SELECT ft.id_common, tft.type_file, tft.type_class"
				."  FROM ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft"
				." WHERE ft.type_field = tft.type_field"
				."   AND ft.id_common IN (".implode(",", $id_field_arr).")"
				." ORDER BY ft.sequence";

		$res=array();

		$rs = mysql_query($query);
		if($rs == false)
			return 'NULL';
		doDebug($query);
		if( mysql_num_rows($rs) < 1 )
			return 'NULL';

		while(list( $id_common, $type_file, $type_class ) = mysql_fetch_row( $rs )) {
			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());

			$lang =getLanguage();
			foreach($idst_user_arr as $idst_user) {
				if (!$this->getUseMultiLang()) {
					$res[$idst_user][$id_common]=$quest_obj->show( $idst_user );
				}
				else {
					$res[$idst_user][$id_common]=$quest_obj->showInLang( $idst_user, $lang );
				}
			}
		}

		return $res;
	}

	/**
	 * @param array $idst_user_arr idst to be associated to the user
	 * @param int $id_field_arr id of the field to get
 	 * @return array with values for the specified fields for each user
 	 * 	array[user_idst][field_idcommon]=field_value
 	 * 	you can find an usage example in /lib/lib.usernotifier.php
	 **/
	function fieldValue($id_field, $idst_user_arr) {

		$query = "SELECT ft.id_common, tft.type_file, tft.type_class"
				."  FROM ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft"
				." WHERE ft.type_field = tft.type_field"
				."   AND ft.id_common = '".$id_field."'"
				." ORDER BY ft.sequence";

		$res=array();

		$rs = mysql_query($query);
		if($rs == false)
			return 'NULL';
		doDebug($query);
		if( mysql_num_rows($rs) < 1 )
			return 'NULL';

		list( $id_common, $type_file, $type_class ) = mysql_fetch_row( $rs );
			
			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
		$quest_obj = new $type_class( $id_common );
		if( $this->field_entry_table !== FALSE )
			$quest_obj->setFieldEntryTable($this->field_entry_table);

		$quest_obj->setMainTable($this->getFieldTable());

		$lang =getLanguage();
		foreach($idst_user_arr as $idst_user) {
			if (!$this->getUseMultiLang()) {
				$res[$idst_user]=$quest_obj->show( $idst_user );
			}
			else {
				$res[$idst_user]=$quest_obj->showInLang( $idst_user, $lang );
			}
		}

		return $res;
	}


	/**
	 * @param int $id_st idst to be associated to the user
	 * @param int $id_field id of the field to get
	 * @param bool $freeze TRUE to get static text, false to get input control
	 * @param bool $mandatory Specified if the field is a mandatory one or not.
 	 * @return html with the form code for play a set of fields
	 **/
	function playFieldForUser($idst_user, $id_field, $freeze, $mandatory=FALSE) {

		$query = "SELECT tft.type_file, tft.type_class"
				."  FROM ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft"
				." WHERE ft.type_field = tft.type_field"
				."   AND ft.id_common = '".$id_field."'"
				." ORDER BY ft.sequence";

		$rs = mysql_query($query);
		doDebug($query);
		if( mysql_num_rows($rs) < 1 )
			return 'NULL';
		list( $type_file, $type_class ) = mysql_fetch_row( $rs );
		require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
		$quest_obj = eval("return new $type_class( $id_field );");
		if( $this->field_entry_table !== FALSE )
			$quest_obj->setFieldEntryTable($this->field_entry_table);

		$quest_obj->setMainTable($this->getFieldTable());
		if (!$this->getUseMultiLang()) {
			return $quest_obj->play( $idst_user, $freeze, $mandatory );
		}
		else {
			return $quest_obj->multiLangPlay( $idst_user, $freeze, $mandatory );
		}
	}


	/**
	 * @param int $id_st idst to be associated to the user
	 * @param array $arr_idst (optional) array of idst of groups
	 *					if this parameter is skipped the groups will be taken
	 *					from $idst_user
 	 * @return html with the form code for play a set of fields
	 **/
	function playFieldsForUser($idst_user, $arr_idst = FALSE, $freeze = FALSE, $add_root = TRUE, $useraccess = FALSE ) {

		$acl =& $GLOBALS['current_user']->getACL();
		if( $arr_idst === FALSE )
			$arr_idst = $acl->getUserGroupsST($idst_user);

		if( $add_root ) {
			$acl_man =& $acl->getAclManager();
			$tmp = $acl_man->getGroup( false, '/oc_0' );
			$arr_idst[] = $tmp[0];
			$tmp = $acl_man->getGroup( false, '/ocd_0' );
			$arr_idst[] = $tmp[0];
		}

		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class, gft.mandatory"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				."  JOIN ".$this->getGroupFieldsTable(). " AS gft"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common = gft.id_field"
				."   AND gft.idst IN ('".implode("','", $arr_idst)."')";

		if($useraccess !== 'false' && is_array($useraccess)) {
			$query .= " AND ( ";
			$first = true;
			foreach($useraccess AS $k => $v) {
				if(!$first) $query .= " OR ";
				else $first = false;
				$query .= " gft.useraccess = '".$v."' ";
			}
			$query .=" ) ";
		}
		$query .=" GROUP BY ft.id_common "
				." ORDER BY ft.sequence, gft.idst, gft.id_field";

		$play_txt = '';
		$re_fields = mysql_query($query);
		doDebug($query);

		if(!mysql_num_rows($re_fields)) return '';
		while(list($id_common, $type_field, $type_file, $type_class, $mandatory) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
			if (!$this->getUseMultiLang()) {
				$play_txt .= $quest_obj->play( $idst_user, $freeze, $this->_mandatoryField($mandatory) );
			}
			else {
				$play_txt .= $quest_obj->multiLangPlay( $idst_user, $freeze, $this->_mandatoryField($mandatory) );
			}
		}
		return $play_txt;
	}
	
	/**
	 * @param array $idst_user_arr idst to be associated to the user
	 * @param int $id_field_arr id of the field to get
 	 * @return array with values for the specified fields for each user
 	 * 	array[user_idst][field_idcommon]=field_value
 	 * 	you can find an usage example in /lib/lib.usernotifier.php
	 **/
	function hiddenFieldForUserArr($idst_user, $arr_idst = FALSE, $freeze = FALSE, $add_root = TRUE, $useraccess = FALSE) {

		$acl =& $GLOBALS['current_user']->getACL();
		if( $arr_idst === FALSE )
			$arr_idst = $acl->getUserGroupsST($idst_user);

		if( $add_root ) {
			$acl_man =& $acl->getAclManager();
			$tmp = $acl_man->getGroup( false, '/oc_0' );
			$arr_idst[] = $tmp[0];
			$tmp = $acl_man->getGroup( false, '/ocd_0' );
			$arr_idst[] = $tmp[0];
		}

		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class, gft.mandatory"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				."  JOIN ".$this->getGroupFieldsTable(). " AS gft"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common = gft.id_field"
				."   AND gft.idst IN ('".implode("','", $arr_idst)."')";

		if($useraccess !== 'false' && is_array($useraccess)) {
			$query .= " AND ( ";
			$first = true;
			foreach($useraccess AS $k => $v) {
				if(!$first) $query .= " OR ";
				else $first = false;
				$query .= " gft.useraccess = '".$v."' ";
			}
			$query .=" ) ";
		}
		$query .=" GROUP BY ft.id_common "
				." ORDER BY ft.sequence, gft.idst, gft.id_field";

		$play_txt = '';
		$re_fields = mysql_query($query);
		doDebug($query);


		while(list($id_common, $type_field, $type_file, $type_class, $mandatory) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());

			$play_txt .= $quest_obj->get_hidden_filled( false, false );




		}

		return $play_txt;
	}
	
	/**
	 * @param int $id_st idst to be associated to the user
	 * @param array $arr_idst (optional) array of idst of groups
	 *					if this parameter is skipped the groups will be taken
	 *					from $idst_user
 	 * @return TRUE if all the mandatory field is filled and all field is valid, an array with the error messsage
	 **/
	function isFilledFieldsForUser($idst_user, $arr_idst = FALSE ) {

		$acl =& $GLOBALS['current_user']->getACL();
		if( $arr_idst === FALSE )
			$arr_idst = $acl->getUserGroupsST($idst_user);
		$acl_man =& $acl->getAclManager();
		$tmp = $acl_man->getGroup( false, '/oc_0' );
		$arr_idst[] = $tmp[0];
		$tmp = $acl_man->getGroup( false, '/ocd_0' );
		$arr_idst[] = $tmp[0];

		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class, gft.mandatory"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				."  JOIN ".$this->getGroupFieldsTable(). " AS gft"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common = gft.id_field"
				."   AND gft.idst IN ('".implode("','", $arr_idst)."')"
				." GROUP BY ft.id_common ";

		doDebug($query);

		$error_message = array();

		$mandatory_filled 	= true;
		$field_valid 		= true;
		$re_fields 			= mysql_query($query);
		while(list($id_common, $type_field, $type_file, $type_class, $is_mandatory) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = new $type_class( $id_common );

			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());

			if(!$quest_obj->isValid( $idst_user )) {

				$error_text = $quest_obj->getLastError();
				if($error_text !== false) $error_message[] = str_replace('[field_name]', $quest_obj->getFieldName(), $error_text);
				else $error_message[] = str_replace('[field_name]', $quest_obj->getFieldName(), def('_FIELD_VALUE_NOT_VALID', 'field', 'framework'));

			} elseif($is_mandatory == 'true' && !$quest_obj->isFilled( $idst_user ) ) {

				$error_text = $quest_obj->getLastError();
				if($error_text !== false) $error_message[] = str_replace('[field_name]', $quest_obj->getFieldName(), $error_text);
				else $error_message[] = str_replace('[field_name]', $quest_obj->getFieldName(), def('_MANDATORY_NOT_FILLED', 'field', 'framework'));
			}
		}
		if(empty($error_message)) return true;
		return $error_message;
	}

	/**
	 * @param int $id_st idst to be associated to the user
	 * @param array $arr_idst (optional) array of idst of groups
	 *					if this parameter is skipped the groups will be taken
	 *					from $idst_user
 	 * @return TRUE if success, FALSE otherwise
	 **/
	function storeFieldsForUser($idst_user, $arr_idst = FALSE, $add_root = TRUE, $int_userid=TRUE ) {

		$acl =& $GLOBALS['current_user']->getACL();
		if( $arr_idst === FALSE )
			$arr_idst = $acl->getUserGroupsST($idst_user);
		if( $add_root ) {
			$acl_man =& $acl->getAclManager();
			$tmp = $acl_man->getGroup( false, '/oc_0' );
			$arr_idst[] = $tmp[0];
			$tmp = $acl_man->getGroup( false, '/ocd_0' );
			$arr_idst[] = $tmp[0];
		}
		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				."  JOIN ".$this->getGroupFieldsTable(). " AS gft"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common = gft.id_field"
				."   AND gft.idst IN ('".implode("','", $arr_idst)."')"
				." GROUP BY ft.id_common ";

		$save_result = true;
		$re_fields = mysql_query($query);
		while(list($id_common, $type_field, $type_file, $type_class) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
			if (!$this->getUseMultiLang()) {
				$save_result &= $quest_obj->store( $idst_user, false, $int_userid );
			}
			else {
				$save_result &= $quest_obj->multiLangStore( $idst_user, false, $int_userid );
			}
		}
		return $save_result;
	}

	/**
	 * @param int $id_st idst to be associated to the user
	 * @param array array of fields to be set idfield=>value
	 * @param bool $is_id if true will consider the passed data as the field id;
	 *                    else the value is taken and reconverted to the id
 	 * @return TRUE if success, FALSE otherwise
	 **/
	function storeDirectFieldsForUser($idst_user, $arr_fields, $is_id = FALSE, $int_userid=TRUE) {

		$acl =& $GLOBALS['current_user']->getACL();

		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common IN ('".implode("','", array_keys($arr_fields))."')"
				." GROUP BY ft.id_common ";

		$save_result = true;
		$re_fields = mysql_query($query);
		if( $re_fields === FALSE ) {
			doDebug($query);
		}
		while(list($id_common, $type_field, $type_file, $type_class) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
			if (!$this->getUseMultiLang()) {
				$save_result &= $quest_obj->storeDirect( $idst_user, $arr_fields[$id_common], $is_id, FALSE, $int_userid );
			}
			else {
				$save_result &= $quest_obj->multiLangStoreDirect( $idst_user, $arr_fields[$id_common], $is_id, FALSE, $int_userid );
			}
		}
		return $save_result;
	}


	/**
	 * @param array $arr_field
	 * @param array $custom_mandatory (optional)
	 *
 	 * @return html with the form code for play a set of specified fields
	 **/
	function playSpecFields($arr_field, $custom_mandatory=FALSE, $user_id=FALSE) {

		$acl =& $GLOBALS['current_user']->getACL();

		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
//				."  JOIN ".$this->getGroupFieldsTable(). " AS gft"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
//				."   AND ft.id_common = gft.id_field"
				."   AND ft.id_common IN ('".implode("','", $arr_field)."')";

		$query .=" GROUP BY ft.id_common ";
//				." ORDER BY ft.sequence";

		if (($user_id === FALSE) || (empty($user_id))) {
			$user_id=-1;
		}

		$play_txt = '';
		$play_arr=array();
		$re_fields = mysql_query($query);
		doDebug($query);
		while(list($id_common, $type_field, $type_file, $type_class) = mysql_fetch_row($re_fields)) {

			if ((isset($custom_mandatory[$id_common])) && ($custom_mandatory[$id_common]))
				$mandatory=true;
			else
				$mandatory=false;


			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
			if (!$this->getUseMultiLang()) {
				$play_arr[$id_common] = $quest_obj->play( $user_id, false, $this->_mandatoryField($mandatory) );
			}
			else {
				$play_arr[$id_common] = $quest_obj->multiLangPlay( $user_id, false, $this->_mandatoryField($mandatory) );
			}
		}

		// This way we get it in the order passed in the $arr_field array:
		foreach($arr_field as $key=>$val) {
			if (isset($play_arr[$val]))
				$play_txt.=$play_arr[$val];
		}

		return $play_txt;
	}


	function playFilters($arr_field, $values, $field_prefix=FALSE) {
		$res="";

		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common IN ('".implode("','", $arr_field)."')";
		$query .=" GROUP BY ft.id_common ";

		$re_fields = mysql_query($query);
		while(list($id_common, $type_field, $type_file, $type_class) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
			$value=(isset($values[$id_common]) ? $values[$id_common] : FALSE);
			$res.=$quest_obj->play_filter($id_common, $value, FALSE, $field_prefix);
		}

		return $res;
	}


	/**
	 * @param array $arr_field array of field id that are mandatory
 	 *
 	 * @return TRUE if all the mandatory field is filled, FALSE otherwise
	 **/
	function isFilledSpecFields($arr_field) {

		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common IN ('".implode("','", $arr_field)."')"
				." GROUP BY ft.id_common ";

		$save_result = true;
		$re_fields = mysql_query($query);
		while(list($id_common, $type_field, $type_file, $type_class) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
			$save_result &= $quest_obj->isFilled( -1 );
		}
		return $save_result;
	}


	/**
	 * @param array $arr_field
	 * @param array $grab_form (optional)
	 * @param bool $dropdown_val (optional). If true will get the value of a dropdown item instead of its id.
 	 *
 	 * @return array with the filled value of the specified fields
	 **/
	function getFilledSpecVal($arr_field, $grab_from=false, $dropdown_val=false) {

		if ($grab_from === FALSE)
			$grab_from=$_POST;

		$query = "SELECT ft.id_common, ft.translation, ft.type_field, tft.type_file, tft.type_class"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common IN ('".implode("','", $arr_field)."')"
				." GROUP BY ft.id_common ";

		$filled_val=array();
		$re_fields = mysql_query($query);
		while(list($id_common, $translation, $type_field, $type_file, $type_class) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
			$filled_val[$id_common]["description"]=$translation;

			if ($type_field == "dropdown")
				$filled_val[$id_common]["value"]=$quest_obj->getFilledVal( $grab_from, $dropdown_val );
			else
				$filled_val[$id_common]["value"]=$quest_obj->getFilledVal( $grab_from );
		}

		return $filled_val;
	}


	/**
	 * @param int $id_field
	 * @param array $associate_owner if true the owner of the data is associated
 	 *
 	 * @return array with the stored value of the specific field
	 **/
	function getAllStoredValue($id_field, $associate_owner = false) {

		$query = "
		SELECT DISTINCT user_entry ".( $associate_owner === true ? ", id_user" : '' )."
		FROM ".$this->getFieldEntryTable() ."
		WHERE id_common = '".$id_field."'";
		$rs = mysql_query( $query );

		$result = array();
		while($data = mysql_fetch_row($rs)){

			if($associate_owner === true)  $result[$data[1]] = $data[0];
			else $result[] = $data[0];
		}
		return $result;
	}


	/**
	 * @param int $id_field id of the field to be associated to $id_st
	 * @param int $id_st idst to be associated to field
 	 * @return TRUE if success, FALSE otherwise
	 **/
	function addFieldToGroup($id_field, $idst, $mandatory = 'false', $useraccess = 'readonly' ) {
		$query = "SELECT idst FROM ".$this->getGroupFieldsTable()
				." WHERE idst = '".$idst."' AND id_field = '".$id_field."'";
		$rs = mysql_query( $query );
		if( mysql_num_rows( $rs ) > 0 ) {
			$query = "UPDATE ".$this->getGroupFieldsTable()
					." SET idst = '".(int)$idst."',"
					."     id_field = '".(int)$id_field."',"
					."     mandatory = '".$mandatory."',"
					."     useraccess = '".$useraccess."'"
					." WHERE idst = '".$idst."' AND id_field = '".$id_field."'";
		} else {
			$query = "INSERT INTO ".$this->getGroupFieldsTable()
					." (idst, id_field, mandatory, useraccess) "
					." VALUES ('".(int)$idst."','".(int)$id_field."',"
					."'".$mandatory."','".$useraccess."')";
		}
		return mysql_query( $query );
	}

	/**
	 * @param int $id_field id of the field to be removed from $id_st
	 * @param int $id_st idst to be removed to field
 	 * @return TRUE if success, FALSE otherwise
	 **/
	function removeFieldFromGroup($id_field, $idst) {
		$query = "DELETE FROM ".$this->getGroupFieldsTable()
				." WHERE idst = '".(int)$idst."'"
				."   AND id_field = '".(int)$id_field."'";
		return mysql_query( $query );
	}

	/**
	 * @param int 	$idst_user 	the user
	 * @param int 	$id_group 	cast the delete action only to the field of this group
	 * @param array $arr_fields cast the delete action only to the field specified
 	 * @return TRUE if success, FALSE otherwise
	 **/
	function removeUserEntry($idst_user, $id_group = FALSE, $arr_field = FALSE) {

		$save_result = true;
		$arr_idst = array();
		if( $arr_field !== FALSE ) {

			$to_remove =& $arr_field;
		} elseif( $id_group !== FALSE ) {

			$acl =& $GLOBALS['current_user']->getACL();
			$allgroup_idst = $acl->getUserGroupsST($idst_user);
			// Leave the passed group
			$inc_group = array_search($id_group, $allgroup_idst);
			unset($allgroup_idst[$inc_group]);

			if(!empty($allgroup_idst)) {
				$query = "SELECT gft.id_field "
						."  FROM ".$this->getGroupFieldsTable(). " AS gft"
						." WHERE gft.idst IN ('".implode("','", $allgroup_idst)."')";
				$rs = mysql_query( $query );$result = array();
				while( list($id) = mysql_fetch_row($rs) )
					$all_field[$id] = $id;
			}
			$query = "SELECT gft.id_field "
					."  FROM ".$this->getGroupFieldsTable(). " AS gft"
					." WHERE gft.idst = '".$id_group."'";
			$rs = mysql_query( $query );
			$to_remove = array();
			while( list($id) = mysql_fetch_row($rs) ) {
				if(!isset($all_field[$id])) $to_remove[] = $id;
			}
		}
		if(empty($to_remove)) return $save_result;

		$query = "SELECT ft.id_common, ft.type_field, tft.type_file, tft.type_class"
				."  FROM ( ".$this->getFieldTable() ." AS ft"
				."  JOIN ".$this->getTypeFieldTable(). " AS tft )"
				."  JOIN ".$this->getGroupFieldsTable(). " AS gft"
				." WHERE ft.lang_code = '".getLanguage()."'"
				."	 AND ft.type_field = tft.type_field"
				."   AND ft.id_common = gft.id_field"
				."   AND gft.idst IN ('".implode("','", $to_remove)."')"
				." GROUP BY ft.id_common ";

		$re_fields = mysql_query($query);
		while(list($id_common, $type_field, $type_file, $type_class) = mysql_fetch_row($re_fields)) {

			require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
			$quest_obj = eval("return new $type_class( $id_common );");
			if( $this->field_entry_table !== FALSE )
				$quest_obj->setFieldEntryTable($this->field_entry_table);

			$quest_obj->setMainTable($this->getFieldTable());
			$save_result &= $quest_obj->deleteUserEntry( $idst_user );
		}
		return $save_result;
	}


	/**
	 * Find wich users entries matches with search information
	 * @author Giovanni Derks
	 *
	 * @param array $fields list of id_common values
	 * @param string $method "OR" or "AND".
	 * @param array $like array($id_common => [off, both, start, end])
	 * @param array $search array($id_common => $what_to_search)
	 * @param bool $return_raw if TRUE then will return the raw array
	 *
	 * @return array list of user idst found (if $return_raw is FALSE)
	 */
	function quickSearchUsersFromEntry($fields, $method, $like, $search, $return_raw=FALSE) {
		$res=array();


		if (($GLOBALS['do_debug'] == 'on') && (count($fields) != count($search))) {
			echo "<b>Warning</b>: (lib.field.php) ";
			echo "Please make sure that the search array have the same size of the fields one.<br />";
		}

		// -------------------------

		$qtxt ="SELECT * FROM ".$this->getFieldEntryTable()." ";
		$qtxt.="WHERE id_common IN (".implode(",", $fields).") AND (";

		$where_arr=array();
		foreach($fields as $id_common) {

			$where ="";

			if (isset($search[$id_common])) {

				$where.="(id_common='".$id_common."' AND user_entry ";

				$search_val=$search[$id_common];

				if ((!isset($like[$id_common])) || ($like[$id_common] == "off")) {
					$where.="='".$search_val."'";
				}
				else if ($like[$id_common] == "both") {
					$where.=" LIKE '%".$search_val."%'";
				}
				else if ($like[$id_common] == "start") {
					$where.=" LIKE '%".$search_val."'";
				}
				else if ($like[$id_common] == "end") {
					$where.=" LIKE '".$search_val."%'";
				}

				$where.=")";

				$where_arr[]=$where;
			}
		}

		$qtxt.=implode(" OR ", $where_arr);

		$qtxt.=")";

		// -------------------------

		$q=mysql_query($qtxt);

		$raw_res=array();
		$raw_res["field"]=array();
		$raw_res["user"]=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {

				$id_common=$row["id_common"];
				$id_user=$row["id_user"];

				// ----------------------------------------------------------

				if (!isset($raw_res[$id_common]))
					$raw_res["field"][$id_common]=array();

				if (!in_array($id_user, $raw_res["field"][$id_common]))
					$raw_res["field"][$id_common][]=$id_user;

				// ----------------------------------------------------------

				if (!isset($raw_res["user"][$id_user]))
					$raw_res["user"][$id_user]=array();

				if (!in_array($id_common, $raw_res["user"][$id_user]))
					$raw_res["user"][$id_user][]=$id_common;

				// ----------------------------------------------------------

				if (($method == "OR") && (!in_array($row["id_user"], $res)))
					$res[]=$row["id_user"];

			}
		}


		if ($return_raw) {
			return $raw_res;
		}
		else if ($method == "AND") {

			$tot=count($fields);
			foreach($raw_res["user"] as $user_id=>$field_arr) {

				$tot_found=count($field_arr);
				if (($tot_found > 0) && ($tot_found == $tot)) {
					$res[]=$user_id;
				}
			}

		}


		return $res;
	}
	
	function getFieldIdCommonFromTranslation($translation) {
		
		$query = "SELECT id_common" .
			" FROM ".$this->getFieldTable()."" .
			" WHERE translation LIKE '".$translation."'";
  
		list($res) = mysql_fetch_row(mysql_query($query));
  
  		return $res;
	}

}
?>
