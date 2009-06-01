<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2006                                                    */
/* http://www.docebo.org                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @package admin-library
 * @subpackage interaction
 * @version  $Id:  $
 *
 * Class to easily manage search form and filters.
 */

// ----------------------------------------------------------------------------




class SearchUI {

	var $object_key=NULL;
	var $lang=NULL;
	var $session_var="search_info";


	/**
	 * @param string $object_key a keyword the identify the search form / filter set
	 * @param string $platform
	 */
	function SearchUI($object_key, $session_var=FALSE, $platform=FALSE) {

		if ($platform === FALSE)
			$platform="framework";

		if ($session_var !== FALSE) {
			$this->_setSessionVar($session_var);
		}

		$this->object_key=$object_key;
		$this->lang=& DoceboLanguage::createInstance('search', $platform);
	}


	function getKey() {
		return $this->object_key;
	}


	function _setSessionVar($session_var) {
		$this->session_var=$session_var;
	}


	function _getSessionVar() {
		return $this->session_var;
	}


	/**
	 * @param object $form the user initialized form object to use
	 * @param string $url the url wich the form must point to
	 *
	 * @return string the html with the necessary code to open a search form
	 */
	function openSearchForm(&$form, $url) {
		$res="";

		if ((isset($_POST["clear_search"])) && ($this->sameSource())) {
			$this->clearSearchFilter();
		}

		$res.=$form->openForm("search_form_".$this->getKey(), $url);
		$res.=$form->openElementSpace();

		$res.=$form->getHidden("do_search", "do_search", 1);
		$source_key="search_source_".$this->getKey();
		$res.=$form->getHidden($source_key, $source_key, $this->getKey());

		return $res;
	}


	/**
	 * @param object $form the user initialized form object to use
	 *
	 * @return string the html with the necessary code to close a search form
	 */
	function closeSearchForm(&$form) {
		$res="";

		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('search', 'search', $this->lang->def('_SEARCH'));
		$res.=$form->getButton('clear_search', 'clear_search', $this->lang->def('_CLEAR_SEARCH'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function &_getSearchInfo() {
		$res=array();

		if (isset($_SESSION[$this->_getSessionVar()])) {
			$res=unserialize(stripslashes($_SESSION[$this->_getSessionVar()]));
		}

		//-DEBUG-// echo "<pre>\n"; print_r($res); echo "\n</pre>";
		return $res;
	}


	function _saveSearchInfo(& $info) {
		$_SESSION[$this->_getSessionVar()]=addslashes(serialize($info));
	}


	/**
	 * remove all search filters
	 */
	function clearSearchFilter() {
		$search_info=& $this->_getSearchInfo();

		$filter_name=$this->getKey()."_filter";
		unset($search_info[$filter_name]);
		$this->_saveSearchInfo($search_info);
	}


	/**
	 * @return boolean true if search data has been sent
	 */
	function doSearch() {

		if (($this->sameSource()) && (isset($_POST["do_search"])))
			$res=TRUE;
		else
			$res=FALSE;

		//-DEBUG-// echo "doSearch: "; var_dump($res); echo "<br /><br />";
		return $res;
	}


	/**
	 * @return boolean true if search data has been sent from the same form
	 */
	function sameSource() {
		$source_key="search_source_".$this->getKey();

		if (!isset($_POST[$source_key]))
			$res=FALSE;
		else
			$res=($_POST[$source_key] == $this->getKey() ? TRUE : FALSE);

		//-DEBUG-// echo $source_key.": "; var_dump($res); echo "<br /><br />";
		return $res;
	}


	/**
	 * If search data are sent, then apply search items
	 *
	 * How it works: if you pass as a first parameter an array the function
	 * will call the setSearchItem method with both name and value properties
	 * according to array key=>value association.
	 * Else it will read the list of parameters and call the setSearchItem
	 * method with only the name parameter.
	 */
	function applySearch() {
		if (($this->doSearch()) && (func_num_args() > 0)) {

			if (is_array(func_get_arg(0))) {

				$search_items=func_get_arg(0);

				foreach($search_items as $name=>$value) {
					$this->setSearchItem($name, $value);
				}
			}
			else {

				$arg_list=func_get_args();

				foreach($arg_list as $search_item) {

					if (!empty($search_item))
						$this->setSearchItem($search_item);

				}
			}
		}
	}


	/**
	 * @param string $name the name of the filter
	 * @param string $type the value of the filter
	 */
	function setSearchItem($name, $value=FALSE) {
		$search_info=& $this->_getSearchInfo();

		$filter_name=$this->getKey()."_filter";
		$set_val=FALSE;

		if ($value !== FALSE)
			$set_val=$value;
		else if (isset($_POST[$name]))
			$set_val=$_POST[$name];

		if (($set_val === FALSE) || (empty($set_val))) {
			if (isset($search_info[$filter_name][$name]))
				unset($search_info[$filter_name][$name]);
		}
		else {
			$search_info[$filter_name][$name]=$set_val;
		}
		$this->_saveSearchInfo($search_info);
	}


	/**
	 * @param string $name the name of the filter
	 * @param string $type the default datatype of the filter
	 *
	 * @return mixed the value of the requested filter
	 */
	function getSearchItem($name, $type, $default_val=FALSE) {
		$search_info=& $this->_getSearchInfo();

		$filter_name=$this->getKey()."_filter";

		if (isset($search_info[$filter_name][$name])) {
			return $search_info[$filter_name][$name];
		}
		else {

			switch ($type) {
				case "string": {
					return ($default_val !== FALSE ? $default_val : "");
				} break;
				case "bool": {
					return ($default_val !== FALSE ? $default_val : FALSE);
				} break;
				case "int":
				case "integer":{
					return ($default_val !== FALSE ? $default_val : 0);
				} break;
			}
		}
	}


	/**
	 * Switch the show/hide form flag
	 */
	function showHideSearchForm() {
		$search_info=& $this->_getSearchInfo();

		$var_name="hide_".$this->getKey()."_search_form";

		if ((!isset($search_info[$var_name])) || (!$search_info[$var_name])) {
			$search_info[$var_name]=1;
		}
		else {
			unset($search_info[$var_name]);
		}

		$this->_saveSearchInfo($search_info);
	}


	/**
	 * @param boolean $default the default value that has to be returned
	 *
	 * @return boolean let you know if you have or not to show the search form
	 */
	function getShowSearchForm($default=TRUE) {
		$search_info=& $this->_getSearchInfo();
		$res=$default;

		$var_name="hide_".$this->getKey()."_search_form";

		if ((isset($search_info[$var_name])) && ($search_info[$var_name]))
			$res=FALSE;

		return $res;
	}


}



?>
