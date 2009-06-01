<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS["where_framework"]."/lib/lib.profile_search.php");


class CmsProfileSearch extends ProfileSearch {


	function CmsProfileSearch() {


		parent::ProfileSearch();
	}


	/**
	 * instance the viewer class of the profile
	 */
	function initViewer() {

		$this->_ps_viewer = new CmsProfileSearchViewer($this);
	}


}

// ========================================================================================================== //
// ========================================================================================================== //
// ========================================================================================================== //

class CmsProfileSearchViewer extends ProfileSearchViewer {

	/**
	 * print the title of the page
	 * @param mixed $text the title of the area, or the array with zone path and name
	 * @param string $image the image to load before the title
	 *
	 * @return string the html code for space open
	 */
	function getTitleArea($text, $image = '') {

		return getCmsTitleArea($text, $image);
	}

	/**
	 * Print the head of the module space after the getTitle area
	 * @return string the html code for space open
	 */
	function getHead() {

		return ''."\n";
	}

	/**
	 * Print the footer of the module space
	 * @return string the html code for space close
	 */
	function getFooter() {

		return ''."\n";
	}

}


?>
