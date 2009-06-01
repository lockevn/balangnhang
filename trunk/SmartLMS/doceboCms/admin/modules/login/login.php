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

if ($op == 'login') {
	global $loginInCorrect, $logoutCorrect;
	//login form
	echo '<div class="loginBlock">';
	echo '<form method="post" action="admin.php?modulename=login&amp;op=confirm">'
		.'<div class="line">'
		.'<label for="userid">'
		.'<input type="text" id="userid" name="userIns" maxlength="50" value="'
		.( isset($_POST['userIns']) ? $_POST['userIns'] : '').'" alt="userid" />'
		.'&nbsp;&nbsp;'._USER.'</label></div>'
		.'<div class="line">'
		.'<label for="password"><input type="password" id="password" name="passIns" maxlength="20" alt="password" />&nbsp;&nbsp;'._PASS.'</label></div>'
		.'<div class="line">'
		.'<input class="buttonLogin" type="submit" value="'._LOGIN.'" />'
		.'</div>'
		.'</form>';
	if(isset($loginInCorrect) && $loginInCorrect) echo '<div class="confirmLoginBlock">'._NOACCESS."</div>";
	
	if(isset($logoutCorrect) && $logoutCorrect) echo '<div class="logoutBlock">'._UNLOGGED."</div>";
		
	
	echo '</div>';
}



?>