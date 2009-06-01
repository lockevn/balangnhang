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
 * @package admin-library
 * @subpackage language
 * @version $Id:$
 */
 
/** Global unique instance of the translation associative array
	any element in array: key is the triple (platform,module,key) composition,
	value is an array with key = lang_code, value = the current translation
*/
$globTranslation = array();

/** Global unique instance of the language loaded modules
 *	any element in array is indexed by pair (platform,module) and is an array
 *	of language code
 */
$globLangModule = array();

/** Global unique instance of the DoceboLanguage associative array
 *	any element in the array is indexed by platform module lang concatenation
 *	with & character
 *	'lms&advice&en_std' => <instance of DoceboLanguage>
 **/
$globDoceboLanguageInstances = array();

/** Global unique instance of the globals loaded modules associative array
 *  any element in the array is indexed by platform module lang
 *  with & character
 *  'lms&advice' => ''
 */
$globDoceboLanguageGlobalInstances = array();

/**
 * @internal
 * Define for language table names
 **/
define("DOCEBO_LANGUAGE_text","_lang_text");
//define("DOCEBO_LANGUAGE_text_translation","_lang_text_translation");
define("DOCEBO_LANGUAGE_translation","_lang_translation");
define("DOCEBO_LANGUAGE_language","_lang_language");

/**
 * Language management classes
 *
 *
 * @package  DoceboCore
 * @version  $Id: lib.lang.php 1000 2007-03-23 16:03:43Z fabio $
 * @category Language
 * @author   Emanuele Sandri <esandri@tiscali.it>
 */
class DoceboLanguage {
	var $langCode;
	var $module;
	var $platform;
	var $globTranslation;

	/**
	 * DoceboLanguage constructor
	 * @param string $module module
	 * @param string $lang_code code of language
	 */
	function DoceboLanguage($module, $lang_code, $platform ) {
		global $prefix_fw, $globTranslation, $globLangModule, $globLangManager;
		$this->prefix = $prefix_fw;
		$this->lang_code = $lang_code;
		$this->module = $module;
		$this->platform = $platform;
		$this->globTranslation = &$globTranslation;

		$plafmod = DoceboLangManager::composeKey( $module, $platform );
		if( !isset($globLangModule[$plafmod]) )
			$globLangModule[$plafmod] = array();
		$globLangModule[$plafmod][] = $lang_code;

		$arrTranslations = $globLangManager->getModuleLangTranslations($platform, $module, $lang_code);

		// fill in the associative array of keys
		foreach( $arrTranslations as $arr_elem ) {
			$composedKey = DoceboLangManager::composeKey($arr_elem[1],$arr_elem[0],$platform);
			if( !isset($this->globTranslation[$composedKey]) )
				$this->globTranslation[$composedKey] = array();
			$this->globTranslation[$composedKey][$lang_code] = $arr_elem[2];
		}
	}

	/**
	 * Create an instance of DoceboLanguage
	 * @static
	 * @param string $module the name of the module. If not assigned the module
	 *			will be computed from $_GET['module']
	 * @param string $platform the platform of the module. If not assigned the
	 *			platform will be computed with $GLOBAL['platform']
	 * @param string $lang_code the code of the language. If not assigned the
	 *			lang_code will be computed with getLanguage()
	 * @return DoceboLanguage an instance of DoceboLanguage or FALSE.
	 **/
	function &createInstance( $module = FALSE, $platform = FALSE, $lang_code = FALSE ) {
		global $globDoceboLanguageInstances;
		if( $module === FALSE )	$module = importVar('modname');
		if( $platform === FALSE ) $platform = $GLOBALS['platform'];
		if( $lang_code === FALSE ) $lang_code = getLanguage();	// from manTemplateLanguage.php

		$composedKey = DoceboLangManager::composeKey($lang_code,$module,$platform);
		if( !isset( $globDoceboLanguageInstances[$composedKey]) )
			$globDoceboLanguageInstances[$composedKey] = new DoceboLanguage($module, $lang_code, $platform);
		return $globDoceboLanguageInstances[$composedKey];
	}

	/**
	 * Set this platform,module as global
	 **/
	function setGlobal() {
		global $globDoceboLanguageGlobalInstances;
		$composedKey = DoceboLangManager::composeKey($this->module,$this->platform);
		$globDoceboLanguageGlobalInstances[$composedKey] = $composedKey;
	}

	/**
	 * @internal
	 * @static
	 * Return a string for the undefined message. This function will be used to
	 *	print out special tags and javascript to implement auto-controller
	 *	feature
	 * @param string $key key of the text within module
	 * @param string $module name of the module
	 * @param string $platform name of the platform
	 * @param string $lang_code code of the language
	 * @return string text to signal error/missed translation
	 **/
	function _undefinedKey($key, $module, $platform, $lang_code = FALSE) {
		if( isset($GLOBALS['lang_hide_edit']) && $GLOBALS['lang_hide_edit'] == '1' ) return;
		if( $module === FALSE )	$module = $this->module;
		if( $platform === FALSE ) $platform = $this->platform;


		if( $lang_code === FALSE ) {
			$out_debug = "Undefined language key: ".$key
				." for module ".$module
				." in platform ".$platform;

			} else
				$out_debug = "Undefined language key: ".$key
					." for module ".$module
					." in platform ".$platform
					." with lang_code=".$lang_code;

		if($GLOBALS['do_debug'] == 'on') {

			$out=$out_debug;
			addYahooJs();
			if(isset($GLOBALS['page'])) $GLOBALS['page']->add( '<a href="#" onclick="w = window.open(\''
					.( $GLOBALS['where_framework_relative'] != '' ? $GLOBALS['where_framework_relative'].'/' : '' )
					.'index.php?modname=lang&amp;op=translator&amp;tranm=english'
					.'&amp;modulef='.$module.'&amp;key='.$key.'&amp;platformf='.$platform.'&amp;fastadd\',\'liblang\'); w.focus(); return false;">Define '.$key.' in module '.$module.'</a>',
					'def_lang' );
			else echo '<a href="#" onclick="w = window.open(\''
					.( $GLOBALS['where_framework_relative'] != '' ? $GLOBALS['where_framework_relative'].'/' : '' ).'index.php?modname=lang&amp;op=translator&amp;tranm=english'
					.'&amp;modulef='.$module.'&amp;key='.$key.'&amp;platformf='.$platform.'&amp;fastadd\',\'liblang\'); w.focus(); return false;" '
					.'title="Define '.$key.' in module '.$module.'">'
					.'<img src="'.getPathImage().'standard/undefined.gif" alt="undef" /></a><br/>';
		}
		else {
			// $out ="<abbr title=\"".$out_debug."\">";
			$out ="";
			$out.=ucfirst(strtolower(trim(str_replace("_", " ", $key))));
			// $out.="</abbr>\n";
		}
		//$out .= ;


		return $out;
	}
	
	/**
	 * Return the translation for a key module pair
	 * @param string $key the key of the translation to search
	 * @param string $module the module of the language
	 * @param string $lang_code the code of the language to search to
	 * @return string the translation of the key module pair
	 **/
	function getLangText( $key, $module = FALSE, $platform = FALSE, $lang_code = FALSE ) {
		if( $module === FALSE )	$module = $this->module;
		if( $platform === FALSE ) $platform = $this->platform;
		global $globDoceboLanguageGlobalInstances;
		$key = str_replace(' ', '_', $key);
		$composedKey = DoceboLangManager::composeKey($key,$module,$platform);
		$lang_code = ($lang_code===FALSE)?$this->lang_code:$lang_code;
		if( isset($this->globTranslation[$composedKey]) ) {
			if( isset($this->globTranslation[$composedKey][$lang_code]) ) {

				if($GLOBALS['lang_edit'] == 'on') {

					if(isset($GLOBALS['page'])) {

						$GLOBALS['page']->add( '<a href="" onclick="w = window.open(\''
							.( $GLOBALS['where_framework_relative'] != '' ? $GLOBALS['where_framework_relative'].'/' : '' )
							.'index.php?modname=lang&op=translator&tranm=english'
							.'&modulef='.$module.'&key='.$key.'&platformf='.$platform.'&fastadd\',\'liblang\'); w.focus(); return false;">'
							.'Modify <span class="font_red">'.$key.'</span> '
							.'of module <span class="font_red">'.$module.'</span> '
							.'translation : <span class="font_red">'.$this->globTranslation[$composedKey][$lang_code].'</span>'
							.'</a><br/>',
							'footer' );
					} else {

						echo '<a href="" onclick="w = window.open(\''
							.( $GLOBALS['where_framework_relative'] != '' ? $GLOBALS['where_framework_relative'].'/' : '' )
							.'index.php?modname=lang&op=translator&tranm=english'
							.'&modulef='.$module.'&key='.$key.'&platformf='.$platform.'&fastadd\',\'liblang\'); w.focus(); return false;">'
							.'Modify <span class="font_red">'.$key.'</span> '
							.'of module <span class="font_red">'.$module.'</span> '
							.'translation : <span class="font_red">'.$this->globTranslation[$composedKey][$lang_code].'</span>'
							.'</a><br/>';
					}
				}
				return $this->globTranslation[$composedKey][$lang_code];
			}
			else
				return $this->_undefinedKey($key,$module,$platform,$lang_code);
		} else {
			// search on global modules
			if( count( $globDoceboLanguageGlobalInstances ) > 0 )
				foreach( $globDoceboLanguageGlobalInstances as $baseKey ) {
					$composedKey = DoceboLangManager::composeKey($key,$baseKey);
					if( isset($this->globTranslation[$composedKey]) ) {
						if( isset($this->globTranslation[$composedKey][$lang_code]) ) {


							if($GLOBALS['lang_edit'] == 'on') {


								if(isset($GLOBALS['page'])) {

									$GLOBALS['page']->add( '<a href="" onclick="w = window.open(\''
												.( $GLOBALS['where_framework_relative'] != '' ? $GLOBALS['where_framework_relative'].'/' : '' )
												.'index.php?modname=lang&op=translator&tranm=english'
												.'&modulef='.$module.'&key='.$key.'&platformf='.$platform.'&fastadd\',\'liblang\'); w.focus(); return false;">'
												.'Modify <span class="font_red">'.$key.'</span> '
												.'of module <span class="font_red">'.$module.'</span> '
												.'translation : <span class="font_red">'.$this->globTranslation[$composedKey][$lang_code].'</span>'
												.'</a><br/>',
												'footer' );
								}
							}
							return $this->globTranslation[$composedKey][$lang_code];
						}
						else
							return $this->_undefinedKey($key,$module,$platform,$lang_code);
					}
				}
			return $this->_undefinedKey($key,$module,$platform);
		}
	}


	function isDef( $key, $module = FALSE, $platform = FALSE, $lang_code = FALSE ) {
		if( $module === FALSE )	$module = $this->module;
		if( $platform === FALSE ) $platform = $this->platform;
		global $globDoceboLanguageGlobalInstances;
		$composedKey = DoceboLangManager::composeKey($key,$module,$platform);
		$lang_code = ($lang_code===FALSE)?$this->lang_code:$lang_code;
		if( isset($this->globTranslation[$composedKey]) ) {
			if( isset($this->globTranslation[$composedKey][$lang_code]) )
				return true;
			else
				return false;
		} else {
			// search on global modules
			if( count( $globDoceboLanguageGlobalInstances ) > 0 )
				foreach( $globDoceboLanguageGlobalInstances as $baseKey ) {
					$composedKey = DoceboLangManager::composeKey($key,$baseKey);
					if( isset($this->globTranslation[$composedKey]) ) {
						if( isset($this->globTranslation[$composedKey][$lang_code]) )
							return true;
						else
							return false;
					}
				}
			return false;
		}
	}


	function def( $key, $module = FALSE, $platform = FALSE, $lang_code = FALSE ) {
		return $this->getLangText( $key, $module, $platform, $lang_code );
	}


}

class DoceboLangManager {
	var $globTranslation = NULL;
	var $globLangModule = NULL;
	/** db connection */
	var $dbconn;
	/** prefix for the database */
	var $prefix;

	/**
	 * @static
	 * This function encapsulate the computation of a cross module key from
	 *	a key and a module
	 * @param string $key1 the first key
	 * @param string $key2 the second key
	 * @param mixed $key3 Optional. The third key or FALSE
	 * @return string composed key
	 **/
	function composeKey( $key1, $key2, $key3 = FALSE ) {
		if( $key3 === FALSE )
			return $key2."&".$key1;
		else
			return $key3."&".$key2."&".$key1;
	}

	/**
	 * @static
	 * This function encapsulate the computation of a cross module key from
	 *	a key and a module
	 * @param string $composed_key the module key composed
	 * @return array array with 0=>key1 1=>key2 2=>key3
	 **/
	function decomposeKey( $composed_key ) {
		$composed_key = str_replace('&amp;', '&', $composed_key);
		
		return array_reverse(explode('&', $composed_key, 3));
	}

	/**
	 * _getTableText
	 * @return string table name for text keys
	 **/
	function _getTableText() { return $this->prefix.DOCEBO_LANGUAGE_text; }
	/**
	 * _getTableTextTranslation
	 * @return string table name for text_translation n:m table
	 **/
	//function _getTableTextTranslation() { return $this->prefix.DOCEBO_LANGUAGE_text_translation; }
	/**
	 * _getTableTranslation
	 * @return string table name for text translation
	 **/
	function _getTableTranslation() { return $this->prefix.DOCEBO_LANGUAGE_translation; }
	/**
	 * _getTableLanguage
	 * @return string table name for languages
	 **/
	function _getTableLanguage() { return $this->prefix.DOCEBO_LANGUAGE_language; }

	function _executeQuery( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query ".mysql_error()." -->", 'debug' );
		
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query, $GLOBALS['dbConn'] );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _executeInsert( $query ) {
		if( $GLOBALS['do_debug'] == 'on' ) $GLOBALS['page']->add( "\n<!-- debug $query -->" , 'debug' );
		if( $this->dbconn === NULL ) {
			if( !mysql_query( $query, $GLOBALS['dbConn'] ) )
				return FALSE;
		} else {
			if( !mysql_query( $query, $this->dbconn ) )
				return FALSE;
		}
		if( $this->dbconn === NULL )
			return mysql_insert_id();
		else
			return mysql_insert_id($this->dbconn);
	}


	/**
	 * DoceboLangManager constructor
	 * @param string $param_prefix the prefix for the tables names
	 *			if not given global $prefix variable is used
	 * @param resource $dbconn the connection to the database
	 * 			if not given last connection will be used.
	 */
	function DoceboLangManager( $param_prefix = FALSE, $dbconn = NULL ) {
		global $globTranslation, $globLangModule;
		$this->globTranslation = &$globTranslation;
		$this->globLangModule = &$globLangModule;
		if( $param_prefix === FALSE ) {
			global $prefix_fw;
			$this->prefix = $prefix_fw;
		} else {
			$this->prefix = $param_prefix;
		}
		$this->dbConn = $dbconn;
	}

	/**
	 * return an array with all modules in translations table
	 * @param string $platform
	 * @return array array with all modules
	 */
	function getAllModules($platform = FALSE) {
		if( $platform === FALSE ) $platform = $GLOBALS['platform'];
		$query = "SELECT text_module"
				." FROM ".$this->_getTableText()
				." WHERE text_platform = '".$platform."'"
				." GROUP BY text_module";
		$rs = $this->_executeQuery( $query );
		$result = array();
		while( list($text_module) = mysql_fetch_row($rs) ) {
			$result[] = $text_module;
		}
		return $result;
	}

	/**
	 * return an array with all modules loaded
	 * @return array with all modules loaded
	 */
	function getLoadedModules() {
		global $globLangModule;
		return array_keys( $globLangModule );
	}

	/**
	 * return an array with all lang_code loaded for a given module
	 * @param string $module name of the module
	 * @return array with all lang_code loaded for a given module
	 *			FALSE if the module is not loaded
	 */
	function getLoadedModulesLanguages( $module ) {
		global $globLangModule;
		if( isset( $globLangModule[$module] ) )
			return array_keys( $globLangModule[$module] );
		else
			return FALSE;
	}

	/**
	 * return the text translation for a given $lang_code, $key, $module, $platform
	 * @param string $lang_code the lang code to get translation
	 * @param string $key the key to search or the composed key if $module is FALSE
	 * @param mixed $module the module to search or FALSE if $key is composed key
	 * @param mixed $platform the platform to search or FALSE if $key or $module are composed key
	 * @return mixed string with text translation or FALSE if not found
	 */
	 function getLangTranslationText($lang_code, $key ,$module = FALSE, $platform = FALSE) {
		if( $module === FALSE )
			list($key,$module,$platform) = $this->decomposeKey( $key );
		elseif( $platform === FALSE )
			list($module,$platform) = $this->decomposeKey( $module );
		/*$query = "SELECT tran.translation_text"
				."  FROM ".$this->_getTableText()." AS tt"
				."  JOIN ".$this->_getTableTextTranslation()." AS texttran"
				."  JOIN ".$this->_getTableTranslation()." AS tran"
				." WHERE tt.id_text = texttran.id_text "
				."   AND texttran.id_translation = tran.id_translation "
				."   AND tt.text_key = '".$key."'"
				."   AND tt.text_module = '".$module."'"
				."   AND tt.text_platform = '".$platform."'"
				."   AND tran.lang_code = '".$lang_code."'";*/
		$query = "SELECT tran.translation_text"
				."  FROM ".$this->_getTableText()." AS tt"
				."  JOIN ".$this->_getTableTranslation()." AS tran"
				." ON (tt.id_text=tran.id_text) "
				." WHERE tt.text_key = '".$key."'"
				."   AND tt.text_module = '".$module."'"
				."   AND tt.text_platform = '".$platform."'"
				."   AND tran.lang_code = '".$lang_code."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows($rs) < 1 )
			return FALSE;
		list($translation_text) = mysql_fetch_row($rs);
		return $translation_text;
	 }
	
	/**
	 * return an array with all the translations for a given
	 *	platform module lang_code triple
	 * @param string $platform the platform
	 * @param mixed $module the module name
	 *				if FALSE all modules will be returned
	 * @param string $lang_code the code of the language
	 * @param string $trans_contains the text contains this string
	 * @return array with index numeric values are arrays with
	 *			- 0=>module,
	 *			- 1=>key,
	 *			- 2=>translation or NULL if don't exist translation
	 *			- 3=>attributes
	 *			for that key language pair
	 */
	function getModuleLangTranslations( $platform, $module, $lang_code, $trans_contains = '', $attributes = false, $order_by = false, $get_date = false ) {
		
		$query = "SELECT text_module, text_key, id_text, text_attributes "
				."  FROM ".$this->_getTableText()
				." WHERE text_platform = '".$platform."'"
				.(($module === FALSE)?"":("  AND text_module = '".$module."'"));
		if($attributes !== FALSE && is_array($attributes) && !empty($attributes)) {
			
			$part = array();
			foreach($attributes as $value) 
				$part[] = " text_attributes LIKE '%".$value."%' ";
			$query .= " AND ".implode(' AND ', $part)." ";
		}
		$query .= " ORDER BY text_module, text_key";
		
		$rs = $this->_executeQuery( $query );
		$text_result = array();
		$text_set = array();
		
		while( list($text_module, $text_key, $id_text, $text_attributes) = mysql_fetch_row($rs) ) {
			$text_result[$id_text] = array( $text_module, $text_key, NULL, $text_attributes );
			$text_set[] = $id_text;
		}
		
		if( count($text_set) > 0 ) {
			$query = "SELECT tran.id_text, tran.translation_text ".($get_date === true ? ', save_date ' : '' )
					."  FROM ".$this->_getTableTranslation()." AS tran "
					."	WHERE tran.lang_code='".$lang_code."'"
					."	  AND tran.id_text IN (".join(',',$text_set).")";
			if($trans_contains != '') $query .= " AND tran.translation_text LIKE '%".$trans_contains."%'"; 
			$rs = $this->_executeQuery( $query );
			while( $row = mysql_fetch_row($rs) ) {
				$text_result[$row[0]][2] = $row[1];
				if($get_date === true) $text_result[$row[0]][4] = $row[2];
			}
		} else {
			if(isset($GLOBALS['page'])) $GLOBALS['page']->add( "<!-- getModuleLangTranslations: no translations for $platform, $module, $lang_code -->\n", 'debug' );
		}
		return $text_result;
		/*
		// sort -----------------------------------------------------------
		switch($order_by) {
			case "translation" : $query .= " ORDER BY  tran.translation_text ";break;
			case "translation_i" : $query .= " ORDER BY  tran.translation_text DESC ";break;
			default : $query .= " ORDER BY textt.text_module, textt.text_key"; 
		}*/
	}


	/**
	 * return a key description
	 * @param string $key the key to search or the composed key if $module is FALSE
	 * @param mixed $module the module to search or FALSE if $key is composed key
	 * @param mixed $platform the platform to search or FALSE if $key or $module are composed key
	 * @return mixed
	 *				- string description for given key module platform triple
	 *				- FALSE if key module platform is not found
	 */
	function getKeyDescription( $key, $module = FALSE, $platform = FALSE) {
		if( $module === FALSE )
			list($key,$module,$platform) = $this->decomposeKey( $key );
		elseif( $platform === FALSE )
			list($module,$platform) = $this->decomposeKey( $module );

		$query = "SELECT text_description FROM ".$this->_getTableText()
				." WHERE text_key = '".$key."' "
				."   AND text_module = '".$module."'"
				."   AND text_platform = '".$platform."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows($rs) == 0 ) {
			return FALSE;
		}
		list( $description ) = mysql_fetch_row($rs);
		return $description;
	}

	/**
	 * return the key attributes
	 * @param string $key the key to search or the composed key if $module is FALSE
	 * @param mixed $module the module to search or FALSE if $key is composed key
	 * @param mixed $platform the platform to search or FALSE if $key or $module are composed key
	 * @return mixed
	 *				- string attributes for given key module platform triple
	 *				- FALSE if key module platform is not found
	 */
	function getKeyAttributes( $key, $module = FALSE, $platform = FALSE) {
		if( $module === FALSE )
			list($key,$module,$platform) = $this->decomposeKey( $key );
		elseif( $platform === FALSE )
			list($module,$platform) = $this->decomposeKey( $module );

		$query = "SELECT text_attributes FROM ".$this->_getTableText()
				." WHERE text_key = '".$key."' "
				."   AND text_module = '".$module."'"
				."   AND text_platform = '".$platform."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows($rs) == 0 ) {
			return FALSE;
		}
		list( $attributes ) = mysql_fetch_row($rs);
		return $attributes;
	}

	/**
	 * delete a key and all associated translations
	 * @param string $key the key to search or the composed key if $module is FALSE
	 * @param mixed $module the module to search or FALSE if $key is composed key
 	 * @param mixed $platform the platform to search or FALSE if $key or $module are composed key
	 * @return bool TRUE if success, FALSE otherwise
	 */
	function deleteKey( $key, $module = FALSE, $platform = FALSE) {
		if( $module === FALSE )
			list($key,$module,$platform) = $this->decomposeKey( $key );
		elseif( $platform === FALSE )
			list($module,$platform) = $this->decomposeKey( $module );

		$query = "SELECT id_text FROM ".$this->_getTableText()
				." WHERE text_key = '".$key."' "
				."   AND text_module = '".$module."'"
				."   AND text_platform = '".$platform."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows($rs) == 0 ) {
			return FALSE;
		}
		list( $id_text ) = mysql_fetch_row($rs);

		/*$query = "SELECT id_translation FROM ".$this->_getTableTextTranslation()
				." WHERE id_text='".$id_text."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows($rs) > 0 ) {
			$arr_id_translations = array();
			while( list($id_translation) = mysql_fetch_row($rs))
				$arr_id_translations[] = $id_translation;
			*/
			$query = "DELETE FROM ".$this->_getTableTranslation()
					." WHERE id_text=".$id_text;
			$this->_executeQuery( $query );
			if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- affected: ".mysql_affected_rows()." -->\n", 'debug' );
		//}

/*
		$query = "DELETE FROM ".$this->_getTableTextTranslation()
				." WHERE id_text='".$id_text."'";
		$this->_executeQuery( $query );
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- affected: ".mysql_affected_rows()." -->\n", 'debug' );
*/
		$query = "DELETE FROM ".$this->_getTableText()
				." WHERE id_text='".$id_text."'";
		$this->_executeQuery( $query );
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- affected: ".mysql_affected_rows()." -->\n", 'debug' );

		return TRUE;
	}

	/**
 	 * update a key
 	 * @param string $key the key to search or the composed key if $module is FALSE
 	 * @param mixed $module the module to search or FALSE if $key is composed key
 	 * @param mixed $platform the platform to search or FALSE if $key or $module are composed key
	 * @param mixed $description the description of the key of FALSE for skip
	 * @param mixed $attributes the attributes of key (accessibility,sms)
	 * @return bool TRUE if success, FALSE otherwise
 	 */
	function updateKey( $key, $module = FALSE, $platform = FALSE, $description = FALSE, $attributes = FALSE, $overwrite = TRUE, $no_add = FALSE) {
		if( $module === FALSE )
			list($key,$module,$platform) = $this->decomposeKey( $key );
		elseif( $platform === FALSE )
			list($module,$platform) = $this->decomposeKey( $module );

		$query = "SELECT id_text FROM ".$this->_getTableText()
				." WHERE text_key = '".$key."' "
				."   AND text_module = '".$module."'"
				."   AND text_platform = '".$platform."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows($rs) == 0 ) {
			
			if($no_add === true) return true;
			$query = "INSERT INTO ".$this->_getTableText()
					." (text_key, text_module, text_platform, text_description, text_attributes ) VALUES "
					." ('".$key."','".$module."','".$platform."','".$description."','".$attributes."') ";
			return $this->_executeQuery( $query );
		} elseif ($description !== FALSE) {
			
			if($overwrite === true) {
				
				list( $id_text ) = mysql_fetch_row( $rs );
				$query = "UPDATE ".$this->_getTableText()
						." SET text_description = '".$description."'";
				if( $attributes !== FALSE )
					$query .= ", text_attributes  = '".$attributes."' ";
				$query .= " WHERE id_text = '".$id_text."'";
				return $this->_executeQuery( $query );
			}
		}
		return TRUE;
	}

	/**
	 *
	 **/
	function updateTranslationC( $composed_key, $translation, $lang_code ) {
		list($key,$module,$platform) = $this->decomposeKey( $composed_key );
		return $this->updateTranslation( $key, $module, $platform, $translation, $lang_code );
	}
	/**
	 *
	 **/
	function updateTranslation( $key, $module, $platform, $translation, $lang_code, $save_date = false ) {
		
		if($save_date === false) $save_date = date("Y-m-d H:i:s");
		/*$query = "SELECT tran.id_translation "
				." FROM ".$this->_getTableText()." AS text"
				." INNER JOIN ".$this->_getTableTextTranslation()." AS texttran ON (text.id_text = texttran.id_text)"
				." INNER JOIN ".$this->_getTableTranslation()." AS tran ON (tran.id_translation = texttran.id_translation)"
				." WHERE tran.lang_code = '".$lang_code."'"
				."   AND text.text_platform = '".$platform."'"
				."   AND text.text_module = '".$module."'"
				."  AND text.text_key = '".$key."'";*/
		$query = "SELECT tran.id_translation "
				." FROM ".$this->_getTableText()." AS text"
				." INNER JOIN ".$this->_getTableTranslation()." AS tran ON (text.id_text = tran.id_text)"
				." WHERE tran.lang_code = '".$lang_code."'"
				."   AND text.text_platform = '".$platform."'"
				."   AND text.text_module = '".$module."'"
				."  AND text.text_key = '".$key."'";
		$rs = $this->_executeQuery( $query );
		if( $rs === FALSE )
			return FALSE;
		if( mysql_num_rows($rs) == 1 ) {
			list($id_translation) = mysql_fetch_row($rs);
			
			// update save_date only if the content is changed ------------
			$query = "UPDATE ".$this->_getTableTranslation()
					."   SET save_date  = '".$save_date."' "
					." WHERE id_translation='".$id_translation."' "
					."		AND translation_text <> '".$translation."'";
			$this->_executeQuery( $query );
			
			$query = "UPDATE ".$this->_getTableTranslation()
					."   SET translation_text='".$translation."' "
					." WHERE id_translation='".$id_translation."'";
			
			return $this->_executeQuery( $query );
		} elseif( mysql_num_rows($rs) == 0 ) {
			$query = "SELECT id_text FROM ".$this->_getTableText()
					." WHERE text_platform = '".$platform."'"
					."   AND text_module = '".$module."'"
					."	 AND text_key = '".$key."'";
			$rs = $this->_executeQuery( $query );
			if( $rs === FALSE )
				return FALSE;
			list($id_text) = mysql_fetch_row($rs);
			
			if($translation == '') $save_date = '0000-00-00 00:00:00';
			$query = "INSERT INTO ".$this->_getTableTranslation()
					." (translation_text,lang_code,save_date,id_text) VALUES "
					." ('".$translation."','".$lang_code."','".$save_date."','".$id_text."') ";
			if( ($id_translation = $this->_executeInsert($query)) === FALSE )
				return FALSE;
/*
			$query = "INSERT INTO ".$this->_getTableTextTranslation()
					." (id_text,id_translation) VALUES "
					." ('".$id_text."','".$id_translation."') ";
			return $this->_executeQuery( $query );*/
		} else {
			return FALSE;
		}
	}

	/**
	 * test for a lang_code exist
	 * @param string $lang_code code of lang to test
	 * @return TRUE if language exist, FALSE otherwise
	 **/
	function existLanguage( $lang_code ) {
		$query = "SELECT lang_code"
				." FROM ".$this->_getTableLanguage()
				." WHERE lang_code='".$lang_code."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows( $rs ) !== 1 )
			return FALSE;
		else
			return TRUE;
	}


	/**
	 * return an array with all the lang_codes presents on system
	 * @return array with all language codes in system (index in array is numeric
	 *			starting from 0, value is lang_code)
	 */
	function getAllLangCode() {
		$query = "SELECT lang_code"
				." FROM ".$this->_getTableLanguage();
		$rs = $this->_executeQuery( $query );

		$result = array();
		while( list($lang_code) = mysql_fetch_row($rs) ) {
			$result[] = $lang_code;
		}
		return $result;
	}


	/**
	 * return an array with all the languages presents on system
	 * @return array with all language codes in system (index in array is numeric
	 *			starting from 0, value is an array with (0=> lang_code, 1=> description )
	 *			return an empty array if no languages is present
	 */
	function getAllLanguages() {
		$query = "SELECT lang_code, lang_description, lang_direction "
				." FROM ".$this->_getTableLanguage();
		$rs = $this->_executeQuery( $query );

		$result = array();
		while( list($lang_code, $lang_description, $lang_direction) = mysql_fetch_row($rs) ) {
			$result[] = array( $lang_code, $lang_description, $lang_direction);
		}
		return $result;
	}

	/**
	 * return language description for a given lang_code
	 * @param string $lang_code
	 * @return string language description
	 **/
	function getLanguageDescription($lang_code) {
		$query = "SELECT lang_description"
				." FROM ".$this->_getTableLanguage()
				." WHERE lang_code='".$lang_code."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows( $rs ) !== 1 )
			return FALSE;

		list($description) = mysql_fetch_row( $rs );
		return $description;
	}

	/**
	 * return language charset for a given lang_code
	 * @param string $lang_code
	 * @return string language charset
	 **/
	function getLanguageCharset($lang_code) {
		$query = "SELECT lang_charset"
				." FROM ".$this->_getTableLanguage()
				." WHERE lang_code='".$lang_code."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows( $rs ) !== 1 )
			return FALSE;

		list($lang_charset) = mysql_fetch_row( $rs );
		return $lang_charset;
	}

	/**
	 * return language browsercode for a given lang_code
	 * @param string $lang_code
	 * @return string language browser code
	 **/
	function getLanguageBrowsercode($lang_code) {
		$query = "SELECT lang_browsercode"
				." FROM ".$this->_getTableLanguage()
				." WHERE lang_code='".$lang_code."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows( $rs ) !== 1 )
			return FALSE;

		list($lang_browsercode) = mysql_fetch_row( $rs );
		return $lang_browsercode;
	}
		
	/**
	 * return language browsercode for a given lang_code
	 * @param string $lang_code
	 * @return string language browser code
	 **/
	function getLanguageDirection($lang_code) {
		$query = "SELECT lang_direction"
				." FROM ".$this->_getTableLanguage()
				." WHERE lang_code='".$lang_code."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows( $rs ) !== 1 )
			return FALSE;

		list($lang_direction) = mysql_fetch_row( $rs );
		return $lang_direction;
	}
	
	function findLanguageFromBrowserCode() {
		
		if(!isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			return getDefaultLanguage();
		}
		$accept_language = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
		$al_arr = explode(",", $accept_language);

		$i=0;
		$res="";
		while(list(,$value) = each($al_arr)) {

			$bl_arr = explode(";", $value);
			$browser_language = $bl_arr[0];
			$browser_language =mysql_escape_string(substr($browser_language, 0, 5));
			
			$query = "SELECT lang_code "
				." FROM ".$this->_getTableLanguage()
				." WHERE lang_browsercode LIKE '%".$browser_language."%'";
			$rs = $this->_executeQuery( $query );
			if( mysql_num_rows( $rs ) != 0 ) {
				list($lang_code) = mysql_fetch_row( $rs );
				return $lang_code;
			}
		}
		return getDefaultLanguage();	
	}

	/**
	 * update a lang_code
	 * @param string $lang_code code of lang to test
	 * @param string $lang_description optional
	 * @param string $lang_charset optional
	 * @param string $lang_brosercode optional
	 * @return TRUE if success, FALSE otherwise
	 **/
	function setLanguage( $lang_code, $lang_description = FALSE, $lang_charset = FALSE, $lang_brosercode = FALSE, $lang_direction = FALSE ) {
		$query = "SELECT lang_code"
				." FROM ".$this->_getTableLanguage()
				." WHERE lang_code='".$lang_code."'";
		$rs = $this->_executeQuery( $query );
		if( mysql_num_rows( $rs ) !== 1 ) {
			return $this->insertLanguage($lang_code, $lang_description, $lang_charset, $lang_brosercode, $lang_direction);
		} elseif( $lang_description !== FALSE ) {
			return $this->updateLanguage($lang_code, $lang_description, $lang_charset, $lang_brosercode, $lang_direction);
		}
		return TRUE;
	}

	/**
	 * update a lang_code entry
	 * @param string $lang_code
	 * @param string $lang_description
	 * @param string $lang_charset optional
	 * @param string $lang_brosercode optional
	 * @return bool TRUE if success, FALSE otherwise
	 **/
	function updateLanguage($lang_code, $lang_description, $lang_charset = FALSE, $lang_brosercode = FALSE, $lang_direction = FALSE ) {
		$query = "UPDATE ".$this->_getTableLanguage()
				." SET lang_description='".$lang_description."'"
				.( ($lang_charset!==FALSE) ? ", lang_charset='".$lang_charset."'" : "" )
				.( ($lang_brosercode!==FALSE) ? ", lang_browsercode='".$lang_brosercode."'" : "" )
				.( ($lang_direction !== FALSE) ? ", lang_direction ='".$lang_direction."'" : "" )
				." WHERE lang_code='".$lang_code."'";
		return $this->_executeQuery($query);
	}

	/**
	 * insert a lang_code entry
	 * @param string $lang_code
	 * @param string $lang_description
	 * @return bool TRUE if success, FALSE otherwise
	 **/
	function insertLanguage($lang_code, $lang_description, $lang_charset = FALSE, $lang_brosercode = FALSE, $lang_direction = FALSE ) {
		$query = "INSERT INTO ".$this->_getTableLanguage()
				." ( lang_code, lang_description "
				.( ($lang_charset !== FALSE) ? ", lang_charset" : "" )
				.( ($lang_brosercode !== FALSE) ? ", lang_browsercode" : "" )
				.( ($lang_direction !== FALSE) ? ", lang_direction" : "" )
				." )"
				." VALUES ('".$lang_code."','".$lang_description."'"
				.( ($lang_charset !== FALSE) ? ",'".$lang_charset."'" : "" )
				.( ($lang_brosercode !== FALSE) ? ",'".$lang_brosercode."'" : "" )
				.( ($lang_direction !== FALSE) ? ",'".$lang_direction."'" : "" )
				.")";
		return $this->_executeQuery($query);
	}

	/**
	 * delete a lang_code entry
	 * @param string $lang_code
	 * @return bool TRUE if success, FALSE otherwise

	 **/
	function deleteLanguage($lang_code) {
		$control = true;
		
		$query = "DELETE FROM ".$this->_getTableLanguage()
				." WHERE lang_code='".$lang_code."'";
		print_r($query);print_r('<br><br>');
		if (!$this->_executeQuery($query))
		{
			$control = false;
			return $control;
		}
		
		/*$query = 	"SELECT id_translation"
					." FROM ".$this->_getTableTranslation().""
					." WHERE lang_code = '".$lang_code."'";
		print_r($query);print_r('<br><br>');
		$result = mysql_query($query);
		
		$id_to_delete = array();
		
		while (list($id_translation) = mysql_fetch_row($result))
			$id_to_delete[] = $id_translation;*/
		
		/*$query =	"DELETE FROM ".$this->_getTableTextTranslation().""
					." WHERE id_translation IN (".implode(',',$id_to_delete).")";
		print_r($query);print_r('<br><br>');
		if (!$this->_executeQuery($query))
		{
			$control = false;
			return $control;
		}*/
		
		$query =	" DELETE FROM ".$this->_getTableTranslation().""
					." WHERE lang_code = '".$lang_code."'";
		//print_r($query);print_r('<br><br>');
		if (!$this->_executeQuery($query))
		{
			$control = false;
			return $control;
		}
		return $control;
	}
	
	function getLangStat() {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
		$pl_man =& PlatformManager::createInstance();
		$platform_list = array_keys($pl_man->getActivePlatformList());
		
		$stats = array();
		$lang_stat = ""
		." SELECT COUNT(*)"
		." FROM ".$this->_getTableText()." "
		." WHERE 0 ";
		foreach($platform_list as $plat) {
			
			$lang_stat .= " OR text_platform = '".$plat."' ";
		}
		
		list($stats['tot_lang']) = mysql_fetch_row(mysql_query($lang_stat));
		
		$lang_stat = ""
		."SELECT lang_code, COUNT(*) "
		."FROM ".$this->_getTableTranslation()." " 
		."WHERE translation_text <> '' "
		."GROUP BY lang_code";
		$re_stat = mysql_query($lang_stat);
		while(list($lc, $tot) = mysql_fetch_row($re_stat)) {
			
			$stats[$lc] = $tot;
		}
		return $stats;
	}
}


/**
 * Global single instance (Singleton) of DoceboLanguageManager class
 **/
$GLOBALS['globLangManager'] = new DoceboLangManager();

/**
 * return the language text for a given key,module,language
 * @param string $key key to search
 * @param string $module the name of the module. If not assigned the module
 *			will be computed from $_GET['module']
 * @param string $lang_code the code of the language. If not assigned the
 *			lang_code will be computed with getLanguage()
 * @return DoceboLanguage an instance of DoceboLanguage or FALSE.
 **/
function getLangText( $key, $module = FALSE, $platform = FALSE, $lang_code = FALSE ) {
	$lang =& DoceboLanguage::createInstance($module, $platform, $lang_code);
	return $lang->getLangText($key);
}

function def( $key, $module = FALSE, $platform = FALSE, $lang_code = FALSE ) {
	$lang =& DoceboLanguage::createInstance($module, $platform, $lang_code);
	return $lang->getLangText($key);
}

/**
 * @return string 	the unicode for the current language
 */
function getUnicode() {

	return $GLOBALS['globLangManager']->getLanguageCharset(getLanguage());
}

function getBrowsercode() {

	return $GLOBALS['globLangManager']->getLanguageBrowsercode(getLanguage());
}

/**
 * @return string 	current laguage
 */
function getLanguage() {

	if(!isset($_SESSION['custom_lang'])) {

        if($GLOBALS['current_user']->isAnonymous()) {
            $_SESSION['custom_lang'] = $GLOBALS['globLangManager']->findLanguageFromBrowserCode();
        } else {
			$_SESSION['custom_lang'] = $GLOBALS['current_user']->preference->getLanguage();
        }
		// the lang exists?
		if(!$GLOBALS['globLangManager']->existLanguage($_SESSION['custom_lang'])) {

			// seems taht the language selected does not exist
			$all_language = $GLOBALS['globLangManager']->getAllLangCode();
			$browser_deduction = $GLOBALS['globLangManager']->findLanguageFromBrowserCode();

			if(array_search($browser_deduction, $all_language)) $_SESSION['custom_lang'] = $browser_deduction;
			elseif(array_search('english', $all_language)) $_SESSION['custom_lang'] = 'english';
			else $_SESSION['custom_lang'] = array_pop($all_language);
		}
    }
    return $_SESSION['custom_lang'];
}

function getDefaultLanguage() {

	require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
	$plt_man =& PlatformManager::createInstance();
	return $plt_man->getLanguageForPlatform();
}

/**
 * @return string 	current laguage
 */
function setLanguage($new_language) {

	return $_SESSION['custom_lang'] = $new_language;
}

?>
