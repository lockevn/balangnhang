<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/**
 * @package 	DoceboLMS
 * @category 	Layout
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id: footer.php 251 2006-04-13 15:28:56Z fabio $
 */

$GLOBALS['page']->setWorkingZone('footer');
$GLOBALS['page']->add(
	'<div class="powered_by">'
	.'<img class="valid" src="'.getPathImage().'valid-xhtml11.png" alt="Valid xhmtl 1.1" />'
	.'<img class="valid" src="'.getPathImage().'valid-css.png" alt="Valid css" />'
	.'<a href="http://www.docebo.org"
		onclick="window.open(\'http://www.docebo.org\'); return false;"
		onkeypress="window.open(\'http://www.docebo.org\'); return false;">'
	.'<img src="'.getPathImage().'powered.png" alt="Docebo" title="Powered by Docebo.com" /></a>'
	.'</div>' );

?>