<?php

/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

class Template {
	
	function path() {
		
		return $GLOBALS['where_scs'].'/templates/standard/video_conference';
	}
	
	function image() {
		
		return Template::path().'/images';
	}
	
	function style() {
		
		return Template::path().'/style';
	}

	function layout() {
		
		return Template::path().'/layout';
	}
}

class Layout {
	
	function heading() {
		
		$html = '';
		$bw_info = getBrowserInfo();
		if($bw_info['browser'] != 'msie') $html .= '<?xml version="1.0" encoding="'.Layout::charset().'"?>'."\n";
		/*$html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"'."\n".
			'	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'."\n".
			'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.Layout::lang_code().'">'."\n";*/
		return $html;
	}
	
	function charset() 					{ return 'utf-8'; }
	
	function lang_code() 				{ return 'it'; }
	
	function image() 					{ return Template::image(); }
	
	function style() 					{ return Template::style(); }
	
	function title() 					{ return 'Sistema di videoconferenza (beta)'; }
	
	function description() 				{ return 'description'; }
	
	function keyword() 					{ return 'keyword'; }
	
	function head() 					{ return $GLOBALS['page']->getContent('page_head')."\n"; }
	
	function blind_navigation() 		{ return $GLOBALS['page']->getContent('blind_navigation'); }
	
	function page_header() 				{ return $GLOBALS['page']->getContent('header')."\n"; }
	
	function menu($menu_code) 			{ 
		
		switch($menu_code) {
			case "menu_over" : return $GLOBALS['page']->getContent('menu_over'); 
			case "menu" : return $GLOBALS['page']->getContent('menu');
		}
	}
	
	function content() 					{ return $GLOBALS['page']->getContent('content')."\n"; }
	
	function page_footer() 				{ return $GLOBALS['page']->getContent('footer')."\n"; }
	
	function closure() 					{ return '</html>'; }
	
	function javascript_onload() 		{ return $GLOBALS['page']->getContent('onload'); }
	
	function parse_docebo_xml($input) {

		// replace tags
		$output = preg_replace_callback(	'/<docebo:([_a-z]+)-([-_a-z]+)\s+([^>]+)>/Ui', 
											"parse_docebo_tag", 
											$input );

		// replace vars											
		$output = preg_replace_callback(	'/{--([\w\d\_]+)--}/Ui', 
											"parse_docebo_var", 
											$output );
		// ok all done
		return $output;
	}
	
	function get_where($base, $rel = true) {
		
		$rel = ( $rel ? '_relative' : '' );
		switch($base) {
			case "basesite" 	: return $GLOBALS['where_scs'.$rel].'/modules/video_conference';				break;
			case "addons" 		: return $GLOBALS['where_framework'.$rel].'/addons';							break;
			case "framework" 	: return $GLOBALS['where_framework'.$rel];										break;
			case "template" 	: return $GLOBALS['where_scs'.$rel].'/templates/standard/video_conference';		break;
			case "files" 		: return $GLOBALS['where_files'.$rel];											break;
			case "siteurl" 		: return $GLOBALS['base_url'];													break;
		}
		
	}
	
	/**
	 * Insipirated from roundcube project 
	 * Author: Thomas Bruederli <roundcube@gmail.com> 
	 */
	function parse_template($name = 'main', $exit = TRUE) {
		
		$templ = '';
		$path = Template::layout().'/'.$name.'.html';
		
		if($fp = @fopen($path, 'r')) {
			
			$templ = fread($fp, filesize($path));
			fclose($fp);
		} else {
			die('Template file not found : "'.$path.'"');
			return false;
		}
		
		// parse for specialtags
		$output = Layout::heading()
			.Layout::parse_docebo_xml($templ);
  
		//return trim(parse_with_globals($output)), $skin_path);
		
		return trim($output);
	}
	
	/**
	 * Insipirated from roundcube project 
	 * Author: Thomas Bruederli <roundcube@gmail.com> 
	 */
	function parse_php_template($name = 'main', $exit = TRUE) {
		
		$templ = '';
		$path = Template::layout().'/'.$name.'.html';
		
		// save current cached content
		$old_ob = ob_get_contents();
		ob_clean();
		ob_start();
		
		// include and parse php of the template file
		if(!file_exists($path)) {
			die('Template file not found : "'.$path.'"');
			return false;
		}
		@include($path);
		
		//take the output of the included file
		$templ = ob_get_contents();
		
		// restore buffer
		ob_clean();
		ob_start();
		echo($old_ob);
		
		// parse for specialtags
		$output = Layout::heading()
			.Layout::parse_docebo_xml($templ);
  
		//return trim(parse_with_globals($output)), $skin_path);
		
		return trim($output);
	}
}

function parse_attrib_string($str) {
	
	$attrib = array();
	preg_match_all('/\s*([-_a-z]+)=["]([^"]+)["]?/i', stripslashes($str), $regs, PREG_SET_ORDER);
	
	// convert attributes to an associative array (name => value)
	if(!$regs) return $attrib;
	foreach ($regs as $attr)
		$attrib[strtolower($attr[1])] = $attr[2];
	return $attrib;
}

function parse_docebo_var($matches) {
	
	$var 	= strtolower($matches[1]);
	
	// execute command
	switch ($var) {
		case "page_title" : {
			
			return Layout::title();
		};break;
		case "template_path" : {
			
			return Layout::get_where('template');
		};break;
		case "vc_path" : {
			
			return 'modules/video_conference';
		};break;
		
	}	
	return '';
}

function parse_docebo_tag($matches) {
	
	$source = strtolower($matches[1]);
	$tag 	= strtolower($matches[2]);
	$args 	= parse_attrib_string($matches[3]);
	
	// execute command
	if($source == 'general')
	switch ($tag) {
		
		case "var" : {
			
			return ( isset($GLOBALS['substitue'][$args['name']]) ? $GLOBALS['substitue'][$args['name']] : '' );
		};break;
		case "meta" : {
			
			return '<meta http-equiv="Content-Type" content="text/html; charset='.Layout::charset().'" />';
		};break;
		
		case "object" : {
			
			if($args['name'] == 'pagetitle') return Layout::title();
		};break;
		
		case "style" : {
			
			return '<link type="text/css" href="'.Layout::get_where($args['from']).$args['file'].'" rel="stylesheet" />';
		};break;
		
		case "script" : {
			
			return '<script type="text/javascript" src="'.Layout::get_where($args['from']).$args['file'].'"></script>';
		};break;
		
		//include a file 
		case 'include': {
			
			if($fp = @fopen($args['file'], 'r')) {
				$incl = fread($fp, filesize($args['file']));
				fclose($fp);        
				return Layout::parse_docebo_xml($incl);
			}
		}
		break;
	}	
	return '';
}

?>