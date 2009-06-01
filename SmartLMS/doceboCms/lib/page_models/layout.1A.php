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

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS["where_cms"]."/class/class.layout.php");

class Layout_1A extends Layout {

	function Layout_1A() {

		$this->main_block_number = 2;
		$this->content_block_number = 0;

		$this->param_main_block = array(
			0 => array('type' => 'menu', 'width' => '25', 'sequence' => 0 ),
			1 => array('type' => 'content', 'width' => '75', 'sequence' => 1 )
		);
		return;
	}

}

$layout = new Layout_1A();

?>