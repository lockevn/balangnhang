<?php

/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

//control permission access
//-TP// if( !controlPermission() ) die('You can\'t access');

//load navigator bar

/*echo '<div class="high_line">';

echo '<div class="navigatorBox">'."\n";
navigatorArea( getIdArea() );
echo '</div>'."\n";

//load flag for language change
echo '<div class="langBox">'."\n";
//-TP// loadFlagForChange();
echo '</div>'

	.'<div class="noFloat"></div>'
	.'</div>'."\n"; */

//load page
$GLOBALS["page"]->add('<div class="principalBox">'."\n", "content");
loadArea();
$GLOBALS["page"]->add('</div> <div class="no_float"></div>'
	."\n", "content");

?>