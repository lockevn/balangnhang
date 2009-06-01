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

$GLOBALS['page']->setWorkingZone('footer');
$GLOBALS['page']->add(
	'<div class="powered_by">'
	.'<img class="valid" src="'.getPathImage('fw').'valid-xhtml11.png" alt="Valid xhmtl 1.1" />'
	.'<img class="valid" src="'.getPathImage('fw').'valid-css.png" alt="Valid css" />'
	.'<a href="http://www.docebo.org/" 
		onclick="window.open(this.href); return false;" 
		onkeypress="window.open(this.href); return false;">'
	.'<img class="valid" src="'.getPathImage('fw').'powered.png" alt="Powered By Docebo" /></a>'
	.'</div>' );

?>
