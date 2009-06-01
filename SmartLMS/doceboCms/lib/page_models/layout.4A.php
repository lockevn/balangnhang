<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks                                  */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS["where_cms"]."/class/class.layout.php");

class Layout_4A extends Layout {

	function Layout_4A() {

		$this->main_block_number = 2;
		$this->content_block_number = 3;

		$this->param_main_block = array(
			           array('type' => 'menu', 'width' => '25', 'sequence' => 0 ),
			"area1" => array('type' => 'content', 'width' => '75', 'sequence' => 1 )
		);

		$this->param_content_block = array(
			"area1" => array(
				array('type' => 'block_content', 'width' => '50', 'sequence' => 0 ),
				array('type' => 'block_content', 'width' => '50', 'sequence' => 1 ),
				array('type' => 'block_content', 'width' => '100', 'sequence' => 2 )
			)
		);

		return;
	}

}

$layout = new Layout_4A();


?>