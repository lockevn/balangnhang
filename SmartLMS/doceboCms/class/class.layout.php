<?php

/*************************************************************************/
/* DOCEBO - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

class Layout {
	
	//number of main block it can be 1 2 or 3 1 content block + 1 or 2 menu
	var $main_block_number = 1;
	
	//number of element in contentblock
	var $content_block_number = 0;
	
	var $param_main_block = array();
	
	var $param_content_block = array();
	
	function Layout() {
		return;
	}
	
	function getParamMainNumber() {
		return $this->main_block_number;
	}
	
	function getParamContentNumber() {
		return $this->content_block_number;
	}
	
	function getParamMain() {
		return $this->param_main_block;
	}
	
	function getParamContent() {
		return $this->param_content_block;
	}
}

?>