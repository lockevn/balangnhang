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
 * @package  admin-library
 * @subpackage module
 * @version  $Id: lib.company.php 905 2007-01-12 11:21:18Z fabio $
 */
 
if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

define("_MESSAGE_UNREADED", 0);
define("_MESSAGE_READED", 1);
define("_MESSAGE_MY", 2);
define("_MESSAGE_VALID", 0);
define("_MESSAGE_DELETED", 1);

// ----------------------------------------------------------------------------

class MessageModule {
	
	function saveMessageAttach($attach) {
	
		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	
		$path = _PATH_MESSAGE;
		$file = '';
		sl_open_fileoperations();
		if(isset($attach['tmp_name']['attach']) && $attach['tmp_name']['attach'] != '') {
	
			$file = getLogUserId().'_'.mt_rand(0, 100).'_'.time().'_'.$attach['name']['attach'];
			if(!sl_upload($attach['tmp_name']['attach'], $path.$file)) {
				$error = 1;
				$file = '';
			}
		}
		sl_close_fileoperations();
		if(!$error) return $file;
		return false;
	}
	
	function deleteAttach($attach) {
	
		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	
		$path = _PATH_MESSAGE;
		sl_open_fileoperations();
		$re = sl_unlink($path.$attach);
		sl_close_fileoperations();
		return $re;
	}
	
	function message() {
		//checkPerm('view');
		require_once($GLOBALS['where_framework'].'/lib/lib.tab.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	
		$lang 		=& DoceboLanguage::createInstance('message', 'lms');
		$send_perm 	= true;//checkPerm('send_all', true) || checkPerm('send_upper', true);
		$out		= $GLOBALS['page'];
		$out->setWorkingZone('content');
		$um =& UrlManager::getInstance("message");
	
		$tab_man = new TabView('course_message', '');
		$inbox_tab = new TabElemDefault(	'inbox',
							$lang->def('_INBOX'),
							getPathImage('fw').'message/inbox.gif');
		$outbox_tab = new TabElemDefault(	'outbox',
							$lang->def('_OUTBOX'),
							getPathImage('fw').'message/outbox.gif');
		$tab_man->addTab($inbox_tab);
		if($send_perm) $tab_man->addTab($outbox_tab);
		$tab_man->parseInput($_POST, $_SESSION);
		$active_tab = $tab_man->getActiveTab();
	
		if($active_tab != 'inbox' && $active_tab != 'outbox') {
	
			$active_tab = importVar('active_tab', false, 'inbox');
			$tab_man->setActiveTab($active_tab);
		}
		$out->add(
			Form::openForm('tab_advice', $um->getUrl())
			.$tab_man->printTabView_Begin('', false), 'content');
	
		$course_man = new Man_Course();
		$all_value = array(0 => $lang->def('_ALL_COURSES'));
		$all_courses = $course_man->getUserCourses( getLogUserId() );
		$all_value = $all_value + $all_courses;
	
	
	  $_filter = importVar('msg_course_filter');
	  if ($_filter=='') {
      if (isset($_SESSION['idCourse'])) { $_filter=$_SESSION['idCourse']; }
      else $_filter=0;
    }
		if (count($all_value) > 0) {
			$out->add(
				Form::getLineDropdown(	'form_line_right',
										'label_padded',
										$lang->def('_FILTER_MESSAGE_FOR'),
										'dropdown_nowh',
										'msg_course_filter',
										'msg_course_filter',
										$all_value,
										/*( isset($_POST['msg_course_filter'])
											? $_POST['msg_course_filter']
											: (isset($_SESSION['idCourse']) ? $_SESSION['idCourse'] : 0 ) ),*/
											$_filter,
										' onchange="form.submit();"',
										' '.Form::getButton( 'refresh_msg_filter', 'refresh_msg_filter', $lang->def('_REFRESH'), 'button_nowh' ),
										'')
				."
				<script type=\"text/javascript\"><!--
					var hide_refresh = document.getElementById('refresh_msg_filter');
					hide_refresh.style.display = 'none';
				--></script>"
				.Form::getBreakRow()
				, 'content');
		}
		else {
			$out->add(Form::getHidden("msg_course_filter", "msg_course_filter", 0));
		}
		
		switch($active_tab) {
			case "inbox" : {
				MessageModule::inbox($all_courses);
			};break;
			case "outbox" : {
				MessageModule::outbox($all_courses);
			};break;
		}
	
		$out->add(
			$tab_man->printTabView_End()
			.Form::closeForm(), 'content');
	}
	
	function inbox(&$course_list) {
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
		$lang 		=& DoceboLanguage::createInstance('message', 'lms');
		$send_perm 	= true;//checkPerm('send_all', true) || checkPerm('send_upper', true);
		$out		= $GLOBALS['page'];
		$out->setWorkingZone('content');
		$um =& UrlManager::getInstance("message");
	
		$tb = new TypeOne(_MESSAGE_VISU_ITEM, $lang->def('_INBOX_CAPTION'), $lang->def('_INBOX_SUMMARY'));
		$tb->initNavBar('ini', 'button');
		$ini = $tb->getSelectedElement();
		$acl_man =& $GLOBALS['current_user']->getAclManager();
	
		$query = "
		SELECT m.idMessage, m.idCourse, m.sender, m.posted, m.attach, m.title, m.priority, user.read
		FROM ".$GLOBALS['prefix_fw']."_message AS m JOIN
			".$GLOBALS['prefix_fw']."_message_user AS user
		WHERE m.idMessage = user.idMessage AND
			m.sender <> '".getLogUserId()."' AND
			user.idUser = '".getLogUserId()."' AND
			user.deleted = '"._MESSAGE_VALID."'";
		/*if(isset($_POST['msg_course_filter']) && ($_POST['msg_course_filter'] != false)) {
			$query .= " AND m.idCourse = '".$_POST['msg_course_filter']."'";
		}*/
		$_filter = importVar('msg_course_filter');
		if(($_filter != '') && ($_filter != '0')) {
			$query .= " AND m.idCourse = '".$_filter."'";
		} else {
      if (isset($_SESSION['idCourse']) && ($_filter=='')) {
        $_filter = $_SESSION['idCourse'];
        $query .= " AND m.idCourse = '".$_filter."'";
      } else $_filter='0';
    }
		$query .= "ORDER BY ";
		if(isset($_POST['ord'])) {
			switch($_POST['ord']) {
				case "pry" : $query .= "m.priority DESC,"; break;
				case "sen" : $query .= "m.sender,"; break;
				case "tit" : $query .= "m.title,"; break;
				case "ath" : $query .= "m.attach DESC,"; break;
				case "rid" : $query .= "user.read,"; break;
			}
		}
		$query .= "m.posted DESC LIMIT $ini,"._MESSAGE_VISU_ITEM;
		$re_message = mysql_query($query);
	
		// -----------------------------------------------------
		$query = "
		SELECT COUNT(*)
		FROM ".$GLOBALS['prefix_fw']."_message AS m JOIN
			".$GLOBALS['prefix_fw']."_message_user AS user
		WHERE m.idMessage = user.idMessage AND
			user.idUser = '".getLogUserId()."' AND
			m.sender <> '".getLogUserId()."'";
		/*if(isset($_POST['msg_course_filter']) && ($_POST['msg_course_filter'] != false)) {
			$query .= " AND m.idCourse = '".$_POST['msg_course_filter']."'";
		}*/
		if(($_filter != '') && ($_filter != '0')) {
			$query .= " AND m.idCourse = '".$_filter."'";
		}
		
		list($tot_message) = mysql_fetch_row(mysql_query($query));
	
		$cont_h = array(
			'<img src="'.getPathImage('fw').'message/unread.gif" title="'.$lang->def('_UNREAD_TITLE').'" alt="'.$lang->def('_ALT_UNREAD').'" />',
			$lang->def('_TITLE'),
			'<img src="'.getPathImage().'standard/attach.gif" title="'.$lang->def('_ATTACH_TITLE').'" alt="'.$lang->def('_ATTACHMENT').'" />',
			$lang->def('_SENDER'),
			$lang->def('_WHEN'),
			'<img src="'.getPathImage().'/standard/rem.gif" title="'.$lang->def('_DEL').'" alt="'.$lang->def('_DEL').'" />'
		);
		$type_h = array('image', '', 'image', '', 'message_posted', 'image');
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);
	
		while( list($id_mess, $id_course, $sender, $posted, $attach, $title, $priority, $read) = mysql_fetch_row($re_message) ) {
	
			$sender_info = $acl_man->getUser($sender, false);
			$author = ( $sender_info[ACL_INFO_LASTNAME].$sender_info[ACL_INFO_FIRSTNAME] == '' ?
						$acl_man->relativeId($sender_info[ACL_INFO_USERID]) :
						$sender_info[ACL_INFO_LASTNAME].' '.$sender_info[ACL_INFO_FIRSTNAME] );
			$cont = array();
			/*
			$cont[] = '<img src="'.getPathImage().'message/priority'.($priority - 1).'.gif" '
				.'title="'.$lang->def('_TITLE_PRY_'.($priority - 1)).'" '
				.'alt="'.$lang->def('_ALT_PRY_'.($priority - 1)).'" />';
			*/
			if($read == _MESSAGE_READED) {
				$cont[] = '<img src="'.getPathImage('fw').'message/read.gif" title="'.$lang->def('_TITLE_READ').'" '
								.'alt="'.$lang->def('_ALT_READ').'" />';
			} elseif($read == _MESSAGE_UNREADED) {
				$cont[] = '<img src="'.getPathImage('fw').'message/unread.gif" title="'.$lang->def('_TITLE_UNREADED').'" '
								.'alt="'.$lang->def('_ALT_UNREADED').'" />';
			}
			$cont[] = '<a href="'.$um->getUrl("op=readmessage&from=out&id_message=".$id_mess).'" '
							.'title="'.$lang->def('_READ_MESS').'">'.$title.'</a>';
	
			if($attach != '') {
				$cont[] = '<img src="'.getPathImage('fw').mimeDetect($attach).'" alt="'.$lang->def('_MIME').'" />';
			} else {
				$cont[] = '&nbsp;';
			}
			$cont[] = $author.' '
				.( ((!isset($_POST['msg_course_filter']) || ($_POST['msg_course_filter'] == false)) && $id_course != 0)
						? '['.$course_list[$id_course].']'
						: '' );
			$cont[] = $GLOBALS['regset']->databaseToRegional($posted);
	
			//$cont[] = '<a href="'.$um->getUrl("op=delmessage&from=out&id_message=".$id_mess).'">'
			$add_filter = '';
			if (($_filter != '') && ($_filter != '0')) $add_filter = "&msg_course_filter=".$_filter;
			$cont[] = '<a href="'.$um->getUrl("op=delmessage&from=out&id_message=".$id_mess.$add_filter)
            .'">'
						.'<img src="'.getPathImage().'/standard/rem.gif"  '
							.'title="'.$lang->def('_DEL').' : '.strip_tags($title).'" '
							.'alt="'.$lang->def('_DEL').' : '.strip_tags($title).'" /></a>';
			$tb->addBody( $cont );
		}
		//if(checkPerm('send_all', true) || checkPerm('send_upper', true)) {
			$tb->addActionAdd('<a href="'.$um->getUrl("op=addmessage&from=out").'">'
				.'<img src="'.getPathImage().'standard/add.gif" title="'.$lang->def('_ADDT').'" alt="'.$lang->def('_ADD').'" />'
				.$lang->def('_SENDMESSAGE').'</a>');
		//}
	
		$out->add(
			'<div class="std_block">');
		if(isset($_GET['result'])) {
			switch($_GET['result']) {
				case "ok" 	: $out->add(getResultUi($lang->def('_SEND_SUCCESS')));break;
				case "ok_del" 	: $out->add(getResultUi($lang->def('_MESSAGE_DELETED')));break;
				case "err" 	: $out->add(getErrorUi($lang->def('_SEND_FAIL')));break;
			}
		}
		$out->add(
			$tb->getTable()
			.$tb->getNavBar($ini, $tot_message)
			.'</div>'
		);
	}
	
	function outbox(&$course_list) {
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
		//if(!checkPerm('send_all', true) && !checkPerm('send_upper', true)) die("You can't access");
	
		$lang 		=& DoceboLanguage::createInstance('message', 'lms');
		$out		= $GLOBALS['page'];
		$out->setWorkingZone('content');
		$um =& UrlManager::getInstance("message");
		$acl_man 	=& $GLOBALS['current_user']->getAclManager();
		$tb = new TypeOne(_MESSAGE_VISU_ITEM, $lang->def('_OUTBOX_CAPTION'), $lang->def('_OUTBOX_SUMMARY'));
		$tb->initNavBar('ini', 'button');
		$ini = $tb->getSelectedElement();
	
	
		$query = "
		SELECT m.idMessage, m.posted, m.attach, m.title, m.priority
		FROM ".$GLOBALS['prefix_fw']."_message AS m JOIN
			".$GLOBALS['prefix_fw']."_message_user AS user
		WHERE m.idMessage = user.idMessage AND
			user.idUser = '".getLogUserId()."' AND
			m.sender = '".getLogUserId()."' AND
			user.deleted = '"._MESSAGE_VALID."'";
		/*if(isset($_POST['msg_course_filter']) && ($_POST['msg_course_filter'] != false)) {
			$query .= " AND m.idCourse = '".$_POST['msg_course_filter']."'";
		}*/
		$_filter = importVar('msg_course_filter');
		if(($_filter != '') && ($_filter != false)) {
			$query .= " AND m.idCourse = '".$_filter."'";
		} else {
      if (isset($_SESSION['idCourse']) && ($_filter!=false)) {
        $_filter = $_SESSION['idCourse'];
        $query .= " AND m.idCourse = '".$_filter."'";
      }
    }
		$query .= "	ORDER BY ";
		if(isset($_POST['ord'])) {
			switch($_POST['ord']) {
				case "pry" : $query .= "m.priority DESC,"; break;
				case "sen" : $query .= "m.sender,"; break;
				case "tit" : $query .= "m.title,"; break;
				case "ath" : $query .= "m.attach DESC,"; break;
			}
		}
		$query .= "m.posted DESC LIMIT $ini,"._MESSAGE_VISU_ITEM;
		$re_message = mysql_query($query);
		
		$query = "
		SELECT COUNT(*)
		FROM ".$GLOBALS['prefix_fw']."_message AS m JOIN
			".$GLOBALS['prefix_fw']."_message_user AS user
		WHERE m.idMessage = user.idMessage AND
			user.idUser = '".getLogUserId()."' AND
			m.sender = '".getLogUserId()."' AND
			user.deleted = '"._MESSAGE_VALID."'";
		/*if(isset($_POST['msg_course_filter']) && ($_POST['msg_course_filter'] != false)) {
			$query .= " AND m.idCourse = '".$_POST['msg_course_filter']."'";
		}*/
		if(($_filter != '') && ($_filter != false)) {
			$query .= " AND m.idCourse = '".$_filter."'";
		}
		list($tot_message) = mysql_fetch_row(mysql_query($query));
	
		$cont_h = array(
			$lang->def('_TITLE'),
			'<img src="'.getPathImage().'standard/attach.gif" title="'.$lang->def('_ATTACH_TITLE').'" alt="'.$lang->def('_ATTACHMENT').'" />',
			$lang->def('_WHEN'),
			$lang->def('_DEST'),
			'<img src="'.getPathImage().'/standard/rem.gif" title="'.$lang->def('_DEL').'" alt="'.$lang->def('_DEL').'" />'
		);
		$type_h = array('', 'image', 'message_posted', 'message_posted', 'image');
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);
	
		while( list($id_mess, $posted, $attach, $title, $priority) = mysql_fetch_row($re_message) ) {
	
			$cont = array();
			/*$cont[] ='<img src="'.getPathImage().'message/priority'.($priority - 1).'.gif" '
				.'title="'.$lang->def('_TITLE_PRY_'.($priority - 1)).'" '
				.'alt="'.$lang->def('_ALT_PRY_'.($priority - 1)).'" />');*/
	
			$cont[] = '<a href="'.$um->getUrl("op=readmessage&id_message=".$id_mess).'" '
							.'title="'.$lang->def('_READ_MESS').'">'.$title.'</a>';
			if($attach != '') {
				$cont[] = '<img src="'.getPathImage('fw').mimeDetect($attach).'" alt="'.$lang->def('_MIME').'" />';
			} else {
				$cont[] = '&nbsp;';
			}
			$cont[] = $GLOBALS['regset']->databaseToRegional($posted);
			
			$sql_receiver = "
				SELECT user.idUser
				FROM ".$GLOBALS['prefix_fw']."_message_user AS user
				WHERE user.idMessage = '".$id_mess."' AND
					user.idUser != '".getLogUserId()."'";

			$result_receiver = mysql_query($sql_receiver);
			$counter_receiver = 0;
			$cont_temp = "";
			while ($receiver = mysql_fetch_array($result_receiver))
			{
				if ($counter_receiver == 0)
				{
					$message_user = $acl_man->getUser($receiver[0], false);
					$username =$acl_man->relativeId($message_user[ACL_INFO_USERID]);
					$cont_temp = $username;
				}
				else
				{
					$message_user = $acl_man->getUser($receiver[0], false);
					$username =$acl_man->relativeId($message_user[ACL_INFO_USERID]);
					$cont_temp .= ", ".$username;
				}
				$counter_receiver++;
			}
			$cont[] = $cont_temp;
			
			//$cont[] = '<a href="'.$um->getUrl("op=delmessage&id_message=".$id_mess.'&out=out').'">'
			$add_filter = '';
			if (($_filter != '') && ($_filter != false)) $add_filter = "&msg_course_filter=".$_filter;
			$cont[] = '<a href="'.$um->getUrl("op=delmessage&id_message=".$id_mess.'&out=out'.$add_filter).'">'
						.'<img src="'.getPathImage().'/standard/rem.gif"  '
							.'title="'.$lang->def('_DEL').' : '.strip_tags($title).'" '
							.'alt="'.$lang->def('_DEL').' : '.strip_tags($title).'" /></a>';
			$tb->addBody( $cont );
		}
		//if(checkPerm('send_all', true) || checkPerm('send_upper', true)) {
			$tb->addActionAdd('<a href="'.$um->getUrl("op=addmessage").'">'
				.'<img src="'.getPathImage().'standard/add.gif" title="'.$lang->def('_ADDT').'" alt="'.$lang->def('_ADD').'" />'
				.$lang->def('_SENDMESSAGE').'</a>');
		//}
	
		$out->add(
			'<div class="std_block">'
		);
		if(isset($_GET['result'])) {
			switch($_GET['result']) {
				case "ok" 	: $out->add(getResultUi($lang->def('_SEND_SUCCESS')));break;
				case "ok_del" 	: $out->add(getResultUi($lang->def('_MESSAGE_DELETED')));break;
				case "err" 	: $out->add(getErrorUi($lang->def('_SEND_FAIL')));break;
			}
		}
	
		$out->add(
			Form::getHidden('active_tab','active_tab', 'outbox')
			.$tb->getTable()
			.$tb->getNavBar($ini, $tot_message)
			.'</div>'
		);
	}
	
	function addmessage() {
		$send_all 		=true;// checkPerm('send_all', true);
		$send_upper 	=true;// checkPerm('send_upper', true);
		if(!$send_all && !$send_upper) die("You can't access");
	
		require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	
		$lang 		=& DoceboLanguage::createInstance('message', 'lms');
		$out		= $GLOBALS['page'];
		$out->setWorkingZone('content');
		$from = importVar('out');
		$um =& UrlManager::getInstance("message");
	
		$aclManager 	= new DoceboACLManager();
		$user_select 	= new Module_Directory();
	
		$user_select->show_user_selector = TRUE;
		$user_select->show_group_selector = TRUE;
		$user_select->show_orgchart_selector = FALSE;
	
		$user_select->nFields = 0;
	
		if(isset($_POST['message']['recipients'])) {
	
			$recipients = unserialize(urldecode($_POST['message']['recipients']));
			$user_select->resetSelection($recipients);
		}
	
		$me = array(getLogUserId());
	
		$course_man = new Man_Course();
		$all_value = array(0 => $lang->def('_ALL_COURSES'));
		$all_courses = $course_man->getUserCourses( getLogUserId() );
		$all_value = $all_value + $all_courses;
	
		if (count($all_value) > 0) {
			$drop = Form::getLineDropdown(	'form_line_right',
										'label_padded',
										$lang->def('_FILTER_MESSAGE_FOR'),
										'dropdown_nowh',
										'msg_course_filter',
										'msg_course_filter',
										$all_value,
										( isset($_POST['msg_course_filter'])
											? $_POST['msg_course_filter']
											: ( isset($_SESSION['idCourse']) ? $_SESSION['idCourse'] : 0 ) ),
										'',
										' '.Form::getButton( 'refresh_msg_filter', 'refresh_msg_filter', $lang->def('_REFRESH'), 'button_nowh' ),
										'');
			$drop .= "
				<script type=\"text/javascript\"><!--
					var hide_refresh = document.getElementById('refresh_msg_filter');
					hide_refresh.style.display = 'none';
					var option_elem = document.getElementById('msg_course_filter');
					option_elem.onchange = function() {
						var hide_refresh = document.getElementById('refresh_msg_filter');
						hide_refresh.click();
					}
				--></script>";
			$user_select->addFormInfo($drop);
		}
		else {
			$user_select->addFormInfo(Form::getHidden("msg_course_filter", "msg_course_filter", 0));
		}
	
		$user_select->setUserFilter('exclude', $me);
		if(isset($_POST['msg_course_filter'])) $filter = $_POST['msg_course_filter'];
		elseif(isset($_GET['set_course_filter'])) $filter = $_GET['set_course_filter'];
		else $filter = 0;
		
		if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
			if($filter != 0) {
				
				$arr_idstGroup = $aclManager->getGroupsIdstFromBasePath('/lms/course/'.$_POST['msg_course_filter'].'/subscribed/');
				
				$user_select->setUserFilter('group', $arr_idstGroup);
				$user_select->setGroupFilter('path', '/lms/course/'.$_POST['msg_course_filter'].'/group');
			} else {
				$tot_user = array();
				$tot_group = array();
				$user_select->show_only_group_name = true;
					foreach($all_courses as $id => $name) {
		
					$arr_idstGroup = $aclManager->getGroupsIdstFromBasePath('/lms/course/'.$id.'/subscribed/');
					$tot_user = $tot_user + $arr_idstGroup;
		
					//$arr_idstCourseG = $aclManager->getGroupsIdstFromBasePath('/lms/course/'.$id.'/group', array('course'));
					//$tot_group = $tot_group + $arr_idstCourseG;
					$user_select->setGroupFilter('path', '/lms/course/'.$id.'/group');
				}
				require_once($GLOBALS["where_framework"]."/lib/lib.myfriends.php");
				$myfriends =new MyFriends(getLogUserId());
				$friends =$myfriends->getFriendsList(FALSE, FALSE, 0, TRUE);
				$tot_user =$tot_user+$friends;
				$user_select->setUserFilter('user', $tot_user);
				if(empty($all_courses)) $user_select->show_group_selector = false;
				
			}
		}
		
		//$user_select->requested_tab = PEOPLEVIEW_TAB;
		$id_forward=importVar('id_forward',true,0);
		
		$user_select->setPageTitle(
			MessageModule::messageGetTitleArea(array($um->getUrl(( $from == 'out' ? '&active_tab=outbox' : '' )) => $lang->def('_MESSAGE'),
			$lang->def('_SEND') ),
			'forum'));
		$user_select->loadSelector($um->getUrl('op=addmessage&id_forward='.$id_forward.''.( $from == 'out' ? '&from=out' : '' )),
				false,
				$lang->def('_SEND_TO'),
				true,
				true );
		
	}

	function writemessage() {
		$send_all 		=true;// checkPerm('send_all', true);
		$send_upper 	=true;// checkPerm('send_upper', true);
		if(!$send_all && !$send_upper) die("You can't access");
	
		require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
		$lang 		=& DoceboLanguage::createInstance('message', 'lms');
		$out		= $GLOBALS['page'];
		$out->setWorkingZone('content');
		$from 		= importVar('out');
		$acl_man 	=& $GLOBALS['current_user']->getAclManager();
		$um =& UrlManager::getInstance("message");
	
		if(!isset($_POST['message']['recipients'])) {
	
			if(isset($_GET['reply_recipients'])) {
				$user_selected = unserialize(stripslashes(urldecode($_GET['reply_recipients'])));
				$recipients = urlencode(serialize($user_selected));
			} else {
				$user_select 	= new Module_Directory();
				$user_selected = $user_select->getSelection($_POST);
				$recipients = urlencode(serialize($user_selected));
			}
		} else {
			$user_selected = unserialize(urldecode($_POST['message']['recipients']));
			$recipients = urlencode($_POST['message']['recipients']);
		}
		$out->add(
			MessageModule::messageGetTitleArea(array($um->getUrl(( $from == 'out' ? '&active_tab=outbox' : '' )) => $lang->def('_MESSAGE'),
				$lang->def('_SENDMESSAGE')) ,'message')
			.'<div class="std_block">');
	
		if(isset($_POST['send'])) {
	
			if($_POST['message']['subject'] == '') {
				$out->add(getErrorUi($lang->def('_MUST_INS_SUBJECT')));
			} else {
				// send message
				$attach = '';
				if($_FILES['message']['tmp_name']['attach'] != '') {
					$attach = MessageModule::saveMessageAttach($_FILES['message']);
				}
	
				$query_mess = "
				INSERT INTO ".$GLOBALS['prefix_fw']."_message
				( idCourse, sender, posted, title, textof, attach, priority ) VALUES
				(
					'".$_POST['msg_course_filter']."',
					'".getLogUserId()."',
					'".date("Y-m-d H:i:s")."',
					'".$_POST['message']['subject']."',
					'".$_POST['message_textof']."',
					'".addslashes($attach)."',
					'".$_POST['message']['priority']."'
				)";
	
				if(!mysql_query($query_mess)) {
	
					if($attach) deleteAttach($attach);
	
					jumpTo($um->getUrl('result=err'));
				}
				list($id_message) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	
				if(!in_array(getLogUserId(), $user_selected)) $user_selected[] = getLogUserId();
				$send_to_idst =& $acl_man->getAllUsersFromIdst($user_selected);
	
				$re = true;
				$recip_alert = array();
				if(is_array($send_to_idst)) {
	
					$logged_user =  getLogUserId();
					while(list(, $id_recipient) = each($send_to_idst)) {
	
						$query_recipients = "
						INSERT INTO ".$GLOBALS['prefix_fw']."_message_user
						( idMessage, idUser, idCourse, `read` ) VALUES
						(
							'".$id_message."',
							'".$id_recipient."',
							'".$_POST['msg_course_filter']."',
							'".( $id_recipient == $logged_user ? _MESSAGE_MY : _MESSAGE_UNREADED  )."'
						) ";
						$re_single = mysql_query($query_recipients);
						if($re_single && $id_recipient != $logged_user) {
							$recip_alert[] = $id_recipient;
						}
						$re &= $re_single;
					}
					if(!empty($recip_alert)) {
	
						require_once($GLOBALS['where_lms'] . '/lib/lib.course.php');
						require_once($GLOBALS['where_framework'] . '/lib/lib.eventmanager.php');
						if ((isset($_SESSION['idCourse'])) && (isset($GLOBALS['course_descriptor'])))
							$course_name = $GLOBALS['course_descriptor']->getValue('name');
						else $course_name = $lang->def('_NOTIN_COURSE');
						// message to user that is odified
						$msg_composer = new EventMessageComposer('message', 'lms');
	
						$msg_composer->setSubjectLangText('email', '_YOU_RECIVE_MSG_SUBJECT', false);
						$msg_composer->setBodyLangText('email', '_YOU_RECIVE_MSG_TEXT', array(	'[url]' => _MESSAGE_PL_URL,
																									'[course]' => $course_name,
																									'[from]' => $GLOBALS['current_user']->getUsername() ) );
	
						$msg_composer->setSubjectLangText('sms', '_YOU_RECIVE_MSG_SUBJECT_SMS', false);
						$msg_composer->setBodyLangText('sms', '_YOU_RECIVE_MSG_TEXT_SMS', array(	'[url]' => _MESSAGE_PL_URL,
																									'[course]' => $course_name,
																									'[from]' => $GLOBALS['current_user']->getUsername() ) );
	
						createNewAlert(	'MsgNewReceived', 'directory', 'moderate', '1', 'User group subscription to moderate',
									$recip_alert, $msg_composer );
	
					}
				}
				jumpTo($um->getUrl('result='.( $re ? 'ok' : 'err' )));
			}
		}
		$prio_arr = array(
			'5' => $lang->def('_VERYHIGH'),
			'4' => $lang->def('_HIGH'),
			'3' => $lang->def('_NORMAL'),
			'2' => $lang->def('_LOW'),
			'1' => $lang->def('_VERYLOW')
		);
	
		$first = true;
		$attach = '';
	
		if(!is_array($user_selected) || empty($user_selected)) {
	
			$out->add(
				'<span class="text_bold">'.$lang->def('_NO_RECIPIENTS_SELECTED').'</span>'
				.Form::openForm('message', $um->getUrl('op=writemessage'), false, false, 'multipart/form-data')
				.Form::getHidden('out', 'out', $from)
				.Form::getHidden('msg_course_filter', 'msg_course_filter', $_POST['msg_course_filter'])
				.Form::getHidden('message_recipients', 'message[recipients]', $recipients)
				.Form::openButtonSpace()
				.Form::getButton('back_recipients', 'back_recipients', $lang->def('_BACK_RECIPIENTS'))
				.Form::closeButtonSpace()
				.Form::closeForm()
			);
			return;
		}
	
		$only_users =& $acl_man->getUsers($user_selected);
		$only_groups = $acl_man->getGroups($user_selected);
	
		$out->add(
			'<span class="text_bold">'.$lang->def('_RECIPIENTS').'</span>'
			.'<div class="recipients">');
	
	
		if(is_array($only_groups) && !empty($only_groups)) {
	
			$out->add('<strong>');
			while(list(, $group_info) = each($only_groups)) {
				if($first) $first = false;
				else $attach = ', ';
	
				$groupid = substr($group_info[ACL_INFO_GROUPID], strrpos($group_info[ACL_INFO_GROUPID], '/')+1);
				$out->add( $attach.$groupid);
	
				// find user of group
				$members = $acl_man->getGroupAllUser($group_info[ACL_INFO_IDST]);
				$group_users =& $acl_man->getUsers($members);
				$out->add(' <span class="message_group_members">( ');
				$m_first = true;
				while(list(, $user_info) = each($group_users)) {
					if($m_first) $m_first = false;
					else $attach = ', ';
					$out->add( $attach
							.( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
									? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
									: $acl_man->relativeId($user_info[ACL_INFO_USERID]) ));
				}
				$out->add(' )</span> ');
	
			}
			$out->add('</strong>');
		}
		$acl_man->setContext('/');
		if(is_array($only_users) && !empty($only_users))
		while(list(, $user_info) = each($only_users)) {
			if($first) $first = false;
			else $attach = ', ';
			$out->add( $attach
					.( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
							? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
							: $acl_man->relativeId($user_info[ACL_INFO_USERID]) ));
		}
		$id_forward=importVar('id_forward',true,0);
		$sql_text = "SELECT message.textof, message.title FROM ".$GLOBALS['prefix_fw']."_message AS message WHERE message.idMessage = '".$id_forward."'";
		$title = '';
		$text_message = '';
		if ($message_forward = mysql_fetch_row(mysql_query($sql_text)))
		{
			list($text_message, $title) = $message_forward;
			$title = 'Reply: '.$title;
				$text_message = '<br /><br /><font color="#808080">-------<br /><br />'.$text_message.'</font>';
		}
		$out->add(
			'</div><br />'
			.Form::openForm('message', $um->getUrl('op=writemessage'), false, false, 'multipart/form-data')
			.Form::getHidden('out', 'out', $from)
			.Form::getHidden('msg_course_filter', 'msg_course_filter', $_POST['msg_course_filter'])
			.Form::getHidden('message_recipients', 'message[recipients]', $recipients)
	
			.Form::getTextfield($lang->def('_SUBJECT'), 'message_subject', 'message[subject]', 255,
				( isset($_POST['message']['subject']) ? $_POST['message']['subject'] : "$title" ) )
	
			.Form::getDropdown($lang->def('_PRIORITY'), 'message_priority', 'message[priority]', $prio_arr,
				( isset($_POST['message']['priority']) ? $_POST['message']['priority'] : 3 ) )
	
			.Form::getTextarea($lang->def('_TEXTOF'), 'message_textof', 'message_textof',
				( isset($_POST['message_textof']) ? $_POST['message_textof'] : "$text_message" ) )
	
			.Form::getFilefield($lang->def('_ATTACHMENT'), 'message_attach', 'message[attach]', 255 )
			.Form::openButtonSpace()
			.Form::getButton('back_recipients', 'back_recipients', $lang->def('_BACK_RECIPIENTS'))
	
			.Form::getButton('send', 'send', $lang->def('_SEND'))
			.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
			.Form::closeButtonSpace()
			.Form::closeForm()
			.'</div>'
		);
	}
	
	function delmessage() {
		//checkPerm('view');
	
		$lang 		=& DoceboLanguage::createInstance('message', 'lms');
		$out		= $GLOBALS['page'];
		$out->setWorkingZone('content');
		$um =& UrlManager::getInstance("message");
	
		$from = importVar('out');
	
		if(isset($_GET['confirm'])) {
	
			$re = true;
			$del_query = "
			UPDATE ".$GLOBALS['prefix_fw']."_message_user
			SET deleted = '"._MESSAGE_DELETED."'
			WHERE idUser='".getLogUserId()."' AND idMessage = '".(int)$_GET['id_message']."'";
			if(!mysql_query($del_query)) {
				if ($from === 'out')
					jumpTo($um->getUrl('&active_tab=outbox&result=err'));
				jumpTo($um->getUrl('&active_tab=inbox&result=err'));
				//jumpTo($um->getUrl(( $from == 'out' ? '&active_tab=outbox' : '').'&result=err'));
			}
	
			$query = "
			SELECT idMessage
			FROM ".$GLOBALS['prefix_fw']."_message_user
			WHERE idMessage = '".(int)$_GET['id_message']."'";
			if(!mysql_num_rows(mysql_query($query))) {
	
				list($attach) = mysql_fetch_row(mysql_query("
				SELECT attach
				FROM ".$GLOBALS['prefix_fw']."_message
				WHERE idMessage = '".$_GET['id_message']."'"));
				if($attach != '' ) {
	
					if(!deleteAttach($attach)) {
						if ($from === 'out')
							jumpTo($um->getUrl('&active_tab=outbox&result=err'));
						jumpTo($um->getUrl('&active_tab=inbox&result=err'));
						//jumpTo($um->getUrl(( $from == 'out' ? '&active_tab=outbox' : '').'&result=err'));
					}
				}
				if(!mysql_query("
				DELETE FROM ".$GLOBALS['prefix_fw']."_message_user
				WHERE idMessage = '".$_GET['id_message']."'")) {
					if ($from === 'out')
						jumpTo($um->getUrl('&active_tab=outbox&result=err'));
					jumpTo($um->getUrl('&active_tab=inbox&result=err'));
					//jumpTo($um->getUrl(( $from == 'out' ? '&active_tab=outbox' : '').'&result=err'));
				}
				if(!mysql_query("
				DELETE FROM ".$GLOBALS['prefix_fw']."_message
				WHERE idMessage = '".$_GET['id_message']."'")) {
					if ($from === 'out')
						jumpTo($um->getUrl('&active_tab=outbox&result=err'));
					jumpTo($um->getUrl('&active_tab=inbox&result=err'));
					//jumpTo($um->getUrl(( $from == 'out' ? '&active_tab=outbox' : '').'&result=err'));
				}
			}
			
      $_filter = importVar('msg_course_filter');
			if (($_filter != '') && ($_filter != false)) { $add_filter = "&msg_course_filter=".$_filter; }
			else $add_filter = '';
			
			if ($from === 'out')
				jumpTo($um->getUrl('&active_tab=outbox&result=ok_del'.$add_filter));
			jumpTo($um->getUrl('&active_tab=inbox&result=ok_del'.$add_filter));
			//jumpTo($um->getUrl(( $from == 'out' ? '&active_tab=outbox' : '').'&result=ok_del'));
		} else {
			list($title) = mysql_fetch_row(mysql_query("
			SELECT title
			FROM ".$GLOBALS['prefix_fw']."_message
			WHERE idMessage = '".$_GET['id_message']."'"));
	
			$page_title = array(
				$um->getUrl(( $from == 'out' ? '&active_tab=outbox' : '' )) => $lang->def('_MESSAGE'),
				$lang->def('_DEL_MESSAGE')
			);
			
			$_filter = importVar('msg_course_filter');
			$add_filter = '';
			if (($_filter != '') && ($_filter != false)) $add_filter = "&msg_course_filter=".$_filter;
			
			$out->add(
				MessageModule::messageGetTitleArea($page_title, 'message')
				.'<div class="std_block">'
				.getDeleteUi(	$lang->def('_AREYOUSURE'),
								'<span>'.$lang->def('_TITLE').' : </span> "'.$title,
								true,
								$um->getUrl('op=delmessage&id_message='.$_GET['id_message']
									.( $from == 'out' ? '&out=out' : '').'&confirm=1'.$add_filter),
								$um->getUrl(( $from == 'out' ? '&active_tab=outbox' : ''))
							)
				.'</div>'
			);
		}
	}
	
	//-----------------------------------------------------------------//
	
	function readmessage() {
		//checkPerm('view');
	
		$lang 		=& DoceboLanguage::createInstance('message', 'lms');
		$out		= $GLOBALS['page'];
		$out->setWorkingZone('content');
		$um=& UrlManager::getInstance("message");
	
		$acl_man =& $GLOBALS['current_user']->getAclManager();
		$from = importVar('out');
	
		$re_user = mysql_query("
		UPDATE ".$GLOBALS['prefix_fw']."_message_user AS user
		SET user.read = '"._MESSAGE_READED."'
		WHERE user.idMessage = '".$_GET['id_message']."' AND user.idUser = '".getLogUserId()."' AND user.read = '"._MESSAGE_UNREADED."' ");
	
		list($sender, $posted, $title, $textof, $attach, $priority) = mysql_fetch_row(mysql_query("
		SELECT sender, posted, title, textof, attach, priority
		FROM ".$GLOBALS['prefix_fw']."_message
		WHERE idMessage = '".$_GET['id_message']."'"));
	
		$sender_info = $acl_man->getUser($sender, false);
	
		$author = ( $sender_info[ACL_INFO_LASTNAME].$sender_info[ACL_INFO_FIRSTNAME] == '' ?
						$acl_man->relativeId($sender_info[ACL_INFO_USERID]) :
						$sender_info[ACL_INFO_LASTNAME].' '.$sender_info[ACL_INFO_FIRSTNAME] );
	
		$page_title = array(
			$um->getUrl(( $from == 'out' ? '&active_tab=outbox' : '' )) => $lang->def('_MESSAGE'),
			$lang->def('_READ_MESSAGE').' : '.$title
		);
		$out->add(
			MessageModule::messageGetTitleArea($page_title, 'message')
			.'<div class="std_block">'
			.'<h2 class="message_title"><b>'.$lang->def('_TITLE').' : </b>'.$title.' '
				.'<span class="send_date">( '.$GLOBALS['regset']->databaseToRegional($posted).' )</span></h2>'
			.'<p class="message_sender"><b>'.$lang->def('_SENDER').' : </b>'.$author.'</p>'
			.'<p class="message_textof"><b>'.$lang->def('_TEXTOF').' : </b></p>'
			.'<div>'.$textof.'</div>'
			.( $attach != ''
				? '<div class="message_attach"><span class="text_bold">'.$lang->def('_ATTACHMENT').' : </span>'
					.'<a href="'.$um->getUrl('op=download&id_message='.$_GET['id_message']).'">'
					.'<img src="'.getPathImage('fw').mimeDetect($attach).'" alt="'.$lang->def('_MIME').'" />'.$attach.'</a></div>'
				: '' )
		);
		//if(checkPerm('send_all') && isset($_GET['from'])) {
			$sender_arr[$sender_info[ACL_INFO_IDST]] = $sender_info[ACL_INFO_IDST];
			if ($sender == getLogUserId())
			{
				$out->add('<p class="message_reply"><a href="'.$um->getUrl("op=addmessage&id_forward=".$_GET['id_message']."").'">'.$lang->def('_FORWARD').'</a></p>');
			} 
			else
			{
				$out->add('<p class="message_reply"><a href="'.$um->getUrl('op=writemessage&reply_recipients='
						.urlencode(serialize($sender_arr))).'">'.$lang->def('_REPLY').'</a></p>');
			}
		//}
		$out->add('</div>');
	}
	
	function download() {
		//checkPerm('view');
	
		require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
	
		//find selected file
	
		list($filename) = mysql_fetch_row(mysql_query("
		SELECT attach
		FROM ".$GLOBALS['prefix_fw']."_message
		WHERE idMessage = '".$_GET['id_message']."'"));
	
		if(!$filename) {
			$GLOBALS['page']->add(getErrorUi('Sorry, such file does not exist!'), 'content');
			return;
		}
		//recognize mime type
		$extens = array_pop(explode('.', $filename));
		sendFile(_PATH_MESSAGE, $filename, $extens);
	}
	
	
	function messageGetTitleArea($text, $image = '', $alt_image = '') {
		$res="";
	
		if ($GLOBALS["platform"] == "cms") {
			$res=getCmsTitleArea($text, $image = '', $alt_image = '');
		}
		else {
			$res=getTitleArea($text, $image = '', $alt_image = '');
		}
	
		return $res;
	}
	
	function quickSendMessage($sender, $recipients, $subject, $textof) {
	
		if(!is_array($recipients)) $recipients = array($recipients);
		
		$query_mess = "
		INSERT INTO ".$GLOBALS['prefix_fw']."_message
		( idCourse, sender, posted, title, textof, attach, priority ) VALUES
		(
			'0',
			'".$sender."',
			'".date("Y-m-d H:i:s")."',
			'".$subject."',
			'".$textof."',
			'',
			'3'
		)";
		if(!mysql_query($query_mess)) return false;
		list($id_message) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	
		$re = true;
		$recipients[] = getLogUserId();
		$logged_user =  getLogUserId();
		while(list(, $id_recipient) = each($recipients)) {

			$query_recipients = "
			INSERT INTO ".$GLOBALS['prefix_fw']."_message_user
			( idMessage, idUser, idCourse, `read` ) VALUES
			(
				'".$id_message."',
				'".$id_recipient."',
				'0',
				'".( $id_recipient == $logged_user ? _MESSAGE_MY : _MESSAGE_UNREADED  )."'
			) ";
			$re &= mysql_query($query_recipients);
		}
		return $re;
	}
	
}


function messageDispatch($op) {

	if(isset($_POST['undo'])) 	$op = 'message';
	if(isset($_POST['okselector'])) 	$op = 'writemessage';
	if(isset($_POST['cancelselector'])) $op = 'message';
	if(isset($_POST['back_recipients'])) $op = 'addmessage';
	switch($op) {
		case "message" : {
			MessageModule::message();
		};break;
		case "addmessage" : {
			MessageModule::addmessage();
		};break;
		case "writemessage" : {
			MessageModule::writemessage();
		};break;
		case "delmessage" : {
			MessageModule::delmessage();
		};break;
		case "readmessage" : {
			MessageModule::readmessage();
		};break;
		case "download" : {
			MessageModule::download();
		};break;
	}
}



// ----------------------------------------------------------------------------



class Man_Message {

	function getCountUnreaded($id_user, $courses, $last_access, $return_sum = false) {

		if($return_sum === true) $unreaded = 0;
		else $unreaded = array();

		$query_unreaded = "
		SELECT user.idCourse, COUNT(*)
		FROM ".$GLOBALS['prefix_fw']."_message_user AS user
		WHERE user.idUser = '".$id_user."' AND user.read = '"._MESSAGE_UNREADED."' AND user.deleted = '"._MESSAGE_VALID."'
		GROUP BY user.idCourse ";
		$re_message = mysql_query($query_unreaded);
		while(list($id_c, $message) = mysql_fetch_row($re_message)) {

			if($return_sum === true) $unreaded += $message;
			else $unreaded[$id_c] = $message;
		}
		if ($unreaded != 0)
			return '<b>'.$unreaded.'</b>';
		else
			return $unreaded;
	}

}

?>
