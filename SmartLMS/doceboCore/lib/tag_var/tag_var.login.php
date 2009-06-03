<?php

/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

if(!defined("IN_DOCEBO")) die("You can't access this file directly!");

require_once($GLOBALS['where_framework'].'/lib/tag_var/tag_var.class.php');

class Login_TagVar extends TagVar {

	function Login_TagVar() {

		$this->_my_tag_ref = 'login';
		
		require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		
	}

	/**
	 * Identify and manage a var substitution found in the layout file
	 * @param 	array 	$mathces contains the match of the preg_replace_callback
	 *
	 * @return string	the substitution text
	 */
	function parse_docebo_var($var) {

		// execute command
		return '';
	}

	/**
	 * Identify and manage a tag found in the layout file
	 * @param 	array 	$mathces contains the match of the preg_replace_callback
	 *
	 * @return string	the substitution text
	 */
	function parse_docebo_tag($tag, $args) {
		$args = $this->parse_attrib_string($args);
		
		
		$lang =& DoceboLanguage::createInstance('login');
				
		// execute command
		switch ($tag) {
			case "menu" : {				
				$li = '';
				$ul = '<ul id="'.( isset($args['id']) ? $args['id'] : 'main_menu' ).'">';
				
				require_once($GLOBALS['where_lms'].'/setting.php');
				
				$lang_d = DoceboLanguage::createInstance('course', 'lms');
				if($GLOBALS['lms']['course_block'] == 'on') {
					
					$li .= '<li><a href="index.php?modname=login&amp;op=courselist">'.$lang_d->def('_COURSELIST').'</a></li>';
				}
				
				$query = "
				SELECT idPages, title 
				FROM ".$GLOBALS['prefix_lms']."_webpages 
				WHERE publish = '1' AND language = '".getLanguage()."' ";
				$query .= " ORDER BY sequence ";
				$result = mysql_query( $query);
				
				$numof = mysql_num_rows($result);
				$numof--;

				$i = 0;
				while( list($idPages, $title) = mysql_fetch_row($result)) {
					$li .= '<li'.( $i == $numof ? ' class="last"' : '' ).'><a href="index.php?modname=login&amp;op=readwebpages&amp;idPages='.$idPages.'">'
						.$title.'</a></li>';
					$i++;
				}
					/*		
				$html .= '<li><a href="#">'.$lang->def('_COURSE_CATALOGUE').'</a></li>'
					.'<li><a class="odd" href="#">VOCE MENU</a></li>'
					.'<li><a href="#">PROVA</a></li>'
					.'<li class="last"><a class="odd" href="#">FAQ</a></li>';
					*/
				
				return ( $li != '' ? $ul.$li.'</ul>' : '' );
			};break;
			
			case "language_selection" : {				
				$lang_sel = getLanguage();
				
				$langs_var = $GLOBALS['globLangManager']->getAllLanguages();
				if(count($langs_var) <= 1) return '';
				
				$html = '<ul id="'.( isset($args['id']) ? $args['id'] : 'language_selection' ).'">';
				foreach($langs_var as $k => $v) {
					
					$html .= '<li><a '.($v[0] == $lang_sel ? 'class="current" ' : '' )
						.'href="'.( isset($args['redirect_on']) ? $args['redirect_on'] : 'index.php' )
							.'?special=changelang&amp;new_lang='.rawurlencode($v[0]).'" title="'.$v[1].'">'
						.'<img src="'.getPathImage('cms').'language/'.rawurlencode($v[0]).'.png" alt="'.$v[0].'" />'
						.'</a></li>';
				}
				$html .= '</ul>';
				
				return $html;
			};break;
			
			case "login_form" : {
				/*
				$html = '<form method="post" action="#">
					<div>
						<label for="username">'.$lang->def('_USERNAME').'</label>
						<input class="textfield" type="text" id="username" name="username" value="" maxlenght="255" />
						<br />

						<label for="password">'.$lang->def('_PASSWORD').'</label>
						<input class="textfield" type="password" id="password" name="password" maxlenght="255" />
						<br />

						<input class="button" type="submit" id="login" name="login" value="'.$lang->def('_LOGIN').'" />
					</div>
				</form>';
				*/
				
				$user_manager = new UserManager();
				$user_manager->_render->hideLoginLanguageSelection();
				$user_manager->setLoginStyle(false);
				
				$html = Form::openForm('login_confirm', $GLOBALS['where_lms_relative'].'/index.php?modname=login&amp;op=confirm')
					.$user_manager->getExtLoginMask($GLOBALS['where_lms_relative'].'/index.php?modname=login&amp;op=login', '')
					.Form::closeForm();
				
				return $html;
			};break;
			
			case "links" : {				
				$user_manager = new UserManager();
				$html = '<div id="'.( isset($args['id']) ? $args['id'] : 'link' ).'">'
					.'<a href="index.php?modname=login&amp;op=lostpwd">'.$lang->def('_LOG_LOSTPWD').'</a>';
				if($user_manager->_option->getOption('register_type') == 'self' || $user_manager->_option->getOption('register_type') == 'moderate') {
					
					$html .= ' <a href="index.php?modname=login&amp;op=register">'.$lang->def('_LOG_REGISTER').'</a>';
				}
				$html .= '</div>';
				return $html;
			};break;
			
			case "service_msg" : {				
				$html = '';
				if(isset($_GET['access_fail']) || isset($_GET['logout'])) {
					
					$html .= '<div id="service_msg">';
					if(isset($_GET['logout'])) {
						$html .= '<b class="logout">'.$lang->def('_UNLOGGED').'</b>';
					}
					if(isset($_GET['access_fail'])) {
						$html .= '<b class="login_failed">'.$lang->def('_NOACCESS').'</b>';
					}
					$html .= '</div>';
				}
				return $html;
		
	
			};break;
		}
		return '';
	}


}

?>