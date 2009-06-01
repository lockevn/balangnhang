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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');

DEFINE('PUBLIC_FORUM_COURSE_ID', 0);
DEFINE('PUBLIC_FORUM_EDITION_ID', 0);
DEFINE('PUBLIC_FORUM_COURSE_NAME', 'Public Forum');
function loadUnreaded() {

	$id_course = PUBLIC_FORUM_COURSE_ID;

	if(!isset($_SESSION['unreaded_forum'][$id_course])) {

		unset($_SESSION['unreaded_forum']);
		//-find last access---------------------------------------------------------------
		$no_entry = false;
		$reLast = mysql_query("SELECT UNIX_TIMESTAMP(lastenter)" .
							" FROM core_user" .
							" WHERE idst = '".getLogUserId()."'");
		if(mysql_num_rows($reLast)) {
			list($last_forum_access_time) = mysql_fetch_row($reLast);
		} else {
			$last_forum_access_time = 0;
			$no_entry = true;
		}
		$unreaded = array();
		$reUnreaded = mysql_query("
		SELECT t.idThread, t.idForum, m.generator, COUNT(m.idMessage)
		FROM ".$GLOBALS['prefix_lms']."_forumthread AS t JOIN ".$GLOBALS['prefix_lms']."_forummessage AS m
		WHERE t.idThread = m.idThread AND m.author <> '".getLogUserId()."' AND UNIX_TIMESTAMP(m.posted) >= '".$last_forum_access_time."'
		GROUP BY t.idThread, t.idForum, m.generator");

		while(list($id_thread, $id_forum, $is_generator, $how_much_mess) = mysql_fetch_row($reUnreaded)) {

			if($is_generator) {

				if(isset($unreaded[$id_forum]['new_thread']))
					$unreaded[$id_forum][$id_thread] = 'new_thread';
				else
					$unreaded[$id_forum][$id_thread] = 'new_thread';
			} else {
				if(isset($unreaded[$id_forum][$id_thread]))
					$unreaded[$id_forum][$id_thread] += $how_much_mess;
				else
					$unreaded[$id_forum][$id_thread] = $how_much_mess;
			}
		}
		$_SESSION['unreaded_forum'][$id_course] = $unreaded;
		//-set as now the last forum access------------------------------------------------
		if($no_entry) {
			mysql_query("
			INSERT INTO  ".$GLOBALS['prefix_lms']."_forum_timing
			SET last_access = NOW(),
				idUser = '".getLogUserId()."',
				idCourse = '".PUBLIC_FORUM_COURSE_ID."'");
		} else {
			mysql_query("
			UPDATE ".$GLOBALS['prefix_lms']."_forum_timing
			SET  last_access = NOW()
			WHERE idUser = '".getLogUserId()."' AND idCourse = '".PUBLIC_FORUM_COURSE_ID."'");
		}
	}
}

function forum() {
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::CreateInstance('forum');

	$mod_perm 	= checkPerm('mod', true);
	$moderate 	= checkPerm('moderate', true);
	$add_perm	= checkPerm('add', true);
	$base_link 	= 'index.php?modname=public_forum&amp;op=forum';
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	// Find and set unreaded message
	loadUnreaded();

	$tb = new typeOne( $GLOBALS['lms']['visuItem'], '', $lang->def('_ELEFORUM'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink($base_link);

	$ini = $tb->getSelectedElement();

	// Construct query for forum display
	if($mod_perm) {

		$query_view_forum = "
		SELECT f.idForum, f.title, f.description, f.num_thread, f.num_post, f.locked, f.emoticons
		FROM ".$GLOBALS['prefix_lms']."_forum AS f
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."'
		ORDER BY f.sequence
		LIMIT $ini, ".$GLOBALS['lms']['visuItem'];

		$query_num_view = "
		SELECT COUNT(*) FROM ".$GLOBALS['prefix_lms']."_forum AS f
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."'";
	} else {

		$acl 	=& $GLOBALS['current_user']->getAcl();
		$all_user_idst = $acl->getSTGroupsST(getLogUserId());
		$all_user_idst[] = getLogUserId();
/*
		$query_view_forum = "
		SELECT DISTINCT f.idForum, f.title, f.description, f.num_thread, f.num_post, f.locked, f.emoticons
		FROM ".$GLOBALS['prefix_lms']."_forum AS f
			LEFT JOIN ".$GLOBALS['prefix_lms']."_forum_access AS fa ON ( f.idForum = fa.idForum )
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND
			( fa.idMember IS NULL OR fa.idMember IN (".implode($all_user_idst, ',')." )  )
		ORDER BY f.sequence ";

		$query_num_view = "
		SELECT COUNT( DISTINCT f.idForum )
		FROM ".$GLOBALS['prefix_lms']."_forum AS f
			LEFT JOIN ".$GLOBALS['prefix_lms']."_forum_access AS fa ON ( f.idForum = fa.idForum )
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND
			( fa.idMember IS NULL OR fa.idMember IN (".implode($all_user_idst, ',')." )  ) ";
			*/
		$query_view_forum = "
		SELECT DISTINCT f.idForum, f.title, f.description, f.num_thread, f.num_post, f.locked, f.emoticons
		FROM ".$GLOBALS['prefix_lms']."_forum AS f
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."'
		ORDER BY f.sequence ";

		$query_num_view = "
		SELECT COUNT( DISTINCT f.idForum )
		FROM ".$GLOBALS['prefix_lms']."_forum AS f
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."'";

	}

	$re_forum = mysql_query($query_view_forum);
	list($tot_forum) = mysql_fetch_row(mysql_query($query_num_view));
	
	$re_last_post = mysql_query("
	SELECT f.idForum, m.idThread, m.posted, m.title, m.author
	FROM ".$GLOBALS['prefix_lms']."_forum AS f LEFT JOIN
		".$GLOBALS['prefix_lms']."_forummessage AS m ON ( f.last_post = m.idMessage )
	WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."'");
	while(list($idF_p, $idT_p, $posted, $title_p, $id_a) = mysql_fetch_row($re_last_post)) {

		if($posted !== NULL) {
			$last_post[$idF_p]['info'] = $GLOBALS['regset']->databaseToRegional($posted).'<br />'.substr(strip_tags($title_p), 0, 15).' ...';
			$last_post[$idF_p]['author'] = $id_a;
			$last_authors[] = $id_a;
		}
	}

	// find authors names
	if(isset($last_authors)) {
		$authors_names =& $acl_man->getUsers($last_authors);
	}
	// switch to one of the 2 visualization method
	if($GLOBALS['lms']['forum_as_table'] == 'on') {

		// show forum list in a table -----------------------------------------
		// table header
		$type_h = array('image', 'image', 'forumTitle', '', 'align_center', 'align_center', 'align_center');
		if($mod_perm) {
			$type_h[] = 'image'; $type_h[] = 'image'; $type_h[] = 'image'; $type_h[] = 'image'; $type_h[] = 'image'; $type_h[] = 'image';
		}
		$tb->setColsStyle($type_h);

		$cont_h = array(
			'<img src="'.getPathImage().'forum/forum.gif" title="'.$lang->def('_FREET').'" alt="'.$lang->def('_FREE').'" />',
			'<img src="'.getPathImage('fw').'forum/emoticons.gif" title="'.$lang->def('_EMOTICONS').'" alt="'.$lang->def('_EMOTICONS').'" />',
			$lang->def('_TITLE'),
			$lang->def('_DESCRIPTION'),
			$lang->def('_NUMTHREAD'),
			$lang->def('_NUMPOST'),
			$lang->def('_LASTPOST')
		);
		if($mod_perm) {
			$cont_h[] = '<img src="'.getPathImage().'standard/down.gif" title="'.$lang->def('_DOWNFORUM').'" alt="'.$lang->def('_DOWN').'" />';
			$cont_h[] = '<img src="'.getPathImage().'standard/up.gif" title="'.$lang->def('_UPFORUM').'" alt="'.$lang->def('_UP').'" />';
			$cont_h[] = '<img src="'.getPathImage().'standard/moduser.gif" title="'.$lang->def('_MODGROUPST').'" alt="'.$lang->def('_MODGROUPS').'" />';
			$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_MODFORUM').'" alt="'.$lang->def('_MOD').'" />';
			$cont_h[] = '<img src="'.getPathImage().'standard/export.gif" title="'.$lang->def('_EXPORT_FORUM').'" alt="'.$lang->def('_EXPORT_FORUM').'" />';
			$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_DELFORUM').'" alt="'.$lang->def('_DEL').'" />';
		}
		$tb->addHead($cont_h);

		// table body
		$i = 1;
		while(list($idF, $title, $descr, $num_thread, $num_post, $locked, $emoticons) = mysql_fetch_row($re_forum) ) {
			if (checkPublicForumPerm('view', $idF) || checkPerm('mod', true))
			{
				$c_css 			= '';
				$mess_notread 	= 0;
				$thread_notread = 0;
				// NOTES: status
				if($locked)	$status = '<img src="'.getPathImage().'forum/forum_locked.gif" alt="'.$lang->def('_LOCKED').'" />';
				elseif( isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$idF])) {
	
					if(isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$idF]) && is_array($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$idF])) {
						foreach($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$idF] as $k => $n_mess)
							if($n_mess != 'new_thread') $mess_notread += $n_mess;
							else $thread_notread += 1;
					}
					if($mess_notread > 0 || $thread_notread > 0) {
						$status = '<img src="'.getPathImage().'forum/forum_unreaded.gif" alt="'.$lang->def('_UNREADED').'" />';
						$c_css = ' class="text_bold"';
					} else {
						$status = '<img src="'.getPathImage().'forum/forum.gif" alt="'.$lang->def('_FREE').'" />';
					}
				} else $status = '<img src="'.getPathImage().'forum/forum.gif" alt="'.$lang->def('_FREE').'" />';
	
				// NOTES: other content
				$content = array( $status,
								'<img src="'.getPathImage('fw').'emoticons/'.$emoticons.'" title="'.$lang->def('_EMOTICONS').'" alt="'.$lang->def('_EMOTICONS').'" />',
								'<a'.$c_css.' href="index.php?modname=public_forum&amp;op=thread&amp;idForum='.$idF.'">'.$title.'</a>',
								$descr,
								$num_thread.( $thread_notread ? '<div class="forum_notread">'.$thread_notread.' '.$lang->def('_ADD').'</div>' : '' ),
								$num_post.( $mess_notread ? '<div class="forum_notread">'.$mess_notread.' '.$lang->def('_ADD').'</div>' : '' ) );
				if(isset($last_post[$idF])) {
	
					$author = $last_post[$idF]['author'];
					$content[] = $last_post[$idF]['info'].' ( '.$lang->def('_BY').': <span class="mess_author">'
								.( isset($authors_names[$author])
									? ( $authors_names[$author][ACL_INFO_LASTNAME].$authors_names[$author][ACL_INFO_FIRSTNAME] == ''
											? $acl_man->relativeId($authors_names[$author][ACL_INFO_USERID])
											: $authors_names[$author][ACL_INFO_LASTNAME].' '.$authors_names[$author][ACL_INFO_FIRSTNAME] )
									: $lang->def('_UNKNOWN_AUTHOR')
								).'</span> )';
				} else {
	
					$content[] = $lang->def('_NONE');
				}
				// NOTES: mod and perm
				if($mod_perm) {
					if($i != $tot_forum) $content[] = '<a href="index.php?modname=public_forum&amp;op=downforum&amp;idForum='.$idF.'">
						<img src="'.getPathImage().'standard/down.gif" title="'.$lang->def('_DOWNFORUM').'" alt="'.$lang->def('_DOWN').'" /></a>';
					else $content[] = '';
	
					if($i != 1) $content[] = '<a href="index.php?modname=public_forum&amp;op=moveupforum&amp;idForum='.$idF.'">
						<img src="'.getPathImage().'standard/up.gif" title="'.$lang->def('_UPFORUM').'" alt="'.$lang->def('_UP').'" /></a>';
					else $content[] = '';
	
					$content[] = '<a href="index.php?modname=public_forum&amp;op=modforumaccess&amp;idForum='.$idF.'&amp;load=1">
						<img src="'.getPathImage().'standard/moduser.gif" title="'.$lang->def('_MODGROUPST').'" alt="'.$lang->def('_MODGROUPS').'" /></a>';
					$content[] = '<a href="index.php?modname=public_forum&amp;op=modforum&amp;idForum='.$idF.'">
						<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_MODFORUM').'" alt="'.$lang->def('_MOD').'" /></a>';
					$content[] = '<a href="index.php?modname=public_forum&amp;op=export&amp;idForum='.$idF.'" ' .
						'title="'.$lang->def('_EXPORTFORUM').' : '.strip_tags($title).'">
					<img src="'.getPathImage().'standard/export.gif" alt="'.$lang->def('_DEL').'" /></a>';
					$content[] = '<a href="index.php?modname=public_forum&amp;op=delforum&amp;idForum='.$idF.'" title="'.$lang->def('_DELFORUM').' : '.strip_tags($title).'">
						<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_DELFORUM').' : '.strip_tags($title).'" alt="'.$lang->def('_DEL').'" /></a>';
				}
				$tb->addBody( $content );
				++$i;
			}
		}
		if($mod_perm) {

			$tb->addActionAdd('<a href="index.php?modname=public_forum&amp;op=addforum">
				<img src="'.getPathImage().'standard/add.gif" title="'.$lang->def('_ADDFORUMT').'" alt="'.$lang->def('_ADD').'" /> '
				.$lang->def('_ADDFORUM').'</a>');
		}
		$GLOBALS['page']->add(
			getTitleArea($lang->def('_FORUM'), 'forum')
			.'<div class="std_block">'
			.Form::openForm('search_forum', 'index.php?modname=public_forum&amp;op=search')
			.'<div class="search_mask form_line_l">'
			.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
			.Form::getInputTextfield(	'textfield_nowh',
										'search_arg',
										'search_arg',
										'',
										$lang->def('_SEARCH'), 255, '' )
			.' <input class="button_nowh" type="submit" id="search_button" name="search_button" value="'.$lang->def('_SEARCH').'" />'
			.'</div>'
			.Form::closeForm()
			.$tb->getTable()
			.$tb->getNavBar($ini, $tot_forum)
			.'</div>', 'content' );
	} else {

		// second view styles
		$i = 1;
		$GLOBALS['page']->add(
			getTitleArea($lang->def('_FORUM'), 'forum')
			.'<div class="std_block">'
			.Form::openForm('search_forum', 'index.php?modname=public_forum&amp;op=search')
			.'<div class="search_mask form_line_l">'
			.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
			.Form::getInputTextfield(	'textfield_nowh',
										'search_arg',
										'search_arg',
										'',
										$lang->def('_SEARCH'), 255, '' )
			.' <input class="button_nowh" type="submit" id="search_button" name="search_button" value="'.$lang->def('_SEARCH').'" />'
			.'</div>'
			.Form::closeForm()
			, 'content');
		while( list($idF, $title, $descr, $num_thread, $num_post, $locked, $emoticons) = mysql_fetch_row( $re_forum ) ) {
			if (checkPublicForumPerm('view', $idF) || checkPerm('mod', true))
			{
			$c_css = '';
			$thread_notread = 0;
			$mess_notread = 0;
			// NOTES: status
			if($locked)	$status = '<img src="'.getPathImage().'forum/forum_locked.gif" alt="'.$lang->def('_LOCKED').'" />';
			elseif( isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$idF])) {

				if(isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$idF]) && is_array($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$idF])) {
					foreach($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$idF] as $k => $n_mess)
						if($n_mess != 'new_thread') $mess_notread += $n_mess;
						else $thread_notread += 1;
				}
				if($mess_notread > 0 || $thread_notread > 0) {
					$status = '<img src="'.getPathImage().'forum/forum_unreaded.gif" alt="'.$lang->def('_UNREADED').'" />';
					$c_css = ' class="text_bold"';
				} else {
					$status = '<img src="'.getPathImage().'forum/forum.gif" alt="'.$lang->def('_FREE').'" />';
				}
			} else {
				$status = '<img src="'.getPathImage().'forum/forum.gif" alt="'.$lang->def('_FREE').'" />';
			}

			$GLOBALS['page']->add(
				'<table class="forum_table" cellspacing="0" summary="'.$lang->def('_FORUM_INFORMATION').'">'
				.'<tr class="forum_header">'
					.'<th class="forum_title">'.$status.'&nbsp;'
					.'<img src="'.getPathImage('fw').'emoticons/'.$emoticons.'" title="'.$lang->def('_EMOTICONS').'" alt="'.$lang->def('_EMOTICONS').'" />'
					.'&nbsp;'
					.'<a'.$c_css.' href="index.php?modname=public_forum&amp;op=thread&amp;idForum='.$idF.'">'.$title.'</a>'
					.'</th>'
					.'<th class="image" nowrap="nowrap">'.$lang->def('_NUMTHREAD').'</th>'
					.'<th class="image" nowrap="nowrap">'.$lang->def('_NUMPOST').'</th>'
				.'</tr>'
				.'<tr>'
					.'<td>'.$descr.'</td>'
					.'<td class="image" nowrap="nowrap">'.$num_thread
						.( $thread_notread ? '<div class="forum_notread">'.$thread_notread.' '.$lang->def('_ADD').'</div>' : '' )
					.'</td>'
					.'<td class="image" nowrap="nowrap">'.$num_post
						.( $mess_notread ? '<div class="forum_notread">'.$mess_notread.' '.$lang->def('_ADD').'</div>' : '' )
					.'</td>'
				.'</tr>'
				.'<tr>'
					.'<td colspan="3">', 'content');

			if(isset($last_post[$idF])) {

				$author = $last_post[$idF]['author'];
				$GLOBALS['page']->add('<span class="forum_lastpost">'.$lang->def('_LASTPOST').' : '.$last_post[$idF]['info'].' ( '.$lang->def('_BY').': <span class="mess_author">'
					.( isset($authors_names[$author])
						? ( $authors_names[$author][ACL_INFO_LASTNAME].$authors_names[$author][ACL_INFO_FIRSTNAME] == ''
								? $acl_man->relativeId($authors_names[$author][ACL_INFO_USERID])
								: $authors_names[$author][ACL_INFO_LASTNAME].' '.$authors_names[$author][ACL_INFO_FIRSTNAME] )
						: $lang->def('_UNKNOWN_AUTHOR')
					).'</span> )'
					.'</span>'
				, 'content');

			} else {

				//$GLOBALS['page']->add($lang->def('_NONE'), 'content');
			}
			$GLOBALS['page']->add(
					'</td>'
				.'</tr>'
				.'<tr>'
					.'<td colspan="3" class="forum_manag">', 'content');
			if($mod_perm) {

				$GLOBALS['page']->add('<ul class="adjac_link">', 'content');
				if($i != $tot_forum) {
					$GLOBALS['page']->add('<li><a href="index.php?modname=public_forum&amp;op=downforum&amp;idForum='.$idF.'">
					<img src="'.getPathImage().'standard/down.gif" title="'.$lang->def('_DOWNFORUM').'" alt="'.$lang->def('_DOWN').'" /></a></li>'
					, 'content');
				}
				if($i != 1) {
					$GLOBALS['page']->add('<li><a href="index.php?modname=public_forum&amp;op=moveupforum&amp;idForum='.$idF.'">
					<img src="'.getPathImage().'standard/up.gif" title="'.$lang->def('_UPFORUM').'" alt="'.$lang->def('_UP').'" /></a></li>', 'content');
				} else {
					$GLOBALS['page']->add('<li><div style=" display: inline; margin: 0px 11px;"></div></li>', 'content');
				}
				$GLOBALS['page']->add(
					'<li><a href="index.php?modname=public_forum&amp;op=modforumaccess&amp;idForum='.$idF.'&amp;load=1">
						<img src="'.getPathImage().'standard/moduser.gif" title="'.$lang->def('_MODGROUPST').'" alt="'.$lang->def('_MODGROUPS').'" /></a></li>'
					.'<li><a href="index.php?modname=public_forum&amp;op=modforum&amp;idForum='.$idF.'">
						<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_MODFORUM').'" alt="'.$lang->def('_MOD').'" /></a></li>'
					.'<li><a href="index.php?modname=public_forum&amp;op=export&amp;idForum='.$idF.'"' .
							' title="'.$lang->def('_EXPORTFORUM').' : '.strip_tags($title).'">
						<img src="'.getPathImage().'standard/export.gif" alt="'.$lang->def('_DEL').'" /></a></li>'
					.'<li><a href="index.php?modname=public_forum&amp;op=delforum&amp;idForum='.$idF.'" title="'.$lang->def('_DELFORUM').' : '.strip_tags($title).'">
						<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_DELFORUM').' : '.strip_tags($title).'" alt="'.$lang->def('_DEL').'" /></a></li>'
				, 'content');
				$GLOBALS['page']->add('</ul>', 'content');
			}
			$GLOBALS['page']->add('</td>'
				.'</tr>'
				.'</table>', 'content');
				$i++;
			}
		}
		if($add_perm) {
			$GLOBALS['page']->add(
				'<div class="add_container">'
				.'<a href="index.php?modname=public_forum&amp;op=addforum">'
				.'<img src="'.getPathImage().'standard/add.gif" title="'.$lang->def('_ADDFORUMT').'" alt="'.$lang->def('_ADD').'" /> '
				.$lang->def('_ADDFORUM').'</a></div>', 'content');
		}
		$GLOBALS['page']->add(
			$tb->getNavBar($ini, $tot_forum)
			.'</div>', 'content' );
	}
	if($mod_perm) {
					
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delforum]');
	}
	
}

//---------------------------------------------------------------------------//

function addforum() {
	checkPerm('add');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum');

	$default = 'blank.gif';
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_FORUM'), 'forum')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=public_forum&amp;op=forum', $lang->def('_BACK'))
		.Form::openForm('addforumform', 'index.php?modname=public_forum&amp;op=insforum')
		.Form::openElementSpace()
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $lang->def('_NOTITLE'))
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $lang->def('_DESCRIPTION'))
		.Form::openFormLine()
		.Form::getLabel('emoticons', $lang->def('_EMOTICONS'))
		.'<select class="dropdown" id="emoticons" name="emoticons">'
	, 'content');
	$templ = dir(getPathImage('fw').'emoticons/');
	while($elem = $templ->read()) {

		if(ereg('.gif', $elem)) {
			$GLOBALS['page']->add(
				'<option value="'.$elem.'" class="option_with_image" style="background-image: url(\''.getPathImage('fw').'emoticons/'.$elem.'\');"'
				.( $elem == $default ? ' selected="selected"' : '' )
				.'>'
				.$elem.'</option>'
			, 'content');
		}
	}
	closedir($templ->handle);
	$GLOBALS['page']->add(
		'</select>'
		.Form::closeFormLine()
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('insforum', 'insforum', $lang->def('_INSERT'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function insforum() {
	checkPerm('add');

	$lang =& DoceboLanguage::createInstance('forum');

	if(isset($_POST['undo'])) jumpTo('index.php?modname=public_forum&op=forum');
	if($_POST['title'] == '') {
		$_POST['title'] = $lang->def('_NOTITLE');
	}

	// finding sequence
	list($seq) = mysql_fetch_row(mysql_query("
	SELECT MAX(sequence) + 1
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."'"));

	$ins_query = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_forum
	( idCourse, title, description, sequence, emoticons ) VALUES
	( '".(int)PUBLIC_FORUM_COURSE_ID."',
		'".$_POST['title']."',
		'".$_POST['description']."',
		'$seq',
		'".$_POST['emoticons']."' )";
	if(!mysql_query( $ins_query )) jumpTo('index.php?modname=public_forum&op=forum&result=err');

	if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
	
		list($idForum) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		$id_user = getLogUserId();
		$perm = array();
		
		$perm['view'] = array($id_user);
		$perm['write'] = array($id_user);
		$perm['upload'] = array($id_user);
		$perm['moderate'] = array($id_user);
		
		saveForumPerm($idForum, $perm, array());
		
		$GLOBALS['current_user']->loadUserSectionST();
		$GLOBALS['current_user']->SaveInSession();
	}
	$recipients = '';
	if(!empty($recipients)) {

		require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');



		$msg_composer = new EventMessageComposer('forum', 'lms');

		$msg_composer->setSubjectLangText('email', '_NEW_FORUM', false);
		$msg_composer->setBodyLangText('email', '_NEW_FORUM_BODY', array(	'[url]' => $GLOBALS['lms']['url'],
																			'[course]' => PUBLIC_FORUM_COURSE_NAME,
																			'[title]' => $_POST['title'],
																			'[text]' => $_POST['description'] ) );

		$msg_composer->setSubjectLangText('sms', '_NEW_FORUM_SMS', false);
		$msg_composer->setBodyLangText('sms', '_NEW_FORUM_BODY_SMS', array(	'[url]' => $GLOBALS['lms']['url'],
																			'[course]' => PUBLIC_FORUM_COURSE_NAME,
																			'[title]' => $_POST['title'],
																			'[text]' => $_POST['description'] ) );

		createNewAlert(		'ForumNewCategory',
							'forum',
							'addforum',
							1,
							$lang->def('_NEW_FORUM'),
							$recipients,
							$msg_composer );

	}

	jumpTo('index.php?modname=public_forum&op=forum&result=ok');
}

//---------------------------------------------------------------------------//

function modforum() {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum');

	list($title, $text, $emoticons) = mysql_fetch_row(mysql_query("
	SELECT title, description, emoticons
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".(int)$_GET['idForum']."'"));

	$GLOBALS['page']->add(
		getTitleArea($lang->def('_FORUM'), 'forum', $lang->def('_FORUM'))
		.'<div class="std_block">'
		.Form::openForm('addforumform', 'index.php?modname=public_forum&amp;op=upforum')
		.Form::openElementSpace()
		.Form::getHidden('idForum', 'idForum', (int)$_GET['idForum'])
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $title)
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $text)
		.Form::openFormLine()
		.Form::getLabel('emoticons', $lang->def('_EMOTICONS'))
		.'<select id="emoticons" name="emoticons">'
	, 'content');
	$templ = dir(getPathImage('fw').'emoticons/');
	while($elem = $templ->read()) {

		if(ereg('.gif', $elem)) {
			$GLOBALS['page']->add(
				'<option value="'.$elem.'" class="option_with_image" style="background-image: url(\''.getPathImage('fw').'emoticons/'.$elem.'\');"'
				.( $elem == $emoticons ? ' selected="selected"' : '' )
				.'>'
				.$elem.'</option>'
			, 'content');
		}
	}
	closedir($templ->handle);
	$GLOBALS['page']->add(
		'</select>'
		.Form::closeFormLine()
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('insforum', 'insforum', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function upforum() {
	checkPerm('mod');

	if(isset($_POST['undo'])) jumpTo('index.php?modname=public_forum&op=forum');
	if($_POST['title'] == '') $_POST['title'] = def('_NOTITLE', 'forum');

	$ins_query = "
	UPDATE ".$GLOBALS['prefix_lms']."_forum
	SET title = '".$_POST['title']."',
		description = '".$_POST['description']."',
		emoticons = '".$_POST['emoticons']."'
	WHERE idForum = '".(int)$_POST['idForum']."'AND idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."'";
	if(!mysql_query( $ins_query )) jumpTo('index.php?modname=public_forum&op=forum&result=err');
	jumpTo('index.php?modname=public_forum&op=forum&result=ok');
}

function moveforum($idForum, $direction) {
	checkPerm('mod');

	list( $seq ) = mysql_fetch_row(mysql_query("
	SELECT sequence
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".(int)$idForum."'"));

	if($direction == 'up') {
		//move up
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forum
		SET sequence = '$seq'
		WHERE idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND sequence = '".($seq - 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forum
		SET sequence = sequence - 1
		WHERE idCourse = '".PUBLIC_FORUM_COURSE_ID."' AND idForum = '".(int)$idForum."'");
	}
	if($direction == 'down') {
		//move down
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forum
		SET sequence = '$seq'
		WHERE idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND sequence = '".($seq + 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forum
		SET sequence = sequence + 1
		WHERE idCourse = '".PUBLIC_FORUM_COURSE_ID."' AND idForum = '".(int)$idForum."'");
	}
	jumpTo('index.php?modname=public_forum&op=forum');
}

function changestatus() {
	checkPerm('mod');

	list( $lock ) = mysql_fetch_row(mysql_query("
	SELECT locked
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".(int)$_GET['idForum']."'"));

	if($lock == 1) $new_status = 0;
	else $new_status = 1;

	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_forum
	SET locked = '$new_status'
	WHERE idCourse = '".PUBLIC_FORUM_COURSE_ID."' AND idForum = '".(int)$_GET['idForum']."'");
	jumpTo('index.php?modname=public_forum&op=thread&idForum='.(int)$_GET['idForum']);
}

//---------------------------------------------------------------------------//

function delforum() {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum');
	$id_forum = importVar('idForum', true, 0);

	list($title, $text, $seq) = mysql_fetch_row(mysql_query("
	SELECT title, description, sequence
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".$id_forum."'"));

	if(isset($_POST['undo'])) jumpTo('index.php?modname=public_forum&op=forum');
	if(isset($_GET['confirm'])) {

		$re_thread = mysql_query("
		SELECT idThread
		FROM ".$GLOBALS['prefix_lms']."_forumthread
		WHERE idForum = '".(int)$_GET['idForum']."'");
		while(list($idT) = mysql_fetch_row($re_thread)) {

			if(!mysql_query("
			DELETE FROM ".$GLOBALS['prefix_lms']."_forummessage
			WHERE idThread = '$idT'")) jumpTo('index.php?modname=public_forum&op=forum&result=err_del');
			unsetNotify('thread', $idT);
		}
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_forumthread
		WHERE idForum= '".$id_forum."'")) jumpTo('index.php?modname=public_forum&op=forum&result=err_del');
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_forum_access
		WHERE idForum='".$id_forum."'")) jumpTo('index.php?modname=public_forum&op=forum&result=err_del');
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_forum
		WHERE idForum='".$id_forum."'")) jumpTo('index.php?modname=public_forum&op=forum&result=err_del');

		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forum
		SET sequence = sequence - 1
		WHERE idForum = '".$id_forum."' AND sequence > '".$seq."'");

		unsetNotify('forum', $id_forum);
		jumpTo('index.php?modname=public_forum&op=forum&result=ok');
	} else {

		$GLOBALS['page']->add(
			getTitleArea($lang->def('_FORUM'), 'forum', $lang->def('_FORUM'))
			.'<div class="std_block">'
			.getDeleteUi($lang->def('_AREYOUSURE'),
				'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />'
				.'<span class="text_bold">'.$lang->def('_DESCRIPTION').' :</span> '.$text,
				true,
				'index.php?modname=public_forum&amp;op=delforum&amp;idForum='.$_GET['idForum'].'&amp;confirm=1',
				'index.php?modname=public_forum&amp;op=forum' )
			.'</div>', 'content');
	}
}

//---------------------------------------------------------------------------//

/*function modforumaccess() {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	$lang =& DoceboLanguage::createInstance('forum', 'lms');
	$out =& $GLOBALS['page'];
	$id_forum = importVar('idForum', true, 0);
	
	$aclManager = new DoceboACLManager();
	$user_select = new Module_Directory();
	$user_select->show_user_selector = TRUE;
	$user_select->show_group_selector = TRUE;
	$user_select->show_orgchart_selector = FALSE;

	$user_select->nFields = 0;

	if(isset($_POST['cancelselector'])) jumpTo('index.php?modname=public_forum&amp;op=forum');
	if(isset($_POST['okselector'])) {

		$user_selected 	= $user_select->getSelection($_POST);

		$query_reader = "
		SELECT idMember
		FROM ".$GLOBALS['prefix_lms']."_forum_access
		WHERE idForum = '".$id_forum."'";
		$re_reader = mysql_query($query_reader);
		$old_users = array();
		while(list($id_user) = mysql_fetch_row($re_reader)) {

			$old_users[] = $id_user;
		}
		$add_reader = array_diff($user_selected, $old_users);
		$del_reader = array_diff($old_users, $user_selected);

		if(is_array($add_reader)) {

			while(list(, $idst) = each($add_reader)) {

				$query_insert = "
				INSERT INTO ".$GLOBALS['prefix_lms']."_forum_access
				( idForum, idMember ) VALUES
				( 	'".$id_forum."',
					'".$idst."' )";
				mysql_query($query_insert);
			}
		}
		if(is_array($del_reader)) {

			while(list(, $idst) = each($del_reader)) {

				$query_delete = "
				DELETE FROM ".$GLOBALS['prefix_lms']."_forum_access
				WHERE idForum = '".$id_forum."' AND idMember = '".$idst."'";
				mysql_query($query_delete);
			}
		}
		jumpTo('index.php?modname=public_forum&amp;op=forum&amp;result=ok');
	}

	if(isset($_GET['load'])) {

		$query_reader = "
		SELECT idMember
		FROM ".$GLOBALS['prefix_lms']."_forum_access
		WHERE idForum = '".$id_forum."'";
		$re_reader = mysql_query($query_reader);
		$users = array();
		while(list($id_user) = mysql_fetch_row($re_reader)) {

			$users[$id_user] = $id_user;
		}
		$user_select->resetSelection($users);
	}
	$query_forum_name = "
		SELECT f.title
		FROM ".$GLOBALS['prefix_lms']."_forum AS f
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."'
			AND f.idForum = '".$id_forum."'
		";
	$row = mysql_fetch_row(mysql_query($query_forum_name));
	$forum_name = $row[0];
	$arr_idstGroup = $aclManager->getGroupsIdstFromBasePath('/lms/course/'.(int)PUBLIC_FORUM_COURSE_ID.'/subscribed/');
	$user_select->setUserFilter('group',$arr_idstGroup);
	$user_select->setGroupFilter('path', '/lms/course/'.PUBLIC_FORUM_COURSE_ID.'/group');
	$user_select->setPageTitle(getTitleArea(
		array('index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
		''.$lang->def('_FORUM_ACCESS').' "'.$forum_name.'" '.$lang->def('_FORUM_TO').''), 'forum'));
	$user_select->loadSelector('index.php?modname=public_forum&amp;op=modforumaccess&amp;idForum='.$id_forum,
			'',
			$lang->def('_CHOOSE_FORUM_ACCESS'),
			true,
			true );
}*/

function modforumaccess() {
	checkPerm('mod');

	require_once($GLOBALS['where_cms']."/lib/lib.simplesel.php");

	$out =& $GLOBALS['page'];
	$out->setWorkingZone("content");
	
	$lang =& DoceboLanguage::createInstance('public_forum', 'lms');

	$idForum = (int)importVar("idForum");


	$ssel = new SimpleSelector(true, $lang);

	$perm = array();
	$perm["view"]["img"] = getPathImage()."standard/view.gif";
	$perm["view"]["alt"] = $lang->def("_ALT_VIEW");
	$perm["write"]["img"] = getPathImage()."forum/write.gif";
	$perm["write"]["alt"] = $lang->def("_ADD");
	$perm["upload"]["img"] = getPathImage()."forum/upload.gif";
	$perm["upload"]["alt"] = $lang->def("_ALT_UPLOAD");
	/*$perm["add"]["img"] = getPathImage()."standard/add.gif";
	$perm["add"]["alt"] = $lang->def("_ADD");
	$perm["mod"]["img"] = getPathImage()."standard/mod.gif";
	$perm["mod"]["alt"] = $lang->def("_MOD");
	$perm["del"]["img"] = getPathImage()."standard/rem.gif";
	$perm["del"]["alt"] = $lang->def("_DEL");*/
	$perm["moderate"]["img"] = getPathImage()."forum/moderate.gif";
	$perm["moderate"]["alt"] = $lang->def("_ALT_MODERATE");

	$ssel->setPermList($perm);

	$url = "index.php?modname=public_forum&amp;op=modforumaccess&amp;idForum=".$idForum;
	$back_url = "index.php?modname=public_forum&amp;op=forum";
	$ssel->setLinks($url, $back_url);

	$op = $ssel->getOp();

	if (($op == "main") || ($op == "manual_init") || ($op == "orgchartselector"))
		$saved_data=loadForumSavedPerm($idForum);

	$page_body="";
	$full_page="";
	
	switch($op) {

		case "main": {
			$ssel->setSavedData($saved_data);
			$page_body=$ssel->loadSimpleSelector(false, true);
		} break;

		case "manual_init":{

			// Saving permissions of simple selector
			$save_info=$ssel->getSaveInfo();
			saveForumPerm($idForum, $save_info["selected"], $save_info["database"]);

			$ssel->setSavedData($saved_data);
			$full_page = $ssel->loadManualSelector($lang->def( '_FORUM_PERM' ));
		} break;
		case "manual": {
			$full_page = $ssel->loadManualSelector($lang->def( '_FORUM_PERM' ));
		} break;

		case "save_manual": {

			// Saving permissions of manual selector
			$save_info=$ssel->getSaveInfo();
			saveForumPerm($idForum, $save_info["selected"], $save_info["database"]);

			jumpTo(str_replace("&amp;", "&", $url));
		} break;

		case "save": {

			// Saving permissions of simple selector
			$save_info=$ssel->getSaveInfo();
			saveForumPerm($idForum, $save_info["selected"], $save_info["database"]);

			jumpTo(str_replace("&amp;", "&", $back_url));
		} break;
		
		case 'orgchartselector':
			$ssel->setSavedData($saved_data);
			$page_body = $ssel->orgchartSelector();
		break;
		
		case 'save_org':
			$save_info=$ssel->getSaveInfoOrg();
			saveForumPerm($idForum, $save_info["selected"], $save_info["database"]);
			jumpTo(str_replace("&amp;", "&", $back_url));
		break;

	}

	if (!empty($full_page))
		$out->add($full_page);

	if (!empty($page_body)) {
		// If we have only the page body, then better to add the area title.
		$ta_array=array();
		$ta_array["index.php?modname=public_forum&amp;op=forum"] = $lang->def("_FORUM");
		$ta_array[]=$lang->def( '_FORUM_PERM' );

		$out->add(getTitleArea($ta_array, 'forum', $lang->def('_FORUM')));
		$out->add("<div class=\"std_block\">");
		$out->add($page_body);
		$out->add("</div>");
	}
}


function saveForumPerm($idForum, $selected_items, $database_items) {

		$pl=getForumPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();
		foreach($pl as $key=>$val) {
			if ((isset($selected_items[$val])) && (is_array($selected_items[$val]))) {

				$role_id="/lms/course/public/public_forum/".$idForum."/".$val;
				$role=$acl_manager->getRole(false, $role_id);
				if (!$role)
					$idst=$acl_manager->registerRole($role_id, "");
				else
					$idst=$role[ACL_INFO_IDST];

				foreach($selected_items[$val] as $pk=>$pv) {
					if ((!isset($database_items[$val])) || (!is_array($database_items[$val])) ||
						(!in_array($pv, array_keys($database_items[$val])))) {
							$acl_manager->addToRole($idst, $pv);
					}
				}

				if ((isset($database_items[$val])) && (is_array($database_items[$val])))
					$to_rem=array_diff(array_keys($database_items[$val]), $selected_items[$val]);
				else
					$to_rem=array();
				foreach($to_rem  as $pk=>$pv) {
					$acl_manager->removeFromRole($idst, $pv);
				}

			}
		}

	include_once($GLOBALS['where_cms']."/lib/lib.reloadperm.php");
	setCmsReloadPerm();
}

function getForumPermList() {
	return array("view", "write", "upload", /*"add", "mod", "del",*/ "moderate");
}


function loadForumSavedPerm($idForum) {
	$res=array();
	$pl=getForumPermList();
	$acl_manager=& $GLOBALS['current_user']->getACLManager();

	foreach($pl as $key=>$val) {

		$role_id="/lms/course/public/public_forum/".$idForum."/".$val;
		$role=$acl_manager->getRole(false, $role_id);

		if (!$role) {
			$res[$val]=array();
		}
		else {
			$idst=$role[ACL_INFO_IDST];
			$res[$val]=array_flip($acl_manager->getRoleMembers($idst));
		}
	}

	return $res;
}

//---------------------------------------------------------------------------//

function thread() {
	if(!checkPublicForumPerm('view', (int)$_GET['idForum']))
		die("You can't access'");

	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.navbar.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang =& DoceboLanguage::createInstance('forum');

	$mod_perm 	= checkPerm('mod', true);
	$id_forum 	= importVar('idForum', true, 0);
	
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	
	$ord 		= importVar('ord');
	$jump_url	= 'index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum;
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	$all_read	= importVar('allread', true, 0);
	
	if ($all_read)
		unset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID]);

	list($title, $tot_thread, $locked_f) = mysql_fetch_row(mysql_query("
	SELECT title, num_thread, locked
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND idForum = '$id_forum'"));

	$nav_bar 	= new NavBar('ini', $GLOBALS['lms']['visuItem'], $tot_thread, 'link');
	$ini 		= $nav_bar->getSelectedElement();
	$ini_page 	= $nav_bar->getSelectedPage();
	$nav_bar->setLink($jump_url.'&amp;ord='.$ord);

	$query_thread = "
	SELECT t.idThread, t.author AS thread_author, t.posted, t.title, t.num_post, t.num_view, t.locked, t.erased, t.rilevantForum
	FROM ".$GLOBALS['prefix_lms']."_forumthread AS t LEFT JOIN
			".$GLOBALS['prefix_lms']."_forummessage AS m ON ( t.last_post = m.idMessage )
	WHERE t.idForum = '$id_forum'";
	
	if(PUBLIC_FORUM_EDITION_ID) $query_thread .= " AND id_edition = '".PUBLIC_FORUM_EDITION_ID."'";
	
	$query_thread .= " ORDER BY t.rilevantForum DESC " ;
	switch($ord) {
		case "obji"		: $query_thread .= " , t.title DESC " ;	break;
		case "obj" 		: $query_thread .= " , t.title " ;		break;
		case "authi"	: $query_thread .= " , t.author DESC " ;	break;
		case "auth" 	: $query_thread .= " , t.author " ;		break;
		case "posti" 	: $query_thread .= " , m.posted " ;		break;
		case "post"		:
		default 		: {
			$ord = 'post';
			$query_thread .= " , m.posted DESC " ;	break;
		}
	}
	$query_thread .= " LIMIT $ini, ".$GLOBALS['lms']['visuItem'];
	$re_thread = mysql_query($query_thread);

	$re_last_post = mysql_query("
	SELECT m.idThread, t.author AS thread_author, m.posted, m.title, m.author  AS mess_author, m.generator
	FROM ".$GLOBALS['prefix_lms']."_forumthread AS t LEFT JOIN
		".$GLOBALS['prefix_lms']."_forummessage AS m ON ( t.last_post = m.idMessage )
	WHERE t.idForum = '".$id_forum."'");
	while(list($idT_p, $id_ta, $posted, $title_p, $id_a, $is_gener) = mysql_fetch_row($re_last_post)) {

		$last_authors[$id_ta] = $id_ta;
		if($posted !== NULL) {

			$last_post[$idT_p]['info'] = $GLOBALS['regset']->databaseToRegional($posted).'<br />'.substr(strip_tags($title_p), 0, 15).' ...';
			$last_post[$idT_p]['author'] = $id_a;
			$last_authors[$id_a] = $id_a;
		}
	}
	if(isset($last_authors)) {
		$authors_names =& $acl_man->getUsers($last_authors);
	}
	$page_title = array(
		'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
		$title
	);
	$GLOBALS['page']->add(
		 getTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.Form::openForm('search_forum', 'index.php?modname=public_forum&amp;op=search&amp;idForum='.$id_forum)
		.'<div class="search_mask form_line_l">'
		.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
		.Form::getInputTextfield(	'textfield_nowh',
									'search_arg',
									'search_arg',
									'',
									$lang->def('_SEARCH'), 255, '' )
		.' <input class="button_nowh" type="submit" id="search_button" name="search_button" value="'.$lang->def('_SEARCH').'" />'
		.'</div>'
		.Form::closeForm()
	, 'content');

	$tb = new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_THREAD_CAPTION'), $lang->def('_THRAD_SUMMARY'));

	$img_up 	= '<img src="'.getPathImage().'standard/ord_asc.gif" alt="'.$lang->def('_ORD_ASC').'" />';
	$img_down 	= '<img src="'.getPathImage().'standard/ord_desc.gif" alt="'.$lang->def('_ORD_DESC').'" />';

	$cont_h = array(
		'<img src="'.getPathImage().'forum/thread.gif" title="'.$lang->def('_FREET').'" alt="'.$lang->def('_FREE').'" />',
		'<a href="'.$jump_url.'&amp;ord='.( $ord == 'obj' ? 'obji' : 'obj' ).'" title="'.$lang->def('_ORD_THREAD').'">'
			.( $ord == 'obj' ? $img_up : ( $ord == 'obji' ? $img_down : '' ) ).$lang->def('_THREAD').'</a>',
		$lang->def('_NUMREPLY'),
		'<a href="'.$jump_url.'&amp;ord='.( $ord == 'auth' ? 'authi' : 'auth' ).'" title="'.$lang->def('_ORD_AUTHOR').'">'
			.( $ord == 'auth' ? $img_up : ( $ord == 'authi' ? $img_down : '' ) ).$lang->def('_AUTHOR').'</a>',
		$lang->def('_NUMVIEW'),
		//$lang->def('_POSTED'),
		'<a href="'.$jump_url.'&amp;ord='.( $ord == 'post' ? 'posti' : 'post' ).'" title="'.$lang->def('_ORD_POST').'">'
			.( $ord == 'post' ? $img_up : ( $ord == 'posti' ? $img_down : '' ) ).$lang->def('_LASTPOST').'</a>'
	);
	$type_h = array('image', '', 'align_center', 'align_center', 'image',
	//'align_center',
	'align_center');
	if($mod_perm || $moderate) {

		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_MODTHREAD_TITLE').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/move.gif" alt="'.$lang->def('_ALT_MOVE').'" title="'.$lang->def('_MOVETHREAD_TITLE').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_DELTHREAD_TITLE').'" />';
		$type_h[] = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($idT, $t_author, $posted, $title, $num_post, $num_view, $locked, $erased, $important) = mysql_fetch_row($re_thread)) {
		
		$msg_for_page = $GLOBALS['lms']['visuItem'];
		if (isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT]) && $_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT] != 'new_thread')
		{
			$unread_message = $_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT];
			$first_unread_message = $num_post - $unread_message + 2;
			if ($first_unread_message % $msg_for_page)
				$ini_unread = ($first_unread_message - ($first_unread_message % $msg_for_page)) / $msg_for_page + 1;
			else
				$ini_unread = $first_unread_message / $msg_for_page;
			$first_unread_message_in_page = $first_unread_message % $msg_for_page;
		}
		else
		{
			$first_unread_message_in_page = 1;
			$ini_unread = 1;
		}
		
		if ((($num_post + 1) % $msg_for_page))
			$number_of_pages = (($num_post + 1) - (($num_post + 1) % $msg_for_page)) / $msg_for_page + 1;
		else
			$number_of_pages = ($num_post + 1) / $msg_for_page;
		
		$c_css = '';
		// thread author
		$t_author = ( isset($authors_names[$t_author])
				? ( $authors_names[$t_author][ACL_INFO_LASTNAME].$authors_names[$t_author][ACL_INFO_FIRSTNAME] == '' ?
					$acl_man->relativeId($authors_names[$t_author][ACL_INFO_USERID]) :
					$authors_names[$t_author][ACL_INFO_LASTNAME].' '.$authors_names[$t_author][ACL_INFO_FIRSTNAME] )
				: $lang->def('_UNKNOWN_AUTHOR') );

		// last post author
		if(isset($last_post[$idT])) {

			$author = $last_post[$idT]['author'];
			$last_mess_write = $last_post[$idT]['info'].' ( '.$lang->def('_BY').': <span class="mess_author">'
				.( isset($authors_names[$author])
					? ( $authors_names[$author][ACL_INFO_LASTNAME].$authors_names[$author][ACL_INFO_FIRSTNAME] == '' ?
						$acl_man->relativeId($authors_names[$author][ACL_INFO_USERID]) :
						$authors_names[$author][ACL_INFO_LASTNAME].' '.$authors_names[$author][ACL_INFO_FIRSTNAME] )
					: $lang->def('_UNKNOWN_AUTHOR') )
				.'</span> )';
		} else {
			$last_mess_write = $lang->def('_NONE');
		}
		// status of the thread
		
		if($erased) {
			$status = '<img src="'.getPathImage().'forum/thread_erased.gif" alt="'.$lang->def('_FREE').'" />';
		} elseif($locked) {
			$status = '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKED').'" />';
		} elseif(isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT])) {

			$status = '<img src="'.getPathImage().'forum/thread_unreaded.gif" alt="'.$lang->def('_UNREADED').'" />';
			$c_css = ' class="text_bold"';
		} else {
			$status = '<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" />';
		}
		$content = array($status);
		//'<img src="'.getPathImage().'forum/important.gif" alt="'.$lang->def('_IMPORTANT').'" />'
		$content_temp = ( $erased && !$mod_perm ?
						'<div class="forumErased">'.$lang->def('_ERASED').'</div>' :
						($important ? '<img src="'.getPathImage().'forum/important.gif" alt="'.$lang->def('_IMPORTANT').'" />' : '').' <a'.$c_css.' href="index.php?modname=public_forum&amp;op=message&amp;idThread='.$idT.'">'.$title.'</a>');
		
		$content_temp .= '<p class="forum_pages">';
		if ($first_unread_message_in_page != 1) {
			$content_temp .= '<a'.$c_css.' href="index.php?modname=public_forum&amp;op=message&amp;idThread='.$idT.'&amp;firstunread='.$first_unread_message_in_page.'&amp;ini='.$ini_unread.($first_unread_message_in_page != 1 ? '&#firstunread' : '').'">'.$lang->def('_FIRST_UNREAD').'</a> ';
		}
		if ($number_of_pages > 1)
		{	
			if ($number_of_pages > 4)
			{
				$content_temp .= '( <a href="index.php?modname=public_forum&amp;op=message&amp;idThread='.$idT.'&amp;ini=1">1</a> ... ';
				$content_temp .= ' <a href="index.php?modname=public_forum&amp;op=message&amp;idThread='.$idT.'&amp;ini='.($number_of_pages - 2).'">'.($number_of_pages - 2).'</a> ';
				$content_temp .= ' <a href="index.php?modname=public_forum&amp;op=message&amp;idThread='.$idT.'&amp;ini='.($number_of_pages - 1).'">'.($number_of_pages - 1).'</a> ';
				$content_temp .= ' <a href="index.php?modname=public_forum&amp;op=message&amp;idThread='.$idT.'&amp;ini='.$number_of_pages.'">'.$number_of_pages.'</a> )'; 
			}
			else
			{
				$content_temp .= '(';
				for ($i = 1; $i <= $number_of_pages; $i++)
					$content_temp .= ' <a href="index.php?modname=public_forum&amp;op=message&amp;idThread='.$idT.'&amp;ini='.$i.'">'.$i.'</a> ';
				$content_temp .= ')';
			}
		}
		$content_temp .= '</p>';
		$content[] = $content_temp;
		
		$content[] = $num_post
			.( isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT]) && $_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT] != 'new_thread'
				? '<br />(<span class="forum_notread">'.$_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT].' '.$lang->def('_ADD').')</span>'
				: ( isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT]) && $_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT] == 'new_thread'
					? '<br />(<span class="forum_notread">'.$lang->def('_NEW_THREAD').')</span>'
					: '') );
		$content[] = $t_author;
		$content[] = $num_view;
		//$content[] = $GLOBALS['regset']->databaseToRegional($posted);
		$content[] = $last_mess_write;
		if($mod_perm || $moderate) {

			$content[] = '<a href="index.php?modname=public_forum&amp;op=modthread&amp;idThread='.$idT.'" '
				.'title="'.$lang->def('_MODTHREAD_TITLE').' : '.strip_tags($title).'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($title).'" /></a>';
			$content[] = '<a href="index.php?modname=public_forum&amp;op=movethread&amp;id_forum='.$id_forum.'&amp;id_thread='.$idT.'"><img src="'.getPathImage().'standard/move.gif" alt="'.$lang->def('_ALT_MOVE').'" title="'.$lang->def('_MOVETHREAD_TITLE').'" /></a>';
			$content[] = '<a href="index.php?modname=public_forum&amp;op=delthread&amp;idThread='.$idT.'" '
				.'title="'.$lang->def('_DELTHREAD_TITLE').' : '.strip_tags($title).'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.strip_tags($title).'" /></a>';
		}
		$tb->addBody($content);
	}
	if(!$locked_f  && checkPublicForumPerm('view', $id_forum)/*checkPerm('write', true)*/) {
		$tb->addActionAdd('<a href="index.php?modname=public_forum&amp;op=addthread&amp;idForum='.$id_forum.'">'
			.'<img src="'.getPathImage().'standard/add.gif" title="'.$lang->def('_ADDTHREADT').'" alt="'.$lang->def('_ADD').'" /> '
			.$lang->def('_ADDTHREAD').'</a>');
	}
	if($mod_perm || $moderate) {
					
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delthread]');
	}

	// NOTE: If notify request register it
	require_once($GLOBALS['where_framework'].'/lib/lib.usernotifier.php');

	$can_notify = usernotifier_getUserEventStatus(getLogUserId(), 'ForumNewThread');

	if(isset($_GET['notify']) && $can_notify) {
		if(issetNotify('forum', $id_forum, getLogUserId())) {
			$re = unsetNotify('forum', $id_forum, getLogUserId());
			$is_notify = !$re;
		} else {
			$re = setNotify('forum', $id_forum, getLogUserId());
			$is_notify = $re;
		}
		if($re) $GLOBALS['page']->add(getResultUi($lang->def('_NOTIFY_CHANGE_STATUS_CORRECT')), 'content');
		else $GLOBALS['page']->add(getErrorUi($lang->def('_NOTIFY_CHANGE_STATUS_FAILED')), 'content');
	} elseif($can_notify) {
		$is_notify = issetNotify('forum', $id_forum, getLogUserId());
	}


	$text_inner = '';
	if($can_notify) {

		$text_inner .= '<li><a href="index.php?modname=public_forum&amp;op=thread&amp;notify=1&amp;idForum='.$id_forum.'&amp;ini='.$ini_page.'" '
		.( !$is_notify ?
			'title="'.$lang->def('_NOTIFY_ME_FORUM_TITLE').'">'
			.'<img src="'.getPathImage().'forum/notify.gif" alt="'.$lang->def('_NOTIFY').'" /> '.$lang->def('_NOTIFY_ME_FORUM').'</a> '
			:
			'title="'.$lang->def('_UNNOTIFY_ME_FORUM_TITLE').'">'
			.'<img src="'.getPathImage().'forum/unnotify.gif" alt="'.$lang->def('_UNNOTIFY').'" /> '.$lang->def('_UNNOTIFY_ME_FORUM').'</a> '
		).'</li>';
	}
	if($mod_perm) {
		$text_inner .= '<li><a href="index.php?modname=public_forum&amp;op=modstatus&amp;idForum='.$id_forum.'">'
			.( $locked_f
				?'<img src="'.getPathImage().'forum/forum.gif" alt="'.$lang->def('_UNLOCKFORUMALT').'" /> '.$lang->def('_UNLOCKFORUM')
				: '<img src="'.getPathImage().'forum/forum_locked.gif" alt="'.$lang->def('_LOCKFORUMALT').'" /> '.$lang->def('_LOCKFORUM') )
			.'</a></li>';
	}
	$GLOBALS['page']->add($nav_bar->getNavBar($ini), 'content');
	if($text_inner != '') $GLOBALS['page']->add('<div class="forum_action_top"><ul class="adjac_link">'.$text_inner.'</ul></div>', 'content');
	if (isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID]) && count($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID]))
		$GLOBALS['page']->add('<div><p align="right"><a href="index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;allread=1">'.$lang->def('_ALL_THREAD_READ').'</a></p>', 'content');
	$GLOBALS['page']->add($tb->getTable(), 'content');
	if($text_inner != '') $GLOBALS['page']->add('<div class="forum_action_bottom"><ul class="adjac_link">'.$text_inner.'</ul></div>', 'content');
	$GLOBALS['page']->add(
		$nav_bar->getNavBar($ini)
		.'</div>', 'content');
}

//---------------------------------------------------------------------------//

function addthread() {
	checkPublicForumPerm('view', (int)$_GET['idForum']);

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum');
	$id_forum = importVar('idForum', true, 0);

	list($title) = mysql_fetch_row(mysql_query("
	SELECT title
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND idForum = '".$id_forum."'"));

	$page_title = array(
		'index.php?modname=public_forum&amp;forum' => $lang->def('_FORUM'),
		'index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum => $title,
		$lang->def('_NEW_THREAD')
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'forum', $lang->def('_FORUM'))
		.'<div class="std_block">'
		.getBackUi('index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum, $lang->def('_BACK'))
		.Form::openForm('form_forum', 'index.php?modname=public_forum&amp;op=insthread', false, false, 'multipart/form-data')
		.Form::openElementSpace()

		.Form::getHidden('idForum', 'idForum', $id_forum)
		.Form::getTextfield($lang->def('_SUBJECT'), 'title', 'title', 255)
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof')
	, 'content');
	if(checkPublicForumPerm('upload', (int)$_GET['idForum'])) {

		$GLOBALS['page']->add(Form::getFilefield($lang->def('_UPLOAD'), 'attach', 'attach'), 'content');
	}
	$is_important = array('No', 'Si');
	if (checkPerm('mod', true) || checkPublicForumPerm('moderate', (int)$_GET['idForum']))
		$GLOBALS['page']->add(Form::getDropdown($lang->def('_IMPORTANT_THREAD'), 'important', 'important', $is_important), 'content');
	$GLOBALS['page']->add(
		Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('post_thread', 'post_thread', $lang->def('_SEND'))
		.Form::getButton('undp', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'
	, 'content');
}

//---------------------------------------------------------------------------//

function save_file($file) {
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');

	$path = '/doceboLms/'.$GLOBALS['lms']['pathforum'];

	if($file['name'] != '') {

		$savefile = PUBLIC_FORUM_COURSE_ID.'_'.rand(0,100).'_'.time().'_'.$file['name'];
		if(!file_exists($GLOBALS['where_files_relative'].$path.$savefile)) {

			sl_open_fileoperations();
			if(!sl_upload($file['tmp_name'], $path.$savefile)) {

				$savefile = '';
			}
			sl_close_fileoperations();
			return $savefile;
		}
	}
	return '';
}

function delete_file( $name ) {
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');

	$path = '/doceboLms/'.$GLOBALS['lms']['pathforum'];
	if($name != '') return sl_unlink($path.$name);
}

function insthread() {
	checkPublicForumPerm('write', (int)$_GET['idForum']);

	$lang =& DoceboLanguage::createInstance('forum');
	$id_forum = importVar('idForum', true , 0);
	if(isset($_POST['undo'])) jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum);

	list($forum_title) = mysql_fetch_row(mysql_query("
	SELECT title
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND idForum = '".$id_forum."'"));

	$locked = false;
	if(!checkPublicForumPerm('moderate', (int)$_GET['idForum'])) {

		$query_view_forum = "
		SELECT idMember, locked
		FROM ".$GLOBALS['prefix_lms']."_forum AS f
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND f.idForum = '".$id_forum."'";
		$re_forum = mysql_query($query_view_forum);
		while(list($id_m, $lock_s) = mysql_fetch_row($re_forum)) {

			$locked = $lock_s;
			if($id_m != NULL) $members[] = $id_m;
		}
	}
	$continue = false;
	if(!isset($members)) $continue = true;
	else {
		$acl 	=& $GLOBALS['current_user']->getAcl();
		$all_user_idst = $acl->getSTGroupsST(getLogUserId());
		$all_user_idst[] = getLogUserId();

		$can_access = array();
		$can_access = array_intersect($members, $all_user_idst);
		if(!empty($can_access)) $continue = true;
	}
	if(!$continue) jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=err_cannotsee');
	if($locked) jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=err_lock');

	if($_POST['title'] == '') {
		if($_POST['textof'] != '') {

			$_POST['title'] = substr(strip_tags($_POST['textof']), 0, 50).( count($_POST['textof']) > 50 ? '...' : '' );
		} else {

			$_POST['title'] = $lang->def('_NOTITLE');
		}
	}
	$now = date("Y-m-d H:i:s");
	$important = importVar('important', true, '0');
	$ins_query = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_forumthread
	( idForum, id_edition, title, author, num_post, last_post, posted, rilevantForum )
	VALUES (
		'".$id_forum."',
		'".PUBLIC_FORUM_EDITION_ID."',
		'".$_POST['title']."',
		'".getLogUserId()."',
		 0,
		 0,
		 '".$now ."',
		 '".$important."')";
	if(!mysql_query($ins_query)) jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=err_ins');
	list($id_thread) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

	$name_file = '';
	if(($_FILES['attach']['name'] != '') && checkPublicForumPerm('upload', (int)$_GET['idForum'])) {

		$name_file = save_file($_FILES['attach']);
	}

	$ins_mess_query = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_forummessage
	( idThread, idCourse, title, textof, author, posted, answer_tree, attach, generator )
	VALUES (
		'".$id_thread."',
		'".(int)PUBLIC_FORUM_COURSE_ID."',
		'".$_POST['title']."',
		'".$_POST['textof']."',
		'".getLogUserId()."',
		'".$now ."',
		'/".$now ."',
		'".addslashes($name_file)."',
		'1' ) ";
	if(!mysql_query( $ins_mess_query )) {

		mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_forumthread
		WHERE idThread = '$id_thread'");
		delete_file($name_file);

		jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=err_ins2');
	}
	list($id_message) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_forumthread
	SET last_post = '$id_message'
	WHERE idThread = '$id_thread'");

	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_forum
	SET num_thread = num_thread + 1,
		num_post = num_post + 1,
		last_post = '$id_message'
	WHERE idForum = '$id_forum'");

	$course_name = PUBLIC_FORUM_COURSE_NAME;

	// launch notify
	require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');

	$msg_composer = new EventMessageComposer('forum', 'lms');

	$msg_composer->setSubjectLangText('email', '_SUBJECT_NOTIFY_THREAD', false);
	$msg_composer->setBodyLangText('email', '_NEW_THREAD_INSERT_IN_FORUM', array(	'[url]' => $GLOBALS['lms']['url'],
																		'[course]' => PUBLIC_FORUM_COURSE_NAME,
																		'[forum_title]' => $forum_title,
																		'[thread_title]' => $_POST['title'] ) );

	$msg_composer->setSubjectLangText('sms', '_SUBJECT_NOTIFY_THREAD_SMS', false);
	$msg_composer->setBodyLangText('sms', '_NEW_THREAD_INSERT_IN_FORUM_SMS', array(	'[url]' => $GLOBALS['lms']['url'],
																		'[course]' => PUBLIC_FORUM_COURSE_NAME,
																		'[forum_title]' => $forum_title,
																		'[thread_title]' => $_POST['title'] ) );

	launchNotify('forum', $id_forum, $lang->def('_NEW_THREAD'), $msg_composer);

	jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread);
}

//---------------------------------------------------------------------------//

function modthread() {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'lms');
	$id_thread 	= importVar('idThread', true, 0);
	$ini 	= importVar('ini');

	$mod_perm	= checkPerm('mod', true);
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	// retrive info about message
	$mess_query = "
	SELECT idMessage, title, textof, author
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE idThread = '".$id_thread."' AND generator = '1'";
	list($id_message, $title, $textof, $author) = mysql_fetch_row(mysql_query($mess_query));

	// Some info about forum and thread
	$thread_query = "
	SELECT idForum
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum) = mysql_fetch_row(mysql_query($thread_query));
	
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	
	if(!$moderate && !$mod_perm && ($author != getLogUserId()) ) die("You can't access");
	
	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));

	$page_title = array(
		'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
		'index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum => $forum_title,
		$lang->def('_MOD_THREAD')
	);

	$GLOBALS['page']->add(
		getTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.( isset($_GET['search'])
			? getBackUi('index.php?modname=public_forum&op=search&amp;ini='.$ini, $lang->def('_BACK'))
			: getBackUi('index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum, $lang->def('_BACK'))
		)
		.Form::openForm('form_forum', 'index.php?modname=public_forum&amp;op=upthread', false, false, 'multipart/form-data')
		.Form::openElementSpace()

		.Form::getHidden('search', 'search', (isset($_GET['search']) ? '1' : '0' ) )
		.Form::getHidden('ini', 'ini', importVar('ini') )
		.Form::getHidden('idThread', 'idThread', $id_thread)
		.Form::getTextfield($lang->def('_SUBJECT'), 'title', 'title', 255, $title)
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', $textof)
	, 'content');
	if(checkPublicForumPerm('upload', $id_forum)) {

		$GLOBALS['page']->add(Form::getFilefield($lang->def('_UPLOAD'), 'attach', 'attach'), 'content');
	}
	$is_important = array('No', 'Si');
	if (checkPublicForumPerm('moderate', $id_forum) || checkPerm('mod', true))
		$GLOBALS['page']->add(Form::getDropdown($lang->def('_IMPORTANT_THREAD'), 'important', 'important', $is_important), 'content');
	$GLOBALS['page']->add(
		Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('post_thread', 'post_thread', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function upthread() {
	$id_thread 	= importVar('idThread', true, 0);
	$ini 	= importVar('ini');
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$id_thread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));
	
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	$mod_perm	= checkPerm('mod', true);

	$lang =& DoceboLanguage::createInstance('forum');

	// retrive info about message
	$mess_query = "
	SELECT idMessage, author, attach
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE  idThread = '".$id_thread."' AND generator = '1'";
	list($id_message, $author, $attach) = mysql_fetch_row(mysql_query($mess_query));
	if(isset($_POST['undo'])) {

		if($_POST['search'] == 1) jumpTo('index.php?modname=public_forum&op=search&amp;ini='.$ini);
		else jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini);
	}

	if(!$moderate && !$mod_perm && ($author != getLogUserId()) ) die("You can't access");

	list($id_forum, $locked_t, $erased_t) = mysql_fetch_row(mysql_query("
	SELECT idForum, locked, erased
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'"));

	if($locked_t ||$erased_t && (!$mod_perm && !$moderate)) {
		if($_POST['search'] == 1) jumpTo('index.php?modname=public_forum&op=search&amp;ini='.$ini);
		else jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_lock');
	}
	if($_POST['title'] == '') $_POST['title'] = substr(strip_tags($_POST['textof']), 0, 50).'...';

	$now = date("Y-m-d H:i:s");

	//save attachment
	$name_file = $attach;
	if($_FILES['attach']['name'] != '' && checkPublicForumPerm('upload', $id_forum) ) {

		delete_file($attach);
		$name_file = save_file($_FILES['attach']);
	}
	$upd_mess_query = "
	UPDATE ".$GLOBALS['prefix_lms']."_forummessage
	SET title = '".$_POST['title']."',
		textof = '".$_POST['textof']."',
		attach = '".$name_file."',
		modified_by = '".getLogUserId()."',
		modified_by_on = '".$now."'
	WHERE idMessage = '".$id_message."' AND idCourse = '".PUBLIC_FORUM_COURSE_ID."'";
	if(!mysql_query($upd_mess_query)) {

		delete_file($name_file);
		if($_POST['search'] == 1) jumpTo('index.php?modname=public_forum&op=search&amp;ini='.$ini);
		else jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=err_ins');
	}
	$is_rilevant = importVar('important', true, 0);
	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_forumthread
	SET title = '".$_POST['title']."'," .
		" rilevantForum = '".$is_rilevant."'
	WHERE idThread = '".$id_thread."'");
	if($_POST['search'] == 1) jumpTo('index.php?modname=public_forum&op=search&amp;ini='.$ini);
	else jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=ok');
}

//---------------------------------------------------------------------------//

function delthread() {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'lms');
	$id_thread = importVar('idThread', true, 0);
	$ini = importVar('ini');

	$thread_query = "
	SELECT idForum, title, last_post
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $last_post) = mysql_fetch_row(mysql_query($thread_query));

	if(isset($_POST['undo'])) {
		if(get_req('search', DOTY_INT) == 1) jumpTo('index.php?modname=public_forum&op=search&amp;ini='.$ini);
		else jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum);
	}
	$confirm = isset($_POST['confirm']);
	if(!$confirm) $confirm = get_req('confirm', DOTY_INT, 0);
	if($confirm) {

		$forum_query = "
		SELECT last_post
		FROM ".$GLOBALS['prefix_lms']."_forum
		WHERE idForum = '".$id_forum."'";
		list($last_post_forum) = mysql_fetch_row(mysql_query($forum_query));

		$mess_query = "
		SELECT attach
		FROM ".$GLOBALS['prefix_lms']."_forummessage
		WHERE idThread = '".$id_thread."'";
		$re_mess = mysql_query($mess_query);
		while(list($file) = mysql_fetch_row($re_mess)) {

			if($file != '') delete_file($file);
		}
		$post_deleted = mysql_num_rows($re_mess);
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_forummessage
		WHERE idThread = '".$id_thread."'"))
			if(get_req('search', DOTY_INT) == 1) jumpTo('index.php?modname=public_forum&op=search&amp;ini='.$ini);
			else jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=err_del');


		if($last_post_forum == $last_post) {

			$query_text = "
			SELECT idThread, posted
			FROM ".$GLOBALS['prefix_lms']."_forumthread
			WHERE idForum = '".$id_forum."'
			ORDER BY posted DESC";
			$re = mysql_query($query_text);
			list($id_new, $post) = mysql_fetch_row($re);
		}

		if(!mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forum
		SET num_thread = num_thread - 1,
			num_post = num_post - ".$post_deleted
		.( $last_post_forum == $last_post ? " , last_post = '".$id_new."' " : " " )
		." WHERE idForum = '".$id_forum."'"))
			if($_POST['search'] == 1) jumpTo('index.php?modname=public_forum&op=search&amp;ini='.$ini);
			else jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=err_del');

		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_forumthread
		WHERE idThread = '".$id_thread."'"))
			if($_POST['search'] == 1) jumpTo('index.php?modname=public_forum&op=search&amp;ini='.$ini);
			else jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=err_del');

		unsetNotify('thread', $id_thread);
		if($_POST['search'] == 1) jumpTo('index.php?modname=public_forum&op=search');
		else jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=ok');
	} else {

		$forum_query = "
		SELECT title
		FROM ".$GLOBALS['prefix_lms']."_forum
		WHERE idForum = '".$id_forum."'";
		list($forum_title) = mysql_fetch_row(mysql_query($forum_query));

		$page_title = array(
			'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
			'index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum => $forum_title,
			$lang->def('_DEL_THREAD')
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.Form::openForm('del_thread', 'index.php?modname=public_forum&amp;op=delthread')
			.Form::getHidden('idThread', 'idThread', $id_thread)
			.Form::getHidden('search', 'search', (isset($_GET['search']) ? '1' : '0' ) )
			.Form::getHidden('ini', 'ini', importVar('ini') )
			.getDeleteUi(
				$lang->def('_AREYOUSURE_THREAD'),
				'<span>'.$lang->def('_TITLE').' :</span> '.$thread_title,
				false,
				'confirm',
				'undo'
			)
			.Form::closeForm()
			.'</div>', 'content');
	}
}

//---------------------------------------------------------------------------//

// XXX: distance
function loadDistance( $date ) {

	// yyyy-mm-dd hh:mm:ss
	// 0123456789012345678
	$year 	= substr($date, 0, 4);
	$month 	= substr($date, 5, 2);
	$day	= substr($date, 8, 2);

	$hour 	= substr($date, 11, 2);
	$minute = substr($date, 14, 2);
	$second	= substr($date,17 , 2);

	$distance = time() - mktime($hour, $minute, $second, $month, $day, $year);
	//second -> minutes
	$distance = (int)($distance / 60);
	//< 1 hour print minutes
	if( ($distance >= 0 ) && ($distance < 60) ) return $distance.' '.def('_MINUTES');

	//minutes -> hour
	$distance = (int)($distance / 60);
	if( ($distance >= 0 ) && ($distance < 60) ) return $distance.' '.def('_HOURS');

	//hour -> day
	$distance = (int)($distance / 24);
	if( ($distance >= 0 ) && ($distance < 30 ) ) return $distance.' '.def('_DAYS');

	//echo > 1 month
	return def('_ONEMONTH');
}

function message() {
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.user_profile.php');

	$lang =& DoceboLanguage::createInstance('forum', 'lms');
	$id_thread = importVar('idThread', true, 0);
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$id_thread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));

	if (!checkPublicForumPerm('view', $id_forum))
		die('You can\'t access!');
	
	$sema_perm 	= checkPerm('sema', true);
	
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	$mod_perm	= checkPerm('mod', true);
	$write_perm = checkPublicForumPerm('write', $id_forum);
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	$profile_man = new UserProfile(0);
	$profile_man->init('profile', 'lms', 'index.php?modname=public_forum&op=forum');

	$tb 	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CAPTION_FORUM_MESSAGE'), $lang->def('_SUMMARY_FORUM_MESSAGE'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink('index.php?modname=public_forum&amp;op=message&amp;idThread='.$id_thread);
	$ini 	= $tb->getSelectedElement();
	$ini_page = $tb->getSelectedPage();
	$first_unread_message = importVar('firstunread', true, 0);
	$ini_first_unread_message = importVar('ini', true, 0);
	
	$set_important = importVar('important', true, 0);
	if ($set_important == 1)
	{
		$query_set_important = "UPDATE ".$GLOBALS['prefix_lms']."_forumthread" .
								" SET rilevantForum = 1" .
								" WHERE idThread = '".$id_thread."'";
		
		$result_set_important = mysql_query($query_set_important);
	}
	if ($set_important == 2)
	{
		$query_set_important = "UPDATE ".$GLOBALS['prefix_lms']."_forumthread" .
								" SET rilevantForum = 0" .
								" WHERE idThread = '".$id_thread."'";
		
		$result_set_important = mysql_query($query_set_important);
	}
	
	$result = mysql_fetch_row(mysql_query("SELECT rilevantForum FROM ".$GLOBALS['prefix_lms']."_forumthread WHERE idThread = '".$id_thread."'"));
	$is_important = $result[0];
	
	// Some info about forum and thread
	$thread_query = "
	SELECT idForum, title, num_post, locked, erased
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $tot_message, $locked_t, $erased_t) = mysql_fetch_row(mysql_query($thread_query));
	
	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));
	++$tot_message;

	//set as readed if needed
	if(isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$id_thread])) unset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$id_thread]);

	if( ($ini == 0) && (!isset($_GET['result'])) ) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forumthread
		SET num_view = num_view + 1
		WHERE idThread = '".$id_thread."'");
	}
	$page_title = array(
		'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
		'index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum => $forum_title,
		$thread_title
	);
	if($erased_t && !$mod_perm && !$moderate) {

		$GLOBALS['page']->add(
			getTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_CANNOTENTER')
			.'</div>', 'content');
		return;
	}
	// Who have semantic evaluation
	$re_sema = mysql_query("
	SELECT DISTINCT idmsg
	FROM ".$GLOBALS['prefix_lms']."_forum_sema");
	while(list($msg_sema) = mysql_fetch_row($re_sema)) $forum_sema[$msg_sema] = 1;

	// Find post
	$messages 		= array();
	$authors 		= array();
	$authors_names	= array();
	$authors_info	= array();
	$re_message = mysql_query("
	SELECT idMessage, posted, title, textof, attach, locked, author, modified_by, modified_by_on
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE idThread = '".$id_thread."'
	ORDER BY posted
	LIMIT $ini, ".$GLOBALS['lms']['visuItem']);
	while($record = mysql_fetch_assoc($re_message)) {

		$messages[$record['idMessage']] 	= $record;
		$authors[$record['author']] 		= $record['author'];
		if($record['modified_by'] != 0) {
			$authors[$record['modified_by']] 	= $record['modified_by'];
		}
	}
	$authors_names =& $acl_man->getUsers($authors);
	$level_name = CourseLevel::getLevels();

	// Retriving level and number of post of the authors
	if(!empty($authors)) {

		$re_num_post = mysql_query("
		SELECT u.idUser, u.level, COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_forummessage AS m, ".$GLOBALS['prefix_lms']."_courseuser AS u
		WHERE m.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND m.author = u.idUser AND m.author IN ( ".implode($authors, ',')." )
		GROUP BY u.idUser, u.level");
		while( list($id_u, $level_u, $num_post_a) = mysql_fetch_row($re_num_post) ) {

			$authors_info[$id_u] = array( 'num_post' => $num_post_a, 'level' => $level_name[$level_u] );
		}
		$profile_man->setCahceForUsers($authors);
	}
	$type_h = array('forum_sender', 'forum_text');
	$cont_h = array($lang->def('_AUTHOR'), $lang->def('_TEXTOF'));
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);

	// Compose messagges display
	$path = $GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['framework']['pathphoto'];
	$counter = 0;
	while(list($id_message, $message_info) = each($messages)) {
		$counter++;
		// sender info
		$m_author = $message_info['author'];

		//if(isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_AVATAR] != '') $img_size = @getimagesize($path.$authors_names[$m_author][ACL_INFO_AVATAR]);

		$profile_man->setIdUser($m_author);
		
		$author = $profile_man->getUserPanelData(false, 'normal');
		$sender = '';
		
		$sender = '<div class="forum_author">';
		
		$sender = $author['actions']
			.$author['display_name']
			.'</div>'
			.( isset($authors_info[$m_author])
				? '<div class="forum_level">'.$authors_info[$m_author]['level'].'</div>' : '' )
			.'<br/>'
			.(strstr($author['avatar'], './templates/standard/images/profile/user.png') ? $author['photo'] : $author['avatar'])
			.'<div class="forum_numpost">'.$lang->def('_NUMPOST').' : '
			.( isset($authors_info[$m_author]['num_post'])
				? $authors_info[$m_author]['num_post']
				: 0 )
			.'</div>'
			
			.'<img src="'.getPathImage().'standard/user.gif" alt="&gt;" />&nbsp;'
			.'<a href="index.php?modname=public_forum&amp;op=viewprofile&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'&amp;idThread='.$id_thread.'">'.$lang->def('_VIEWPROFILE').'</a>';
			
			/*.( isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_AVATAR] != ''
				? '<img class="forum_avatar'.( $img_size[0] > 150 || $img_size[1] > 150 ? ' image_limit' : '' ).'" src="'.$path.$authors_names[$m_author][ACL_INFO_AVATAR].'" alt="'.$lang->def('_AVATAR').'" />'
				: '' )*/
			
			
			/*
			.( isset($authors_names[$m_author])
				?( $authors_names[$m_author][ACL_INFO_LASTNAME].$authors_names[$m_author][ACL_INFO_FIRSTNAME] == '' ?
					$acl_man->relativeId($authors_names[$m_author][ACL_INFO_USERID]) :
					$authors_names[$m_author][ACL_INFO_LASTNAME].' '.$authors_names[$m_author][ACL_INFO_FIRSTNAME] )
				: $lang->def('_UNKNOWN_AUTHOR') )
			*/
			/*.'<img src="'.getPathImage().'standard/user.gif" alt="&gt;" />&nbsp;'
			.'<a href="index.php?modname=public_forum&amp;op=viewprofile&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'">'.$lang->def('_VIEWPROFILE').'</a>';
		*/
		// msg info
		$msgtext = '';
		if ($counter == $first_unread_message)
			$msgtext .= '<a name="firstunread"></a><div class="forum_post_posted">';
		else
		$msgtext .= '<div class="forum_post_posted">';
		
		$msgtext .= $lang->def('_POSTED').' : '.$GLOBALS['regset']->databaseToRegional($message_info['posted'])
			.' ( '.loadDistance($message_info['posted']).' )'
			.'</div>';
		if($message_info['locked']) {

			$msgtext .= '<div class="forum_post_locked">'.$lang->def('_LOCKEDMESS').'</div>';
		} else {
			if($message_info['attach'] != '') {

				$msgtext .= '<div class="forum_post_attach">'
					.'<a href="index.php?modname=public_forum&amp;op=download&amp;id='.$id_message.'">'
					.$lang->def('_ATTACHMENT').' : '
					.'<img src="'.getPathImage('fw').mimeDetect($message_info['attach']).'" alt="'.$lang->def('_ATTACHMENT').'" /></a>'
					.'</div>';
			}
			$msgtext .= '<div class="forum_post_title">'.$lang->def('_SUBJECT').' : '.$message_info['title'].'</div>';
			$msgtext .= '<div class="forum_post_text">'
						.str_replace('[quote]', '<blockquote class="forum_quote">', str_replace('[/quote]', '</blockquote>', $message_info['textof']))
						.'</div>';

			if($message_info['modified_by'] != 0) {

				$modify_by = $message_info['modified_by'];
				$msgtext .= '<div class="forum_post_modified_by">'
						.$lang->def('_MODIFY_BY').' : '
						.( isset($authors_names[$m_author])
							?( $authors_names[$modify_by][ACL_INFO_LASTNAME].$authors_names[$modify_by][ACL_INFO_FIRSTNAME] == '' ?
								$acl_man->relativeId($authors_names[$modify_by][ACL_INFO_USERID]) :
								$authors_names[$modify_by][ACL_INFO_LASTNAME].' '.$authors_names[$modify_by][ACL_INFO_FIRSTNAME] )
							: $lang->def('_UNKNOWN_AUTHOR')
						)
						.' '.$lang->def('_MODIFY_BY_ON').' : '
						.$GLOBALS['regset']->databaseToRegional($message_info['modified_by_on'])
						.'</div>';
			}

			if(isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_SIGNATURE] != '') {
				$msgtext .= '<div class="forum_post_sign_separator"></div>'
					.'<div class="forum_post_sign">'
					.$authors_names[$m_author][ACL_INFO_SIGNATURE]
					.'</div>';
			}
		}
		$content = array($sender, $msgtext);
		$tb->addBody($content);

		// some action that you can do with this message
		$action = '';
		/*if($sema_perm) {
			if(isset($forum_sema[$id_message])) $img_sema = 'sema_check';
			else $img_sema = 'sema';
			$action .= '<a href="index.php?modname=public_forum&amp;op=editsema&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
					.'title="'.$lang->def('_EDITSEMA_TITLE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'forum/'.$img_sema.'.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_SEMATAG').'</a> ';
		}*/
		if($moderate || $mod_perm) {
			if($message_info['locked']) {

				$action .= '<a href="index.php?modname=public_forum&amp;op=moderatemessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
						.'title="'.$lang->def('_DEMODERATE_MESSAGE').' : '.strip_tags($message_info['title']).'">'
					.'<img src="'.getPathImage().'forum/demoderate.gif" alt="'.$lang->def('_ALT_DEMODERATE').' : '.strip_tags($message_info['title']).'" /> '
					.$lang->def('_DEMODERATE').'</a> ';
			} else {

				$action .= '<a href="index.php?modname=public_forum&amp;op=moderatemessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
						.'title="'.$lang->def('_MODERATE_MESSAGE').' : '.strip_tags($message_info['title']).'">'
					.'<img src="'.getPathImage().'forum/moderate.gif" alt="'.$lang->def('_ALT_MODERATE').' : '.strip_tags($message_info['title']).'" /> '
					.$lang->def('_MODERATE').'</a> ';
			}
		}
		if(!$locked_t && !$locked_f && !$message_info['locked'] && $write_perm) {
			$action .= '<a href="index.php?modname=public_forum&amp;op=addmessage&amp;idThread='.$id_thread.'&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
					.'title="'.$lang->def('_REPLY_TITLE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'forum/reply.gif" alt="'.$lang->def('_ALT_REPLY').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_QUOTE').'</a>';
		}
		if($moderate || $mod_perm || ($m_author == getLogUserId()) ) {

			$action .= '<a href="index.php?modname=public_forum&amp;op=modmessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
					.'title="'.$lang->def('_MOD_MESSAGE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_MOD').'</a>'
				.'<a href="index.php?modname=public_forum&amp;op=delmessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
					.'title="'.$lang->def('_DEL_MESSAGE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_DEL').'</a> ';
		}
		$tb->addBodyExpanded($action, 'forum_action');
	}
	if(!$locked_t && !$locked_f && $write_perm) {

		$tb->addActionAdd(
			'<a href="index.php?modname=public_forum&amp;op=addmessage&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'" title="'.$lang->def('_ADDMESSAGET').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '
			.$lang->def('_REPLY_TO_THIS_THREAD').'</a>'
		);
	}
	if($moderate || $mod_perm) {
					
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delmessage]');
	}
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.Form::openForm('search_forum', 'index.php?modname=public_forum&amp;op=search&amp;idThread='.$id_thread)
		.'<div class="search_mask form_line_l">'
		.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
		.Form::getInputTextfield(	'textfield_nowh',
									'search_arg',
									'search_arg',
									'',
									$lang->def('_SEARCH'), 255, '' )
		.' <input class="button_nowh" type="submit" id="search_button" name="search_button" value="'.$lang->def('_SEARCH').'" />'
		.'</div>'
		.Form::closeForm(), 'content');

	// NOTE: If notify request register it
	require_once($GLOBALS['where_framework'].'/lib/lib.usernotifier.php');

	$can_notify = usernotifier_getUserEventStatus(getLogUserId(), 'ForumNewResponse');

	if(isset($_GET['notify']) && $can_notify) {
		if(issetNotify('thread', $id_thread, getLogUserId())) {
			$re = unsetNotify('thread', $id_thread, getLogUserId());
			$is_notify = !$re;
		} else {
			$re = setNotify('thread', $id_thread, getLogUserId());
			$is_notify = $re;
		}
		if($re) $GLOBALS['page']->add(getResultUi($lang->def('_NOTIFY_CHANGE_STATUS_CORRECT')), 'content');
		else $GLOBALS['page']->add(getErrorUi($lang->def('_NOTIFY_CHANGE_STATUS_FAILED')), 'content');
	} elseif($can_notify) {
		$is_notify = issetNotify('thread', $id_thread, getLogUserId());
	}

	$text_inner = '';
	if($can_notify) {

		$text_inner .= '<li><a href="index.php?modname=public_forum&amp;op=message&amp;notify=1&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'" '
		.( !$is_notify ?
			'title="'.$lang->def('_NOTIFY_ME_THREAD_TITLE').'">'
			.'<img src="'.getPathImage().'forum/notify.gif" alt="'.$lang->def('_NOTIFY').'" /> '.$lang->def('_NOTIFY_ME_THREAD').'</a> '
			:
			'title="'.$lang->def('_UNNOTIFY_ME_THREAD_TITLE').'">'
			.'<img src="'.getPathImage().'forum/unnotify.gif" alt="'.$lang->def('_UNNOTIFY').'" /> '.$lang->def('_UNNOTIFY_ME_THREAD').'</a> '
		).'</li>';
	}
	if($mod_perm) {

		$text_inner .= '<li><a href="index.php?modname=public_forum&amp;op=modstatusthread&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'">'
			.( $locked_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_FREETHREAD')
				: '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKTHREAD').'" /> '.$lang->def('_LOCKTHREAD') )
			.'</a></li>';
	}
	if($mod_perm) {

		$text_inner .= '<li><a href="index.php?modname=public_forum&amp;op=changeerased&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'">'
			.( $erased_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_UNERASE')
				: '<img src="'.getPathImage().'forum/thread_erased.gif" alt="'.$lang->def('_DEL').'" /> '.$lang->def('_ERASE') )
			.'</a></li>';
	}
	if($text_inner != '') {
		$GLOBALS['page']->add('<div class="forum_action_top"><ul class="adjac_link">'.$text_inner.'</ul></div>', 'content');
	}
	if ($is_important)
		if (checkPublicForumPerm('moderate', $id_forum) || checkPerm('mod', true))
			$GLOBALS['page']->add('<div><p align="right"><a href="index.php?modname=public_forum&op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'&amp;important=2">'.$lang->def('_SET_NOT_IMPORTANT_THREAD').'</a></p>', 'content');
	else
		if (checkPublicForumPerm('moderate', $id_forum) || checkPerm('mod', true))
			$GLOBALS['page']->add('<div><p align="right"><a href="index.php?modname=public_forum&op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'&amp;important=1">'.$lang->def('_SET_IMPORTANT_THREAD').'</a></p>', 'content');
	$GLOBALS['page']->add($tb->getNavBar($ini, $tot_message), 'content');
	$GLOBALS['page']->add($tb->getTable(), 'content');
	if($text_inner != '') {
		$GLOBALS['page']->add('<div class="forum_action_bottom"><ul class="adjac_link">'.$text_inner.'</ul></div>'	, 'content');
	}
	$GLOBALS['page']->add(
		$tb->getNavBar($ini, $tot_message)
		.'</div>', 'content');
}

//---------------------------------------------------------------------------//

function moderatemessage() {
	list( $id_thread, $lock ) = mysql_fetch_row(mysql_query("
	SELECT idThread, locked
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE idMessage = '".(int)$_GET['idMessage']."'"));
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$id_thread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));
	
	if(!checkPublicForumPerm('moderate', $id_forum) && !checkPerm('mod', true)) die("You can't access");
	
	if($lock == 1) $new_status = 0;
	else $new_status = 1;

	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_forummessage
	SET locked = '$new_status'
	WHERE idMessage = '".(int)$_GET['idMessage']."'");

	jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&ini='.$_GET['ini']);
}

function modstatusthread() {
	$id_thread 		= importVar('idThread', true, 0);
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$id_thread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));
	
	if(!checkPublicForumPerm('moderate', $id_forum) && !checkPerm('mod', true)) die("You can't access");
	
	list( $idF, $lock ) = mysql_fetch_row(mysql_query("
	SELECT idForum, locked
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread ."'"));

	if($lock == 1) $new_status = 0;
	else $new_status = 1;

	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_forumthread
	SET locked = '$new_status'
	WHERE idThread = '".$id_thread ."'");

	jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&ini='.$_GET['ini']);
}

function changeerase() {
	$id_thread 		= importVar('idThread', true, 0);
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$id_thread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));
	
	if(!checkPublicForumPerm('moderate', $id_forum) && !checkPerm('mod', true)) die("You can't access");
	
	list( $idF, $erased ) = mysql_fetch_row(mysql_query("
	SELECT idForum, erased
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'"));

	if($erased == 1) $new_status = 0;
	else $new_status = 1;

	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_forumthread
	SET erased = '$new_status'
	WHERE idThread = '".$id_thread."'");

	jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&ini='.$_GET['ini']);
}

//---------------------------------------------------------------------------//

function showMessageForAdd($id_thread, $how_much) {

	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$lang =& DoceboLanguage::createInstance('forum', 'lms');

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	$tb 	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CAPTION_FORUM_MESSAGE_ADD'), $lang->def('_SUMMARY_FORUM_MESSAGE_ADD'));

	// Find post
	$messages 		= array();
	$authors 		= array();
	$authors_names	= array();
	$authors_info	= array();
	$re_message = mysql_query("
	SELECT idMessage, posted, title, textof, attach, locked, author, modified_by
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE idThread = '".$id_thread."'
	ORDER BY posted DESC
	LIMIT 0, ".$how_much);
	while($record = mysql_fetch_assoc($re_message)) {

		$messages[$record['idMessage']] 	= $record;
		$authors[$record['author']] 		= $record['author'];
		if($record['modified_by'] != 0) {
			$authors[$record['modified_by']] 	= $record['modified_by'];
		}
	}
	$authors_names =& $acl_man->getUsers($authors);
	$level_name = CourseLevel::getLevels();
	
	// Retriving level and number of post of th authors
	$re_num_post = mysql_query("
	SELECT u.idUser, u.level, COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_forummessage AS m, ".$GLOBALS['prefix_lms']."_courseuser AS u
	WHERE m.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND m.author = u.idUser AND m.author IN ( ".implode($authors, ',')." )
	GROUP BY u.idUser, u.level");
	while( list($id_u, $level_u, $num_post_a) = mysql_fetch_row($re_num_post) ) {

		$authors_info[$id_u] = array( 'num_post' => $num_post_a, 'level' => $level_name[$level_u] );
	}
	$type_h = array('forum_sender', 'forum_text');
	$cont_h = array($lang->def('_AUTHOR'), $lang->def('_TEXTOF'));
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);

	// Compose messagges display
	$path = $GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['framework']['pathphoto'];
	while(list($id_message, $message_info) = each($messages)) {

		// sender info
		$m_author = $message_info['author'];

		if(isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_AVATAR] != '')
			$img_size = @getimagesize($path.$authors_names[$m_author][ACL_INFO_AVATAR]);
		elseif(isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_PHOTO] != '')
			$img_size = @getimagesize($path.$authors_names[$m_author][ACL_INFO_PHOTO]);

		$sender = '<div class="forum_author">'
			.( isset($authors_names[$m_author])
				?( $authors_names[$m_author][ACL_INFO_LASTNAME].$authors_names[$m_author][ACL_INFO_FIRSTNAME] == '' ?
					$acl_man->relativeId($authors_names[$m_author][ACL_INFO_USERID]) :
					$authors_names[$m_author][ACL_INFO_LASTNAME].' '.$authors_names[$m_author][ACL_INFO_FIRSTNAME] )
				: $lang->def('_UNKNOWN_AUTHOR')
			)
			.'</div>'
			.( isset($authors_info[$m_author])
				? '<div class="forum_level">'.$lang->def('_LEVEL').' : '.$authors_info[$m_author]['level'].'</div>' : '' )
			.( isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_AVATAR] != ''
				? '<img class="forum_avatar'.( $img_size[0] > 150 || $img_size[1] > 150 ? ' image_limit' : '' ).'" src="'.$path.$authors_names[$m_author][ACL_INFO_AVATAR].'" alt="'.$lang->def('_AVATAR').'" />'
				: (isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_PHOTO] != ''
					? '<img class="forum_avatar'.( $img_size[0] > 150 || $img_size[1] > 150 ? ' image_limit' : '' ).'" src="'.$path.$authors_names[$m_author][ACL_INFO_PHOTO].'" alt="'.$lang->def('_PHOTO').'" />'
					: '') )
			.'<div class="forum_numpost">'.$lang->def('_NUMPOST').' : '
			.( isset($authors_info[$m_author]['num_post'])
				? $authors_info[$m_author]['num_post']
				: 0 )
			.'</div>'
			.'<img src="'.getPathImage().'standard/user.gif" alt="&gt;" />&nbsp;'
			.'<a href="index.php?modname=public_forum&amp;op=viewprofile&amp;idMessage='.$id_message.'">'.$lang->def('_VIEWPROFILE').'</a>';

		// msg info
		$msgtext = '';

		$msgtext .= '<div class="forum_post_posted">'
			.$lang->def('_POSTED').' : '.$GLOBALS['regset']->databaseToRegional($message_info['posted'])
			.' ( '.loadDistance($message_info['posted']).' )'
			.'</div>';
		if($message_info['locked']) {

			$msgtext .= '<div class="forum_post_locked">'.$lang->def('_LOCKEDMESS').'</div>';
		} else {
			$msgtext .= '<div class="forum_post_title">'.$lang->def('_SUBJECT').' : '.$message_info['title'].'</div>';

			$msgtext .= '<div class="forum_post_text">'
				.str_replace('[quote]', '<blockquote class="forum_quote">', str_replace('[/quote]', '</blockquote>', $message_info['textof']))
				.'</div>';

			if($message_info['modified_by'] != 0) {

				$modify_by = $message_info['modified_by'];
				$msgtext .= '<div class="forum_post_modified_by">'
						.$lang->def('_MODIFY_BY').' : '
						.( isset($authors_names[$modify_by])
							?( $authors_names[$modify_by][ACL_INFO_LASTNAME].$authors_names[$modify_by][ACL_INFO_FIRSTNAME] == '' ?
								$acl_man->relativeId($authors_names[$modify_by][ACL_INFO_USERID]) :
								$authors_names[$modify_by][ACL_INFO_LASTNAME].' '.$authors_names[$modify_by][ACL_INFO_FIRSTNAME] )
							: $lang->def('_UNKNOWN_AUTHOR')
						).'</div>';
			}
			if(isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_SIGNATURE] != '') {
				$msgtext .= '<div class="forum_post_sign_separator"></div>'
					.'<div class="forum_post_sign">'
					.$authors_names[$m_author][ACL_INFO_SIGNATURE]
					.'</div>';
			}
		}
		$content = array($sender, $msgtext);
		$tb->addBody($content);
	}
	$GLOBALS['page']->add($tb->getTable(), 'content');
}

function addmessage() {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'lms');
	$id_thread 		= importVar('idThread', true, 0);
	$id_message 	= importVar('idMessage', true, 0);
	$ini = importVar('ini');
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$id_thread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));
	
	if (!checkPublicForumPerm('write', $id_forum))
	
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	$mod_perm	= checkPerm('mod', true);
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	// Some info about forum and thread
	$thread_query = "
	SELECT idForum, title , locked, erased
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $locked_t, $erased_t) = mysql_fetch_row(mysql_query($thread_query));
	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));

	$page_title = array(
		'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
		'index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum => $forum_title,
		'index.php?modname=public_forum&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini => $thread_title,
		$lang->def('_REPLY_TO_THIS_THREAD')
	);
	if(($erased_t || $locked_t) && !$mod_perm && !$moderate) {

		$GLOBALS['page']->add(
			getTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_CANNOTENTER')
			.'</div>', 'content');
		return;
	}
	// retrive info about quoting
	if($id_message != 0) {

		$message_query = "
		SELECT title, textof, locked, author
		FROM ".$GLOBALS['prefix_lms']."_forummessage
		WHERE idMessage = '".$id_message."'";
		list($m_title, $m_textof, $m_locked, $author) = mysql_fetch_row(mysql_query($message_query));
		if ($m_locked) {
            unset($m_title, $m_textof);
            $id_message=0;
        }
	}
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=public_forum&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini, $lang->def('_BACK'))
		.Form::openForm('form_forum', 'index.php?modname=public_forum&amp;op=insmessage', false, false, 'multipart/form-data')
		.Form::openElementSpace()

		.Form::getHidden('idThread', 'idThread', $id_thread)
		.Form::getHidden('idMessage', 'idMessage', $id_message)
		.Form::getHidden('ini', 'ini', $ini)
		.Form::getTextfield($lang->def('_SUBJECT'), 'title', 'title', 255, ($id_message != '' ? $lang->def('_RE').' '.$m_title : '' ))
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', ($id_message != '' ? '<em>'.$lang->def('_WRITTED_BY').': '.$acl_man->getUserName($author).'</em><br /><br />[quote]'.$m_textof.'[/quote]' : '' ))
	, 'content');
	if(checkPublicForumPerm('upload', $id_forum)) {

		$GLOBALS['page']->add(Form::getFilefield($lang->def('_UPLOAD'), 'attach', 'attach'), 'content');
	}
	$GLOBALS['page']->add(
		Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('post_thread', 'post_thread', $lang->def('_SEND'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
	, 'content');
	showMessageForAdd($id_thread, 3);
	$GLOBALS['page']->add(
		Form::openButtonSpace()
		.Form::getButton('post_thread_2', 'post_thread', $lang->def('_SEND'))
		.Form::getButton('undo_2', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function insmessage() {
	$id_thread 	= importVar('idThread', true, 0);
	$id_message 	= importVar('idMessage', true, 0);
	$ini 	= importVar('ini');
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$id_thread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));
	
	if(!checkPublicForumPerm('write', $id_forum))
	
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	$mod_perm	= checkPerm('mod', true);

	if(isset($_POST['undo'])) jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini);

	$lang =& DoceboLanguage::createInstance('forum');

	// Some info about forum and thread
	list($id_forum, $thread_title, $locked_t, $erased_t) = mysql_fetch_row(mysql_query("
	SELECT idForum, title, locked, erased
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'"));
	$forum_query = "
	SELECT title
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title) = mysql_fetch_row(mysql_query($forum_query));

	$locked_f = false;
	if(!checkPublicForumPerm('moderate', $id_forum)) {

		$query_view_forum = "
		SELECT idMember, locked
		FROM ".$GLOBALS['prefix_lms']."_forum AS f L
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND f.idForum = '".$id_forum."'";
		$re_forum = mysql_query($query_view_forum);
		while(list($id_m, $lock_s, $erase_s) = mysql_fetch_row($re_forum)) {

			$locked_f = $lock_s;
			if($id_m != NULL) $members[] = $id_m;
		}
	}
	$continue = false;
	if(!isset($members)) $continue = true;
	else {
		$acl 	=& $GLOBALS['current_user']->getAcl();
		$all_user_idst = $acl->getSTGroupsST(getLogUserId());
		$all_user_idst[] = getLogUserId();

		$can_access = array();
		$can_access = array_intersect($members, $all_user_idst);
		if(!empty($can_access)) $continue = true;
	}
	if(!$continue) jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_cannotsee');
	if($locked_f || $locked_t ||$erased_t && (!$mod_perm && !$moderate)) {
		jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_lock');
	}

	if($_POST['title'] == '') {
		if($_POST['textof'] != '') {

			$_POST['title'] = substr(strip_tags($_POST['textof']), 0, 50).( count($_POST['textof']) > 50 ? '...' : '' );
		} else {

			$_POST['title'] = $lang->def('_NOTITLE');
		}
	}

	$now = date("Y-m-d H:i:s");

	//save attachment
	$name_file = '';
	if($_FILES['attach']['name'] != '' && checkPublicForumPerm('upload', $id_forum) ) {
		$name_file = save_file($_FILES['attach']);
	}
	$answer_tree = '';
	if($id_message != 0) {

		list($answer_tree) = mysql_fetch_row(mysql_query("
		SELECT answer_tree
		FROM ".$GLOBALS['prefix_lms']."_forummessage
		WHERE idMessage = '".$id_message."'"));
	}
	$answer_tree .= '/'.$now;

	$ins_mess_query = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_forummessage
	( idThread, idCourse, title, textof, author, posted, answer_tree, attach ) VALUES
	( 	'".$id_thread."',
		'".(int)PUBLIC_FORUM_COURSE_ID."',
		'".$_POST['title']."',
		'".$_POST['textof']."',
		'".getLogUserId()."',
		'".$now."',
		'".$answer_tree."',
		'".addslashes($name_file)."' )";
	if(!mysql_query($ins_mess_query)) {

		delete_file($name_file);
		jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_ins');
	}
	list($new_id_message) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_forum
	SET num_post = num_post + 1,
		last_post = '".$new_id_message."'
	WHERE idForum = '".$id_forum."'");

	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_forumthread
	SET num_post = num_post + 1,
		last_post = '".$new_id_message."'
	WHERE idThread = '".$id_thread."'");

	// launch notify
	require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');

	$msg_composer = new EventMessageComposer('forum', 'lms');

	$msg_composer->setSubjectLangText('email', '_SUBJECT_NOTIFY_MESSAGE', false);
	$msg_composer->setBodyLangText('email', '_NEW_MESSAGE_INSERT_IN_THREAD', array(	'[url]' => $GLOBALS['lms']['url'],
																		'[course]' => PUBLIC_FORUM_COURSE_NAME,
																		'[forum_title]' => $forum_title,
																		'[thread_title]' => $_POST['title'] ) );

	$msg_composer->setSubjectLangText('sms', '_SUBJECT_NOTIFY_MESSAGE_SMS', false);
	$msg_composer->setBodyLangText('sms', '_NEW_MESSAGE_INSERT_IN_THREAD_SMS', array(	'[url]' => $GLOBALS['lms']['url'],
																		'[course]' => PUBLIC_FORUM_COURSE_NAME,
																		'[forum_title]' => $forum_title,
																		'[thread_title]' => $_POST['title'] ) );

	launchNotify('thread', $id_thread, $lang->def('_NEW_MESSAGE'), $msg_composer);

	jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=ok');
}

//---------------------------------------------------------------------------//

function modmessage() {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'lms');
	$id_message 	= importVar('idMessage', true, 0);
	$ini 			= importVar('ini');

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	// retrive info about message
	$mess_query = "
	SELECT idThread, title, textof, author
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE  idMessage = '".$id_message."'";
	list($id_thread, $title, $textof, $author) = mysql_fetch_row(mysql_query($mess_query));
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$id_thread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));
	
	if (!checkPublicForumPerm('view', $id_forum))
		die("You can't access!'");
	
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	$mod_perm	= checkPerm('mod', true);	
	
	if(!$moderate && !$mod_perm && ($author != getLogUserId()) ) die("You can't access");

	// Some info about forum and thread
	$thread_query = "
	SELECT idForum, title , locked, erased
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $locked_t, $erased_t) = mysql_fetch_row(mysql_query($thread_query));
	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));

	$page_title = array(
		'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
		'index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum => $forum_title,
		'index.php?modname=public_forum&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini => $thread_title,
		$lang->def('_MOD_MESSAGE')
	);
	if($erased_t && !$mod_perm && !$moderate) {

		$GLOBALS['page']->add(
			getTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_CANNOTENTER')
			.'</div>', 'content');
		return;
	}

	$GLOBALS['page']->add(
		getTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=public_forum&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini, $lang->def('_BACK'))
		.Form::openForm('form_forum', 'index.php?modname=public_forum&amp;op=upmessage', false, false, 'multipart/form-data')
		.Form::openElementSpace()

		.Form::getHidden('idMessage', 'idMessage', $id_message)
		.Form::getHidden('ini', 'ini', $ini)
		.Form::getTextfield($lang->def('_SUBJECT'), 'title', 'title', 255, $title)
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', $textof)
	, 'content');
	if(checkPublicForumPerm('upload', $id_forum)) {

		$GLOBALS['page']->add(Form::getFilefield($lang->def('_UPLOAD'), 'attach', 'attach'), 'content');
	}
	$GLOBALS['page']->add(
		Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('post_thread', 'post_thread', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
	, 'content');
	showMessageForAdd($id_thread, 3);
	$GLOBALS['page']->add(
		Form::openButtonSpace()
		.Form::getButton('post_thread_2', 'post_thread', $lang->def('_SAVE'))
		.Form::getButton('undo_2', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function upmessage() {
	$id_message 	= importVar('idMessage', true, 0);
	$ini 	= importVar('ini');

	$lang =& DoceboLanguage::createInstance('forum');

	// retrive info about message
	$mess_query = "
	SELECT idThread, author, attach, generator
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE  idMessage = '".$id_message."'";
	list($id_thread, $author, $attach, $is_generator) = mysql_fetch_row(mysql_query($mess_query));
	if(isset($_POST['undo'])) jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini);

	list($id_forum, $locked_t, $erased_t) = mysql_fetch_row(mysql_query("
	SELECT idForum, locked, erased
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'"));
	
	if (!checkPublicForumPerm('view', $id_forum))
		die("You can't access!'");
	
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	$mod_perm	= checkPerm('mod', true);
	
	if(!$moderate && !$mod_perm && ($author != getLogUserId()) ) die("You can't access");
	
	if($locked_t ||$erased_t && (!$mod_perm && !$moderate)) {
		jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_lock');
	}
	if($_POST['title'] == '') $_POST['title'] = substr(strip_tags($_POST['textof']), 0, 50).'...';

	$now = date("Y-m-d H:i:s");

	//save attachment
	$name_file = $attach;
	if($_FILES['attach']['name'] != '' && checkPublicForumPerm('upload', $id_forum) ) {

		delete_file($attach);
		$name_file = save_file($_FILES['attach']);
	}
	$upd_mess_query = "
	UPDATE ".$GLOBALS['prefix_lms']."_forummessage
	SET title = '".$_POST['title']."',
		textof = '".$_POST['textof']."',
		attach = '".$name_file."',
		modified_by = '".getLogUserId()."',
		modified_by_on = '".$now."'
	WHERE idMessage = '".$id_message."' AND idCourse = '".PUBLIC_FORUM_COURSE_ID."'";
	if(!mysql_query($upd_mess_query)) {

		delete_file($name_file);
		jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_ins');
	}

	if($is_generator) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forumthread
		SET title = '".$_POST['title']."'
		WHERE idThread = '".$id_thread."'");
	}
	jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=ok');
}

//---------------------------------------------------------------------------//

function delmessage() {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'lms');

	$id_message 	= importVar('idMessage', true, 0);
	$ini 	= importVar('ini');

	$mess_query = "
	SELECT idThread, title, textof, author, attach, answer_tree
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE idMessage = '".$id_message."'";
	list($id_thread, $title, $textof, $author, $file, $answer_tree) = mysql_fetch_row(mysql_query($mess_query));

	$thread_query = "
	SELECT idForum, title, num_post, last_post
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $num_post, $last_post) = mysql_fetch_row(mysql_query($thread_query));
	
	if(!checkPublicForumPerm('view', $id_forum))
		die("You can't access");
	
	$moderate 		= checkPublicForumPerm('moderate', $id_forum);
	$mod_perm		= checkPerm('mod', true);
	
	if(!$moderate && !$mod_perm && ($author != getLogUserId()) ) die("You can't access");
	
	$forum_query = "
	SELECT title
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title) = mysql_fetch_row(mysql_query($forum_query));

	if(isset($_POST['undo'])) jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;ini='.$ini);
	if(isset($_POST['confirm']) || get_req('confirm', DOTY_INT)) {

		$new_answer_tree = substr($answer_tree, 0, -21);
		if(!mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forummessage
		SET answer_tree = CONCAT( '$new_answer_tree', SUBSTRING( answer_tree FROM ".strlen($answer_tree)." ) )
		WHERE answer_tree LIKE '".$answer_tree."/%' AND idCourse = '".PUBLIC_FORUM_COURSE_ID."'"))
			jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;result=err_del');

		if(!mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forum
		SET num_post = num_post - 1
			".( $num_post == 0 ? " ,num_thread = num_thread - 1 " : " " )."
		WHERE idForum = '".$id_forum."'"))
			jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;result=err_del');

		if(($num_post != 0) && ($last_post == $id_message)) {

			$query_text = "
			SELECT idMessage
			FROM ".$GLOBALS['prefix_lms']."_forummessage
			WHERE idThread = '".$id_thread."'
			ORDER BY posted DESC";
			$re = mysql_query($query_text);
			list($id_new, $post) = mysql_fetch_row($re);
		}
		if($num_post == 0) {

			if(!mysql_query("
			DELETE FROM ".$GLOBALS['prefix_lms']."_forumthread
			WHERE idThread = '".$id_thread."'"))
				jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;result=err_del');
			unsetNotify('thread', $id_thread);
		} else {

			if(!mysql_query("
			UPDATE ".$GLOBALS['prefix_lms']."_forumthread
			SET num_post = num_post - 1 "
				.( ($last_post == $id_message) ? " , last_post = '".$id_new."'" : '' )."
			WHERE idThread = '".$id_thread."'"))
				jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;result=err_del');
		}
		delete_file($file);

		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_forummessage
		WHERE idMessage = '".$id_message."' AND idCourse = '".PUBLIC_FORUM_COURSE_ID."'"))
			jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;result=err_del');

		if($num_post == 0) jumpTo('index.php?modname=public_forum&op=thread&idForum='.$id_forum.'&amp;result=ok');
		else jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;result=ok');
	} else {

		$page_title = array(
			'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
			'index.php?modname=public_forum&amp;op=thread&amp;idForum='.$id_forum => $forum_title,
			'index.php?modname=public_forum&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini => $thread_title,
			$lang->def('_DEL_MESSAGE')
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.Form::openForm('del_thread', 'index.php?modname=public_forum&amp;op=delmessage')
			.Form::getHidden('idMessage', 'idMessage', $id_message)
			.Form::getHidden('ini', 'ini', $ini)
			.getDeleteUi(
				$lang->def('_AREYOUSURE_MESSAGE'),
				'<span>'.$lang->def('_SUBJECT').' :</span> '.$title.'<br />'
				.$textof,
				false,
				'confirm',
				'undo' )
			.Form::closeForm()
			.'</div>', 'content');
	}
}

//---------------------------------------------------------------------------//

function viewprofile() {
	//checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
	$lang =& DoceboLanguage::createInstance('forum');

	$id_message = importVar('idMessage');
	$ini = importVar('ini', true, 1);
	$idThread = importVar('idThread', true, 1);

	list($id_thread, $idst_user) = mysql_fetch_row(mysql_query("
	SELECT idThread, author
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE idMessage = '".$id_message."'"));
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$idThread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));
	
	if(!checkPublicForumPerm('view', $id_forum))
		die("You can't access");
	
	require_once($GLOBALS['where_lms'].'/lib/lib.lms_user_profile.php');

	$lang =& DoceboLanguage::createInstance('profile', 'framework');

	$profile = new LmsUserProfile( $idst_user );
	$profile->init('profile', 'lms', 'modname=public_forum&op=viewprofile&idMessage='.$id_message.'&ini='.$ini, 'ap');

	$GLOBALS['page']->add(
		$profile->getTitleArea()

		.$profile->getHead()

		.$profile->performAction()

		.forumBackUrl()

		.$profile->getFooter()
	, 'content');
}

//---------------------------------------------------------------------------//

function forumBackUrl()
	{
		$lang =& DoceboLanguage::createInstance('profile', 'framework');
		$id_user = importVar('id_user', true, 0);
		$ap = importVar('ap', true, 0);
		$ini = importVar('ini',true, 0);
		$id_thread = importVar('idThread', true, 0);
		$id_message = importVar('idMessage', true, 0);
		if ($id_user === 0)
			return getBackUi('index.php?modname=public_forum&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini.'&amp;idMessage='.$id_message.'', '<< '.$lang->def('_BACK').'');
		return getBackUi('index.php?modname=public_forum&amp;op=viewprofile&amp;idThread='.$id_thread.'&amp;ini='.$ini.'&amp;idMessage='.$id_message.'', '<< '.$lang->def('_BACK').'');
	}
	
function editsema() {
	checkPerm('sema');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$id_message = importVar('idMessage');
	$ini = importVar('ini');

	$lang =& DoceboLanguage::createInstance('forum');

	list($id_thread, $title, $textof, $idst_user) = mysql_fetch_row(mysql_query("
	SELECT idThread, title, textof, author
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE idMessage = '".(int)$_GET['idMessage']."'"));

	$sema_values = array();
	$query_prev_entry = "
	SELECT *
	FROM ".$GLOBALS['prefix_lms']."_forum_sema
	WHERE idmsg = '".$id_message."'";
	$re_prev_entry = mysql_query($query_prev_entry);
	if (mysql_num_rows($re_prev_entry)) {

		while( $sema_info  = mysql_fetch_array($re_prev_entry)) {

			$sema_values[$sema_info['idsema']] = $sema_info['idsemaitem'];
		}
	}
	$query_sema = "
	SELECT *
	FROM ".$GLOBALS['prefix_lms']."_pagel_sema
	ORDER BY title";
	$re_sema = mysql_query($query_sema);

	$page_title = array(
		'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
		$lang->def('_ASSIGN_SEMA_TAG')
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=public_forum&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini, $lang->def('_BACK'))

		.Form::openForm('editsema', 'index.php?modname=public_forum&amp;op=savesema')
		.Form::openElementSpace()
		.Form::getHidden('idMessage', 'idMessage', $id_message)
		.Form::getHidden('ini', 'ini', $ini)
	, 'content');
	if(mysql_num_rows($re_sema) > 0) {

		while($pagel_sema = mysql_fetch_assoc($re_sema)) {

			$items = array();
			$query_item = "
			SELECT id, title
			FROM ".$GLOBALS['prefix_lms']."_pagel_sema_items
			WHERE idref = '".$pagel_sema['id']."'
			ORDER BY ord";
			$re_item = mysql_query($query_item);
			while(list($item_id, $text) = mysql_fetch_row($re_item)) {
				$items[$item_id] = $text;
			}
			$GLOBALS['page']->add(
				Form::getDropdown(	$pagel_sema['title'],
									'sema_'.$pagel_sema['id'],
									'sema['.$pagel_sema['id'].']',
									$items,
									( isset($sema_values[$pagel_sema['id']]) ? $sema_values[$pagel_sema['id']] : '' ) )
			, 'content');
		}
	}
	$GLOBALS['page']->add(
		Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');

}

function savesema() {
	checkPerm('sema');;

	$id_message 	= importVar('idMessage');
	$ini 			= importVar('ini');

	list($id_thread, $idst_user) = mysql_fetch_row(mysql_query("
	SELECT idThread, author
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE idMessage = '".$id_message."'"));

	$sema_values = array();
	$query_prev_entry = "
	SELECT *
	FROM ".$GLOBALS['prefix_lms']."_forum_sema
	WHERE idmsg = '".$id_message."'";
	$re_prev_entry = mysql_query($query_prev_entry);
	if (mysql_num_rows($re_prev_entry)) {

		while( $sema_info  = mysql_fetch_array($re_prev_entry)) {

			$sema_values[$sema_info['idsema']] = 1;
		}
	}
	$re = true;
	while(list($id, $value) = each($_POST['sema'])) {

		if (isset($sema_values[$id])) {

			$update = "
			UPDATE ".$GLOBALS['prefix_lms']."_forum_sema
			SET	idprof = '".getLogUserId()."',
				idsemaitem = '".$value."'
			WHERE iduser = '".$idst_user."' AND idmsg = '".$id_message."' AND idsema = '".$id."'";
			$re &= mysql_query($update);
		} else {

			$insert = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_forum_sema
			( idc, idprof, iduser, idmsg, idsema, idsemaitem ) VALUES
			(	'".PUBLIC_FORUM_COURSE_ID."',
				'".getLogUserId()."',
				'".$idst_user."',
				'".$id_message."',
				'".$id."',
				'".$value."' )";
			$re &= mysql_query($insert);
		}
	}
	jumpTo('index.php?modname=public_forum&op=message&idThread='.$id_thread.'&amp;result='.( $re ? 'ok' : 'err_sema' ));
}

//---------------------------------------------------------------------------//

function forumsearch() {
	//checkPerm('view');

	if(isset($_POST['search_arg'])) {
		$_SESSION['forum']['search_arg'] = $_POST['search_arg'];
		$search_arg = importVar('search_arg');
	} else {
		$search_arg = $_SESSION['forum']['search_arg'];
	}
	$ord = importVar('ord');
	$mod_perm = checkPerm('mod', true);
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.navbar.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang =& DoceboLanguage::createInstance('forum');

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	if($mod_perm) {

		$query_view_forum = "
		SELECT DISTINCT idForum
		FROM ".$GLOBALS['prefix_lms']."_forum
		WHERE idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."'";
	} else {

		$acl 	=& $GLOBALS['current_user']->getAcl();
		$all_user_idst = $acl->getSTGroupsST(getLogUserId());
		$all_user_idst[] = getLogUserId();

		$query_view_forum = "
		SELECT DISTINCT f.idForum
		FROM ".$GLOBALS['prefix_lms']."_forum AS f
		WHERE f.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' ";

	}
	$forums = array();
	$re_forum = mysql_query($query_view_forum);
	while(list($id_f) = mysql_fetch_row($re_forum)) {

		if (checkPublicForumPerm('view', $id_f) || checkPerm('mod', true))
			$forums[] = $id_f;
	}
	if(empty($forums)) {

		$page_title = array(
			'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
			$lang->def('_SEARCH_RESULT_FOR').' : '.$search_arg
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_NO_PLACEFORSEARCH')
			.'</div>', 'content');
	}
	$query_num_thread = "
	SELECT COUNT(DISTINCT t.idThread)
	FROM ".$GLOBALS['prefix_lms']."_forumthread AS t JOIN
			".$GLOBALS['prefix_lms']."_forummessage AS m
	WHERE t.idThread = m.idThread AND t.idForum IN ( ".implode($forums, ',')." )";
	
	if (isset($search_arg))
		$query_num_thread .= " AND ( m.title LIKE '%".$search_arg."%' OR m.textof LIKE '%".$search_arg."%' ) ";
	
	list($tot_thread) = mysql_fetch_row(mysql_query($query_num_thread));

	$jump_url = 'index.php?modname=public_forum&amp;op=search';
	$nav_bar 	= new NavBar('ini', $GLOBALS['lms']['visuItem'], $tot_thread, 'link');
	$nav_bar->setLink($jump_url.'&amp;ord='.$ord);
	$ini 		= $nav_bar->getSelectedElement();
	$ini_page	= $nav_bar->getSelectedPage();

	$query_thread = "
	SELECT DISTINCT t.idThread, t.idForum, t.author AS thread_author, t.posted, t.title, t.num_post, t.num_view, t.locked, t.erased
	FROM ".$GLOBALS['prefix_lms']."_forumthread AS t JOIN
			".$GLOBALS['prefix_lms']."_forummessage AS m
	WHERE t.idThread = m.idThread AND t.idForum IN ( ".implode($forums, ',')." )
		AND ( m.title LIKE '%".$search_arg."%' OR m.textof LIKE '%".$search_arg."%' ) ";
	switch($ord) {
		case "obji"		: $query_thread .= " ORDER BY t.title DESC " ;	break;
		case "obj" 		: $query_thread .= " ORDER BY t.title " ;			break;
		case "authi"	: $query_thread .= " ORDER BY t.author DESC " ;	break;
		case "auth" 	: $query_thread .= " ORDER BY t.author " ;		break;
		case "posti" 	: $query_thread .= " ORDER BY m.posted " ;		break;
		case "post"		:
		default 		: {
			$ord = 'post';
			$query_thread .= " ORDER BY m.posted DESC " ;	break;
		}
	}
	$query_thread .= " LIMIT $ini, ".$GLOBALS['lms']['visuItem'];
	$re_thread = mysql_query($query_thread);

	$re_last_post = mysql_query("
	SELECT m.idThread, t.author AS thread_author, m.posted, m.title, m.author  AS mess_author, m.generator
	FROM ".$GLOBALS['prefix_lms']."_forumthread AS t LEFT JOIN
		".$GLOBALS['prefix_lms']."_forummessage AS m ON ( t.last_post = m.idMessage )
	WHERE t.idForum IN ( ".implode($forums, ',')." )");
	while(list($idT_p, $id_ta, $posted, $title_p, $id_a, $is_gener) = mysql_fetch_row($re_last_post)) {

		$last_authors[$id_ta] = $id_ta;
		if($posted !== NULL) {

			$last_post[$idT_p]['info'] = $GLOBALS['regset']->databaseToRegional($posted).'<br />'.substr(strip_tags($title_p), 0, 15).' ...';
			$last_post[$idT_p]['author'] = $id_a;
			$last_authors[$id_a] = $id_a;
		}
	}
	if(isset($last_authors)) {
		$authors_names =& $acl_man->getUsers($last_authors);
	}

	$page_title = array(
		'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
		$lang->def('_SEARCH_RESULT_FOR').' : '.$search_arg
	);
	$GLOBALS['page']->add(
		 getTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.Form::openForm('search_forum', 'index.php?modname=public_forum&amp;op=search')
		.'<div class="search_mask form_line_l">'
		.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
		.Form::getInputTextfield(	'textfield_nowh',
									'search_arg',
									'search_arg',
									$search_arg,
									$lang->def('_SEARCH'), 255, '' )
		.' <input class="button_nowh" type="submit" id="search_button" name="search_button" value="'.$lang->def('_SEARCH').'" />'
		.'</div>'
		.Form::closeForm()
	, 'content');

	$tb = new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_THREAD_CAPTION'), $lang->def('_THRAD_SUMMARY'));

	$img_up 	= '<img src="'.getPathImage().'standard/ord_asc.gif" alt="'.$lang->def('_ORD_ASC').'" />';
	$img_down 	= '<img src="'.getPathImage().'standard/ord_desc.gif" alt="'.$lang->def('_ORD_DESC').'" />';

	$cont_h = array(
		'<img src="'.getPathImage().'forum/thread.gif" title="'.$lang->def('_FREET').'" alt="'.$lang->def('_FREE').'" />',
		'<a href="'.$jump_url.'&amp;ord='.( $ord == 'obj' ? 'obji' : 'obj' ).'" title="'.$lang->def('_ORD_THREAD').'">'
			.( $ord == 'obj' ? $img_up : ( $ord == 'obji' ? $img_down : '' ) ).$lang->def('_THREAD').'</a>',
		$lang->def('_NUMREPLY'),
		'<a href="'.$jump_url.'&amp;ord='.( $ord == 'auth' ? 'authi' : 'auth' ).'" title="'.$lang->def('_ORD_AUTHOR').'">'
			.( $ord == 'auth' ? $img_up : ( $ord == 'authi' ? $img_down : '' ) ).$lang->def('_AUTHOR').'</a>',
		$lang->def('_NUMVIEW'),
		//$lang->def('_POSTED'),
		'<a href="'.$jump_url.'&amp;ord='.( $ord == 'post' ? 'posti' : 'post' ).'" title="'.$lang->def('_ORD_POST').'">'
			.( $ord == 'post' ? $img_up : ( $ord == 'posti' ? $img_down : '' ) ).$lang->def('_LASTPOST').'</a>'
	);
	$type_h = array('image', '', 'align_center', 'align_center', 'image',
	//'align_center',
	'align_center');
	if($mod_perm) {

		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_MODTHREAD_TITLE').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_DELTHREAD_TITLE').'" />';
		$type_h[] = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($idT, $id_forum, $t_author, $posted, $title, $num_post, $num_view, $locked, $erased) = mysql_fetch_row($re_thread)) {

		$c_css = '';
		// thread author
		$t_author = ( isset($authors_names[$t_author])
				?( $authors_names[$t_author][ACL_INFO_LASTNAME].$authors_names[$t_author][ACL_INFO_FIRSTNAME] == '' ?
					$acl_man->relativeId($authors_names[$t_author][ACL_INFO_USERID]) :
					$authors_names[$t_author][ACL_INFO_LASTNAME].' '.$authors_names[$t_author][ACL_INFO_FIRSTNAME] )
				: $lang->def('_UNKNOWN_AUTHOR')
			);
		// last post author
		if(isset($last_post[$idT])) {

			$author = $last_post[$idT]['author'];
			$last_mess_write = $last_post[$idT]['info'].' ( '.$lang->def('_BY').': <span class="mess_author">'
						.( isset($authors_names[$author])
							?( $authors_names[$author][ACL_INFO_LASTNAME].$authors_names[$author][ACL_INFO_FIRSTNAME] == '' ?
								$acl_man->relativeId($authors_names[$author][ACL_INFO_USERID]) :
								$authors_names[$author][ACL_INFO_LASTNAME].' '.$authors_names[$author][ACL_INFO_FIRSTNAME] )
							: $lang->def('_UNKNOWN_AUTHOR')
						).'</span> )';
		} else {
			$last_mess_write = $lang->def('_NONE');
		}
		// status of the thread
		if($erased) {
			$status = '<img src="'.getPathImage().'forum/thread_erased.gif" alt="'.$lang->def('_FREE').'" />';
		} elseif($locked) {
			$status = '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKED').'" />';
		} elseif(isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT])) {

			$status = '<img src="'.getPathImage().'forum/thread_unreaded.gif" alt="'.$lang->def('_UNREADED').'" />';
			$c_css = ' class="text_bold"';
		} else {
			$status = '<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" />';
		}
		$content = array($status);
		$content[] = ( $erased && !$mod_perm ?
					'<div class="forumErased">'.$lang->def('_ERASED').'</div>' :
					'<a'.$c_css.' href="index.php?modname=public_forum&amp;op=searchmessage&amp;idThread='.$idT.'&amp;ini_thread='.$ini_page.'">'
					.( $search_arg !== ''
							? eregi_replace($search_arg, '<span class="filter_evidence">'.$search_arg.'</span>', $title)
							: $title ).'</a>');
		$content[] = $num_post
			.( isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT]) && $_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT] != 'new_thread'
				? '<br />(<span class="forum_notread">'.$_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$idT].' '.$lang->def('_ADD').')</span>'
				: '' );
		$content[] = $t_author;
		$content[] = $num_view;
		//$content[] = $GLOBALS['regset']->databaseToRegional($posted);
		$content[] = $last_mess_write;
		if($mod_perm) {

			$content[] = '<a href="index.php?modname=public_forum&amp;op=modthread&amp;idThread='.$idT.'&amp;search=1&amp;ini='.$ini_page.'" '
				.'title="'.$lang->def('_MODTHREAD_TITLE').' : '.strip_tags($title).'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($title).'" /></a>';
			$content[] = '<a href="index.php?modname=public_forum&amp;op=delthread&amp;idThread='.$idT.'&amp;search=1&amp;ini='.$ini_page.'" '
				.'title="'.$lang->def('_DELTHREAD_TITLE').' : '.strip_tags($title).'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.strip_tags($title).'" /></a>';
		}
		$tb->addBody($content);
	}
	$GLOBALS['page']->add($tb->getTable(), 'content');

	$GLOBALS['page']->add(
		$nav_bar->getNavBar($ini)
		.'</div>', 'content');
}

function forumsearchmessage() {
	$search_arg = $_SESSION['forum']['search_arg'];

	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'lms');
	$id_thread = importVar('idThread', true, 0);
	$ini_thread = importVar('ini_thread');
	
	$query_id_forum = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = '".$id_thread."'";
	
	list($id_forum) = mysql_fetch_row(mysql_query($query_id_forum));
	
	if(!checkPublicForumPerm('view', $id_forum))
		die("You can't access");
	
	$sema_perm 	= checkPerm('sema', true);
	
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	$mod_perm	= checkPerm('mod', true);
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	$tb 	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CAPTION_FORUM_MESSAGE_SEARCH'), $lang->def('_SUMMARY_FORUM_MESSAGE_SEARCH'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink('index.php?modname=public_forum&amp;op=searchmessage&amp;idThread='.$id_thread.'&amp;ini_thread='.$ini_thread);
	$ini 	= $tb->getSelectedElement();
	$ini_page = $tb->getSelectedPage();

	// Some info about forum and thread
	$thread_query = "
	SELECT idForum, title, num_post, locked, erased
	FROM ".$GLOBALS['prefix_lms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $tot_message, $locked_t, $erased_t) = mysql_fetch_row(mysql_query($thread_query));
	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));
	++$tot_message;

	//set as readed if needed
	if(isset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$id_thread])) unset($_SESSION['unreaded_forum'][PUBLIC_FORUM_COURSE_ID][$id_forum][$id_thread]);

	if( ($ini == 0) && (!isset($_GET['result'])) ) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_forumthread
		SET num_view = num_view + 1
		WHERE idThread = '".$id_thread."'");
	}
	$page_title = array(
		'index.php?modname=public_forum&amp;op=forum' => $lang->def('_FORUM'),
		'index.php?modname=public_forum&amp;op=search&amp;ini='.$ini_thread => $thread_title,
		$lang->def('_SEARCH_RESULT_FOR').' : '.$search_arg
	);
	if($erased_t && !$mod_perm && !$moderate) {

		$GLOBALS['page']->add(
			getTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_CANNOTENTER')
			.'</div>', 'content');
		return;
	}
	// Who have semantic evaluation
	$re_sema = mysql_query("
	SELECT DISTINCT idmsg
	FROM ".$GLOBALS['prefix_lms']."_forum_sema");
	while(list($msg_sema) = mysql_fetch_row($re_sema)) $forum_sema[$msg_sema] = 1;

	// Find post
	$messages 		= array();
	$authors 		= array();
	$authors_names	= array();
	$authors_info	= array();
	$re_message = mysql_query("
	SELECT idMessage, posted, title, textof, attach, locked, author, modified_by, modified_by_on
	FROM ".$GLOBALS['prefix_lms']."_forummessage
	WHERE idThread = '".$id_thread."'
	ORDER BY posted
	LIMIT $ini, ".$GLOBALS['lms']['visuItem']);
	while($record = mysql_fetch_assoc($re_message)) {

		$messages[$record['idMessage']] 	= $record;
		$authors[$record['author']] 		= $record['author'];
		if($record['modified_by'] != 0) {
			$authors[$record['modified_by']] 	= $record['modified_by'];
		}
	}
	$authors_names =& $acl_man->getUsers($authors);
	$level_name = CourseLevel::getLevels();

	// Retriving level and number of post of th authors
	$re_num_post = mysql_query("
	SELECT u.idUser, u.level, COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_forummessage AS m, ".$GLOBALS['prefix_lms']."_courseuser AS u
	WHERE m.idCourse = '".(int)PUBLIC_FORUM_COURSE_ID."' AND m.author = u.idUser AND m.author IN ( ".implode($authors, ',')." )
	GROUP BY u.idUser, u.level");
	while( list($id_u, $level_u, $num_post_a) = mysql_fetch_row($re_num_post) ) {

		$authors_info[$id_u] = array( 'num_post' => $num_post_a, 'level' => $level_name[$level_u] );
	}
	$type_h = array('forum_sender', 'forum_text');
	$cont_h = array($lang->def('_AUTHOR'), $lang->def('_TEXTOF'));
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);

	// Compose messagges display
	$path = $GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['framework']['pathphoto'];
	while(list($id_message, $message_info) = each($messages)) {

		// sender info
		$m_author = $message_info['author'];

		if(isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_AVATAR] != '')
			$img_size = @getimagesize($path.$authors_names[$m_author][ACL_INFO_AVATAR]);
		elseif(isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_PHOTO] != '')
			$img_size = @getimagesize($path.$authors_names[$m_author][ACL_INFO_PHOTO]);

		$sender = '<div class="forum_author">'
			.( isset($authors_names[$m_author])
				?( $authors_names[$m_author][ACL_INFO_LASTNAME].$authors_names[$m_author][ACL_INFO_FIRSTNAME] == '' ?
					$acl_man->relativeId($authors_names[$m_author][ACL_INFO_USERID]) :
					$authors_names[$m_author][ACL_INFO_LASTNAME].' '.$authors_names[$m_author][ACL_INFO_FIRSTNAME] )
				: $lang->def('_UNKNOWN_AUTHOR')
			)
			.'</div>'
			.'<div class="forum_level">'.$lang->def('_LEVEL').' : '.$authors_info[$m_author]['level'].'</div>'
			.( isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_AVATAR] != ''
				? '<img class="forum_avatar'.( $img_size[0] > 150 || $img_size[1] > 150 ? ' image_limit' : '' ).'" src="'.$path.$authors_names[$m_author][ACL_INFO_AVATAR].'" alt="'.$lang->def('_AVATAR').'" />'
				: (isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_PHOTO] != ''
					? '<img class="forum_avatar'.( $img_size[0] > 150 || $img_size[1] > 150 ? ' image_limit' : '' ).'" src="'.$path.$authors_names[$m_author][ACL_INFO_PHOTO].'" alt="'.$lang->def('_PHOTO').'" />'
					: '') )
			.'<div class="forum_numpost">'.$lang->def('_NUMPOST').' : '
			.( isset($authors_info[$m_author]['num_post'])
				? $authors_info[$m_author]['num_post']
				: 0 )
			.'</div>'
			.'<img src="'.getPathImage().'standard/user.gif" alt="&gt;" />&nbsp;'
			.'<a href="index.php?modname=public_forum&amp;op=viewprofile&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'&amp;idThread='.$id_thread.'">'.$lang->def('_VIEWPROFILE').'</a>';

		// msg info
		$msgtext = '';

		$msgtext .= '<div class="forum_post_posted">'
			.$lang->def('_POSTED').' : '.$GLOBALS['regset']->databaseToRegional($message_info['posted'])
			.' ( '.loadDistance($message_info['posted']).' )'
			.'</div>';
		if($message_info['locked']) {

			$msgtext .= '<div class="forum_post_locked">'.$lang->def('_LOCKEDMESS').'</div>';
		} else {
			if($message_info['attach'] != '') {

				$msgtext .= '<div class="forum_post_attach">'
					.'<a href="index.php?modname=public_forum&amp;op=download&amp;id='.$id_message.'">'
					.$lang->def('_ATTACHMENT').' : '
					.'<img src="'.getPathImage('fw').mimeDetect($message_info['attach']).'" alt="'.$lang->def('_ATTACHMENT').'" /></a>'
					.'</div>';
			}

			$textof = str_replace('[quote]', '<blockquote class="forum_quote">', str_replace('[/quote]', '</blockquote>', $message_info['textof']));
			$msgtext .= '<div class="forum_post_title">'.$lang->def('_SUBJECT').' : '
						.( $search_arg !== ''
							? eregi_replace($search_arg, '<span class="filter_evidence">'.$search_arg.'</span>', $message_info['title'])
							: $message_info['title'] )
						.'</div>';
			$msgtext .= '<div class="forum_post_text">'
						.( $search_arg !== ''
							? eregi_replace($search_arg, '<span class="filter_evidence">'.$search_arg.'</span>', $textof)
							: $textof )
						.'</div>';

			if($message_info['modified_by'] != 0) {

				$modify_by = $message_info['modified_by'];
				$msgtext .= '<div class="forum_post_modified_by">'
						.$lang->def('_MODIFY_BY').' : '
						.( isset($authors_names[$modify_by])
							?( $authors_names[$modify_by][ACL_INFO_LASTNAME].$authors_names[$modify_by][ACL_INFO_FIRSTNAME] == '' ?
								$acl_man->relativeId($authors_names[$modify_by][ACL_INFO_USERID]) :
								$authors_names[$modify_by][ACL_INFO_LASTNAME].' '.$authors_names[$modify_by][ACL_INFO_FIRSTNAME] )
							: $lang->def('_UNKNOWN_AUTHOR')
						)
						.' '.$lang->def('_MODIFY_BY_ON').' : '
						.$GLOBALS['regset']->databaseToRegional($message_info['modified_by_on'])
						.'</div>';
			}

			if(isset($authors_names[$m_author]) && $authors_names[$m_author][ACL_INFO_SIGNATURE] != '') {
				$msgtext .= '<div class="forum_post_sign_separator"></div>'
					.'<div class="forum_post_sign">'
					.$authors_names[$m_author][ACL_INFO_SIGNATURE]
					.'</div>';
			}
		}
		$content = array($sender, $msgtext);
		$tb->addBody($content);

		// some action that you can do with this message
		$action = '';
		if($sema_perm) {
			if(isset($forum_sema[$id_message])) $img_sema = 'sema_check';
			else $img_sema = 'sema';
			$action .= '<a href="index.php?modname=public_forum&amp;op=editsema&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
					.'title="'.$lang->def('_EDITSEMA_TITLE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'forum/'.$img_sema.'.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_SEMATAG').'</a> ';
		}
		if($moderate || $mod_perm) {
			if($message_info['locked']) {

				$action .= '<a href="index.php?modname=public_forum&amp;op=moderatemessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
						.'title="'.$lang->def('_DEMODERATE_MESSAGE').' : '.strip_tags($message_info['title']).'">'
					.'<img src="'.getPathImage().'forum/demoderate.gif" alt="'.$lang->def('_ALT_DEMODERATE').' : '.strip_tags($message_info['title']).'" /> '
					.$lang->def('_DEMODERATE').'</a> ';
			} else {

				$action .= '<a href="index.php?modname=public_forum&amp;op=moderatemessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
						.'title="'.$lang->def('_MODERATE_MESSAGE').' : '.strip_tags($message_info['title']).'">'
					.'<img src="'.getPathImage().'forum/moderate.gif" alt="'.$lang->def('_ALT_MODERATE').' : '.strip_tags($message_info['title']).'" /> '
					.$lang->def('_MODERATE').'</a> ';
			}
		}
		if((!$locked_t && !$locked_f) || $mod_perm || $moderate) {
			$action .= '<a href="index.php?modname=public_forum&amp;op=addmessage&amp;idThread='.$id_thread.'&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
					.'title="'.$lang->def('_REPLY_TITLE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'forum/reply.gif" alt="'.$lang->def('_ALT_REPLY').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_QUOTE').'</a>';
		}
		if($moderate || $mod_perm || ($m_author == getLogUserId()) ) {

			$action .= '<a href="index.php?modname=public_forum&amp;op=modmessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
					.'title="'.$lang->def('_MOD_MESSAGE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_MOD').'</a>'
				.'<a href="index.php?modname=public_forum&amp;op=delmessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page.'" '
					.'title="'.$lang->def('_DEL_MESSAGE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_DEL').'</a> ';
		}
		$tb->addBodyExpanded($action, 'forum_action');
	}
	if( (!$locked_t && !$locked_f) || $mod_perm || $moderate ) {

		$tb->addActionAdd(
			'<a href="index.php?modname=public_forum&amp;op=addmessage&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'" title="'.$lang->def('_ADDMESSAGET').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '
			.$lang->def('_REPLY_TO_THIS_THREAD').'</a>'
		);
	}
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.Form::openForm('search_forum', 'index.php?modname=public_forum&amp;op=search&amp;idThread='.$id_thread)
		.'<div class="search_mask form_line_l">'
		.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
		.Form::getInputTextfield(	'textfield_nowh',
									'search_arg',
									'search_arg',
									$search_arg,
									$lang->def('_SEARCH'), 255, '' )
		.' <input class="button_nowh" type="submit" id="search_button" name="search_button" value="'.$lang->def('_SEARCH').'" />'
		.'</div>'
		.Form::closeForm(), 'content');

	if($moderate || $mod_perm) {
		$GLOBALS['page']->add(
			'<div class="forum_action_top">'
			.'<a href="index.php?modname=public_forum&amp;op=modstatusthread&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'">'
			.( $locked_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_FREETHREAD')
				: '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKTHREAD').'" /> '.$lang->def('_LOCKTHREAD') )
			.'</a> '
			.'<a href="index.php?modname=public_forum&amp;op=changeerased&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'">'
			.( $erased_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_UNERASE')
				: '<img src="'.getPathImage().'forum/thread_erased.gif" alt="'.$lang->def('_DEL').'" /> '.$lang->def('_ERASE') )
			.'</a>'
			.'</div>' , 'content');
	}
	$GLOBALS['page']->add($tb->getTable(), 'content');
	if($moderate || $mod_perm) {
		$GLOBALS['page']->add(
			'<div class="forum_action_bottom">'
			.'<a href="index.php?modname=public_forum&amp;op=modstatusthread&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'">'
			.( $locked_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_FREETHREAD')
				: '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKTHREAD').'" /> '.$lang->def('_LOCKTHREAD') )
			.'</a> '
			.'<a href="index.php?modname=public_forum&amp;op=changeerased&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'">'
			.( $erased_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_UNERASE')
				: '<img src="'.getPathImage().'forum/thread_erased.gif" alt="'.$lang->def('_DEL').'" /> '.$lang->def('_ERASE') )
			.'</a>'
			.'</div>' , 'content');
	}
	$GLOBALS['page']->add(
		$tb->getNavBar($ini, $tot_message)
		.'</div>', 'content');

}

//-XXX: notify functions-----------------------------------------------------//

/**
 * Register a new notify
 * @param string	$notify_is_a 	specifie if the notify is for a thread or for a forum
 * @param int		$id_notify 		specifie the id of the resource
 * @param int		$id_user 		the user
 *
 * @return	bool	true if success false otherwise
 */
function setNotify($notify_is_a, $id_notify, $id_user) {
	$query_notify = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_forum_notifier
	( id_notify, id_user, notify_is_a ) VALUES (
		'".$id_notify."',
		'".$id_user."',
		'".( $notify_is_a == 'forum' ? 'forum' : 'thread' )."' )";
	return mysql_query($query_notify);
}

/**
 * Erase a register notify
 * @param string	$notify_is_a 	specifie if the notify is for a thread or for a forum
 * @param int		$id_notify 		specifie the id of the resource
 * @param int		$id_user 		the user
 *
 * @return	bool	true if success false otherwise
 */
function unsetNotify($notify_is_a, $id_notify, $id_user = false) {
	$query_notify = "
	DELETE FROM ".$GLOBALS['prefix_lms']."_forum_notifier
	WHERE id_notify = '".$id_notify."' AND
		notify_is_a = '".( $notify_is_a == 'forum' ? 'forum' : 'thread' )."' ";
	if($id_user !== false)  $query_notify .= " AND id_user = '".$id_user."'";
	return mysql_query($query_notify);
}

/**
 * Return if a user as set a notify for a resource
 * @param string	$notify_is_a 	specifie if the notify is for a thread or for a forum
 * @param int		$id_notify 		specifie the id of the resource
 * @param int		$id_user 		the user
 *
 * @return	bool	true if exists false otherwise
 */
function issetNotify($notify_is_a, $id_notify, $id_user) {
	$query_notify = "
	SELECT id_notify
	FROM ".$GLOBALS['prefix_lms']."_forum_notifier
	WHERE id_notify = '".$id_notify."' AND
		id_user = '".$id_user."' AND
		notify_is_a = '".( $notify_is_a == 'forum' ? 'forum' : 'thread' )."'";
	$re = mysql_query($query_notify);
	return ( mysql_num_rows($re) == 0 ? false : true );
}

/**
 * Return all the users registered notify
 * @param int		$id_user 		the user
 * @param string	$notify_is_a 	specifie if the notify is for a thread or for a forum
 *
 * @return	array	[thread]=>(  [id] => id, ...), [forum]=>(  [id] => id, ...)
 */
function getAllNotify($id_user, $notify_is_a = false) {
	$notify = array();
	$query_notify = "
	SELECT id_notify, notify_is_a
	FROM ".$GLOBALS['prefix_lms']."_forum_notifier
	WHERE id_user = '".$id_user."'";
	if($notify_is_a !== false) $query_notify .= " AND notify_is_a = '".( $notify_is_a == 'forum' ? 'forum' : 'thread' )."'";
	$re = mysql_query($query_notify);
	while(list($id_n, $n_is_a) = mysql_fetch_row($re)) {

		$notify[$n_is_a][$id_n] = $id_n;
	}
	return $notify;
}

function launchNotify($notify_is_a, $id_notify, $description, &$msg_composer) {

	require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');

	$recipients = array();
	$query_notify = "
	SELECT id_user
	FROM ".$GLOBALS['prefix_lms']."_forum_notifier
	WHERE id_notify = '".$id_notify."' AND
		notify_is_a = '".( $notify_is_a == 'forum' ? 'forum' : 'thread' )."' AND
		id_user <> '".getLogUserId()."'";
	if($notify_is_a !== false) $query_notify .= " AND notify_is_a = '".( $notify_is_a == 'forum' ? 'forum' : 'thread' )."'";
	$re = mysql_query($query_notify);
	echo $query_notify;
	while(list($id_user) = mysql_fetch_row($re)) {

		$recipients[] = $id_user;
	}
	if(!empty($recipients)) {

		createNewAlert(		( $notify_is_a == 'forum' ? 'ForumNewThread' : 'ForumNewResponse' ),
							'forum',
							( $notify_is_a == 'forum' ? 'new_thread' : 'responce' ),
							1,
							$description,
							$recipients,
							$msg_composer );
	}
	return;
}

function moveThread($id_thread, $id_forum)
{
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::CreateInstance('forum');
	
	$mod_perm 	= checkPerm('mod', true);
	$moderate 	= checkPublicForumPerm('moderate', $id_forum);
	
	$action = importVar('action', true, 0);
	
	if(isset($_GET['confirm']))
	{
		$id_new_forum = importVar('new_forum', true, 0);
		$id_thread = importVar('id_thread', true, 0);
		$id_forum = importVar('id_forum', true, 0);
		$confirm = importVar('confirm', true, 0);
		
		if ($confirm)
		{
			// Move the thread to the new forum
			$query = "UPDATE ".$GLOBALS['prefix_lms']."_forumthread" .
					" SET idForum = '".$id_new_forum."'" .
					" WHERE idThread = '".$id_thread."'";
			
			$result = mysql_query($query);
			
			// Select thenumber of the post in the thread
			$query_2 = "SELECT num_post" .
						" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
						" WHERE idThread = '".$id_thread."'";
			
			list($num_post) = mysql_fetch_row(mysql_query($query_2));
			
			// Update the forum info
			$query_3 = "SELECT idForum, num_thread, num_post" .
						" FROM ".$GLOBALS['prefix_lms']."_forum" .
						" WHERE idForum = '".$id_forum."'" .
						" OR idForum = '".$id_new_forum."'";
			
			$result_3 = mysql_query($query_3);
			
			$num_post_update = array();
			$num_thread_update = array();
			
			while(list($idForum, $num_thread_3, $num_post_3) = mysql_fetch_row($result_3))
			{
				if ($idForum == $id_forum)
				{
					$num_post_update[$idForum] = $num_post_3 - $num_post;
					$num_thread_update[$idForum] = $num_thread_3 - 1;
				}
				else
				{
					$num_post_update[$idForum] = $num_post_3 + $num_post;
					$num_thread_update[$idForum] = $num_thread_3 + 1;
				}
			}
			
			$last_message_update = array();
			
			$query_4 = "SELECT idMessage" .
						" FROM ".$GLOBALS['prefix_lms']."_forummessage" .
						" WHERE idThread IN" .
						"(" .
							" SELECT idThread" .
							" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
							" WHERE idForum = '".$id_forum."'" .
						")" .
						" ORDER BY posted DESC" .
						" LIMIT 0,1";
			
			list($last_message_update[$id_forum]) = mysql_fetch_row(mysql_query($query_4));
			
			$query_5 = "SELECT idMessage" .
						" FROM ".$GLOBALS['prefix_lms']."_forummessage" .
						" WHERE idThread IN" .
						"(" .
							" SELECT idThread" .
							" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
							" WHERE idForum = '".$id_new_forum."'" .
						")" .
						" ORDER BY posted DESC" .
						" LIMIT 0,1";
			
			list($last_message_update[$id_new_forum]) = mysql_fetch_row(mysql_query($query_5));
			
			$query_update_1 = "UPDATE ".$GLOBALS['prefix_lms']."_forum" .
						" SET num_post = '".$num_post_update[$id_forum]."'," .
								" num_thread='".$num_thread_update[$id_forum]."'," .
								" last_post = '".$last_message_update[$id_forum]."'" .
						" WHERE idForum = '".$id_forum."'";
			
			$result_update_1 = mysql_query($query_update_1);
			
			$query_update_2 = "UPDATE ".$GLOBALS['prefix_lms']."_forum" .
						" SET num_post = '".$num_post_update[$id_new_forum]."'," .
								" num_thread='".$num_thread_update[$id_new_forum]."'," .
								" last_post = '".$last_message_update[$id_new_forum]."'" .
						" WHERE idForum = '".$id_new_forum."'";
			
			$result_update_2 = mysql_query($query_update_2);
		}
		jumpTo('index.php?modname=public_forum&amp;op=thread&idForum='.$id_forum);
	}
	
	if ($action)
	{
		$id_new_forum = importVar('new_forum', true, 0);
		$id_thread = importVar('id_thread', true, 0);
		$id_forum = importVar('id_forum', true, 0);
		
		list($title) = mysql_fetch_row(mysql_query("SELECT title" .
													" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
													" WHERE idThread = '".$id_thread."'"));
		
		list($from_forum) = mysql_fetch_row(mysql_query("SELECT title" .
													" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
													" WHERE idForum = '".$id_forum."'"));
		
		list($to_forum) = mysql_fetch_row(mysql_query("SELECT title" .
													" FROM ".$GLOBALS['prefix_lms']."_forum" .
													" WHERE idForum = '".$id_new_forum."'"));
		
		$GLOBALS['page']->add
		(
			getTitleArea($lang->def('_MOVE_THREAD_TITLE'), 'forum')
			.'<div class="std_block">'
			.getModifyUi(	$lang->def('_AREYOUSURE_MOVE_THREAD'),
							'<span>'.$lang->def('_TITLE').' : </span> "'.$title.'"'.' '.$lang->def('_FROM_FORUM').' "'.$from_forum.'" '.$lang->def('_TO_FORUM').' "'.$to_forum.'"',
							true,
							'index.php?modname=public_forum&amp;op=movethread&amp;new_forum='.$id_new_forum.'&amp;id_thread='.$id_thread.'&amp;id_forum='.$id_forum.'&amp;confirm=1',
							'index.php?modname=public_forum&amp;op=movethread&amp;id_forum='.$id_forum.'&amp;confirm=0'
						)
			.'</div>', 'content'
		);
	}
	else
	{
		$id_course = (int)PUBLIC_FORUM_COURSE_ID;
		$id_forum = importVar('id_forum', true, 0);
		
		$list_forum = array();
		
		$query = "SELECT idForum, title" .
				" FROM ".$GLOBALS['prefix_lms']."_forum" .
				" WHERE idCourse = '".$id_course."'" .
				" AND idForum <> '".$id_forum."'";
		
		$result = mysql_query($query);
		
		while (list($id_forum_b, $title) = mysql_fetch_row($result))
			$list_forum[$id_forum_b] = $title;
		
		$GLOBALS['page']->add
		(
			getTitleArea($lang->def('_MOVE_THREAD_TITLE'), 'forum')
			.'<div class="std_block">'
			.Form::openForm('search_forum', 'index.php?modname=public_forum&amp;op=movethread&amp;id_thread='.$id_thread.'&amp;id_forum='.$id_forum.'&amp;action=1')
			.'<div class="search_mask form_line_l">'
			.Form::getDropdown($lang->def('_MOVE_TO_FORUM'), 'new_forum', 'new_forum', $list_forum)
			.' <input class="button_nowh" type="submit" id="move_thread" name="move_thread" value="'.$lang->def('_MOVE_THREAD').'" />'
			.'</div>'
			.Form::closeForm()
			.'</div>'
			, 'content'
		);
	}
}

function export()
{
	require_once ($GLOBALS['where_framework'].'/lib/lib.download.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.tags.php');
	
	$acl_man =& $GLOBALS['current_user']->getAclManager();
	$tags = new Tags('lms_forum');
	$id_forum = get_req('idForum', DOTY_INT, 0);
	$csv_string = '';
	$file_nme = '';
	$tag_list = array();
	
	if($id_forum)
	{
		$query =	"SELECT idThread, title, num_post"
					." FROM ".$GLOBALS['prefix_lms']."_forumthread"
					." WHERE idForum = '".$id_forum."'";
		
		$result = mysql_query($query);
		
		if(mysql_num_rows($result));
		{
			$tmp = array();
			$id_list = array();
			
			while(list($id_thread, $thread_title, $num_post) = mysql_fetch_row($result))
			{
				$tmp['int'] = '"nomethread";"n.msg";"titolomsg";"autore";"data";"corpomsg";"allegato";"id_msg"';
				
				$query_msg = "SELECT title, author, posted, textof, attach, idMessage"
							." FROM ".$GLOBALS['prefix_lms']."_forummessage"
							." WHERE idThread = '".$id_thread."'";
				
				$result_msg = mysql_query($query_msg);
				
				$num_post++;
				
				while(list($message_title, $author, $posted, $textof, $attach, $idMessage) = mysql_fetch_row($result_msg))
				{
					$sender_info = $acl_man->getUser($author, false);
					$author = ( $sender_info[ACL_INFO_LASTNAME].$sender_info[ACL_INFO_FIRSTNAME] == '' ?
								$acl_man->relativeId($sender_info[ACL_INFO_USERID]) :
								$sender_info[ACL_INFO_LASTNAME].' '.$sender_info[ACL_INFO_FIRSTNAME] );
					
					$posted = $GLOBALS['regset']->databaseToRegional($posted);
					
					$tmp[$idMessage] = '"'.str_replace('"', '\"', $thread_title).'";"'.$num_post.'";"'.str_replace('"', '\"', $message_title).'";"'.$author.'";"'.$posted.'";"'.str_replace('"', '\"', $textof).'";"'.$attach.'";"'.$idMessage.'"';
					$id_list[] = $idMessage;
				}
			}
			
			$tags_associated = $tags->getResourcesOccurrenceTags($id_list);
			
			$number_of_tag = 0;
			
			if(count($tags_associated))
			{
				foreach($tags_associated as $tag_tmp)
					foreach($tag_tmp as $tmp_tag)
						if(!in_array($tmp_tag['tag'], $tag_list))
						{
							$tag_list[] = $tmp_tag['tag'];
							$number_of_tag++;
						}
				
				reset($tags_associated);
				
				foreach($tag_list as $tag_name)
					$tmp['int'] .= ';"'.str_replace('"', '\"', $tag_name).'"';
				
				reset($tag_list);
			}
			
			$csv_string .= $tmp['int']."\r\n";
			
			unset($tmp['int']);
			
			foreach($tmp as $id_message => $string)
			{
				$csv_string .= $string;
				
				if(count($tags_associated))
				{
					if(isset($tags_associated[$id_message]))
					{
						foreach($tag_list as $tag_name)
						if(isset($tags_associated[$id_message][$tag_name]))
								$csv_string .= ';"'.$tags_associated[$id_message][$tag_name]['occurences'].'"';
							else
								$csv_string .= ';"0"';
					}
					else
						for($i = 0; $i < $number_of_tag; $i++)
							$csv_string .= ';"0"';
				}
				
				$csv_string .= "\r\n";
			}
			
			$query_forum =	"SELECT title"
							." FROM ".$GLOBALS['prefix_lms']."_forum"
							." WHERE idForum = '".$id_forum."'";
			
			list($forum_title) = mysql_fetch_row(mysql_query($query_forum));
			
			$file_name = str_replace(
			array('\\', '/', 	':', 	'\'', 	'\*', 	'?', 	'"', 	'<', 	'>', 	'|'),
			array('', 	'', 	'', 	'', 	'', 	'', 	'', 	'', 	'', 	'' ),
			$forum_title).'.csv';
			
			sendStrAsFile($csv_string, $file_name);
		}
	}
}

//---------------------------------------------------------------------------//

function checkPublicForumPerm($role, $id_forum)
{
	if(checkPerm('mod', true)) return true;

	$res = false;
	$role_id = "";
	$user =& $GLOBALS['current_user'];
	$acl = new DoceboACL();
	
	$role_id = '/lms/course/public/public_forum/'.$id_forum.'/'.$role;
	
	if (($role_id !== "") && ($acl->getRoleST($role_id) != false))
		$res = $user->matchUserRole($role_id);
	
	return $res;
}

function forumDispatch($op) {

	require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
	$url_man =& UrlManager::getInstance('forum');
	$url_man->setStdQuery('index.php?modname=public_forum&op=forum');

	switch($op) {
		case "forum" : {
			forum();
		};break;
		//-----------------------------------------------//
		case "addforum" : {
			addforum();
		};break;
		case "insforum" : {
			insforum();
		};break;
		//-----------------------------------------------//
		case "modforum" : {
			modforum();
		};break;
		case "upforum" : {
			upforum();
		};break;
		case "downforum" : {
			moveforum( $_GET['idForum'], 'down');
		};break;
		case "moveupforum" : {
			moveforum( $_GET['idForum'], 'up');
		};break;
		case "modstatus" : {
			changestatus();
		};break;
		case "export":
			export();
		break;
		//-----------------------------------------------//
		case "delforum" : {
			delforum();
		};break;
		//-----------------------------------------------//
		case "modforumaccess": {
			modforumaccess();
		};break;
		//-----------------------------------------------//
		case "thread" : {
			thread();
		};break;
		//-----------------------------------------------//
		case "addthread" : {
			addthread();
		};break;
		case "insthread" : {
			insthread();
		};break;
		//-----------------------------------------------//
		case "modthread" : {
			modthread();
		};break;
		case "movethread":
		{
			$id_thread = importVar('id_thread', true, 0);
			$id_forum = importVar('id_forum', true, 0);
			moveThread($id_thread, $id_forum);
		}
		break;
		case "upthread" : {
			upthread();
		};break;
		//-----------------------------------------------//
		case "delthread" : {
			delthread();
		};break;
		//-----------------------------------------------//
		case "message" : {
			message();
		};break;
		case "moderatemessage" : {
			moderatemessage();
		};break;
		case "modstatusthread" : {
			modstatusthread();
		};break;
		case "changeerased" : {
			changeerase();
		};break;
		//-----------------------------------------------//
		case "addmessage" : {
			addmessage();
		};break;
		case "insmessage" : {
			insmessage();
		};break;
		//-----------------------------------------------//
		case "modmessage" : {
			modmessage();
		};break;
		case "upmessage" : {
			upmessage();
		};break;
		//-----------------------------------------------//
		case "delmessage" : {
			delmessage();
		};break;
		//-----------------------------------------------//
		case "viewprofile" : {
			viewprofile();
		};break;
		//-----------------------------------------------//
		case "editsema" : {
			editsema();
		};break;
		case "savesema" : {
			savesema();
		};break;
		//-----------------------------------------------//
		case "download" : {
			$query = "SELECT idForum" .
					" FROM ".$GLOBALS['prefix_lms']."_forumthread" .
					" WHERE idThread = " .
					"(" .
						" SELECT idThread" .
						" FROM ".$GLOBALS['prefix_lms']."_forummessage" .
						" WHERE idMessage = '".(int)$_GET['id']."'" .
					")";
			
			list($id_forum) = mysql_fetch_row(mysql_query($query));
			
			checkPublicForumPerm('view', $id_forum);
			
			require_once($GLOBALS['where_framework'].'/lib/lib.download.php' );

			//find file
			list($title, $attach) = mysql_fetch_row(mysql_query("
			SELECT title, attach
			FROM ".$GLOBALS['prefix_lms']."_forummessage
			WHERE idMessage='".(int)$_GET['id']."'"));
			if(!$attach) {
				$GLOBALS['page']->add( getErrorUi('Sorry, such file does not exist!'), 'content');
				return;
			}
			//recognize mime type
			$expFileName = explode('.', $attach);
			$totPart = count($expFileName) - 1;

			$path = '/doceboLms/'.$GLOBALS['lms']['pathforum'];
			//send file
			sendFile($path, $attach, $expFileName[$totPart]);
		};break;
		//-----------------------------------------------//
		case "search" : {
			forumsearch();
		};break;
		case "searchmessage" : {
			forumsearchmessage();
		};break;
		//-----------------------------------------------//
	}
}

?>
