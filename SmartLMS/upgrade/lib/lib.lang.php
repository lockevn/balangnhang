<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

require_once($GLOBALS['where_upgrade'].'/lib/lib.domxml.php');

define("DOCEBO_LANGUAGE_text","_lang_text");
define("DOCEBO_LANGUAGE_text_translation","_lang_text_translation");
define("DOCEBO_LANGUAGE_translation","_lang_translation");
define("DOCEBO_LANGUAGE_language","_lang_language");

$GLOBALS['globLangManager'] = new DoceboLangManager("core");

function lang_importXML($filename, $overwrite = TRUE, $no_add_miss = false) {
	
	$globLangManager=& $GLOBALS['globLangManager'];
    
	$modules = 0;
	$definitions = 0;
	$doc = new DoceboDOMDocument();
	$doc->load( $filename );
	
	$query_new_lang_default = "INSERT INTO ".$globLangManager->_getTableText()
							." (text_key, text_module, text_platform, text_description, text_attributes ) VALUES ";
	
	$query_new_lang = $query_new_lang_default;
	
	$counter = 0;
	$array_for_update = array();
	
	$context = new DoceboDOMXPath( $doc );
	$root = $doc->documentElement;
	
	$arrLang = $context->query('//LANGUAGES/LANG');
	for( $iLang = 0; $iLang < $arrLang->length; $iLang++ ) {
		$lang =& $arrLang->item($iLang);
		$elem = $context->query('lang_code/text()',$lang);
		$elemNode = $elem->item(0);
		$lang_code = $elemNode->textContent;
		
		// if(canEditLang($lang_code)) {
		
			$elem = $context->query('lang_description/text()',$lang);
			$elemNode = $elem->item(0);
			$lang_description = addslashes(urldecode($elemNode->textContent));
			$elem = $context->query('lang_charset/text()',$lang);
			$elemNode = $elem->item(0);
			$lang_charset = $elemNode->textContent;
			$elem = $context->query('lang_browsercode/text()',$lang);
			$elemNode = $elem->item(0);
			$lang_browsercode = $elemNode->textContent;
			
			$globLangManager->setLanguage($lang_code, $lang_description, $lang_charset, $lang_browsercode );
			$arrPlatforms = $context->query('platform',$lang);
			for( $iPlatform = 0; $iPlatform < $arrPlatforms->length; $iPlatform++ ) {
				
				$elem_platform =& $arrPlatforms->item($iPlatform);
				$platform = $elem_platform->getAttribute( 'id' );
				$arrModules = $context->query('module', $elem_platform);
				
				for( $iModule = 0; $iModule < $arrModules->length; $iModule++ ) {
					
					$modules++;
					$elem_module =& $arrModules->item($iModule);
					$module = $elem_module->getAttribute( 'id' );
					$arrKey = $context->query('key', $elem_module);
					
					for( $iKey = 0; $iKey < $arrKey->length; $iKey++ ) {
						
						$elem_key =& $arrKey->item($iKey);
						$definitions++;
						$content = $elem_key->firstChild;
						
						if ($counter == 100) {
							
							if($query_new_lang != $query_new_lang_default) {
								$globLangManager->_executeQuery($query_new_lang);
							}
							
							$query_new_lang = $query_new_lang_default;
							
							for ($i = 0; $i < $counter; $i++) {
								
								list($key,$module,$platform) = $globLangManager->decomposeKey($array_for_update[$i]['id']);
								if( $platform === FALSE ) list($module,$platform) = $globLangManager->decomposeKey( $module );
								
								$query = "SELECT id_text FROM ".$globLangManager->_getTableText()
										." WHERE text_platform = '".$platform."'"
										."   AND text_module = '".$module."'"
										."	 AND text_key = '".$key."'";
								$rs = $globLangManager->_executeQuery( $query );
								
								if( $rs != FALSE ) {
									
									list($id_text) = mysql_fetch_row($rs);
									$query = "INSERT INTO ".$globLangManager->_getTableTranslation()
											." (translation_text,lang_code,save_date, id_text) VALUES "
											." ('".$array_for_update[$i]['translation']."',"
											."	'".$array_for_update[$i]['lang_code']."',"
											."	'".$array_for_update[$i]['save_date']."', "
											." '".$id_text."' )";
									$id_translation = $globLangManager->_executeInsert($query);
								}
							}
							
							$array_for_update = array();
							$counter = 0;
						} // end of cahced insert to do
						
						list($key,$module,$platform) = $globLangManager->decomposeKey($elem_key->getAttribute( 'id' ));
						if( $platform === FALSE ) list($module,$platform) = $globLangManager->decomposeKey( $module );
						
						if($elem_key->hasAttribute('save_date')) $save_date = $elem_key->getAttribute('save_date');
						else $save_date = date('Y-m-d H:i:s'); 				
									
						$query = "SELECT id_text FROM ".$globLangManager->_getTableText()
								." WHERE text_key = '".$key."' "
								."   AND text_module = '".$module."'"
								."   AND text_platform = '".$platform."'";
						$rs = $globLangManager->_executeQuery( $query );
						if( mysql_num_rows($rs) == 0) {
							
							// a completly new key -------------------------------------------
							
							if($no_add_miss === false) {
								
								if ($counter) {
									$query_new_lang .= ", ('".$key."','".$module."','".$platform."','','".$elem_key->getAttribute('attributes')."') ";
								} else {
									$query_new_lang .= " ('".$key."','".$module."','".$platform."','','".$elem_key->getAttribute('attributes')."') ";
								}
								if($content != NULL) {
									$value = trim($content->nodeValue);
									if (preg_match("/^<!\\[CDATA\\[/i", $value))
										$str_value = trim(preg_replace("/<!\\[CDATA\\[(.*?)\\]\\]>/si", "\$1", $value));
									else
										$str_value = trim(urldecode($value));
									
									$array_for_update[$counter]['id'] = $elem_key->getAttribute( 'id' );
									$array_for_update[$counter]['translation'] = addslashes($str_value);
									$array_for_update[$counter]['lang_code'] = $lang_code;
									$array_for_update[$counter]['save_date'] = $save_date;
								} else {
									
									$array_for_update[$counter]['id'] = $elem_key->getAttribute( 'id' );
									$array_for_update[$counter]['translation'] = '';
									$array_for_update[$counter]['lang_code'] = $lang_code;
									$array_for_update[$counter]['save_date'] = $save_date;
								}
								$counter++;
							}
						} else {
							
							// the key alredy exists, now we must check if the translation exists
							
                            list($id_text) = mysql_fetch_row($rs);
                            
							$query = "SELECT id_translation, translation_text "
									." FROM ".$globLangManager->_getTableTranslation().""
									." WHERE lang_code = '".$lang_code."'"
									." AND id_text = '".$id_text."'";
							list($id_translation, $translation_text) = mysql_fetch_row(mysql_query($query));
							
							if( $content != NULL ) {
								$value = trim($content->nodeValue);
								if (preg_match("/^<!\\[CDATA\\[/i", $value))
									$str_value = trim(preg_replace("/<!\\[CDATA\\[(.*?)\\]\\]>/si", "\$1", $value));
								else
									$str_value = trim(urldecode($value));
							} else {
								$str_value = '';
							} 
							if($id_translation) {
								
								if($overwrite == true || trim($translation_text) == '') {
									
									$query = "UPDATE ".$globLangManager->_getTableTranslation()
											."   SET translation_text='".addslashes($str_value)."', "
											." save_date  = '".$save_date."'"
											." WHERE id_translation='".$id_translation."'";
									$globLangManager->_executeQuery( $query );
								}
							} else {
								
								$array_for_update[$counter]['id'] = $elem_key->getAttribute( 'id' );
								$array_for_update[$counter]['translation'] = addslashes($str_value);
								$array_for_update[$counter]['lang_code'] = $lang_code;
								$array_for_update[$counter]['save_date'] = $save_date;
						
								$counter++;
							}
							// --------------------------------
						}
						
					} // end for on arrKey
					
				} // end for on modules
				
			} // end for on platforms
			
		// } // end if
		if($query_new_lang != $query_new_lang_default) {
		
			$globLangManager->_executeQuery($query_new_lang);
		}
		$query_new_lang = $query_new_lang_default;
		
		for ($i = 0; $i < $counter; $i++) {
			
			list($key,$module,$platform) = $globLangManager->decomposeKey($array_for_update[$i]['id']);
			if( $platform === FALSE ) list($module,$platform) = $globLangManager->decomposeKey( $module );
								
			$query = "SELECT id_text FROM ".$globLangManager->_getTableText()
					." WHERE text_platform = '".$platform."'"
					."   AND text_module = '".$module."'"
					."	 AND text_key = '".$key."'";
			$rs = $globLangManager->_executeQuery( $query );
			if( $rs != FALSE )
			{
				list($id_text) = mysql_fetch_row($rs);
				
				if($array_for_update[$i]['translation'] == '') $save_date = '0000-00-00 00:00:00';
				$query = "INSERT INTO ".$globLangManager->_getTableTranslation()
						." (translation_text,lang_code,save_date, id_text) VALUES "
						." ('".$array_for_update[$i]['translation']."',"
						."	'".$array_for_update[$i]['lang_code']."',"
						."	'".$array_for_update[$i]['save_date']."', "
						." '".$id_text."' )";
				$id_translation = $globLangManager->_executeInsert($query);
			}
		}
		
		$array_for_update = array();
		$counter = 0;
		
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
	function _getTableTextTranslation() { return $this->prefix.DOCEBO_LANGUAGE_text_translation; }
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
		$rs=$GLOBALS["db"]->querySingle($query);
		
		if (!$rs)
			echo $GLOBALS["db"]->error();
		return $rs;
	}


	function _executeInsert( $query ) {
		$rs=$GLOBALS["db"]->query($query);

		if ($rs)
			return $GLOBALS["db"]->lastInsertId();
		else
			return FALSE;
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
		//$this->dbConn = $dbconn;
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
		while( list($text_module) = $GLOBALS["db"]->fetchRow($rs) ) {
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
		$query = "SELECT tran.translation_text"
				."  FROM ".$this->_getTableText()." AS tt"
				."  JOIN ".$this->_getTableTextTranslation()." AS texttran"
				."  JOIN ".$this->_getTableTranslation()." AS tran"
				." WHERE tt.id_text = texttran.id_text "
				."   AND texttran.id_translation = tran.id_translation "
				."   AND tt.text_key = '".$key."'"
				."   AND tt.text_module = '".$module."'"
				."   AND tt.text_platform = '".$platform."'"
				."   AND tran.lang_code = '".$lang_code."'";
		$rs = $this->_executeQuery( $query );
		if( $GLOBALS["db"]->numRows($rs) < 1 )
			return FALSE;
		list($translation_text) = $GLOBALS["db"]->fetchRow($rs);
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
	function getModuleLangTranslations( $platform, $module, $lang_code, $trans_contains = '', $attributes = false ) {
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

		while( list($text_module, $text_key, $id_text, $text_attributes) = $GLOBALS["db"]->fetchRow($rs) ) {
			$text_result[$id_text] = array( $text_module, $text_key, NULL, $text_attributes );
			$text_set[] = $id_text;
		}
		if( count($text_set) > 0 ) {
			$query = "SELECT texttran.id_text, tran.translation_text"
					."  FROM ".$this->_getTableTranslation()." AS tran "
					."  JOIN ".$this->_getTableTextTranslation()." AS texttran "
					."	WHERE tran.id_translation = texttran.id_translation AND tran.lang_code='".$lang_code."'"
					."	  AND texttran.id_text IN (".join(',',$text_set).")";
			if($trans_contains != '') $query .= " AND tran.translation_text LIKE '%".$trans_contains."%'";
			$rs = $this->_executeQuery( $query );
			while( list($id_text,$translation_text) = $GLOBALS["db"]->fetchRow($rs) ) {
				$text_result[$id_text][2] = $translation_text;
			}
		} else {
			if(isset($GLOBALS['page'])) $GLOBALS['page']->add( "<!-- getModuleLangTranslations: no translations for $platform, $module, $lang_code -->\n", 'debug' );
		}
		return $text_result;
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
		if( $GLOBALS["db"]->numRows($rs) == 0 ) {
			return FALSE;
		}
		list( $description ) = $GLOBALS["db"]->fetchRow($rs);
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
		if( $GLOBALS["db"]->numRows($rs) == 0 ) {
			return FALSE;
		}
		list( $attributes ) = $GLOBALS["db"]->fetchRow($rs);
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
		if( $GLOBALS["db"]->numRows($rs) == 0 ) {
			return FALSE;
		}
		list( $id_text ) = $GLOBALS["db"]->fetchRow($rs);

		$query = "SELECT id_translation FROM ".$this->_getTableTextTranslation()
				." WHERE id_text='".$id_text."'";
		$rs = $this->_executeQuery( $query );
		if( $GLOBALS["db"]->numRows($rs) > 0 ) {
			$arr_id_translations = array();
			while( list($id_translation) = $GLOBALS["db"]->fetchRow($rs))
				$arr_id_translations[] = $id_translation;
			$query = "DELETE FROM ".$this->_getTableTranslation()
					." WHERE id_translation IN "
					."	( ".join(',',$arr_id_translations)." )";
			$this->_executeQuery( $query );
		}


		$query = "DELETE FROM ".$this->_getTableTextTranslation()
				." WHERE id_text='".$id_text."'";
		$this->_executeQuery( $query );

		$query = "DELETE FROM ".$this->_getTableText()
				." WHERE id_text='".$id_text."'";
		$this->_executeQuery( $query );

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
	function updateKey( $key, $module = FALSE, $platform = FALSE, $description = FALSE, $attributes = FALSE, $overwrite = TRUE) {
		if( $module === FALSE )
			list($key,$module,$platform) = $this->decomposeKey( $key );
		elseif( $platform === FALSE )
			list($module,$platform) = $this->decomposeKey( $module );

		$query = "SELECT id_text FROM ".$this->_getTableText()
				." WHERE text_key = '".$key."' "
				."   AND text_module = '".$module."'"
				."   AND text_platform = '".$platform."'";
		$rs = $this->_executeQuery( $query );
		if( $GLOBALS["db"]->numRows($rs) == 0 ) {
			$query = "INSERT INTO ".$this->_getTableText()
					." (text_key, text_module, text_platform, text_description, text_attributes ) VALUES "
					." ('".$key."','".$module."','".$platform."','".$description."','".$attributes."') ";
			return $this->_executeQuery( $query );
		} elseif ($description !== FALSE) {
			
			if($overwrite === true) {
				
				list( $id_text ) = $GLOBALS["db"]->fetchRow( $rs );
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
	function updateTranslationC( $composed_key, $translation, $lang_code, $overwrite = TRUE ) {
		list($key,$module,$platform) = $this->decomposeKey( $composed_key );
		return $this->updateTranslation( $key, $module, $platform, $translation, $lang_code, $overwrite );
	}
	/**
	 *
	 **/
	function updateTranslation( $key, $module, $platform, $translation, $lang_code, $overwrite = TRUE) {
		$query = "SELECT tran.id_translation "
				." FROM ".$this->_getTableText()." AS text"
				." JOIN ".$this->_getTableTextTranslation()." AS texttran "
				." JOIN ".$this->_getTableTranslation()." AS tran "
				." WHERE text.id_text = texttran.id_text AND tran.id_translation = texttran.id_translation "
				." 	 AND tran.lang_code = '".$lang_code."'"
				."   AND text.text_platform = '".$platform."'"
				."   AND text.text_module = '".$module."'"
				."	 AND text.text_key = '".$key."'";
		$rs = $this->_executeQuery( $query );
		if( $rs === FALSE )
			return FALSE;
		if( $GLOBALS["db"]->numRows($rs) == 1 ) {
			
			if($overwrite === true) {
				list($id_translation) = $GLOBALS["db"]->fetchRow($rs);
				$query = "UPDATE ".$this->_getTableTranslation()
						."   SET translation_text='".$translation."'"
						." WHERE id_translation='".$id_translation."'";
				return $this->_executeQuery( $query );
			}
		} elseif( $GLOBALS["db"]->numRows($rs) == 0 ) {
			$query = "SELECT id_text FROM ".$this->_getTableText()
					." WHERE text_platform = '".$platform."'"
					."   AND text_module = '".$module."'"
					."	 AND text_key = '".$key."'";
			$rs = $this->_executeQuery( $query );
			if( $rs === FALSE )
				return FALSE;
			list($id_text) = $GLOBALS["db"]->fetchRow($rs);

			$query = "INSERT INTO ".$this->_getTableTranslation()
					." (translation_text,lang_code) VALUES "
					." ('".$translation."','".$lang_code."') ";
			if( ($id_translation = $this->_executeInsert($query)) === FALSE )
				return FALSE;

			$query = "INSERT INTO ".$this->_getTableTextTranslation()
					." (id_text,id_translation) VALUES "
					." ('".$id_text."','".$id_translation."') ";
			return $this->_executeQuery( $query );
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
		if( $GLOBALS["db"]->numRows( $rs ) !== 1 )
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
		while( list($lang_code) = $GLOBALS["db"]->fetchRow($rs) ) {
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
		$query = "SELECT lang_code, lang_description"
				." FROM ".$this->_getTableLanguage();
		$rs = $this->_executeQuery( $query );

		$result = array();
		while( list($lang_code, $lang_description) = $GLOBALS["db"]->fetchRow($rs) ) {
			$result[] = array( $lang_code, $lang_description);
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
		if( $GLOBALS["db"]->numRows( $rs ) !== 1 )
			return FALSE;

		list($description) = $GLOBALS["db"]->fetchRow( $rs );
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
		if( $GLOBALS["db"]->numRows( $rs ) !== 1 )
			return FALSE;

		list($lang_charset) = $GLOBALS["db"]->fetchRow( $rs );
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
		if( $GLOBALS["db"]->numRows( $rs ) !== 1 )
			return FALSE;

		list($lang_browsercode) = $GLOBALS["db"]->fetchRow( $rs );
		return $lang_browsercode;
	}

	/**
	 * update a lang_code
	 * @param string $lang_code code of lang to test
	 * @param string $lang_description optional
	 * @param string $lang_charset optional
	 * @param string $lang_brosercode optional
	 * @return TRUE if success, FALSE otherwise
	 **/
	function setLanguage( $lang_code, $lang_description = FALSE, $lang_charset = FALSE, $lang_brosercode = FALSE ) {
		$query = "SELECT lang_code"
				." FROM ".$this->_getTableLanguage()
				." WHERE lang_code='".$lang_code."'";
		$rs = $this->_executeQuery( $query );
		if( $GLOBALS["db"]->numRows( $rs ) !== 1 ) {
			return $this->insertLanguage($lang_code, $lang_description, $lang_charset, $lang_brosercode);
		} elseif( $lang_description !== FALSE ) {
			return $this->updateLanguage($lang_code, $lang_description, $lang_charset, $lang_brosercode);
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
	function updateLanguage($lang_code, $lang_description, $lang_charset = FALSE, $lang_brosercode = FALSE ) {
		$query = "UPDATE ".$this->_getTableLanguage()
				." SET lang_description='".$lang_description."'"
				.( ($lang_charset!==FALSE) ? ", lang_charset='".$lang_charset."'" : "" )
				.( ($lang_brosercode!==FALSE) ? ", lang_browsercode='".$lang_brosercode."'" : "" )
				." WHERE lang_code='".$lang_code."'";
		return $this->_executeQuery($query);
	}

	/**
	 * insert a lang_code entry
	 * @param string $lang_code
	 * @param string $lang_description
	 * @return bool TRUE if success, FALSE otherwise
	 **/
	function insertLanguage($lang_code, $lang_description, $lang_charset = FALSE, $lang_brosercode = FALSE ) {
		$query = "INSERT INTO ".$this->_getTableLanguage()
				." ( lang_code, lang_description "
				.( ($lang_charset !== FALSE) ? ", lang_charset" : "" )
				.( ($lang_brosercode !== FALSE) ? ", lang_browsercode" : "" )
				." )"
				." VALUES ('".$lang_code."','".$lang_description."'"
				.( ($lang_charset !== FALSE) ? ",'".$lang_charset."'" : "" )
				.( ($lang_brosercode !== FALSE) ? ",'".$lang_brosercode."'" : "" )
				.")";
		return $this->_executeQuery($query);
	}

	/**
	 * delete a lang_code entry
	 * @param string $lang_code
	 * @return bool TRUE if success, FALSE otherwise

	 **/
	function deleteLanguage($lang_code) {
		$query = "DELETE FROM ".$this->_getTableLanguage()
				." WHERE lang_code='".$lang_code."'";
		return $this->_executeQuery($query);
	}
}



?>
