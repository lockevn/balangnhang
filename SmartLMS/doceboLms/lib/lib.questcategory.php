<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system									*/
/* ============================================							*/
/*																			*/
/* Copyright (c) 2005														*/
/* http://www.docebo.com													*/
/*																			*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Questcategory {
	
	function Questcategory() {}
	
	function getCategory() {
		
		//search query
		$query_quest_cat = "
		SELECT idCategory, name 
		FROM ".$GLOBALS['prefix_lms']."_quest_category 
		ORDER BY name";
		$categories = array( 0 => def('_TEST_CATNONE', 'test', 'lms') );
		$re_quest_cat = mysql_query($query_quest_cat);
		while(list($id, $title) = mysql_fetch_row($re_quest_cat)) {
			
			$categories[$id] = $title;
		}
		return $categories;
	}
	
	function getInfoAboutCategory($category) {
		
		//search query
		$query_quest_cat = "
		SELECT idCategory, name 
		FROM ".$GLOBALS['prefix_lms']."_quest_category 
		WHERE idCategory IN ( ".implode(',', $category)." ) 
		ORDER BY name";
		$categories = array( 0 => def('_TEST_CATNONE', 'test', 'lms') );
		$re_quest_cat = mysql_query($query_quest_cat);
		while(list($id, $title) = mysql_fetch_row($re_quest_cat)) {
			
			$categories[$id] = $title;
		}
		return $categories;
	}
}

?>