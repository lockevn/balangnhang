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

/**
 * This is a singleton class for page rendering, at now the application mustn't use the echo.
 * A module must append the text to display in the proper area
 *
 * @package  admin-library
 * @subpackage layout
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id: lib.pagewriter.php 852 2006-12-16 14:04:44Z giovanni $
 */

/**
 * Global unique instance of the PageWriter
 */

if(!isset($GLOBALS['page'])) $GLOBALS['page'] = null;

/**
 * class for zone management
 * @author 		Emanuele Sandri <esandri@tiscali.it>
 **/
class PageZone {
	/** name ho the zone */
	var $_name;
	/** start block */
	var $_startOut = array();
	/** content block */
	var $_contentOut = array();
	/** end block */
	var $_endOut = array();

	var $print_if_empty;

	function PageZone( $name, $print_if_empty = false ) {
		$this->name = $name;
		$this->print_if_empty = $print_if_empty;
	}

	/**
	 * Prepend one element to the beginning of start block
	 * @access public
	 * @param string $text the text to insert
	 */
	function insertStart( $text ) {
		array_unshift($this->_startOut, $text);
	}
	/**
	 * Append one element to the end of start block
	 * @access public
	 * @param string $text the text to append
	 */
	function appendStart( $text ) {
		array_push($this->_startOut, $text );
	}

	/**
	 * Prepend one element to the beginning of content block
	 * @access public
	 * @param string $text the text to insert
	 */
	function insertContent( $text ) {
		array_unshift($this->_contentOut, $text);
	}
	/**
	 * Append one element to the end of contet block
	 * @access public
	 * @param string $text the text to append
	 */
	function appendContent( $text ) {
		array_push($this->_contentOut, $text );
	}

	/**
	 * Prepend one element to the beginning of end block
	 * @access public
	 * @param string $text the text to insert
	 */
	function insertEnd( $text ) {
		array_unshift($this->_endOut, $text);
	}
	/**
	 * Append one element to the end of content block
	 * @access public
	 * @param string $text the text to append
	 */
	function appendEnd( $text ) {
		array_push($this->_endOut, $text );
	}

	/**
	 * Default operation for start block is append
	 * @access public
	 * @param string $text the text to append
	 */
	function addStart( $text ) {
		$this->appendStart( $text );
	}

	/**
	 * Default operation for content block is append
	 * @access public
	 * @param string $text the text to append
	 */
	function addContent( $text ) {
		$this->appendContent( $text );
	}

	/**
	 * Default operation for end block is insert
	 * @access public
	 * @param string $text the text to append
	 */
	function addEnd( $text ) {
		$this->insertEnd( $text );
	}

	/**
	 * Default operation for a zone is add in content
	 * @access public
	 * @param string $text the text to append
	 */
	function add( $text ) {
		$this->addContent( $text );
	}

	function replace( $needle, $text ) {
		
		foreach($this->_contentOut as $k => $value) {
			
			if(strpos($value, $needle) !== false) {
				$this->_contentOut[$k] = $text;
			}
		}
		
	}

	/**
	 * remove all the output generated
	 * @param bool $full if true clean the start and end alos, otherwise only the content
	 */
	function clean($full = true) {
		
		if($full) $this->_startOut = array();
		$this->_contentOut = array();
		if($full) $this->_endOut = array();
	}

	/**
	 * to get output
	 */
	function getContent() {
		$out = '';

		if(empty($this->_contentOut) && $this->print_if_empty === false) return $out;

		$out.=implode($this->_startOut);
		$out.=implode($this->_contentOut);
		$out.=implode($this->_endOut);

		$out =fillSiteBaseUrlTag($out);

		return $out;
	}
}

class PageZoneLang extends PageZone {
	
	
	function getContent() {
		$out = '';
		
		if($GLOBALS['current_user']->getUserLevelId() == ADMIN_GROUP_USER) return $out;
		
		if(empty($this->_contentOut) && $this->print_if_empty === false) return $out;
		
		$out .= '<div id="def_lang" class="def_lang">'."\n"
				.implode($this->_startOut)
		
				.'<div id="link_container">'
				.implode($this->_contentOut)
				.'</div>'
				
				.'<a id="command" href="#" onclick="YAHOO.Animation.BlindToggle(\'link_container\');" >'
					.def('_NOT_TRANSLATED', 'standard', 'framework').' ('.count($this->_contentOut).')'
				.'</a>'	
				.'<script type="text/javascript">'
					.'YAHOO.util.Dom.get(\'link_container\').style.display = \'none\';'
				.'</script>'
				
				.implode($this->_endOut)
				. '</div>'."\n";

		$out =fillSiteBaseUrlTag($out);

		return $out;
	}
}

class PageWriter {

	/**
	 * indicate the current working zone if setted
	 *
	 * @access private
	 */
	var $_current_work_zone = null;

	/**
	 * array of zones
	 **/
	var $_zones = array();

	/**
	 * PageWriter constructor
	 *
	 * @access private
	 */
	function PageWriter() {
	}

	/**
	 * Add a zone
	 **/
	function addZone( $zone, $print_if_empty = false ) {
		$this->_zones[$zone] = new PageZone( $zone, $print_if_empty );
	}

	function getWorkingZone() {
		return $this->_current_work_zone;
	}

	function setWorkingZone($zone) {
		return $this->_current_work_zone = $zone;
	}

	function _getZone( $zone ) {
		return ($zone===null)?($this->_current_work_zone):$zone;
	}

	/**
	 * Write the passed string into a zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function add($content, $zone = null) {

		if(!isset($this->_zones[$this->_getZone($zone)])) {
			doDebug('Warning: you are trying to write in a zone that doesn\'t exist ('.$this->_getZone($zone).')');
		}
		else $this->_zones[$this->_getZone($zone)]->add($content);
	}

	/**
	 * Write the passed string into a zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function replace($needle, $content, $zone = null) {

		if(!isset($this->_zones[$this->_getZone($zone)])) {
			doDebug('Warning: you are trying to write in a zone that doesn\'t exist ('.$this->_getZone($zone).')');
		}
		else $this->_zones[$this->_getZone($zone)]->replace($needle, $content);
	}

	/**
	 * Write the passed string into the starting block of a zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function addStart($content, $zone = null) {
		$this->_zones[$this->_getZone($zone)]->addStart($content);
	}

	/**
	 * Write the passed string into the content block of a zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function addContent($content, $zone = null) {
		$this->_zones[$this->_getZone($zone)]->addContent($content);
	}

	/**
	 * Write the passed string into the end block of a zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function addEnd($content, $zone = null) {
		$this->_zones[$this->_getZone($zone)]->addEnd($content);
	}

	/**
	 * Write the passed string at the end of start block in zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function appendStart($content, $zone = null) {
		$this->_zones[$this->_getZone($zone)]->appendStart($content);
	}

	/**
	 * Write the passed string at the end of content block in zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function appendContent($content, $zone = null) {
		$this->_zones[$this->_getZone($zone)]->appendContent($content);
	}

	/**
	 * Write the passed string at the end of end block in zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function appendEnd($content, $zone = null) {
		$this->_zones[$this->_getZone($zone)]->appendEnd($content);
	}

	/**
	 * Write the passed string at the beginning of start block in zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function insertStart($content, $zone = null) {
		$this->_zones[$this->_getZone($zone)]->insertStart($content);
	}

	/**
	 * Write the passed string at the beginning of content block in zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function insertContent($content, $zone = null) {
		$this->_zones[$this->_getZone($zone)]->insertContent($content);
	}

	/**
	 * Write the passed string at the beginning of end block in zone
	 * @param string $content
	 * @param string $zone optional zone id
	 * @return nothing
	 * @access public
	 */
	function insertEnd($content, $zone = null) {
		$this->_zones[$this->_getZone($zone)]->insertEnd($content);
	}
	
	/**
	 * remove all the output generated
	 * @param bool $full if true clean the start and end alos, otherwise only the content
	 */
	function clean($zone = null, $full = true) {
		
		$this->_zones[$this->_getZone($zone)]->clean($full);
	}

	/**
	 * function to get output for page
	 */
	 function getContent($zone = false) {

		if($zone === false) {
			
			 $out = '';
			 $pz = current($this->_zones);
			 while( $pz !== FALSE ) {
				 $out .= $pz->getContent();
				 $pz = next($this->_zones);
			 }
			 reset( $this->_zones );
			 return $out;
		} else {
			return $this->_zones[$zone]->getContent();
		}
	 }
}

/**
 * this class is the default page for docebo
 */
class StdPageWriter extends PageWriter {

	function StdPageWriter() {
		$this->addZone( 'page_head' );
		$this->addZone( 'blind_navigation' );
		$this->addZone( 'header' );
		$this->addZone( 'menu_over' );
		$this->addZone( 'menu', true );
		$this->addZone( 'content', true );
		$this->addZone( 'footer' );
		$this->addZone( 'debug' );
		//$this->addZone( 'def_lang' );
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

		$this->addStart( '<ul id="blind_navigation" class="blind_navigation">', 'blind_navigation' );
		$this->addEnd( '</ul>'."\n", 'blind_navigation' );

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
	 * Create an instance of StdPageWriter
	 * @static
	 *
	 * @return an istance of StdPageWriter
	 *
	 * @access public
	 */
	function &createInstance() {
		if($GLOBALS['page'] === null) {
			$GLOBALS['page'] = new StdPageWriter();
		}
		return $GLOBALS['page'];
	}

}


/**
 * this class is the default page for the public area for the cms
 */
class onecolPageWriter extends PageWriter {

	function onecolPageWriter() {
		$this->addZone( 'page_head' );
		$this->addZone( 'blind_navigation' );
		$this->addZone( 'header' );
		$this->addZone( 'menu_over' );
		$this->addZone( 'content' );
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

		$this->addStart( '<ul id="blind_navigation" class="blind_navigation">', 'blind_navigation' );
		$this->addEnd( '</ul>'."\n", 'blind_navigation' );

		$this->addStart( '<div id="header" class="layout_header">'."\n",
			'header' );
		$this->addEnd( '</div>'."\n",
			'header' );

		$this->addStart('<div id="menu_over" class="layout_menu_over">'."\n", 'menu_over');
		$this->addEnd('</div>'."\n", 'menu_over');

		$this->addStart('<div class="layout_colum_container">'."\n",
						'content');

		$this->addEnd('<div class="no_float"></div>'."\n"
						.'</div>'."\n",
						'content');

		$this->addStart( '<div id="footer" class="layout_footer">'."\n", 'footer');
		$this->addEnd( '</div>'."\n"
						.'</body>'."\n"
						.'</html>',
						'footer' );
	}

	/**
	 * Create an instance of StdPageWriter
	 * @static
	 *
	 * @return an istance of StdPageWriter
	 *
	 * @access public
	 */
	function &createInstance() {
		if($GLOBALS['page'] === null) {
			$GLOBALS['page'] = new onecolPageWriter();
		}
		return $GLOBALS['page'];
	}

}


class emptyPageWriter extends PageWriter {

	function emptyPageWriter() {
		$this->addZone( 'page_head' );
		$this->addZone( 'blind_navigation' );
		$this->addZone( 'header' );
		$this->addZone( 'menu_over' );
		$this->addZone( 'content' );
		$this->addZone( 'footer' );
		$this->addZone( 'debug' );
		$this->_zones['def_lang'] = new PageZoneLang( 'def_lang', false );


		$this->addStart( '', 'page_head' );
		$this->addContent( '', 'page_head' );
		$this->addEnd( '', 'page_head');

		$this->addStart( '<ul id="blind_navigation" class="blind_navigation">', 'blind_navigation' );
		$this->addEnd( '</ul>'."\n", 'blind_navigation' );

		$this->addStart( '','header' );
		$this->addEnd( '','header' );

		$this->addStart('', 'menu_over');
		$this->addEnd('', 'menu_over');

		$this->addStart('','content');

		$this->addEnd('',
						'content');

		$this->addStart( '', 'footer');
		$this->addEnd( '', 'footer' );
	}

	/**
	 * Create an instance of StdPageWriter
	 * @static
	 *
	 * @return an istance of StdPageWriter
	 *
	 * @access public
	 */
	function &createInstance() {
		if($GLOBALS['page'] === null) {
			$GLOBALS['page'] = new emptyPageWriter();
		}
		return $GLOBALS['page'];
	}

}

?>
