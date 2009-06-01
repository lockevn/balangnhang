<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Emanuele Sandri, Fabio Pirovano, Giovanni Derks */
/*                      http://www.docebocms.com                         */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

$acl_manger=$GLOBALS["current_user"]->getAclManager();
$GLOBALS["ANONYMOUS_IDST"]=$acl_manger->getAnonymousId();


function loadUnreaded() {

	if(!isset($_SESSION['cms_unreaded_forum'])) {

		//-find last access---------------------------------------------------------------
		$no_entry = false;
		$reLast = mysql_query("
		SELECT UNIX_TIMESTAMP(last_access)
		FROM ".$GLOBALS['prefix_cms']."_forum_timing
		WHERE idUser = '".getLogUserId()."'");
		if(mysql_num_rows($reLast)) {
			list($last_forum_access_time) = mysql_fetch_row($reLast);
		} else {
			$last_forum_access_time = 0;
			$no_entry = true;
		}
		$unreaded = array();
		$reUnreaded = mysql_query("
		SELECT t.idThread, t.idForum, m.generator, COUNT(m.idMessage)
		FROM ".$GLOBALS['prefix_cms']."_forumthread AS t JOIN ".$GLOBALS['prefix_cms']."_forummessage AS m
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
			$_SESSION['cms_unreaded_forum'] = $unreaded;
		}
		//-set as now the last forum access------------------------------------------------
		if($no_entry) {
			mysql_query("
			INSERT INTO  ".$GLOBALS['prefix_cms']."_forum_timing
			SET last_access = NOW(),
				idUser = '".getLogUserId()."'");
		} else {
			mysql_query("
			UPDATE ".$GLOBALS['prefix_cms']."_forum_timing
			SET  last_access = NOW()
			WHERE idUser = '".getLogUserId()."'");
		}
	}
}

function forum() {
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang 		=& DoceboLanguage::CreateInstance('forum', 'cms');

	$mod_perm 	= false; //-TP// checkPerm('forum', 'mod', true);
	$moderate 	= false; //-TP// checkPerm('forum', 'moderate', true);
	$base_link 	= getForumBaseUrl('&amp;op=forum');
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();


	// Find and set unreaded message
	loadUnreaded();

	$tb = new typeOne( $GLOBALS['cms']['visuItem'], '', $lang->def('_ELEFORUM'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink($base_link);
	$tb->setTableStyle("forum_table");

	$ini = $tb->getSelectedElement();

	$acl 	=& $GLOBALS['current_user']->getAcl();
	$all_user_idst = $acl->getSTGroupsST(getLogUserId());
	$all_user_idst[] = getLogUserId();

	$query_view_forum = "
	SELECT DISTINCT f.idForum, f.title, f.description, f.num_thread, f.num_post, f.locked, f.emoticons
		FROM ".$GLOBALS['prefix_cms']."_forum AS f
			LEFT JOIN ".$GLOBALS['prefix_cms']."_area_block_forum AS bf ON ( f.idForum = bf.idForum )
		WHERE
			( bf.idBlock='".(int)$GLOBALS["pb"]."' ) ORDER BY f.sequence";

	$query_num_view = "
	SELECT COUNT( DISTINCT f.idForum )
	FROM ".$GLOBALS['prefix_cms']."_forum AS f
		LEFT JOIN ".$GLOBALS['prefix_cms']."_area_block_forum AS bf ON ( f.idForum = bf.idForum )
	WHERE
		( bf.idBlock='".(int)$GLOBALS["pb"]."' ) ";

	$re_forum = mysql_query($query_view_forum);
	list($tot_forum) = mysql_fetch_row(mysql_query($query_num_view));

	$re_last_post = mysql_query("
	SELECT f.idForum, m.idThread, m.posted, m.title, m.author
	FROM ".$GLOBALS['prefix_cms']."_forum AS f LEFT JOIN
		".$GLOBALS['prefix_cms']."_forummessage AS m ON ( f.last_post = m.idMessage )");
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
	if($GLOBALS['cms']['forum_as_table'] == 'on') {

		// show forum list in a table -----------------------------------------
		// table header
		$type_h = array('image', 'image', 'forumTitle', '', 'align_center', 'align_center', 'align_center');

		$tb->setColsStyle($type_h);

		$cont_h = array(
			'<img src="'.getPathImage().'forum/forum.gif" title="'.$lang->def('_FREET').'" alt="'.$lang->def('_FREE').'" />',
			'<img src="'.getPathImage().'forum/emoticons.gif" title="'.$lang->def('_EMOTICONS').'" alt="'.$lang->def('_EMOTICONS').'" />',
			$lang->def('_TITLE'),
			$lang->def('_DESCRIPTION'),
			$lang->def('_NUMTHREAD'),
			$lang->def('_NUMPOST'),
			$lang->def('_LASTPOST')
		);

		$tb->addHead($cont_h);

		// table body
		$i = 1;
		while(list($idF, $title, $descr, $num_thread, $num_post, $locked, $emoticons) = mysql_fetch_row($re_forum) ) {

			if (checkRoleForItem("forum", $idF, "view")) {

				// Used for mod_rewrite
				$GLOBALS["forum_url_title"]=$title;

				$c_css 			= '';
				$mess_notread 	= 0;
				$thread_notread = 0;
				// NOTES: status
				if($locked)	$status = '<img src="'.getPathImage().'forum/forum_locked.gif" alt="'.$lang->def('_LOCKED').'" />';
				elseif( isset($_SESSION['cms_unreaded_forum'][$idF])) {

					if(isset($_SESSION['cms_unreaded_forum'][$idF]) && is_array($_SESSION['cms_unreaded_forum'][$idF])) {
						foreach($_SESSION['cms_unreaded_forum'][$idF] as $k => $n_mess)
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
								'<a'.$c_css.' href="'.getForumBaseUrl('&amp;op=thread&amp;idForum='.$idF).'">'.$title.'</a>',
								$descr,
								$num_thread.( $thread_notread ? '<div class="forum_notread">'.$thread_notread.' '.$lang->def('_NEW').'</div>' : '' ),
								$num_post.( $mess_notread ? '<div class="forum_notread">'.$mess_notread.' '.$lang->def('_NEW').'</div>' : '' ) );
				if(isset($last_post[$idF])) {

					$author = $last_post[$idF]['author'];
					$content[] = $last_post[$idF]['info'].' ( '.$lang->def('_BY').': <span class="mess_author">'.
						getForumMsgAuthorTxt($authors_names[$author][ACL_INFO_LASTNAME],
						$authors_names[$author][ACL_INFO_FIRSTNAME], $authors_names[$author][ACL_INFO_USERID]).'</span> )';
				} else {

					$content[] = $lang->def('_NONE');
				}
				$tb->addBody( $content );
				++$i;
			}
			else {
				$tot_forum=$tot_forum-1;
			}
		}

		$GLOBALS['page']->add(
			getCmsTitleArea($lang->def('_FORUM'), 'forum')
			.'<div class="std_block">'
			.Form::openForm('search_forum', getForumBaseUrl('&amp;op=search'))
			.'<div class="search_mask form_line_l">'
			.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
			.Form::getInputTextfield(	'textfield_nowh',
										'search_arg',
										'search_arg',
										$lang->def('_SEARCH'),
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
			getCmsTitleArea($lang->def('_FORUM'), 'forum')
			.'<div class="std_block">'
			.Form::openForm('search_forum', getForumBaseUrl('&amp;op=search'))
			.'<div class="search_mask form_line_l">'
			.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
			.Form::getInputTextfield(	'textfield_nowh',
										'search_arg',
										'search_arg',
										$lang->def('_SEARCH'),
										$lang->def('_SEARCH'), 255, '' )
			.' <input class="button_nowh" type="submit" id="search_button" name="search_button" value="'.$lang->def('_SEARCH').'" />'
			.'</div>'
			.Form::closeForm()
			, 'content');
		while( list($idF, $title, $descr, $num_thread, $num_post, $locked, $emoticons) = mysql_fetch_row( $re_forum ) ) {

			// Used for mod_rewrite
			$GLOBALS["forum_url_title"]=$title;

			$c_css = '';
			$thread_notread = 0;
			$mess_notread = 0;
			// NOTES: status
			if($locked)	$status = '<img src="'.getPathImage().'forum/forum_locked.gif" alt="'.$lang->def('_LOCKED').'" />';
			elseif( isset($_SESSION['cms_unreaded_forum'][$idF])) {

				if(isset($_SESSION['cms_unreaded_forum'][$idF]) && is_array($_SESSION['cms_unreaded_forum'][$idF])) {
					foreach($_SESSION['cms_unreaded_forum'][$idF] as $k => $n_mess)
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
					.'<a'.$c_css.' href="'.getForumBaseUrl('&amp;op=thread&amp;idForum='.$idF).'">'.$title.'</a>'
					.'</th>'
					.'<th class="image" nowrap="nowrap">'.$lang->def('_NUMTHREAD').'</th>'
					.'<th class="image" nowrap="nowrap">'.$lang->def('_NUMPOST').'</th>'
				.'</tr>'
				.'<tr>'
					.'<td>'.$descr.'</td>'
					.'<td class="image" nowrap="nowrap">'.$num_thread
						.( $thread_notread ? '<div class="forum_notread">'.$thread_notread.' '.$lang->def('_NEW').'</div>' : '' )
					.'</td>'
					.'<td class="image" nowrap="nowrap">'.$num_post
						.( $mess_notread ? '<div class="forum_notread">'.$mess_notread.' '.$lang->def('_NEW').'</div>' : '' )
					.'</td>'
				.'</tr>'
				.'<tr>'
					.'<td colspan="3">', 'content');

			if(isset($last_post[$idF])) {

				$author = $last_post[$idF]['author'];
				$GLOBALS['page']->add('<span class="forum_lastpost">'.$lang->def('_LASTPOST').' : '.$last_post[$idF]['info'].' ( '.$lang->def('_BY').': <span class="mess_author">'
					.getForumMsgAuthorTxt($authors_names[$author][ACL_INFO_LASTNAME],
					$authors_names[$author][ACL_INFO_FIRSTNAME], $authors_names[$author][ACL_INFO_USERID]).'</span> )'
						.'</span>'
					, 'content');
			} else {
			}
			$GLOBALS['page']->add(
					'</td>'
				.'</tr>'
				.'<tr>'
					.'<td colspan="3" class="forum_manag">', 'content');

			$GLOBALS['page']->add('</td>'
				.'</tr>'
				.'</table>', 'content');
				$i++;
		}

		$GLOBALS['page']->add(
			$tb->getNavBar($ini, $tot_forum)
			.'</div>', 'content' );
	}
}

function changestatus() {

	$idForum=(int)$_GET['idForum'];

	if (!checkForumModeratePerm($idForum)) return 0;

	list( $lock ) = mysql_fetch_row(mysql_query("
	SELECT locked
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$idForum."'"));

	if($lock == 1) $new_status = 0;
	else $new_status = 1;

	mysql_query("
	UPDATE ".$GLOBALS['prefix_cms']."_forum
	SET locked = '$new_status'
	WHERE idForum = '".$idForum."'");
	jumpTo(getForumBaseUrl('&op=thread&idForum='.$idForum));
}


//---------------------------------------------------------------------------//


//---------------------------------------------------------------------------//

function thread() {
	$id_forum 	= importVar('idForum', true, 0);
	if (!checkRoleForItem("forum", $id_forum, "view")) return 0;

	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.navbar.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang =& DoceboLanguage::createInstance('forum', 'cms');

	$write_perm=checkRoleForItem("forum", $id_forum, "write");
	$moderate 	= checkForumModeratePerm($id_forum);
	$mod_perm 	= $moderate;
	$ord 		= importVar('ord');
	$base_url = '&amp;op=thread&amp;idForum='.$id_forum;
	$jump_url	= getForumBaseUrl($base_url);
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	$all_read	= importVar('allread', true, 0);
	
	if ($all_read)
		unset($_SESSION['cms_unreaded_forum']);

	list($title, $tot_thread, $locked_f) = mysql_fetch_row(mysql_query("
	SELECT title, num_thread, locked
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '$id_forum'"));

	$nav_bar 	= new NavBar('ini', $GLOBALS['cms']['visuItem'], $tot_thread, 'link');
	$ini 		= $nav_bar->getSelectedElement();
	$ini_page 	= $nav_bar->getSelectedPage();
	$nav_bar->setLink(getForumBaseUrl($base_url.'&amp;ord='.$ord));

	$query_thread = "
	SELECT t.idThread, t.author AS thread_author, t.posted, t.title, t.num_post, t.num_view, t.locked, t.erased, t.rilevantForum
	FROM ".$GLOBALS['prefix_cms']."_forumthread AS t LEFT JOIN
			".$GLOBALS['prefix_cms']."_forummessage AS m ON ( t.last_post = m.idMessage )
	WHERE t.idForum = '$id_forum' ";
	$query_thread .= " ORDER BY t.rilevantForum DESC " ;
	switch($ord) {
		case "obji"		: $query_thread .= " , t.title DESC " ;	break;
		case "obj" 		: $query_thread .= " , t.title " ;			break;
		case "authi"	: $query_thread .= " , t.author DESC " ;	break;
		case "auth" 	: $query_thread .= " , t.author " ;		break;
		case "posti" 	: $query_thread .= " , m.posted " ;		break;
		case "post"		:
		default 		: {
			$ord = 'post';
			$query_thread .= " , m.posted DESC " ;	break;
		}
	}
	$query_thread .= " LIMIT $ini, ".$GLOBALS['cms']['visuItem'];
	$re_thread = mysql_query($query_thread);

	$re_last_post = mysql_query("
	SELECT m.idThread, t.author AS thread_author, m.posted, m.title, m.author  AS mess_author, m.generator
	FROM ".$GLOBALS['prefix_cms']."_forumthread AS t LEFT JOIN
		".$GLOBALS['prefix_cms']."_forummessage AS m ON ( t.last_post = m.idMessage )
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
		getForumBaseUrl('&amp;op=forum', "forum") => $lang->def('_FORUM'),
		$title
	);
	$GLOBALS['page']->add(
		 getCmsTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.Form::openForm('search_forum', getForumBaseUrl('&amp;op=search&amp;idForum='.$id_forum))
		.'<div class="search_mask form_line_l">'
		.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
		.Form::getInputTextfield(	'textfield_nowh',
									'search_arg',
									'search_arg',
									$lang->def('_SEARCH'),
									$lang->def('_SEARCH'), 255, '' )
		.' <input class="button_nowh" type="submit" id="search_button" name="search_button" value="'.$lang->def('_SEARCH').'" />'
		.'</div>'
		.Form::closeForm()
	, 'content');

	$tb = new TypeOne($GLOBALS['cms']['visuItem'], $lang->def('_THREAD_CAPTION'), $lang->def('_THRAD_SUMMARY'));
	$tb->setTableStyle("forum_table");

	$img_up 	= '<img src="'.getPathImage().'standard/ord_asc.gif" alt="'.$lang->def('_ORD_ASC').'" />';
	$img_down 	= '<img src="'.getPathImage().'standard/ord_desc.gif" alt="'.$lang->def('_ORD_DESC').'" />';

	$cont_h = array(
		'<img src="'.getPathImage().'forum/thread.gif" title="'.$lang->def('_FREET').'" alt="'.$lang->def('_FREE').'" />',
		'<a href="'.getForumBaseUrl($base_url.'&amp;ord='.( $ord == 'obj' ? 'obji' : 'obj' )).'" title="'.$lang->def('_ORD_THREAD').'">'
			.( $ord == 'obj' ? $img_up : ( $ord == 'obji' ? $img_down : '' ) ).$lang->def('_THREAD').'</a>',
		$lang->def('_NUMREPLY'),
		'<a href="'.getForumBaseUrl($base_url.'&amp;ord='.( $ord == 'auth' ? 'authi' : 'auth' )).'" title="'.$lang->def('_ORD_AUTHOR').'">'
			.( $ord == 'auth' ? $img_up : ( $ord == 'authi' ? $img_down : '' ) ).$lang->def('_AUTHOR').'</a>',
		$lang->def('_NUMVIEW'),
		//$lang->def('_POSTED'),
		'<a href="'.getForumBaseUrl($base_url.'&amp;ord='.( $ord == 'post' ? 'posti' : 'post' )).'" title="'.$lang->def('_ORD_POST').'">'
			.( $ord == 'post' ? $img_up : ( $ord == 'posti' ? $img_down : '' ) ).$lang->def('_LASTPOST').'</a>'
	);
	$type_h = array('image', '', 'align_center', 'align_center', 'image',
	//'align_center',
	'align_center');
	if($mod_perm) {

		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_MODTHREAD_TITLE').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/move.gif" alt="'.$lang->def('_ALT_MOVE').'" title="'.$lang->def('_MOVE_THREAD').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_DELTHREAD_TITLE').'" />';
		$type_h[] = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($idT, $t_author, $posted, $title, $num_post, $num_view, $locked, $erased, $important) = mysql_fetch_row($re_thread)) {
		
		if (isset($_SESSION['cms_unreaded_forum'][$id_forum][$idT]) && $_SESSION['cms_unreaded_forum'][$id_forum][$idT] != 'new_thread')
		{
			$unread_message = $_SESSION['cms_unreaded_forum'][$id_forum][$idT];
			$first_unread_message = $num_post - $unread_message + 2;
			if ($first_unread_message % $GLOBALS['cms']['visuItem'])
				$ini_unread = ($first_unread_message - ($first_unread_message % $GLOBALS['cms']['visuItem'])) / $GLOBALS['cms']['visuItem'] + 1;
			else
				$ini_unread = $first_unread_message / $GLOBALS['cms']['visuItem'];
			$first_unread_message_in_page = $first_unread_message % $GLOBALS['cms']['visuItem'];
		}
		else
		{
			$first_unread_message_in_page = 1;
			$ini_unread = 1;
		}
		
		if ((($num_post + 1) % $GLOBALS['cms']['visuItem']))
			$number_of_pages = (($num_post + 1) - (($num_post + 1) % $GLOBALS['cms']['visuItem'])) / $GLOBALS['cms']['visuItem'] + 1;
		else
			$number_of_pages = ($num_post + 1) / $GLOBALS['cms']['visuItem'];
		
		// Used for mod_rewrite
		$GLOBALS["forum_url_title"]=$title;


		$c_css = '';
		// thread author
		$t_author =getForumMsgAuthorTxt($authors_names[$t_author][ACL_INFO_LASTNAME],
					$authors_names[$t_author][ACL_INFO_FIRSTNAME], $authors_names[$t_author][ACL_INFO_USERID]);
		// last post author
		if(isset($last_post[$idT])) {

			$author = $last_post[$idT]['author'];
			$last_mess_write = $last_post[$idT]['info'].' ( '.$lang->def('_BY').': <span class="mess_author">'
				.getForumMsgAuthorTxt($authors_names[$author][ACL_INFO_LASTNAME],
					$authors_names[$author][ACL_INFO_FIRSTNAME], $authors_names[$author][ACL_INFO_USERID]).'</span> )';
		} else {
			$last_mess_write = $lang->def('_NONE');
		}
		// status of the thread
		if($erased) {
			$status = '<img src="'.getPathImage().'forum/thread_erased.gif" alt="'.$lang->def('_FREE').'" />';
		} elseif($locked) {
			$status = '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKED').'" />';
		} elseif(isset($_SESSION['cms_unreaded_forum'][$id_forum][$idT])) {

			$status = '<img src="'.getPathImage().'forum/thread_unreaded.gif" alt="'.$lang->def('_UNREADED').'" />';
			$c_css = ' class="text_bold"';
		} else {
			$status = '<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" />';
		}
		$content = array($status);
		$content_temp = ( $erased && !$mod_perm ?
					'<div class="forumErased">'.$lang->def('_ERASED').'</div>' :
					($important ? '<img src="'.getPathImage().'forum/important.gif" alt="'.$lang->def('_IMPORTANT').'" />' : '').' <a'.$c_css.' href="'.getForumBaseUrl('&amp;op=message&amp;idThread='.$idT).'">'.$title.'</a>');
		if ($first_unread_message_in_page != 1)
			$content_temp .= '<p>( <a href="'.getForumBaseUrl('&amp;op=message&amp;idThread='.$idT.'&amp;firstunread='.$first_unread_message_in_page.'&amp;ini='.$ini_unread, false, '#firstunread').'">'.$lang->def('_FIRST_UNREAD').'</a> ) ';
		else
			$content_temp .= '<p>';
		if ($number_of_pages > 1)
		{
			if ($number_of_pages > 4)
			{
				$content_temp .= $lang->def('_NUM_PAGES').' : <a href="index.php?'.getForumBaseUrl('&amp;op=message&amp;idThread='.$idT.'&amp;ini=1').'">1</a> ... ';
				$content_temp .= ' <a href="'.getForumBaseUrl('&amp;op=message&amp;idThread='.$idT.'&amp;ini='.($number_of_pages - 2)).'">'.($number_of_pages - 2).'</a>, ';
				$content_temp .= ' <a href="'.getForumBaseUrl('&amp;op=message&amp;idThread='.$idT.'&amp;ini='.($number_of_pages - 1)).'">'.($number_of_pages - 1).'</a>, ';
				$content_temp .= ' <a href="'.getForumBaseUrl('&amp;op=message&amp;idThread='.$idT.'&amp;ini='.$number_of_pages).'">'.$number_of_pages.'</a>';
			}
			else
			{
				$content_temp .= $lang->def('_NUM_PAGES').' : ';
				for ($i = 1; $i <= $number_of_pages; $i++) {
					$content_temp .= ($i != 1 ? ',' : '' )
								.' <a href="'.getForumBaseUrl('&amp;op=message&amp;idThread='.$idT.'&amp;ini='.$i).'">'.$i.'</a>';
				}
				$content_temp .= '';
			}
		}
		$content_temp .= '</p>';
		$content[] = $content_temp;
		$content[] = $num_post
			.( isset($_SESSION['cms_unreaded_forum'][$id_forum][$idT]) && $_SESSION['cms_unreaded_forum'][$id_forum][$idT] != 'new_thread'
				? '<br />(<span class="forum_notread">'.$_SESSION['cms_unreaded_forum'][$id_forum][$idT].' '.$lang->def('_NEW').')</span>'
				: ( isset($_SESSION['cms_unreaded_forum'][$id_forum][$idT]) && $_SESSION['cms_unreaded_forum'][$id_forum][$idT] == 'new_thread'
					? '<br />(<span class="forum_notread">'.$lang->def('_NEW_THREAD').')</span>'
					: '') );
		$content[] = $t_author;
		$content[] = $num_view;
		//$content[] = $GLOBALS['regset']->databaseToRegional($posted);
		$content[] = $last_mess_write;
		if($mod_perm) {

			$content[] = '<a href="'.getForumBaseUrl('&amp;op=modthread&amp;idThread='.$idT).'" '
				.'title="'.$lang->def('_MODTHREAD_TITLE').' : '.strip_tags($title).'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($title).'" /></a>';
			$content[] = '<a href="'.getForumBaseUrl('&amp;op=movethread&amp;id_forum='.$id_forum.'&amp;id_thread='.$idT).'" '
				.'title="'.$lang->def('_MOVE_THREAD').' : '.strip_tags($title).'">'
				.'<img src="'.getPathImage().'standard/move.gif" alt="'.$lang->def('_MOVE_DEL').' : '.strip_tags($title).'" /></a>';
			$content[] = '<a href="'.getForumBaseUrl('&amp;op=delthread&amp;idThread='.$idT).'" '
				.'title="'.$lang->def('_DELTHREAD_TITLE').' : '.strip_tags($title).'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.strip_tags($title).'" /></a>';
		}
		$tb->addBody($content);
	}
	if((!$locked_f && $write_perm) || $mod_perm) {
		$tb->addActionAdd('<a href="'.getForumBaseUrl('&amp;op=addthread&amp;idForum='.$id_forum, "newpost").'">'
			.'<img src="'.getPathImage().'standard/add.gif" title="'.$lang->def('_ADDTHREADT').'" alt="'.$lang->def('_ADD').'" /> '
			.$lang->def('_ADDTHREAD').'</a>');
	}

	// NOTE: If notify request register it
	require_once($GLOBALS['where_framework'].'/lib/lib.usernotifier.php');

	$can_notify = (usernotifier_getUserEventStatus(getLogUserId(), 'CmsForumNewThread') && !$GLOBALS["current_user"]->isAnonymous() ? true : false);

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

		$text_inner .= '<li><a href="'.getForumBaseUrl('&amp;op=thread&amp;notify=1&amp;idForum='.$id_forum.'&amp;ini='.$ini_page).'" '
		.( !$is_notify ?
			'title="'.$lang->def('_NOTIFY_ME_FORUM_TITLE').'">'
			.'<img src="'.getPathImage().'forum/notify.gif" alt="'.$lang->def('_NOTIFY').'" /> '.$lang->def('_NOTIFY_ME_FORUM').'</a> '
			:
			'title="'.$lang->def('_UNNOTIFY_ME_FORUM_TITLE').'">'
			.'<img src="'.getPathImage().'forum/unnotify.gif" alt="'.$lang->def('_UNNOTIFY').'" /> '.$lang->def('_UNNOTIFY_ME_FORUM').'</a> '
		).'</li>';
	}
	if($moderate) {
		$text_inner .= '<li><a href="'.getForumBaseUrl('&amp;op=modstatus&amp;idForum='.$id_forum).'">'
			.( $locked_f
				?'<img src="'.getPathImage().'forum/forum.gif" alt="'.$lang->def('_UNLOCKFORUMALT').'" /> '.$lang->def('_UNLOCKFORUM')
				: '<img src="'.getPathImage().'forum/forum_locked.gif" alt="'.$lang->def('_LOCKFORUMALT').'" /> '.$lang->def('_LOCKFORUM') )
			.'</a></li>';
	}
	$GLOBALS['page']->add($nav_bar->getNavBar($ini), 'content');
	if($text_inner != '') $GLOBALS['page']->add('<div class="forum_action_top"><ul class="adjac_link">'.$text_inner.'</ul></div>', 'content');
	if (isset($_SESSION['cms_unreaded_forum']) && count($_SESSION['cms_unreaded_forum']))
		$GLOBALS['page']->add('<div><p align="right"><a href="'.getForumBaseUrl('index.php?modname=forum&op=thread&idForum='.$id_forum.'&amp;allread=1').'">'.$lang->def('_ALL_THREAD_READ').'</a></p>', 'content');
	$GLOBALS['page']->add($tb->getTable(), 'content');
	if($text_inner != '') $GLOBALS['page']->add('<div class="forum_action_bottom"><ul class="adjac_link">'.$text_inner.'</ul></div>', 'content');
	$GLOBALS['page']->add(
		$nav_bar->getNavBar($ini)
		.'</div>', 'content');
}

//---------------------------------------------------------------------------//

function addthread() {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'cms');
	$id_forum = importVar('idForum', true, 0);

	if (!checkRoleForItem("forum", $id_forum, "write")) return 0;

	list($title) = mysql_fetch_row(mysql_query("
	SELECT title
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$id_forum."'"));
	

	$page_title = array(
		getForumBaseUrl('&amp;op=forum') => $lang->def('_FORUM'),
		getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum) => $title,
		$lang->def('_NEW_THREAD')
	);
	$GLOBALS['page']->add(
		getCmsTitleArea($page_title, 'forum', $lang->def('_FORUM'))
		.'<div class="std_block">', "content");
		
		
	$saved_textof=getForumSavedTextof();
	if ($saved_textof !== FALSE) {
		
		$msg=$lang->def("_FORUM_TEXT_RESTORED_FROM_SESSION");
		$GLOBALS["page"]->add(getInfoUi($msg), "content");
		$text_of=$saved_textof;
		
	}
	else {
		$text_of="";
	}		
		
		
	$GLOBALS['page']->add(
		getBackUi(getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum), $lang->def('_BACK'))
		.Form::openForm('form_forum', getForumBaseUrl('&amp;op=insthread'), false, false, 'multipart/form-data')
		.Form::openElementSpace()

		.Form::getHidden('idForum', 'idForum', $id_forum)
		.Form::getTextfield($lang->def('_SUBJECT'), 'title', 'title', 255)
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', $text_of)
	, 'content');

	if(checkRoleForItem("forum", $id_forum, "upload")) {

		$GLOBALS['page']->add(Form::getFilefield($lang->def('_UPLOAD'), 'attach', 'attach'), 'content');
	}
	$is_important = array('No', 'Si');
	if (checkRoleForItem("forum", $id_forum, "moderate") || checkRoleForItem("forum", $id_forum, "mod"))
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

	$path = '/doceboCms/'.$GLOBALS['cms']['pathforum'];

	if($file['name'] != '') {

		$savefile = rand(0,100).'_'.time().'_'.$file['name'];
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

	$path = '/doceboCms/'.$GLOBALS['cms']['pathforum'];
	if($name != '') return sl_unlink($path.$name);
}


function insthread() {
	$lang =& DoceboLanguage::createInstance('forum', 'cms');
	$id_forum = importVar('idForum', true , 0);
	if(isset($_POST['undo'])) jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum));

	$upload_perm=checkRoleForItem("forum", $id_forum, "upload");

	// Expired session check:
	checkExpiredSession($id_forum);
	unsetForumSavedTextof();
	// ------------------------------------------

	if (!checkRoleForItem("forum", $id_forum, "write")) return 0;

	list($forum_title) = mysql_fetch_row(mysql_query("
	SELECT title
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$id_forum."'"));

	$locked = false;
	if(checkForumModeratePerm($id_forum)) {

		$query_view_forum = "
		SELECT idMember, locked
		FROM ".$GLOBALS['prefix_cms']."_forum AS f LEFT JOIN
				".$GLOBALS['prefix_cms']."_forum_access AS fa
					ON ( f.idForum = fa.idForum )
		WHERE AND f.idForum = '".$id_forum."'";
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
	if(!$continue) jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=err_cannotsee'));
	if($locked) jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=err_lock'));
	if($_POST['title'] == '') $_POST['title'] = substr(strip_tags($_POST['textof']), 0, 50).'...';
	$important = importVar('important', true, '0');
	$now = date("Y-m-d H:i:s");
	$ins_query = "
	INSERT INTO ".$GLOBALS['prefix_cms']."_forumthread
	( idForum, title, author, num_post, last_post, posted, rilevantForum )
	VALUES (
		'".$id_forum."',
		'".$_POST['title']."',
		'".getLogUserId()."',
		 0,
		 0,
		 '".$now ."',
		 '".$important."' )";
	if(!mysql_query($ins_query)) jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=err_ins'));
	list($id_thread) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

	$name_file = '';
	if(($_FILES['attach']['name'] != '') && $upload_perm) {

		$name_file = save_file($_FILES['attach']);
	}

	$ins_mess_query = "
	INSERT INTO ".$GLOBALS['prefix_cms']."_forummessage
	( idThread, title, textof, author, posted, answer_tree, attach, generator )
	VALUES (
		'".$id_thread."',
		'".$_POST['title']."',
		'".$_POST['textof']."',
		'".getLogUserId()."',
		'".$now ."',
		'/".$now ."',
		'".addslashes($name_file)."',
		'1' ) ";
	if(!mysql_query( $ins_mess_query )) {

		mysql_query("
		DELETE FROM ".$GLOBALS['prefix_cms']."_forumthread
		WHERE idThread = '$id_thread'");
		delete_file($name_file);

		jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=err_ins2'));
	}
	list($id_message) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

	mysql_query("
	UPDATE ".$GLOBALS['prefix_cms']."_forumthread
	SET last_post = '$id_message'
	WHERE idThread = '$id_thread'");

	mysql_query("
	UPDATE ".$GLOBALS['prefix_cms']."_forum
	SET num_thread = num_thread + 1,
		num_post = num_post + 1,
		last_post = '$id_message'
	WHERE idForum = '$id_forum'");


	// launch notify
	require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');

	$msg_composer = new EventMessageComposer('forum', 'cms');

	$msg_composer->setSubjectLangText('email', '_SUBJECT_NOTIFY_THREAD', false);
	$msg_composer->setBodyLangText('email', '_NEW_THREAD_INSERT_IN_FORUM', array(	'[url]' => $GLOBALS['cms']['url'],
																		'[forum_title]' => $forum_title,
																		'[thread_title]' => $_POST['title'] ) );

	$msg_composer->setSubjectLangText('sms', '_SUBJECT_NOTIFY_THREAD_SMS', false);
	$msg_composer->setBodyLangText('sms', '_NEW_THREAD_INSERT_IN_FORUM_SMS', array(	'[url]' => $GLOBALS['cms']['url'],
																		'[forum_title]' => $forum_title,
																		'[thread_title]' => $_POST['title'] ) );


	launchNotify('forum', $id_forum, $lang->def('_NEW_THREAD'), $msg_composer);


	jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread));
}

//---------------------------------------------------------------------------//

function modthread() {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'cms');
	$id_thread 	= importVar('idThread', true, 0);
	$ini 	= importVar('ini');

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	// retrive info about message
	$mess_query = "
	SELECT idMessage, title, textof, author
	FROM ".$GLOBALS['prefix_cms']."_forummessage
	WHERE idThread = '".$id_thread."' AND generator = '1'";
	list($id_message, $title, $textof, $author) = mysql_fetch_row(mysql_query($mess_query));

	// Some info about forum and thread
	$thread_query = "
	SELECT idForum, rilevantForum
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $is_importnat) = mysql_fetch_row(mysql_query($thread_query));

	$moderate 	= checkForumModeratePerm($id_forum);
	$mod_perm	= $moderate;
	if( !userIsAuthor($author) && !$moderate && !$mod_perm)
		die("You can't access");

	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));

	$page_title = array(
		getForumBaseUrl('&amp;op=forum') => $lang->def('_FORUM'),
		getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum) => $forum_title,
		$lang->def('_MOD_THREAD')
	);


	$GLOBALS['page']->add(
		getCmsTitleArea($page_title, 'forum')
		.'<div class="std_block">', "content");
		
		
	$saved_textof=getForumSavedTextof($id_thread);
	if ($saved_textof !== FALSE) {
		
		$msg=$lang->def("_FORUM_TEXT_RESTORED_FROM_SESSION");
		$GLOBALS["page"]->add(getInfoUi($msg), "content");
		$textof=$saved_textof;
		
	}		
		
		
	$GLOBALS['page']->add(( isset($_GET['search'])
			? getBackUi(getForumBaseUrl('&op=search&amp;ini='.$ini), $lang->def('_BACK'))
			: getBackUi(getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum), $lang->def('_BACK'))
		)
		.Form::openForm('form_forum', getForumBaseUrl('&amp;op=upthread'), false, false, 'multipart/form-data')
		.Form::openElementSpace()

		.Form::getHidden('search', 'search', (isset($_GET['search']) ? '1' : '0' ) )
		.Form::getHidden('ini', 'ini', importVar('ini') )
		.Form::getHidden('idThread', 'idThread', $id_thread)
		.Form::getTextfield($lang->def('_SUBJECT'), 'title', 'title', 255, $title)
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', $textof)
	, 'content');
	if(checkRoleForItem("forum", $id_forum, "upload")) {
		$GLOBALS['page']->add(Form::getFilefield($lang->def('_UPLOAD'), 'attach', 'attach'), 'content');
	}
	$important = array('No', 'Si');
	if (checkRoleForItem("forum", $id_forum, "moderate") || checkRoleForItem("forum", $id_forum, "mod"))
		$GLOBALS['page']->add(Form::getDropdown($lang->def('_IMPORTANT_THREAD'), 'important', 'important', $important, $is_importnat), 'content');
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

	$lang =& DoceboLanguage::createInstance('forum', 'cms');

	// retrive info about message
	$mess_query = "
	SELECT idMessage, author, attach
	FROM ".$GLOBALS['prefix_cms']."_forummessage
	WHERE  idThread = '".$id_thread."' AND generator = '1'";
	list($id_message, $author, $attach) = mysql_fetch_row(mysql_query($mess_query));
	if(isset($_POST['undo'])) {

		if($_POST['search'] == 1) jumpTo(getForumBaseUrl('&op=search&amp;ini='.$ini));
		else jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini));
	}

	list($id_forum, $locked_t, $erased_t) = mysql_fetch_row(mysql_query("
	SELECT idForum, locked, erased
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'"));
	

	// Expired session check:
	checkExpiredSession($id_forum, $id_thread);
	unsetForumSavedTextof($id_thread);
	// ------------------------------------------
		

	$upload_perm=checkRoleForItem("forum", $id_forum, "upload");
	$moderate 	= checkForumModeratePerm($id_forum);
	$mod_perm	= $moderate;

	if(!$moderate && !$mod_perm && (userIsAuthor($author)) ) die("You can't access");
	
	$user_level = $GLOBALS['current_user']->getUserLevelId();
	
	if($user_level !== ADMIN_GROUP_GODADMIN)
		if($locked_t ||$erased_t && (!$mod_perm && !$moderate))
		{
			if($_POST['search'] == 1) jumpTo(getForumBaseUrl('&op=search&amp;ini='.$ini));
			else jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_lock'));
		}
	
	if($_POST['title'] == '') $_POST['title'] = substr(strip_tags($_POST['textof']), 0, 50).'...';

	$now = date("Y-m-d H:i:s");

	//save attachment
	$name_file = $attach;
	if($_FILES['attach']['name'] != '' && $upload_perm ) {

		delete_file($attach);
		$name_file = save_file($_FILES['attach']);
	}
	$upd_mess_query = "
	UPDATE ".$GLOBALS['prefix_cms']."_forummessage
	SET title = '".$_POST['title']."',
		textof = '".$_POST['textof']."',
		attach = '".addslashes($name_file)."',
		modified_by = '".getLogUserId()."',
		modified_by_on = '".$now."'
	WHERE idMessage = '".$id_message."'";
	if(!mysql_query($upd_mess_query)) {

		delete_file($name_file);
		if($_POST['search'] == 1) jumpTo(getForumBaseUrl('&op=search&amp;ini='.$ini));
		else jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=err_ins'));
	}
	$is_rilevant = importVar('important', true, 0);
	mysql_query("
	UPDATE ".$GLOBALS['prefix_cms']."_forumthread
	SET title = '".$_POST['title']."'," .
		" rilevantForum = '".$is_rilevant."'
	WHERE idThread = '".$id_thread."'");
	if($_POST['search'] == 1) jumpTo(getForumBaseUrl('&op=search&amp;ini='.$ini));
	else jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=ok'));
}

//---------------------------------------------------------------------------//

function delthread() {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'cms');
	$id_thread = importVar('idThread', true, 0);
	$ini = importVar('ini');

	$thread_query = "
	SELECT idForum, title, last_post
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $last_post) = mysql_fetch_row(mysql_query($thread_query));

	if(isset($_POST['undo'])) {
		if($_POST['search'] == 1) jumpTo(getForumBaseUrl('&op=search&amp;ini='.$ini));
		else jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum));
	}
	if(isset($_POST['confirm'])) {

		$forum_query = "
		SELECT last_post
		FROM ".$GLOBALS['prefix_cms']."_forum
		WHERE idForum = '".$id_forum."'";
		list($last_post_forum) = mysql_fetch_row(mysql_query($forum_query));

		$mess_query = "
		SELECT attach
		FROM ".$GLOBALS['prefix_cms']."_forummessage
		WHERE idThread = '".$id_thread."'";
		$re_mess = mysql_query($mess_query);
		while(list($file) = mysql_fetch_row($re_mess)) {

			if($file != '') delete_file($file);
		}
		$post_deleted = mysql_num_rows($re_mess);
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_cms']."_forummessage
		WHERE idThread = '".$id_thread."'"))
			if($_POST['search'] == 1) jumpTo(getForumBaseUrl('&op=search&amp;ini='.$ini));
			else jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=err_del'));


		if($last_post_forum == $last_post) {
			
			$query_text = "
			SELECT idThread, posted 
			FROM ".$GLOBALS['prefix_cms']."_forumthread
			WHERE idForum = '".$id_forum."'
			ORDER BY posted DESC";
			$re = mysql_query($query_text);
			list($id_new, $post) = mysql_fetch_row($re);
		}

		if(!mysql_query("
		UPDATE ".$GLOBALS['prefix_cms']."_forum
		SET num_thread = num_thread - 1,
			num_post = num_post - ".$post_deleted
		.( $last_post_forum == $last_post ? " , last_post = '".$id_new."' " : " " )
		." WHERE idForum = '".$id_forum."'"))
			if($_POST['search'] == 1) jumpTo(getForumBaseUrl('&op=search&amp;ini='.$ini));
			else jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=err_del'));

		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_cms']."_forumthread
		WHERE idThread = '".$id_thread."'"))
			if($_POST['search'] == 1) jumpTo(getForumBaseUrl('&op=search&amp;ini='.$ini));
			else jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=err_del'));

		unsetNotify('thread', $id_thread);
		if($_POST['search'] == 1) jumpTo(getForumBaseUrl('&op=search'));
		else jumpTo(getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;result=ok'));
	} else {

		$forum_query = "
		SELECT title
		FROM ".$GLOBALS['prefix_cms']."_forum
		WHERE idForum = '".$id_forum."'";
		list($forum_title) = mysql_fetch_row(mysql_query($forum_query));

		$page_title = array(
			getForumBaseUrl('&amp;op=forum') => $lang->def('_FORUM'),
			getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum) => $forum_title,
			$lang->def('_DEL_THREAD')
		);
		$GLOBALS['page']->add(
			getCmsTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.Form::openForm('del_thread', getForumBaseUrl('&amp;op=delthread'))
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
	if( ($distance >= 0 ) && ($distance < 60) ) return $distance.' '.def('_MINUTES', "sysforum");

	//minutes -> hour
	$distance = (int)($distance / 60);
	if( ($distance >= 0 ) && ($distance < 60) ) return $distance.' '.def('_HOURS', "sysforum");

	//hour -> day
	$distance = (int)($distance / 24);
	if( ($distance >= 0 ) && ($distance < 30 ) ) return $distance.' '.def('_DAYS', "sysforum");

	//echo > 1 month
	return def('_ONEMONTH');
}

function message() {
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.mimetype.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.user_profile.php');
	$lang =& DoceboLanguage::createInstance('forum', 'cms');
	$id_thread = importVar('idThread', true, 0);

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	$profile_man = new UserProfile(0);
	$profile_man->init('forum', 'cms', getForumBaseUrl('&amp;op=message&amp;idThread='.$id_thread));

	$tb 	= new TypeOne($GLOBALS['cms']['visuItem'], $lang->def('_CAPTION_FORUM_MESSAGE'), $lang->def('_SUMMARY_FORUM_MESSAGE'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink(getForumBaseUrl('&amp;op=message&amp;idThread='.$id_thread));
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
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $tot_message, $locked_t, $erased_t) = mysql_fetch_row(mysql_query($thread_query));

	$write_perm=checkRoleForItem("forum", $id_forum, "write");
	$moderate=checkForumModeratePerm($id_forum);
	$mod_perm=false;

	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));
	++$tot_message;

	//set as readed if needed
	if(isset($_SESSION['cms_unreaded_forum'][$id_forum][$id_thread])) unset($_SESSION['cms_unreaded_forum'][$id_forum][$id_thread]);

	if( ($ini == 0) && (!isset($_GET['result'])) ) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_cms']."_forumthread
		SET num_view = num_view + 1
		WHERE idThread = '".$id_thread."'");
	}
	$page_title = array(
		getForumBaseUrl('&amp;op=forum', "forum") => $lang->def('_FORUM'),
		getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum, $forum_title) => $forum_title,
		$thread_title
	);
	if($erased_t && !$mod_perm && !$moderate) {

		$GLOBALS['page']->add(
			getCmsTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_CANNOTENTER')
			.'</div>', 'content');
		return;
	}

	// Find post
	$messages 		= array();
	$authors 		= array();
	$authors_names	= array();
	$authors_info	= array();
	$re_message = mysql_query("
	SELECT idMessage, posted, title, textof, attach, locked, author, modified_by, modified_by_on
	FROM ".$GLOBALS['prefix_cms']."_forummessage
	WHERE idThread = '".$id_thread."'
	ORDER BY posted
	LIMIT $ini, ".$GLOBALS['cms']['visuItem']);
	while($record = mysql_fetch_assoc($re_message)) {
	
		$messages[$record['idMessage']] 	= $record;
		$authors[$record['author']] 		= $record['author'];
		if($record['modified_by'] != 0) {
			$authors[$record['modified_by']] 	= $record['modified_by'];
		}
	}
	$authors_names =& $acl_man->getUsers($authors);
	//$level_name = getLevels();

	// Retriving level and number of post of the authors
	if(!empty($authors)) {

		$re_num_post = mysql_query("
		SELECT m.author, COUNT(*)
		FROM ".$GLOBALS['prefix_cms']."_forummessage AS m
		WHERE m.author IN ( ".implode($authors, ',')." )
		GROUP BY m.author");
		while( list($id_u, $num_post_a) = mysql_fetch_row($re_num_post) ) {

			$authors_info[$id_u] = array( 'num_post' => $num_post_a, 'level' => "" );
		}
		$profile_man->setCahceForUsers($authors);
	}
	$type_h = array('forum_sender', 'forum_text');
	$cont_h = array($lang->def('_AUTHOR'), $lang->def('_TEXTOF'));
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	$tb->setTableStyle("forum_table");


	// Compose messagges display
	$path = $GLOBALS['cms']['url'].$GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['framework']['pathphoto'];
	$counter = 0;
	while(list($id_message, $message_info) = each($messages)) {
		$counter++;
		// sender info
		$m_author = $message_info['author'];

		$profile_man->setIdUser($m_author);
		if ($m_author != $GLOBALS["ANONYMOUS_IDST"]) {
			
			$author = $profile_man->getUserPanelData(false, 'normal');
			
			$sender = ''
				.'<div class="forum_author">'
				.$author['actions']
				.$author['display_name']
				.'</div>'
				.'<br/>'
				.$author['avatar']
				.'<div class="forum_numpost">'.$lang->def('_NUMPOST').' : '
				.( isset($authors_info[$m_author]['num_post'])
					? $authors_info[$m_author]['num_post']
					: 0 )
				.'</div>'
				
				.'<img src="'.getPathImage().'standard/user.gif" alt="&gt;" />&nbsp;'
				.'<a href="'.getForumBaseUrl('&amp;op=viewprofile&amp;idMessage='.$id_message.'&amp;ini='.$ini_page, $author['display_name']."_profile").'">'.$lang->def('_VIEWPROFILE').'</a>';
			
		} else {
			$author_txt=getForumMsgAuthorTxt(	$authors_names[$m_author][ACL_INFO_LASTNAME],
												$authors_names[$m_author][ACL_INFO_FIRSTNAME], $authors_names[$m_author][ACL_INFO_USERID]);

			$sender = '<div class="forum_author">'
					.$author_txt
					.'</div>';
		}
		// msg info
		$msgtext = '';
		if ($counter == $first_unread_message)
			$msgtext .= '<a name="firstunread"></a>';
		$msgtext .= '<div class="forum_post_posted">'
			.$lang->def('_POSTED').' : '.$GLOBALS['regset']->databaseToRegional($message_info['posted'])
			.' ( '.loadDistance($message_info['posted']).' )'
			.'</div>';
		if($message_info['locked']) {
			$msgtext .= '<div class="forum_post_locked">'.$lang->def('_LOCKEDMESS').'</div>';
		} else {
			if($message_info['attach'] != '') {

				$msgtext = '';
		if ($counter == $first_unread_message)
			$msgtext .= '<a name="firstunread"></a>';
		$msgtext .= '<div class="forum_post_posted">';
		
		$msgtext .= '<a href="'.getForumBaseUrl('&amp;op=download&amp;id='.$id_message).'">'
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
						.getForumMsgAuthorTxt($authors_names[$modify_by][ACL_INFO_LASTNAME],
							$authors_names[$modify_by][ACL_INFO_FIRSTNAME], $authors_names[$modify_by][ACL_INFO_USERID])
						.' '.$lang->def('_MODIFY_BY_ON').' : '
						.$GLOBALS['regset']->databaseToRegional($message_info['modified_by_on'])
						.'</div>';
			}

			if($authors_names[$m_author][ACL_INFO_SIGNATURE] != '') {
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

		if($moderate || $mod_perm) {
			if($message_info['locked']) {

				$action .= '<a href="'.getForumBaseUrl('&amp;op=moderatemessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
						.'title="'.$lang->def('_DEMODERATE_MESSAGE').' : '.strip_tags($message_info['title']).'">'
					.'<img src="'.getPathImage().'forum/demoderate.gif" alt="'.$lang->def('_ALT_DEMODERATE').' : '.strip_tags($message_info['title']).'" /> '
					.$lang->def('_DEMODERATE').'</a> ';
			} else {

				$action .= '<a href="'.getForumBaseUrl('&amp;op=moderatemessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
						.'title="'.$lang->def('_MODERATE_MESSAGE').' : '.strip_tags($message_info['title']).'">'
					.'<img src="'.getPathImage().'forum/moderate.gif" alt="'.$lang->def('_ALT_MODERATE').' : '.strip_tags($message_info['title']).'" /> '
					.$lang->def('_MODERATE').'</a> ';
			}
		}
		if((!$locked_t && !$locked_f && !$message_info['locked'] && $write_perm) || $mod_perm || $moderate) {
			$action .= '<a href="'.getForumBaseUrl('&amp;op=addmessage&amp;idThread='.$id_thread.'&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
					.'title="'.$lang->def('_REPLY_TITLE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'forum/reply.gif" alt="'.$lang->def('_ALT_REPLY').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_QUOTE').'</a>';
		}
		if($moderate || $mod_perm || (($m_author == getLogUserId()) && ($m_author != $GLOBALS["ANONYMOUS_IDST"])) ) {

			$action .= '<a href="'.getForumBaseUrl('&amp;op=modmessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
					.'title="'.$lang->def('_MOD_MESSAGE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_MOD').'</a>'
				.'<a href="'.getForumBaseUrl('&amp;op=delmessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
					.'title="'.$lang->def('_DEL_MESSAGE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_DEL').'</a> ';
		}
		$tb->addBodyExpanded($action, 'forum_action');
	}
	if( (!$locked_t && !$locked_f && $write_perm) || $mod_perm || $moderate ) {

		$tb->addActionAdd(
			'<a href="'.getForumBaseUrl('&amp;op=addmessage&amp;idThread='.$id_thread.'&amp;ini='.$ini_page).'" title="'.$lang->def('_ADDMESSAGET').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '
			.$lang->def('_REPLY_TO_THIS_THREAD').'</a>'
		);
	}
	$GLOBALS['page']->add(
		getCmsTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.Form::openForm('search_forum', getForumBaseUrl('&amp;op=search&amp;idThread='.$id_thread))
		.'<div class="search_mask form_line_l">'
		.'<label for="search_arg">'.$lang->def('_SEARCH_LABEL').'</label> '
		.Form::getInputTextfield(	'textfield_nowh',
									'search_arg',
									'search_arg',
									$lang->def('_SEARCH'),
									$lang->def('_SEARCH'), 255, '' )
		.' <input class="button_nowh" type="submit" id="search_button" name="search_button" value="'.$lang->def('_SEARCH').'" />'
		.'</div>'
		.Form::closeForm(), 'content');

	// NOTE: If notify request register it
	require_once($GLOBALS['where_framework'].'/lib/lib.usernotifier.php');

	$can_notify = (usernotifier_getUserEventStatus(getLogUserId(), 'CmsForumNewResponse') && !$GLOBALS["current_user"]->isAnonymous() ? true : false);

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

		$text_inner .= '<li><a href="'.getForumBaseUrl('&amp;op=message&amp;notify=1&amp;idThread='.$id_thread.'&amp;ini='.$ini_page).'" '
		.( !$is_notify ?
			'title="'.$lang->def('_NOTIFY_ME_THREAD_TITLE').'">'
			.'<img src="'.getPathImage().'forum/notify.gif" alt="'.$lang->def('_NOTIFY').'" /> '.$lang->def('_NOTIFY_ME_THREAD').'</a> '
			:
			'title="'.$lang->def('_UNNOTIFY_ME_THREAD_TITLE').'">'
			.'<img src="'.getPathImage().'forum/unnotify.gif" alt="'.$lang->def('_UNNOTIFY').'" /> '.$lang->def('_UNNOTIFY_ME_THREAD').'</a> '
		).'</li>';
	}
	if($moderate) {

		$text_inner .= '<li><a href="'.getForumBaseUrl('&amp;op=modstatusthread&amp;idThread='.$id_thread.'&amp;ini='.$ini_page).'">'
			.( $locked_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_FREETHREAD')
				: '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKTHREAD').'" /> '.$lang->def('_LOCKTHREAD') )
			.'</a></li>';
	}
	if($mod_perm) {

		$text_inner .= '<li><a href="'.getForumBaseUrl('&amp;op=changeerased&amp;idThread='.$id_thread.'&amp;ini='.$ini_page).'">'
			.( $erased_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_UNERASE')
				: '<img src="'.getPathImage().'forum/thread_erased.gif" alt="'.$lang->def('_DEL').'" /> '.$lang->def('_ERASE') )
			.'</a></li>';
	}
	if($text_inner != '') {
		$GLOBALS['page']->add('<div class="forum_action_top"><ul class="adjac_link">'.$text_inner.'</ul></div>', 'content');
	}
	if ($is_important)
		if($moderate)
			$GLOBALS['page']->add('<div><p align="right"><a href="'.getForumBaseUrl('index.php?modname=forum&op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'&amp;important=2').'">'.$lang->def('_SET_NOT_IMPORTANT_THREAD').'</a></p>', 'content');
	else
		if($moderate)
			$GLOBALS['page']->add('<div><p align="right"><a href="'.getForumBaseUrl('index.php?modname=forum&op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini_page.'&amp;important=1').'">'.$lang->def('_SET_IMPORTANT_THREAD').'</a></p>', 'content');
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
	FROM ".$GLOBALS['prefix_cms']."_forummessage
	WHERE idMessage = '".(int)$_GET['idMessage']."'"));

	$thread_query = "
	SELECT idForum
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum) = mysql_fetch_row(mysql_query($thread_query));

	$moderate=checkForumModeratePerm($id_forum);

	if(!$moderate) die("You can't access");

	if($lock == 1) $new_status = 0;
	else $new_status = 1;

	mysql_query("
	UPDATE ".$GLOBALS['prefix_cms']."_forummessage
	SET locked = '$new_status'
	WHERE idMessage = '".(int)$_GET['idMessage']."'");

	jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&ini='.$_GET['ini']));
}

function modstatusthread() {
	$id_thread 		= importVar('idThread', true, 0);

	list( $idF, $lock ) = mysql_fetch_row(mysql_query("
	SELECT idForum, locked
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread ."'"));

	if (!checkForumModeratePerm($idF)) return 0;

	if($lock == 1) $new_status = 0;
	else $new_status = 1;

	mysql_query("
	UPDATE ".$GLOBALS['prefix_cms']."_forumthread
	SET locked = '$new_status'
	WHERE idThread = '".$id_thread ."'");

	jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&ini='.$_GET['ini']));
}

function changeerase() {
	$id_thread 		= importVar('idThread', true, 0);

	list( $idF, $erased ) = mysql_fetch_row(mysql_query("
	SELECT idForum, erased
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'"));

	if (!checkForumModeratePerm($idF)) return 0;

	if($erased == 1) $new_status = 0;
	else $new_status = 1;

	mysql_query("
	UPDATE ".$GLOBALS['prefix_cms']."_forumthread
	SET erased = '$new_status'
	WHERE idThread = '".$id_thread."'");

	jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&ini='.$_GET['ini']));
}

//---------------------------------------------------------------------------//

function showMessageForAdd($id_thread, $how_much) {

	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$lang =& DoceboLanguage::createInstance('forum', 'cms');

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	$tb 	= new TypeOne($GLOBALS['cms']['visuItem'], $lang->def('_CAPTION_FORUM_MESSAGE_ADD'), $lang->def('_SUMMARY_FORUM_MESSAGE_ADD'));

	$tb->setTableStyle("forum_table");

	// Find post
	$messages 		= array();
	$authors 		= array();
	$authors_names	= array();
	$authors_info	= array();
	$re_message = mysql_query("
	SELECT idMessage, posted, title, textof, attach, locked, author, modified_by
	FROM ".$GLOBALS['prefix_cms']."_forummessage
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
	//$level_name = getLevels();

	// Retriving level and number of post of th authors
	$type_h = array('forum_sender', 'forum_text');
	$cont_h = array($lang->def('_AUTHOR'), $lang->def('_TEXTOF'));
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);

	// Compose messagges display
	$path = $GLOBALS['cms']['url'].$GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['framework']['pathphoto'];
	while(list($id_message, $message_info) = each($messages)) {

		// sender info
		$m_author = $message_info['author'];
		$sender = '<div class="forum_author">'
			.getForumMsgAuthorTxt($authors_names[$m_author][ACL_INFO_LASTNAME],
					$authors_names[$m_author][ACL_INFO_FIRSTNAME], $authors_names[$m_author][ACL_INFO_USERID])
			.'</div>'
			//.'<div class="forum_level">'.$lang->def('_LEVEL').' : '.$authors_info[$m_author]['level'].'</div>'
			.( $authors_names[$m_author][ACL_INFO_AVATAR] != ''
				? '<img class="forum_avatar" src="'.$path.$authors_names[$m_author][ACL_INFO_AVATAR].'" alt="'.$lang->def('_AVATAR').'" />'
				: '' );

		if ($m_author != $GLOBALS["ANONYMOUS_IDST"]) {
			$sender.= '<div class="forum_numpost">'.$lang->def('_NUMPOST').' : '
				.( isset($authors_info[$m_author]['num_post'])
					? $authors_info[$m_author]['num_post']
					: 0 )
				.'</div>'
				.'<img src="'.getPathImage().'standard/user.gif" alt="&gt;" />&nbsp;'
				.'<a href="'.getForumBaseUrl('&amp;op=viewprofile&amp;idMessage='.$id_message).'">'.$lang->def('_VIEWPROFILE').'</a>';
		}

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
						.getForumMsgAuthorTxt($authors_names[$modify_by][ACL_INFO_LASTNAME],
							$authors_names[$modify_by][ACL_INFO_FIRSTNAME], $authors_names[$modify_by][ACL_INFO_USERID]).'</div>';
			}
			if($authors_names[$m_author][ACL_INFO_SIGNATURE] != '') {
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
	$lang =& DoceboLanguage::createInstance('forum', 'cms');
	$id_thread 		= importVar('idThread', true, 0);
	$id_message 	= importVar('idMessage', true, 0);
	$ini = importVar('ini');

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	// Some info about forum and thread
	$thread_query = "
	SELECT idForum, title , locked, erased
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $locked_t, $erased_t) = mysql_fetch_row(mysql_query($thread_query));

	$upload_perm	 = checkRoleForItem("forum", $id_forum, "upload");
	$moderate 	= checkForumModeratePerm($id_forum);
	$mod_perm	= $moderate;

	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));

	$page_title = array(
		getForumBaseUrl('&amp;op=forum') => $lang->def('_FORUM'),
		getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum) => $forum_title,
		getForumBaseUrl('&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini) => $thread_title,
		$lang->def('_REPLY_TO_THIS_THREAD')
	);
	if(($erased_t || $locked_t) && !$mod_perm && !$moderate) {

		$GLOBALS['page']->add(
			getCmsTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_CANNOTENTER')
			.'</div>', 'content');
		return;
	}

	$re_title="";

	// retrive info about quoting
	if($id_message > 0) {

		$message_query = "
		SELECT title, textof, locked, author
		FROM ".$GLOBALS['prefix_cms']."_forummessage
		WHERE idMessage = '".$id_message."'";
		list($m_title, $m_textof, $m_locked, $author) = mysql_fetch_row(mysql_query($message_query));

		if ($m_locked) {
			unset($m_title, $m_textof);
			$id_message=0;
		}

		$re_title=$m_title;

	}
	else if (($id_thread > 0) && (!$locked_t)) {
		$re_title=$thread_title;
	}

	if (isset($re_title))
		$re_title=preg_replace("/^".$lang->def('_RE')."\\s/", "", $re_title);

	$GLOBALS['page']->add(
		getCmsTitleArea($page_title, 'forum')
		.'<div class="std_block">', "content");
		
	$saved_textof=getForumSavedTextof($id_thread);
	if ($saved_textof !== FALSE) {
		
		$msg=$lang->def("_FORUM_TEXT_RESTORED_FROM_SESSION");
		$GLOBALS["page"]->add(getInfoUi($msg), "content");
		$text_of=$saved_textof;
		
	}
	else {
		$text_of=($id_message != '' ? '<em>'.$lang->def('_WRITTED_BY').': '.$acl_man->getUserName($author).'</em><br /><br />[quote]'.$m_textof.'[/quote]' : '' );
	}
		
	$GLOBALS["page"]->add(
		getBackUi(getForumBaseUrl('&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini), $lang->def('_BACK'))
		.Form::openForm('form_forum', getForumBaseUrl('&amp;op=insmessage'), false, false, 'multipart/form-data')
		.Form::openElementSpace()

		.Form::getHidden('idThread', 'idThread', $id_thread)
		.Form::getHidden('idMessage', 'idMessage', $id_message)
		.Form::getHidden('ini', 'ini', $ini)
		.Form::getTextfield($lang->def('_SUBJECT'), 'title', 'title', 255, ($re_title != '' ? $lang->def('_RE').' '.$re_title : '' ))
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', $text_of)
	, 'content');
	if($upload_perm) {

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
		Form::openButtonSpace(), 'content');
		
	$GLOBALS['page']->add(Form::getButton('post_thread_2', 'post_thread', $lang->def('_SEND'))
		.Form::getButton('undo_2', 'undo', $lang->def('_UNDO')), 'content');
		
	$GLOBALS['page']->add(Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function insmessage() {
	$id_thread 	= importVar('idThread', true, 0);
	$id_message 	= importVar('idMessage', true, 0);
	$ini 	= importVar('ini');

	if(isset($_POST['undo'])) jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini));

	$lang =& DoceboLanguage::createInstance('forum', 'cms');

	// Some info about forum and thread
	list($id_forum, $thread_title, $locked_t, $erased_t) = mysql_fetch_row(mysql_query("
	SELECT idForum, title, locked, erased
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'"));
	
	// Expired session check:
	checkExpiredSession($id_forum, $id_thread);
	unsetForumSavedTextof($id_thread);
	// ------------------------------------------	
	
	$forum_query = "
	SELECT title
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title) = mysql_fetch_row(mysql_query($forum_query));

	$upload_perm=checkRoleForItem("forum", $id_forum, "upload");
	$moderate 	= checkForumModeratePerm($id_forum);
	$mod_perm	= $moderate;

	$locked_f = false;
	if(!$moderate) {

		$query_view_forum = "
		SELECT idMember, locked
		FROM ".$GLOBALS['prefix_cms']."_forum AS f LEFT JOIN
				".$GLOBALS['prefix_cms']."_forum_access AS fa
					ON ( f.idForum = fa.idForum )
		WHERE f.idForum = '".$id_forum."'";
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
	if(!$continue) jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_cannotsee'));
	if($locked_f || $locked_t ||$erased_t && (!$mod_perm && !$moderate)) {
		jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_lock'));
	}
	if($_POST['title'] == '') $_POST['title'] = substr(strip_tags($_POST['textof']), 0, 50).'...';

	$now = date("Y-m-d H:i:s");

	//save attachment
	$name_file = '';
	if($_FILES['attach']['name'] != '' && $upload_perm ) {
		$name_file = save_file($_FILES['attach']);
	}
	$answer_tree = '';
	if($id_message != 0) {

		list($answer_tree) = mysql_fetch_row(mysql_query("
		SELECT answer_tree
		FROM ".$GLOBALS['prefix_cms']."_forummessage
		WHERE idMessage = '".$id_message."'"));
	}
	$answer_tree .= '/'.$now;

	$ins_mess_query = "
	INSERT INTO ".$GLOBALS['prefix_cms']."_forummessage
	( idThread, title, textof, author, posted, answer_tree, attach ) VALUES
	( 	'".$id_thread."',
		'".$_POST['title']."',
		'".$_POST['textof']."',
		'".getLogUserId()."',
		'".$now."',
		'".$answer_tree."',
		'".addslashes($name_file)."' )";
	if(!mysql_query($ins_mess_query)) {

		delete_file($name_file);
		jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_ins'));
	}
	list($new_id_message) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

	mysql_query("
	UPDATE ".$GLOBALS['prefix_cms']."_forum
	SET num_post = num_post + 1,
		last_post = '".$new_id_message."'
	WHERE idForum = '".$id_forum."'");

	mysql_query("
	UPDATE ".$GLOBALS['prefix_cms']."_forumthread
	SET num_post = num_post + 1,
		last_post = '".$new_id_message."'
	WHERE idThread = '".$id_thread."'");

	// launch notify
	require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');

	$msg_composer = new EventMessageComposer('forum', 'cms');

	$msg_composer->setSubjectLangText('email', '_SUBJECT_NOTIFY_MESSAGE', false);
	$msg_composer->setBodyLangText('email', '_NEW_MESSAGE_INSERT_IN_THREAD', array(	'[url]' => $GLOBALS['cms']['url'],
																		'[forum_title]' => $forum_title,
																		'[thread_title]' => $_POST['title'] ) );

	$msg_composer->setSubjectLangText('sms', '_SUBJECT_NOTIFY_MESSAGE_SMS', false);
	$msg_composer->setBodyLangText('sms', '_NEW_MESSAGE_INSERT_IN_THREAD_SMS', array(	'[url]' => $GLOBALS['cms']['url'],
																		'[forum_title]' => $forum_title,
																		'[thread_title]' => $_POST['title'] ) );

	launchNotify('thread', $id_forum, $lang->def('_NEW_MESSAGE'), $msg_composer);

	jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=ok'));
}

//---------------------------------------------------------------------------//

function modmessage() {
 //-TP// checkPerm('forum', 'view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'cms');
	$id_message 	= importVar('idMessage', true, 0);
	$ini 			= importVar('ini');

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	// retrive info about message
	$mess_query = "
	SELECT idThread, title, textof, author
	FROM ".$GLOBALS['prefix_cms']."_forummessage
	WHERE  idMessage = '".$id_message."'";
	list($id_thread, $title, $textof, $author) = mysql_fetch_row(mysql_query($mess_query));

	// Some info about forum and thread
	$thread_query = "
	SELECT idForum, title , locked, erased
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $locked_t, $erased_t) = mysql_fetch_row(mysql_query($thread_query));

	$upload_perm=checkRoleForItem("forum", $id_forum, "upload");
	$moderate 	= checkForumModeratePerm($id_forum);
	$mod_perm	= $moderate;
	if( !userIsAuthor($author) && !$moderate && !$mod_perm)
		die("You can't access");

	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));

	$page_title = array(
		getForumBaseUrl('&amp;op=forum') => $lang->def('_FORUM'),
		getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum) => $forum_title,
		getForumBaseUrl('&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini) => $thread_title,
		$lang->def('_MOD_MESSAGE')
	);
	if($erased_t && !$mod_perm && !$moderate) {

		$GLOBALS['page']->add(
			getCmsTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_CANNOTENTER')
			.'</div>', 'content');
		return;
	}

	$GLOBALS['page']->add(
		getCmsTitleArea($page_title, 'forum')
		.'<div class="std_block">', "content");
		
	$saved_textof=getForumSavedTextof($id_thread);
	if ($saved_textof !== FALSE) {
		
		$msg=$lang->def("_FORUM_TEXT_RESTORED_FROM_SESSION");
		$GLOBALS["page"]->add(getInfoUi($msg), "content");
		$textof=$saved_textof;
		
	}		
		
	$GLOBALS['page']->add(
		getBackUi(getForumBaseUrl('&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini), $lang->def('_BACK'))
		.Form::openForm('form_forum', getForumBaseUrl('&amp;op=upmessage'), false, false, 'multipart/form-data')
		.Form::openElementSpace()

		.Form::getHidden('idMessage', 'idMessage', $id_message)
		.Form::getHidden('ini', 'ini', $ini)
		.Form::getTextfield($lang->def('_SUBJECT'), 'title', 'title', 255, $title)
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', $textof)
	, 'content');
	if($upload_perm) {

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

	$lang =& DoceboLanguage::createInstance('forum', 'cms');

	// retrive info about message
	$mess_query = "
	SELECT idThread, author, attach, generator
	FROM ".$GLOBALS['prefix_cms']."_forummessage
	WHERE  idMessage = '".$id_message."'";
	list($id_thread, $author, $attach, $is_generator) = mysql_fetch_row(mysql_query($mess_query));
	if(isset($_POST['undo'])) jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini));

	list($id_forum, $locked_t, $erased_t) = mysql_fetch_row(mysql_query("
	SELECT idForum, locked, erased
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'"));

	// Expired session check:
	checkExpiredSession($id_forum, $id_thread);
	unsetForumSavedTextof($id_thread);
	// ------------------------------------------

	$upload_perm=checkRoleForItem("forum", $id_forum, "upload");
	$moderate 	= checkForumModeratePerm($id_forum);
	$mod_perm	= $moderate;
	if( !userIsAuthor($author) && !$moderate && !$mod_perm)
		die("You can't access!");


	if($locked_t ||$erased_t && (!$mod_perm && !$moderate)) {
		jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_lock'));
	}
	if($_POST['title'] == '') $_POST['title'] = substr(strip_tags($_POST['textof']), 0, 50).'...';

	$now = date("Y-m-d H:i:s");

	//save attachment
	$name_file = $attach;
	if($_FILES['attach']['name'] != '' && $upload_perm ) {

		delete_file($attach);
		$name_file = save_file($_FILES['attach']);
	}
	$upd_mess_query = "
	UPDATE ".$GLOBALS['prefix_cms']."_forummessage
	SET title = '".$_POST['title']."',
		textof = '".$_POST['textof']."',
		attach = '".addslashes($name_file)."',
		modified_by = '".getLogUserId()."',
		modified_by_on = '".$now."'
	WHERE idMessage = '".$id_message."'";
	if(!mysql_query($upd_mess_query)) {

		delete_file($name_file);
		jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=err_ins'));
	}

	if($is_generator) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_cms']."_forumthread
		SET title = '".$_POST['title']."'
		WHERE idThread = '".$id_thread."'");
	}
	jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini.'&amp;result=ok'));
}

//---------------------------------------------------------------------------//

function delmessage() {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('forum', 'cms');

	$id_message 	= importVar('idMessage', true, 0);
	$ini 	= importVar('ini');

	$mess_query = "
	SELECT idThread, title, textof, author, attach, answer_tree
	FROM ".$GLOBALS['prefix_cms']."_forummessage
	WHERE  idMessage = '".$id_message."'";
	list($id_thread, $title, $textof, $author, $file, $answer_tree) = mysql_fetch_row(mysql_query($mess_query));


	$thread_query = "
	SELECT idForum, title, num_post, last_post 
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $num_post, $last_post) = mysql_fetch_row(mysql_query($thread_query));

	$moderate 	= checkForumModeratePerm($id_forum);
	$mod_perm	= $moderate;
	if( !userIsAuthor($author) && !$moderate && !$mod_perm)
		die("You can't access!");

	$forum_query="SELECT title FROM ".$GLOBALS["prefix_cms"]."_forum WHERE idForum = '".$id_forum."'";
	list($forum_title) = mysql_fetch_row(mysql_query($forum_query));

	if(isset($_POST['undo'])) jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;ini='.$ini));
	if(isset($_POST['confirm'])) {

		$new_answer_tree = substr($answer_tree, 0, -21);
		if(!mysql_query("
		UPDATE ".$GLOBALS['prefix_cms']."_forummessage
		SET answer_tree = CONCAT( '$new_answer_tree', SUBSTRING( answer_tree FROM ".strlen($answer_tree)." ) )
		WHERE answer_tree LIKE '".$answer_tree."/%'"))
			jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;result=err_del'));

		if(!mysql_query("
		UPDATE ".$GLOBALS['prefix_cms']."_forum
		SET num_post = num_post - 1
			".( $num_post == 0 ? " ,num_thread = num_thread - 1 " : " " )."
		WHERE idForum = '".$id_forum."'"))
			jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;result=err_del'));

		if(($num_post != 0) && ($last_post == $id_message)) {
			
			$query_text = "
			SELECT idMessage 
			FROM ".$GLOBALS['prefix_cms']."_forummessage
			WHERE idThread = '".$id_thread."'
			ORDER BY posted DESC";
			$re = mysql_query($query_text);
			list($id_new, $post) = mysql_fetch_row($re);
		}
		if($num_post == 0) {

			if(!mysql_query("
			DELETE FROM ".$GLOBALS['prefix_cms']."_forumthread
			WHERE idThread = '".$id_thread."'"))
				jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;result=err_del'));
			unsetNotify('thread', $id_thread);
		} else {

			if(!mysql_query("
			UPDATE ".$GLOBALS['prefix_cms']."_forumthread
			SET num_post = num_post - 1"
				.( ($last_post == $id_message) ? " , last_post = '".$id_new."'" : '' )."
			WHERE idThread = '".$id_thread."'"))
				jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;result=err_del'));
		}
		delete_file($file);

		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_cms']."_forummessage
		WHERE idMessage = '".$id_message."'"))
			jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;result=err_del'));

		jumpTo(getForumBaseUrl('&op=message&idThread='.$id_thread.'&amp;result=ok'));
	} else {

		$page_title = array(
			getForumBaseUrl('&amp;op=forum') => $lang->def('_FORUM'),
			getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum) => $forum_title,
			getForumBaseUrl('&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini) => $thread_title,
			$lang->def('_DEL_MESSAGE')
		);
		$GLOBALS['page']->add(
			getCmsTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.Form::openForm('del_thread', getForumBaseUrl('&amp;op=delmessage'))
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
	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
	$lang =& DoceboLanguage::createInstance('forum');

	$id_message = importVar('idMessage');
	$ini = importVar('ini');

	list($id_thread, $idst_user, $title) = mysql_fetch_row(mysql_query("
	SELECT t1.idThread, t1.author, t2.title
	FROM ".$GLOBALS['prefix_cms']."_forummessage as t1, ".
	$GLOBALS['prefix_cms']."_forumthread as t2
	WHERE idMessage = '".(int)$_GET['idMessage']."' AND (t1.idThread=t2.idThread)"));

	require_once($GLOBALS["where_cms"]."/modules/profile/class.cms_user_profile.php");

	$lang =& DoceboLanguage::createInstance('profile', 'framework');

	$profile = new CmsUserProfile( $idst_user );
	$profile->init('profile', 'lms', getForumBaseUrl('&amp;op=message&amp;idThread='.$id_thread.'&amp;ini='.$ini, $title), 'ap');

	$GLOBALS['page']->add(
		$profile->getTitleArea()

		.$profile->getHead()

		.$profile->performAction()

		.$profile->getFooter()
	, 'content');
}

//---------------------------------------------------------------------------//


//---------------------------------------------------------------------------//

function forumsearch() {
	if(isset($_POST['search_arg'])) {
		$_SESSION['forum']['search_arg'] = $_POST['search_arg'];
		$search_arg = importVar('search_arg');
	} else {
		$search_arg = $_SESSION['forum']['search_arg'];
	}
	$ord = importVar('ord');

	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.navbar.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang =& DoceboLanguage::createInstance('forum', 'cms');

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	$mod_perm=false;
	$moderate=$mod_perm;

	if($mod_perm) {

		$query_view_forum = "
		SELECT DISTINCT idForum
		FROM ".$GLOBALS['prefix_cms']."_forum";
	} else {

		$acl 	=& $GLOBALS['current_user']->getAcl();
		$all_user_idst = $acl->getSTGroupsST(getLogUserId());
		$all_user_idst[] = getLogUserId();

	$query_view_forum = "
	SELECT DISTINCT f.idForum, f.title, f.description, f.num_thread, f.num_post, f.locked, f.emoticons
		FROM ".$GLOBALS['prefix_cms']."_forum AS f
			LEFT JOIN ".$GLOBALS['prefix_cms']."_area_block_forum AS bf ON ( f.idForum = bf.idForum )
		WHERE
			( bf.idBlock='".(int)$GLOBALS["pb"]."' ) ORDER BY f.sequence";

	}

	$forums = array();
	$re_forum = mysql_query($query_view_forum);
	while(list($id_f) = mysql_fetch_row($re_forum)) {
		$forums[] = $id_f;
	}



	if(empty($forums)) {

		$page_title = array(
			getForumBaseUrl('&amp;op=forum') => $lang->def('_FORUM'),
			$lang->def('_SEARCH_RESULT_FOR').' : '.$search_arg
		);
		$GLOBALS['page']->add(
			getCmsTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_NO_PLACEFORSEARCH')
			.'</div>', 'content');
	}
	$query_num_thread = "
	SELECT COUNT(DISTINCT t.idThread)
	FROM ".$GLOBALS['prefix_cms']."_forumthread AS t JOIN
			".$GLOBALS['prefix_cms']."_forummessage AS m
	WHERE t.idThread = m.idThread AND t.idForum IN ( ".implode($forums, ',')." )
		AND ( m.title LIKE '%".$search_arg."%' OR m.textof LIKE '%".$search_arg."%' ) ";
	list($tot_thread) = mysql_fetch_row(mysql_query($query_num_thread));

	$base_url = '&amp;op=search';
	$jump_url = getForumBaseUrl($base_url);
	$nav_bar 	= new NavBar('ini', $GLOBALS['cms']['visuItem'], $tot_thread, 'link');
	$nav_bar->setLink($jump_url.'&amp;ord='.$ord);
	$ini 		= $nav_bar->getSelectedElement();
	$ini_page	= $nav_bar->getSelectedPage();

	$query_thread = "
	SELECT DISTINCT t.idThread, t.idForum, t.author AS thread_author, t.posted, t.title, t.num_post, t.num_view, t.locked, t.erased
	FROM ".$GLOBALS['prefix_cms']."_forumthread AS t JOIN
			".$GLOBALS['prefix_cms']."_forummessage AS m
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
	$query_thread .= " LIMIT $ini, ".$GLOBALS['cms']['visuItem'];
	$re_thread = mysql_query($query_thread);
	echo mysql_error();
	$re_last_post = mysql_query("
	SELECT m.idThread, t.author AS thread_author, m.posted, m.title, m.author  AS mess_author, m.generator
	FROM ".$GLOBALS['prefix_cms']."_forumthread AS t LEFT JOIN
		".$GLOBALS['prefix_cms']."_forummessage AS m ON ( t.last_post = m.idMessage )
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
		getForumBaseUrl('&amp;op=forum') => $lang->def('_FORUM'),
		$lang->def('_SEARCH_RESULT_FOR').' : '.$search_arg
	);
	$GLOBALS['page']->add(
		 getCmsTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.Form::openForm('search_forum', getForumBaseUrl('&amp;op=search'))
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

	$tb = new TypeOne($GLOBALS['cms']['visuItem'], $lang->def('_THREAD_CAPTION'), $lang->def('_THRAD_SUMMARY'));

	$tb->setTableStyle("forum_table");

	$img_up 	= '<img src="'.getPathImage().'standard/ord_asc.gif" alt="'.$lang->def('_ORD_ASC').'" />';
	$img_down 	= '<img src="'.getPathImage().'standard/ord_desc.gif" alt="'.$lang->def('_ORD_DESC').'" />';

	$cont_h = array(
		'<img src="'.getPathImage().'forum/thread.gif" title="'.$lang->def('_FREET').'" alt="'.$lang->def('_FREE').'" />',
		'<a href="'.getForumBaseUrl($base_url.'&amp;ord='.( $ord == 'obj' ? 'obji' : 'obj' )).'" title="'.$lang->def('_ORD_THREAD').'">'
			.( $ord == 'obj' ? $img_up : ( $ord == 'obji' ? $img_down : '' ) ).$lang->def('_THREAD').'</a>',
		$lang->def('_NUMREPLY'),
		'<a href="'.getForumBaseUrl($base_url.'&amp;ord='.( $ord == 'auth' ? 'authi' : 'auth' )).'" title="'.$lang->def('_ORD_AUTHOR').'">'
			.( $ord == 'auth' ? $img_up : ( $ord == 'authi' ? $img_down : '' ) ).$lang->def('_AUTHOR').'</a>',
		$lang->def('_NUMVIEW'),
		//$lang->def('_POSTED'),
		'<a href="'.getForumBaseUrl($base_url.'&amp;ord='.( $ord == 'post' ? 'posti' : 'post' )).'" title="'.$lang->def('_ORD_POST').'">'
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

		// Used for mod_rewrite
		$GLOBALS["forum_url_title"]=$title;

		$c_css = '';
		// thread author
		$t_author =getForumMsgAuthorTxt($authors_names[$t_author][ACL_INFO_LASTNAME],
							$authors_names[$t_author][ACL_INFO_FIRSTNAME], $authors_names[$t_author][ACL_INFO_USERID]);
		// last post author
		if(isset($last_post[$idT])) {

			$author = $last_post[$idT]['author'];
			$last_mess_write = $last_post[$idT]['info'].' ( '.$lang->def('_BY').': <span class="mess_author">'
				.getForumMsgAuthorTxt($authors_names[$author][ACL_INFO_LASTNAME],
					$authors_names[$author][ACL_INFO_FIRSTNAME], $authors_names[$author][ACL_INFO_USERID]).'</span> )';
		} else {
			$last_mess_write = $lang->def('_NONE');
		}
		// status of the thread
		if($erased) {
			$status = '<img src="'.getPathImage().'forum/thread_erased.gif" alt="'.$lang->def('_FREE').'" />';
		} elseif($locked) {
			$status = '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKED').'" />';
		} elseif(isset($_SESSION['cms_unreaded_forum'][$id_forum][$idT])) {

			$status = '<img src="'.getPathImage().'forum/thread_unreaded.gif" alt="'.$lang->def('_UNREADED').'" />';
			$c_css = ' class="text_bold"';
		} else {
			$status = '<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" />';
		}
		$content = array($status);
		$content[] = ( $erased && !$mod_perm ?
					'<div class="forumErased">'.$lang->def('_ERASED').'</div>' :
					'<a'.$c_css.' href="'.getForumBaseUrl('&amp;op=searchmessage&amp;idThread='.$idT.'&amp;ini_thread='.$ini_page).'">'
					.($search_arg != "" ?
					eregi_replace($search_arg, '<span class="filter_evidence">'.$search_arg.'</span>', $title) : $title )
					.'</a>');
		$content[] = $num_post
			.( isset($_SESSION['cms_unreaded_forum'][$id_forum][$idT]) && $_SESSION['cms_unreaded_forum'][$id_forum][$idT] != 'new_thread'
				? '<br />(<span class="forum_notread">'.$_SESSION['cms_unreaded_forum'][$id_forum][$idT].' '.$lang->def('_NEW').')</span>'
				: '' );
		$content[] = $t_author;
		$content[] = $num_view;
		//$content[] = $GLOBALS['regset']->databaseToRegional($posted);
		$content[] = $last_mess_write;
		if($mod_perm) {

			$content[] = '<a href="'.getForumBaseUrl('&amp;op=modthread&amp;idThread='.$idT.'&amp;search=1&amp;ini='.$ini_page).'" '
				.'title="'.$lang->def('_MODTHREAD_TITLE').' : '.strip_tags($title).'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($title).'" /></a>';
			$content[] = '<a href="'.getForumBaseUrl('&amp;op=delthread&amp;idThread='.$idT.'&amp;search=1&amp;ini='.$ini_page).'" '
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
	require_once($GLOBALS['where_framework'].'/lib/lib.mimetype.php');
	$lang =& DoceboLanguage::createInstance('forum', 'cms');
	$id_thread = importVar('idThread', true, 0);
	$ini_thread = importVar('ini_thread');

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	$tb 	= new TypeOne($GLOBALS['cms']['visuItem'], $lang->def('_CAPTION_FORUM_MESSAGE_SEARCH'), $lang->def('_SUMMARY_FORUM_MESSAGE_SEARCH'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink(getForumBaseUrl('&amp;op=searchmessage&amp;idThread='.$id_thread.'&amp;ini_thread='.$ini_thread));
	$ini 	= $tb->getSelectedElement();
	$ini_page = $tb->getSelectedPage();

	$tb->setTableStyle("forum_table");

	// Some info about forum and thread
	$thread_query = "
	SELECT idForum, title, num_post, locked, erased
	FROM ".$GLOBALS['prefix_cms']."_forumthread
	WHERE idThread = '".$id_thread."'";
	list($id_forum, $thread_title, $tot_message, $locked_t, $erased_t) = mysql_fetch_row(mysql_query($thread_query));

	$write_perm=checkRoleForItem("forum", $id_forum, "write");
	$mod_perm	= checkForumModeratePerm($id_forum);
	$moderate=$mod_perm;

	// Used for mod_rewrite
	$GLOBALS["forum_url_title"]=$thread_title;

	$forum_query = "
	SELECT title, locked
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$id_forum."'";
	list($forum_title, $locked_f) = mysql_fetch_row(mysql_query($forum_query));
	++$tot_message;

	//set as readed if needed
	if(isset($_SESSION['cms_unreaded_forum'][$id_forum][$id_thread])) unset($_SESSION['cms_unreaded_forum'][$id_forum][$id_thread]);

	if( ($ini == 0) && (!isset($_GET['result'])) ) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_cms']."_forumthread
		SET num_view = num_view + 1
		WHERE idThread = '".$id_thread."'");
	}
	$page_title = array(
		getForumBaseUrl('&amp;op=forum', "forum") => $lang->def('_FORUM'),
		getForumBaseUrl('&amp;op=search&amp;ini='.$ini_thread, $thread_title) => $thread_title,
		$lang->def('_SEARCH_RESULT_FOR').' : '.$search_arg
	);
	if($erased_t && !$mod_perm && !$moderate) {

		$GLOBALS['page']->add(
			getCmsTitleArea($page_title, 'forum')
			.'<div class="std_block">'
			.$lang->def('_CANNOTENTER')
			.'</div>', 'content');
		return;
	}
	// Who have semantic evaluation
	// Find post
	$messages 		= array();
	$authors 		= array();
	$authors_names	= array();
	$authors_info	= array();
	$re_message = mysql_query("
	SELECT idMessage, posted, title, textof, attach, locked, author, modified_by, modified_by_on
	FROM ".$GLOBALS['prefix_cms']."_forummessage
	WHERE idThread = '".$id_thread."'
	ORDER BY posted
	LIMIT $ini, ".$GLOBALS['cms']['visuItem']);
	while($record = mysql_fetch_assoc($re_message)) {

		$messages[$record['idMessage']] 	= $record;
		$authors[$record['author']] 		= $record['author'];
		if($record['modified_by'] != 0) {
			$authors[$record['modified_by']] 	= $record['modified_by'];
		}
	}
	$authors_names =& $acl_man->getUsers($authors);
	//$level_name = getLevels();

	// Retriving level and number of post of th authors
	$type_h = array('forum_sender', 'forum_text');
	$cont_h = array($lang->def('_AUTHOR'), $lang->def('_TEXTOF'));
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);

	// Compose messagges display
	$path = $GLOBALS['cms']['url'].$GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['framework']['pathphoto'];
	while(list($id_message, $message_info) = each($messages)) {

		// sender info
		$m_author = $message_info['author'];

		$author_txt=getForumMsgAuthorTxt($authors_names[$m_author][ACL_INFO_LASTNAME],
			$authors_names[$m_author][ACL_INFO_FIRSTNAME], $authors_names[$m_author][ACL_INFO_USERID]);

		$sender = '<div class="forum_author">'
			.$author_txt
			.'</div>'
			.( $authors_names[$m_author][ACL_INFO_AVATAR] != ''
				? '<img class="forum_avatar" src="'.$path.$authors_names[$m_author][ACL_INFO_AVATAR].'" alt="'.$lang->def('_AVATAR').'" />'
				: '' );

		if ($m_author != $GLOBALS["ANONYMOUS_IDST"]) {
			$sender.='<div class="forum_numpost">'.$lang->def('_NUMPOST').' : '
			.( isset($authors_info[$m_author]['num_post'])
				? $authors_info[$m_author]['num_post']
				: 0 )
			.'</div>';
			$sender.='<img src="'.getPathImage().'standard/user.gif" alt="&gt;" />&nbsp;';
			$sender.='<a href="'.getForumBaseUrl('&amp;op=viewprofile&amp;idMessage='.$id_message.'&amp;ini='.$ini_page, $author_txt."_profile").'">'.$lang->def('_VIEWPROFILE').'</a>';
		}

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
					.'<a href="'.getForumBaseUrl('&amp;op=download&amp;id='.$id_message).'">'
					.$lang->def('_ATTACHMENT').' : '
					.'<img src="'.getPathImage('fw').mimeDetect($message_info['attach']).'" alt="'.$lang->def('_ATTACHMENT').'" /></a>'
					.'</div>';
			}

			$textof = str_replace('[quote]', '<blockquote class="forum_quote">', str_replace('[/quote]', '</blockquote>', $message_info['textof']));
			$msgtext .= '<div class="forum_post_title">'.$lang->def('_SUBJECT').' : '
						.($search_arg != "" ?
						eregi_replace($search_arg, '<span class="filter_evidence">'.$search_arg.'</span>', $message_info['title']) : $message_info['title'])
						.'</div>';
			$msgtext .= '<div class="forum_post_text">'
						.($search_arg != "" ?
						eregi_replace($search_arg, '<span class="filter_evidence">'.$search_arg.'</span>', $textof) : $textof)
						.'</div>';

			if($message_info['modified_by'] != 0) {

				$modify_by = $message_info['modified_by'];
				$msgtext .= '<div class="forum_post_modified_by">'
						.$lang->def('_MODIFY_BY').' : '
						.getForumMsgAuthorTxt($authors_names[$modify_by][ACL_INFO_LASTNAME],
							$authors_names[$modify_by][ACL_INFO_FIRSTNAME], $authors_names[$modify_by][ACL_INFO_USERID])
						.' '.$lang->def('_MODIFY_BY_ON').' : '
						.$GLOBALS['regset']->databaseToRegional($message_info['modified_by_on'])
						.'</div>';
			}

			if($authors_names[$m_author][ACL_INFO_SIGNATURE] != '') {
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
		if($moderate || $mod_perm) {
			if($message_info['locked']) {

				$action .= '<a href="'.getForumBaseUrl('&amp;op=moderatemessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
						.'title="'.$lang->def('_DEMODERATE_MESSAGE').' : '.strip_tags($message_info['title']).'">'
					.'<img src="'.getPathImage().'forum/demoderate.gif" alt="'.$lang->def('_ALT_DEMODERATE').' : '.strip_tags($message_info['title']).'" /> '
					.$lang->def('_DEMODERATE').'</a> ';
			} else {

				$action .= '<a href="'.getForumBaseUrl('&amp;op=moderatemessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
						.'title="'.$lang->def('_MODERATE_MESSAGE').' : '.strip_tags($message_info['title']).'">'
					.'<img src="'.getPathImage().'forum/moderate.gif" alt="'.$lang->def('_ALT_MODERATE').' : '.strip_tags($message_info['title']).'" /> '
					.$lang->def('_MODERATE').'</a> ';
			}
		}
		if((!$locked_t && !$locked_f && !$message_info['locked'] && $write_perm) || $mod_perm || $moderate) {
			$action .= '<a href="'.getForumBaseUrl('&amp;op=addmessage&amp;idThread='.$id_thread.'&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
					.'title="'.$lang->def('_REPLY_TITLE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'forum/reply.gif" alt="'.$lang->def('_ALT_REPLY').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_QUOTE').'</a>';
		}
		if($moderate || $mod_perm || (userIsAuthor($m_author)) ) {

			$action .= '<a href="'.getForumBaseUrl('&amp;op=modmessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
					.'title="'.$lang->def('_MOD_MESSAGE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_MOD').'</a>'
				.'<a href="'.getForumBaseUrl('&amp;op=delmessage&amp;idMessage='.$id_message.'&amp;ini='.$ini_page).'" '
					.'title="'.$lang->def('_DEL_MESSAGE').' : '.strip_tags($message_info['title']).'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.strip_tags($message_info['title']).'" /> '
				.$lang->def('_DEL').'</a> ';
		}
		$tb->addBodyExpanded($action, 'forum_action');
	}
	if( (!$locked_t && !$locked_f) || $mod_perm || $moderate ) {

		$tb->addActionAdd(
			'<a href="'.getForumBaseUrl('&amp;op=addmessage&amp;idThread='.$id_thread.'&amp;ini='.$ini_page).'" title="'.$lang->def('_ADDMESSAGET').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '
			.$lang->def('_REPLY_TO_THIS_THREAD').'</a>'
		);
	}
	$GLOBALS['page']->add(
		getCmsTitleArea($page_title, 'forum')
		.'<div class="std_block">'
		.Form::openForm('search_forum', getForumBaseUrl('&amp;op=search&amp;idThread='.$id_thread))
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
			.'<a href="'.getForumBaseUrl('&amp;op=modstatusthread&amp;idThread='.$id_thread.'&amp;ini='.$ini_page).'">'
			.( $locked_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_FREETHREAD')
				: '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKTHREAD').'" /> '.$lang->def('_LOCKTHREAD') )
			.'</a> '
			.'<a href="'.getForumBaseUrl('&amp;op=changeerased&amp;idThread='.$id_thread.'&amp;ini='.$ini_page).'">'
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
			.'<a href="'.getForumBaseUrl('&amp;op=modstatusthread&amp;idThread='.$id_thread.'&amp;ini='.$ini_page).'">'
			.( $locked_t
				?'<img src="'.getPathImage().'forum/thread.gif" alt="'.$lang->def('_FREE').'" /> '.$lang->def('_FREETHREAD')
				: '<img src="'.getPathImage().'forum/thread_locked.gif" alt="'.$lang->def('_LOCKTHREAD').'" /> '.$lang->def('_LOCKTHREAD') )
			.'</a> '
			.'<a href="'.getForumBaseUrl('&amp;op=changeerased&amp;idThread='.$id_thread.'&amp;ini='.$ini_page).'">'
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
	INSERT INTO ".$GLOBALS['prefix_cms']."_forum_notifier
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
	DELETE FROM ".$GLOBALS['prefix_cms']."_forum_notifier
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
	FROM ".$GLOBALS['prefix_cms']."_forum_notifier
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
	FROM ".$GLOBALS['prefix_cms']."_forum_notifier
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
	FROM ".$GLOBALS['prefix_cms']."_forum_notifier
	WHERE id_notify = '".$id_notify."' AND
		notify_is_a = '".( $notify_is_a == 'forum' ? 'forum' : 'thread' )."' AND
		id_user <> '".getLogUserId()."'";
	if($notify_is_a !== false) $query_notify .= " AND notify_is_a = '".( $notify_is_a == 'forum' ? 'forum' : 'thread' )."'";
	$re = mysql_query($query_notify);
	while(list($id_user) = mysql_fetch_row($re)) {

		$recipients[] = $id_user;
	}

	if(!empty($recipients)) {

		createNewAlert(		($notify_is_a == 'forum' ? 'CmsForumNewThread' : 'CmsForumNewResponse'),
							'forum',
							( $notify_is_a == 'forum' ? 'new_thread' : 'responce' ),
							1,
							$description,
							$recipients,
							$msg_composer );
	}
	return;
}



function checkForumModeratePerm($id_forum) {

	$res=false;

	if ((!$GLOBALS["current_user"]->isAnonymous()) && (checkRoleForItem("forum", $id_forum, "moderate")))
		$res=true;

	return $res;
}


function getForumMsgAuthorTxt($lastname, $firstname, $userid) {

	$lang 		=& DoceboLanguage::CreateInstance('forum', 'cms');
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();

	$res=$acl_man->relativeId($userid);

	return $res;
}


function getForumBaseUrl($query_string, $fake_title=FALSE, $ancor = '') {

	$res="";
	$use_mod_rewrite=(bool)($GLOBALS["cms"]["use_mod_rewrite"] == "on");

	if ($use_mod_rewrite) {

		$fake_file="forum.html";


		if (($fake_title !== FALSE) && (!empty($fake_title)))
			$fake_file=format_mod_rewrite_title($fake_title).".html";
		else if ((isset($GLOBALS["forum_url_title"])) && (!empty($GLOBALS["forum_url_title"])))
			$fake_file=basename(format_mod_rewrite_title($GLOBALS["forum_url_title"]).".html");

		$fakeurl=getFakeUrl($query_string);

		$res=$fakeurl["basepath"]."/".getPI().$fakeurl["path"]."/".$fake_file.$ancor;
	}
	else {
		$res="index.php?mn=forum&amp;pi=".getPI().$query_string.'&amp;'.$ancor;
	}

	return $res;

}


function getQueryArray($query_string) {
	$res=array();

	$query_string=str_replace("?", "", $query_string);
	$query_string=str_replace("&amp;", "&", $query_string);
	$query_string=preg_replace("/^&/", "", $query_string);
	$var_arr=explode("&", $query_string);

	foreach ($var_arr as $val) {
		$val_info=explode("=", $val);
		$res[$val_info[0]]=$val_info[1];
	}

	return $res;
}


function getPathVarListArr($op) {
	$res=array();
	$res["basepath"]="forum";

	switch ($op) {

		case "thread": {
			$res=array("op", "idForum", "ini", "ord");
			$res["basepath"]="forum";
		} break;

		case "message": {
			$res=array("op", "idThread", "ini");
			$res["basepath"]="thread";
		} break;

		default: {
			$res=array("op", "idForum", "ini", "ord");
			$res["basepath"]="forum";
		} break;

	}

	return $res;
}


function isValidPathVar($name) {

	$valid=getPathVarListArr();

	return in_array($name, $valid);
}


function getFakeUrl($query_string) {
	$res=array();
	$res["path"]="";
	$res["query"]="";
	$res["basepath"]="forum";


	$query_arr=getQueryArray($query_string);
	$op=(isset($query_arr["op"]) ? $query_arr["op"] : "");
	unset($query_arr["op"]);

	if (isset($query_arr["mr_str"]))
		unset($query_arr["mr_str"]);

	$from=array("-", "_");
	$to=array("--", "__");

	$mr_arr=array();
	foreach($query_arr as $key=>$val) {
		$my_key=str_replace($from, $to, $key);
		$my_val=str_replace($from, $to, $val);

		if (!empty($my_val))
			$mr_arr[]=$my_key."_".$my_val;
	}

	$mr_str=rawurlencode(implode("-", $mr_arr));
	if ($mr_str == "")
		$mr_str="0";
	$res["path"]="/".$op."/".$mr_str;

	return $res;
}


function getFakeUrl_2($query_string) {
	$res=array();
	$res["path"]="";
	$res["query"]="";


	$query_arr=getQueryArray($query_string);
	$op=(isset($query_arr["op"]) ? $query_arr["op"] : "");
	$varlist=getPathVarListArr($op);

	$res["basepath"]=$varlist["basepath"];
	unset($varlist["basepath"]);

	foreach ($varlist as $key=>$val) {

		if (!isset($query_arr[$val]))
			$query_arr[$val]=0;

	}

	if (empty($query_arr["ini"]))
		$query_arr["ini"]=1;

	foreach ($query_arr as $key=>$val) {

		if (in_array($key, $varlist))
			$res["path"].="/".$val;

	}

	return $res;
}


function checkMrString() {

	if ((isset($_GET["mr_str"])) && (!empty($_GET["mr_str"]))) {

		$mr_str=rawurldecode($_GET["mr_str"]);

		$mr_arr=getCleanMrArray($mr_str, "-");

		foreach ($mr_arr as $key=>$val) {
			$val_arr=getCleanMrArray($val, "_");

			$my_key=$val_arr[0];
			$my_val=$val_arr[1];

			if ((!isset($_GET[$my_key])) && (!empty($my_val))) {
				$_GET[$my_key]=$my_val;
			}
		}

	}

}


function getCleanMrArray($mr_str, $sep) {
	$mr_arr=array();

	if (preg_match("/[".$sep."]{2,2}/", $mr_str)) {

		$mr_str_map=preg_replace("/[".$sep."]{2,2}/", "  ", $mr_str);
		$mr_str_map_arr=preg_split("/".$sep."/", $mr_str_map, -1, PREG_SPLIT_OFFSET_CAPTURE);

		$mr_arr=splitFromMap($mr_str, $mr_str_map_arr, $sep);

	}
	else {
		$mr_arr=explode($sep, $mr_str);
	}

	return $mr_arr;
}


function splitFromMap($str, $arr, $sep) {
	$res=array();
	$_OFFSET=1;

	$i=0;
	while($i < count($arr)) {

		if ($i+1 < count($arr))
			$res[$i]=substr($str, $arr[$i][$_OFFSET], ($arr[$i+1][$_OFFSET]-$arr[$i][$_OFFSET]-1));
		else
			$res[$i]=substr($str, $arr[$i][$_OFFSET]);

		$res[$i]=preg_replace("/[".$sep."]{2,2}/", $sep, $res[$i]);

		$i++;
	}

	return $res;
}


function checkMrName() {

	if ((isset($_GET["mr_name"])) && (!empty($_GET["mr_name"])) && (!isset($GLOBALS["forum_url_title"])))
		$GLOBALS["forum_url_title"]=$_GET["mr_name"];

}



function userIsAuthor($author) {
	return (bool)(($author == getLogUserId()) && ($author != $GLOBALS["ANONYMOUS_IDST"]));
}


function checkExpiredSession($id_forum, $id_thread=FALSE) {
	
	$write_perm=checkRoleForItem("forum", $id_forum, "write");
	
	if (($GLOBALS["current_user"]->isAnonymous()) && (!$write_perm)) {
		
		if (isset($_POST['textof'])) {
			
			if ($id_thread === FALSE) {
				$key="add";
			}
			else {
				$key="thread_".$id_thread;
			}
			
			$text=substr(stripslashes($_POST['textof']), 0, 32768);
			$_SESSION["forum_saved_textof"][$key]=addslashes(serialize($text));
		}
			
		
		$ini=(int)importVar("ini", true);
		
		if ($id_thread !== FALSE) {
			jumpTo(getForumBaseUrl('&op=sesexpired&idForum='.$id_forum.'&idThread='.$id_thread.'&amp;ini='.$ini));
		}
		else {
			jumpTo(getForumBaseUrl('&op=sesexpired&idForum='.$id_forum.'&amp;ini='.$ini));
		}
		die();
	}
}


function forumSessionExpired() {
	
	$ini=(int)importVar("ini", TRUE);
	$id_forum=(int)importVar("idForum", TRUE);
	
	if (isset($_GET["idThread"])) {
		$id_thread=(int)$_GET["idThread"];
	}
	else {
		$id_thread=FALSE;
	}
	
	if ($id_forum < 1)
		return 0;
	
	
	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	
	$form=new Form();
	$lang =& DoceboLanguage::createInstance('forum', 'cms');
	
	$view_perm=checkRoleForItem("forum", $id_forum, "view");
	
	
	if ($view_perm) {
		if ($id_thread === FALSE) {
			$url=getForumBaseUrl('&op=thread&idForum='.$id_forum.'&amp;ini='.$ini);
		}
		else {
			$url=getForumBaseUrl('&op=message&idForum='.$id_forum.'&idThread='.$id_thread.'&amp;ini='.$ini);
		}	
	}
	else {
		$url=getForumBaseUrl();
	}
	

	list($title) = mysql_fetch_row(mysql_query("
	SELECT title
	FROM ".$GLOBALS['prefix_cms']."_forum
	WHERE idForum = '".$id_forum."'"));


	$page_title = array(
		getForumBaseUrl('&amp;op=forum') => $lang->def('_FORUM'),
		getForumBaseUrl('&amp;op=thread&amp;idForum='.$id_forum) => $title,
		$lang->def('_FORUM_SESSION_EXPIRED')
	);
	$GLOBALS['page']->add(
		getCmsTitleArea($page_title, 'forum', $lang->def('_FORUM'))
		.'<div class="std_block">', "content");	
	
	$res ="";
	$res.=getErrorUi($lang->def("_FORUM_SESSION_EXPIRED_MSG"));
	
	$res.=$form->openForm("main_form", $url);
	$res.=$form->openButtonSpace();
	$res.=$form->getButton("continue", "continue", $lang->def("_CONTINUE"));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();
	
	$GLOBALS["page"]->add($res.'</div>', 'content');
}


function getForumSavedTextof($id_thread=FALSE) {
	
	if ($id_thread === FALSE) {
		$key="add";
	}
	else {
		$key="thread_".$id_thread;
	}
	
	if (isset($_SESSION["forum_saved_textof"][$key]))
		$res=unserialize(stripslashes($_SESSION["forum_saved_textof"][$key]));
	else
		$res=FALSE;
	
	return $res;
}


function unsetForumSavedTextof($id_thread=FALSE) {
	
	if ($id_thread === FALSE) {
		$key="add";
	}
	else {
		$key="thread_".$id_thread;
	}
	
	if (isset($_SESSION["forum_saved_textof"][$key]))
		unset($_SESSION["forum_saved_textof"][$key]);
}

function moveThread($id_thread, $id_forum)
{
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::CreateInstance('forum');
	
	$write_perm = checkRoleForItem("forum", $id_forum, "write");
  $moderate = checkForumModeratePerm($id_forum);
	
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
			$query = "UPDATE ".$GLOBALS['prefix_cms']."_forumthread" .
					" SET idForum = '".$id_new_forum."'" .
					" WHERE idThread = '".$id_thread."'";
			
			$result = mysql_query($query);
			
			// Select thenumber of the post in the thread
			$query_2 = "SELECT num_post" .
						" FROM ".$GLOBALS['prefix_cms']."_forumthread" .
						" WHERE idThread = '".$id_thread."'";
			
			list($num_post) = mysql_fetch_row(mysql_query($query_2));
			
			// Update the forum info
			$query_3 = "SELECT idForum, num_thread, num_post" .
						" FROM ".$GLOBALS['prefix_cms']."_forum" .
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
						" FROM ".$GLOBALS['prefix_cms']."_forummessage" .
						" WHERE idThread IN" .
						"(" .
							" SELECT idThread" .
							" FROM ".$GLOBALS['prefix_cms']."_forumthread" .
							" WHERE idForum = '".$id_forum."'" .
						")" .
						" ORDER BY posted DESC" .
						" LIMIT 0,1";
			
			list($last_message_update[$id_forum]) = mysql_fetch_row(mysql_query($query_4));
			
			$query_5 = "SELECT idMessage" .
						" FROM ".$GLOBALS['prefix_cms']."_forummessage" .
						" WHERE idThread IN" .
						"(" .
							" SELECT idThread" .
							" FROM ".$GLOBALS['prefix_cms']."_forumthread" .
							" WHERE idForum = '".$id_new_forum."'" .
						")" .
						" ORDER BY posted DESC" .
						" LIMIT 0,1";
			
			list($last_message_update[$id_new_forum]) = mysql_fetch_row(mysql_query($query_5));
			
			$query_update_1 = "UPDATE ".$GLOBALS['prefix_cms']."_forum" .
						" SET num_post = '".$num_post_update[$id_forum]."'," .
								" num_thread='".$num_thread_update[$id_forum]."'," .
								" last_post = '".$last_message_update[$id_forum]."'" .
						" WHERE idForum = '".$id_forum."'";
			
			$result_update_1 = mysql_query($query_update_1);
			
			$query_update_2 = "UPDATE ".$GLOBALS['prefix_cms']."_forum" .
						" SET num_post = '".$num_post_update[$id_new_forum]."'," .
								" num_thread='".$num_thread_update[$id_new_forum]."'," .
								" last_post = '".$last_message_update[$id_new_forum]."'" .
						" WHERE idForum = '".$id_new_forum."'";
			
			$result_update_2 = mysql_query($query_update_2);
		}
		jumpTo(getForumBaseUrl('&amp;op=thread&idForum='.$id_forum));
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
			getTitleArea($lang->def('_MOVE_THREAD'), 'forum')
			.'<div class="std_block">'
			.getModifyUi(	$lang->def('_AREYOUSURE_MOVE_THREAD'),
							'<span>'.$lang->def('_TITLE').' : </span> "'.$title.'"'.' '.$lang->def('_FROM_FORUM').' "'.$from_forum.'" '.$lang->def('_TO_FORUM').' "'.$to_forum.'"',
							true,
							getForumBaseUrl('&amp;op=movethread&amp;new_forum='.$id_new_forum.'&amp;id_thread='.$id_thread.'&amp;id_forum='.$id_forum.'&amp;confirm=1'),
							getForumBaseUrl('&amp;op=movethread&amp;id_forum='.$id_forum.'&amp;confirm=0')
						)
			.'</div>', 'content'
		);
	}
	else
	{
		
		$id_forum = importVar('id_forum', true, 0);
		
		$list_forum = array();
		
		$query = "SELECT idForum, title" .
				" FROM ".$GLOBALS['prefix_cms']."_forum";
				
		
		$result = mysql_query($query);
		
		while (list($id_forum_b, $title) = mysql_fetch_row($result))
			$list_forum[$id_forum_b] = $title;
		
		$GLOBALS['page']->add
		(
			getTitleArea($lang->def('_MOVE_THREAD'), 'forum')
			.'<div class="std_block">'
			.Form::openForm('search_forum', getForumBaseUrl('&amp;op=movethread&amp;id_thread='.$id_thread.'&amp;id_forum='.$id_forum.'&amp;action=1'))
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

//---------------------------------------------------------------------------//

function forumDispatch($op) {

	checkMrString();
	checkMrName();

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
   			require_once($GLOBALS['where_framework'].'/lib/lib.download.php' );

			//find file
			list($title, $attach) = mysql_fetch_row(mysql_query("
			SELECT title, attach
			FROM ".$GLOBALS['prefix_cms']."_forummessage
			WHERE idMessage='".(int)$_GET['id']."'"));
			if(!$attach) {
				$GLOBALS['page']->add( getErrorUi('Sorry, such file does not exist!'), 'content');
				return;
			}
			//recognize mime type
			$expFileName = explode('.', $attach);
			$totPart = strtolower(end($expFileName));

			$attach_name_arr=explode("_", $attach);
			unset($attach_name_arr[0], $attach_name_arr[1]);
			$attach_name=implode('', $attach_name_arr);

			$path = '/doceboCms/'.$GLOBALS['cms']['pathforum'];
			//send file
			sendFile($path, $attach, $expFileName[$totPart], $attach_name);
		};break;
		//-----------------------------------------------//
		case "search" : {
			forumsearch();
		};break;
		case "searchmessage" : {
			forumsearchmessage();
		};break;
		case "sesexpired": {
			forumSessionExpired();
		} break;
		//-----------------------------------------------//
	}
}

?>
