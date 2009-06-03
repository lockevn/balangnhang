<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2006                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_framework'].'/lib/lib.pagewriter.php');

/** 
 * this class is the default page for docebolms
 */
class LmsPageWriter extends PageWriter {
	
	function LmsPageWriter() {
		$this->addZone( 'page_head' );
		$this->addZone( 'blind_navigation' );
		$this->addZone( 'header' );
		$this->addZone( 'quickbar' );
		$this->addZone( 'menu_over' );
		$this->addZone( 'menu', true );
		$this->addZone( 'content', true );
		$this->addZone( 'footer' );
		$this->addZone( 'debug' );
		$this->_zones['def_lang'] = new PageZoneLang( 'def_lang', false );
		
		$browser_code = $GLOBALS['globLangManager']->getLanguageBrowsercode(getLanguage());
		$pos = strpos($browser_code, ';');
		if($pos !== false) $browser_code = substr($browser_code, 0, $pos);
		
		$browser = getBrowserInfo();
		if($browser["browser"] !== 'msie') {
				
			// The world is not ready for this right now, all the xml not valid will not be interpretated form the serious borwsers
			//header("Content-Type: application/xhtml+xml; charset=".getUnicode()."");
			header("Content-Type: text/html; charset=".getUnicode()."");
			$this->addStart('<?xml version="1.0" encoding="'.getUnicode().'"?'.'>'."\n", 'page_head' );
		} else {
			header("Content-Type: text/html; charset=".getUnicode()."");
		}
		
		$this->addStart( ''
			.'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"'."\n"
			.'	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'."\n"
			.'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$browser_code.'">'."\n"
			.'<head>',
			'page_head' );
		$this->addContent( '' 
			.'	<meta http-equiv="Content-Type" content="text/html; charset='.getUnicode().'" />'."\n"
			.'	<meta name="Copyright" content="Docebo srl" />'."\n"
			.'	<link rel="Copyright" href="http://www.docebo.com" title="Copyright Notice" />'."\n"
			.'	<link href="/favicon.ico" rel="shortcut icon" />'."\n",
			'page_head' );
		$this->addEnd( '</head>'."\n"
			.'<body class="yui-skin-docebo yui-skin-sam">'."\n", 
			'page_head');
		
		$this->addStart( '<ul id="blind_avigation" class="blind_navigation">', 'blind_navigation' );
		$this->addEnd( '</ul>'."\n", 'blind_navigation' );
		
		$this->addStart( '<div id="quickbar" class="quickbar">', 'quickbar' );
		$this->addEnd( '</div>'."\n", 'quickbar' );
		
		$this->addStart( '<div id="header" class="layout_header">'."\n",
			'header' );
		$this->addEnd( '</div>'."\n", 
			'header' );
		
		$this->addStart('<div id="menu_over" class="layout_menu_over">'."\n", 'menu_over');
		$this->addEnd('</div>'."\n", 'menu_over');
			
		$this->addStart('<div class="layout_colum_container">'."\n"
					   .'<div id="menu" class="layout_colum_left">'."\n", 
					   'menu');
		$this->addEnd('</div>'."\n", 'menu');
		
		$this->addStart('<div id="content" class="layout_colum_right">'."\n",
						'content');
		$this->addEnd('</div>'."\n"
						.'<div class="no_float"></div>'."\n"
						.'</div>'."\n", 
						'content');

		$this->addStart( '<div id="footer" class="layout_footer">'."\n", 'footer');
		$this->addEnd( '</div>'."\n"
						.'</body>'."\n"
						.'</html>',
						'footer' );
	}
	
	/**
	 * Create an instance of LmsPageWriter
	 * @static
	 *
	 * @return an istance of LmsPageWriter
	 *
	 * @access public
	 */
	function &createInstance() {
		if($GLOBALS['page'] === null) {
			$GLOBALS['page'] = new LmsPageWriter();
		}
		return $GLOBALS['page'];
	}
	
}

?>