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

if(!defined("IN_DOCEBO")) die("You can't access this file directly!");

class HtmlChatEmoticons {
	
	var $_ext;
	
	var $_regexp;
	
	var $_substitute;
	
	function HtmlChatEmoticons($ext = 'png') {
		
		$this->_ext = $ext;
		$this->setChatRegExp();
	}

	function getChatEmoticon($name, $ext="gif") {
	
		return sprintf('<img  src="%s" alt="%s" title="%s" width="22" height="22" />', 
			Template::image()."emoticons/".$name.".".$this->_ext, 
			$name, 
			$name);
	}
	
	function setChatRegExp() {
		
		$arr_emot_allowed 	= $this->getEmotionArr();
		$this->_regexp 		= array_values($arr_emot_allowed);
		$this->_substitute 	= array_keys($arr_emot_allowed);
		
		while(list($index, $e_name) = each($this->_substitute)) {
			
			$this->_substitute[$index] = $this->getChatEmoticon($e_name);
		}
		reset($this->_substitute);
	}
	
	function drawEmoticon($text) {
		
		$text = preg_replace($this->_regexp, $this->_substitute, $text);
		return $text;
	}
	
	function getEmotionArr() {
		
		$arr_emot_allowed = array();
		$arr_emot_allowed['smile'] 			= "/:[-o]?\\)/si";
		$arr_emot_allowed['teeth'] 			= "/:[-]?d/si";
		$arr_emot_allowed['wink'] 			= "/;[-]?\\)/si";
		$arr_emot_allowed['tongue'] 		= "/:[-]?p/si";
		$arr_emot_allowed['confused'] 		= "/:[-]?s/si";
		$arr_emot_allowed['omg'] 			= "/:[-]?o/si";
		$arr_emot_allowed['cry'] 			= "/:?'[-]?(\\(|\\[)/si";
		$arr_emot_allowed['sad'] 			= "/:[-]\\(/si";
		$arr_emot_allowed['embarassed'] 	= "/:[-]\\$/si";
		$arr_emot_allowed['angry'] 			= "/:[-]@/si";
		$arr_emot_allowed['kiss'] 			= "/:[-]x/si";
		$arr_emot_allowed['thumbs_up'] 		= "/\\(y\\)/si";
		$arr_emot_allowed['thumbs_down'] 	= "/\\(n\\)/si";
		$arr_emot_allowed['boy'] 			= "/\\(z\\)/si";
		$arr_emot_allowed['boy_hug'] 		= "/\\(\\{\\)/si";
		$arr_emot_allowed['girl_hug'] 		= "/\\(\\}\\)/si";
		$arr_emot_allowed['girl'] 			= "/\\(x\\)/si";
		$arr_emot_allowed['love'] 			= "/\\(l\\)/si";
		$arr_emot_allowed['unlove'] 		= "/\\(u\\)/si";
		$arr_emot_allowed['rose'] 			= "/\\(f\\)/si";
		$arr_emot_allowed['wilted_rose'] 	= "/\\(w\\)/si";
		$arr_emot_allowed['clock'] 			= "/\\(o\\)/si";
		$arr_emot_allowed['computer'] 		= "/\\(co\\)/si";
		$arr_emot_allowed['phone'] 			= "/\\(t\\)/si";
		$arr_emot_allowed['camera'] 		= "/\\(p\\)/si";
		$arr_emot_allowed['film'] 			= "/\\(~\\)/si";
		$arr_emot_allowed['note'] 			= "/\\(8\\)/si";
		$arr_emot_allowed['email'] 			= "/\\(e\\)/si";
		$arr_emot_allowed['messenger'] 		= "/\\(m\\)/si";
		$arr_emot_allowed['cup'] 			= "/\\(c\\)/si";
		$arr_emot_allowed['cake'] 			= "/\\(\\^\\)/si";
		$arr_emot_allowed['lightbulb'] 		= "/\\(i\\)/si";
		$arr_emot_allowed['star'] 			= "/\\(\\*\\)/si";
		$arr_emot_allowed['present'] 		= "/\\(g\\)/si";
		// :-) :-d ;-) :-P :-s :-o :'-( :-( :-$ :-@ :-x (y) (n) (z) ({) (}) (x) (l) (u) (f) (w) (o) (co) (t) (p) (~) (8) (e) (m) (c) (^) (i) (*) (g)
		return $arr_emot_allowed;
	}
	
}

?>