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

class Layout_Custom extends Layout {

	function Layout_Custom() {

		$this->main_block_number = 2;
		$this->content_block_number = 10;

		$this->param_main_block = array(
			array('type' => 'menu', 'width' => '25', 'sequence' => 0 ),
			"area1" => array('type' => 'content', 'width' => '75', 'sequence' => 1 ),
			"area2" => array('type' => 'content', 'width' => '100', 'sequence' => 2 )
		);

		$this->param_content_block=array();

		$this->param_content_block["area1"] = array(
			array('type' => 'block_content', 'width' => '33', 'sequence' => 0 ),
			array('type' => 'block_content', 'width' => '66right', 'sequence' => 1 ),
			array('type' => 'block_content', 'width' => '66', 'sequence' => 2 ),
			array('type' => 'block_content', 'width' => '33right', 'sequence' => 3 )
		);

		$this->param_content_block["area2"] = array(
			array('type' => 'block_content', 'width' => '66', 'sequence' => 0 ),
			array('type' => 'block_content', 'width' => '33right', 'sequence' => 1 ),
			array('type' => 'block_content', 'width' => '33', 'sequence' => 2 ),
			array('type' => 'block_content', 'width' => '66right', 'sequence' => 3 ),
			array('type' => 'block_content', 'width' => '66', 'sequence' => 4 ),
			array('type' => 'block_content', 'width' => '33right', 'sequence' => 5 )
		);

		return;
	}

}

$layout = new Layout_Custom();

?>