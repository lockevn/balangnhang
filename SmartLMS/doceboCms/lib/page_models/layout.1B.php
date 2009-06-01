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

class Layout_1B extends Layout {

	function Layout_1B() {

		$this->main_block_number = 3;
		$this->content_block_number = 0;

		$this->param_main_block = array(
			array('type' => 'menu', 'width' => '25', 'sequence' => 0 ),
			array('type' => 'content', 'width' => '50', 'sequence' => 1 ),
			array('type' => 'menu', 'width' => '25right', 'sequence' => 2 )
		);

		return;
	}

}

$layout = new Layout_1B();

?>