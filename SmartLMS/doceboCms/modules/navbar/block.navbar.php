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

function navigatorBar() {
	//REQUIRES :areaFunction.php 
	//EFFECTS  :display the navigator bar
	echo '<div class="navigatorBox">'
		.'<img src="'.getImagePath().'nav-tria.gif" alt="\" />';

	navigatorArea(getIdArea());

	echo '</div>';
}

switch($blockOp) {
	case "navigatorBar" : {
		navigatorBar();
	};break;
}

?>