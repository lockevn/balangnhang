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

require_once(dirname(__FILE__).'/class.definition.php');

class Module_Login extends CrmModule {

	function loadBody() {
		
		require_once($GLOBALS['where_crm'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		loginDispatch( $GLOBALS['op'] );
	}
}

?>
