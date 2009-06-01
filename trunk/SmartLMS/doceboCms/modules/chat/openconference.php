<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebolms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

define("_IS_MODERATOR", 'moderate');

addCss('style_openconference');

function openconference() {
	$lang=& DoceboLanguage::createInstance('doceboconference', 'cms');

	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_scs'].'/lib/lib.openconference.php');
	require_once($GLOBALS["where_scs"]."/lib/lib.booking.php");
	require_once($GLOBALS["where_scs"]."/lib/lib.roomperm.php");
	
	$conference 	= new OpenConferenceManager();
	$is_moderator = checkOpenconferencePerm(_IS_MODERATOR, true);
	if($is_moderator) $role = 2;
	else $role = 1;
	
	$GLOBALS['page']->add(
		getCmsTitleArea($lang->def('_OPENCONFERENCE_TITLE'), 'openconference')
		.'<div class="std_block">', 'content');

	$re_room 		= $conference->roomActive($GLOBALS['pb'], 'cms', date("Y-m-d H:i:s"));
	$room_number 	= $conference->totalRoom($re_room);

	if($room_number == 0) {
		
		// no rooms
		$GLOBALS['page']->add('<strong id="noroom">'.$lang->def('_NO_ROOM_AVAILABLE').'</strong>', 'content');
	} else {
		
		// list rooms active in this moment
		$tb = new TypeOne(0, $lang->def('_ROOM_AVAILABLE'), $lang->def('_SUMMARY_ROOM_AVAILABLE'));
		$tb->setTableStyle('soft-table active');

		$cont_h = array($lang->def('_ROOM_TITLE'), 
						$lang->def('_START_DATE'),
						$lang->def('_END_TIME'), 
						$lang->def('_ROOM_LOGIN') 
						
		);
		$type_h = array('table_main_colum', 'align_center nowrap', 'align_center nowrap', 'align_center');
		if ($is_moderator) {
			$cont_h[]="<img src=\"".getPathImage()."chat/user_perm.gif\" alt=\"".$lang->def('_SET_ROOM_VIEW_PERM')."\" title=\"".$lang->def('_SET_ROOM_VIEW_PERM')."\" />";		
			$type_h[]="image";
		}
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);

		$roomperm = new RoomPermissions(0, "openconference");
		while($room = $conference->nextRow($re_room)) {

			$room_id = $room[OC_ROOM_ID];
			$user_idst = getLogUserId();
			$perm = "view";
			$roomperm->setRoomId($room_id);
			$all_perm = $roomperm->getAllPerm();
			$can_view = ( isset($all_perm[$perm]) ? checkRoomPerm($all_perm[$perm], $user_idst) : TRUE );

			if(($can_view) || ($is_moderator)) {

				$login_info = $conference->loginIntoRoom($room_id,
										$role,
										getLogUserId(),
										$GLOBALS['current_user']->getUserName() );
				$open_for = fromDatetimeToTimestamp($room[OC_ROOM_END_DATE]) - time();
				
				$cont = array();
				$cont[] = $room[OC_ROOM_NAME];
				$cont[] = $GLOBALS['regset']->databaseToRegional($room[OC_ROOM_START_DATE]);
				if(($open_for / 60) > 60) {
					$cont[] = $GLOBALS['regset']->databaseToRegional($room[OC_ROOM_END_DATE]);
				} else {
					$cont[] = (int)($open_for / 60).$lang->def('_MINUTE_SYMBOL').' '.($open_for % 60).$lang->def('_SECOND_SYMBOL');
				}
				
				$login_info['url'] = 'index.php?mn=chat&amp;pi='.getPI().'&amp;type=openconf&amp;op=open_conference_module&amp;id_room='.$room[OC_ROOM_ID];
				
				// check for login error
				if($login_info['errorcode'] != 0) $cont[] = '&nbsp;';
				else {
					// show room link
					if($login_info['fullroom'] == 1) {

						$cont[] = $lang->def('_FULLROOM');
					} else {

						$can_enter = TRUE;
						if ($room[OC_ROOM_BOOKABLE] == 1) {
							
							$cb = new ChatBooking("openconference");
							$room_sub = $cb->getRoomSubscriptions($room_id);
							if ((!isset($room_sub[$user_idst])) || ($room_sub[$user_idst]["approved"] != 1))
								$can_enter=FALSE;
						}
						if (($can_enter) || ($is_moderator)) {

							$cont[] = '<a class="goto_conference" href="'.$login_info['url'].'"
										onclick="window.open(\''.$login_info['url'].'\', \'TeleSkill\', \'location=0,status=1,menubar=0,toolbar=0,resizable=1,scrollbars=1,width=1000,height=700\'); return false;"
										onkeypress="window.open(\''.$login_info['url'].'\', \'TeleSkill\', \'location=0,status=1,menubar=0,toolbar=0,resizable=1,scrollbars=1,width=1000,height=700\'); return false;">'
									.$lang->def('_ACCESS_TO_CONFERENCE')
									.'</a>';
						} else {
							$cont[]='<a href="index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=bookroom&amp;roomid="'.$room_id.'>'.$lang->def('_ACCESS_WITH_BOOKING').'</a>';
						}
						if ($is_moderator) {
							$title=$lang->def('_SET_ROOM_VIEW_PERM');
							$img="<img src=\"".getPathImage()."chat/user_perm.gif\" alt=\"".$title."\" title=\"".$title."\" />";
							$url="index.php?mn=chat&amp;&amp;pi=".getPI()."&amp;type=openconf&amp;op=setroomviewperm&amp;roomid=".$room_id;
							$cont[]="<a href=\"".$url."\">".$img."</a>\n";
						}
					}
					$tb->addBody($cont);
				}
			}
		} // end while
		
		$GLOBALS['page']->add($tb->getTable(), 'content');
	}
	// ------------------------------------------------------
	$re_next_room = $conference->roomPlanned($GLOBALS['pb'], 'cms', date("Y-m-d H:i:s"));
	if($conference->totalRoom($re_next_room) > 0) {

		$cb = new ChatBooking("openconference");
		$tbf = new TypeOne(0, $lang->def('_ROOM_PLANNED'), $lang->def('_SUMMARY_ROOM_PLANNED'));
		$tbf->setTableStyle('soft-table future');
		$cont_h = array(	$lang->def('_TH_ROOM_TITLE'),
							$lang->def('_TH_START_DATE'),
							$lang->def('_TH_DURATION'),
							"&nbsp;");
		$type_h = array('room_title', 'openconference_date', 'openconference_date', 'image');
		if ($is_moderator) {
			$cont_h[]="";
			$type_h[]="image";
			$cont_h[]="";
			$type_h[]="image";
			$cont_h[]="";
			$type_h[]="image";
		}
		$tbf->setColsStyle($type_h);
		$tbf->addHead($cont_h);

		$roomperm = FALSE;
		while($room = $conference->nextRow($re_next_room)) {

			$room_id=$room[OC_ROOM_ID];
			$room_sub=$cb->getRoomSubscriptions($room_id);
			$user_idst=$GLOBALS["current_user"]->getIdSt();

			if ($roomperm === FALSE)
				$roomperm=new RoomPermissions($room_id, "openconference");
			else
				$roomperm->setRoomId($room_id);

			$all_perm=$roomperm->getAllPerm();
			$perm="view";
			$can_view=(isset($all_perm[$perm]) ? checkRoomPerm($all_perm[$perm], $user_idst) : TRUE);

			if (($can_view) || ($is_moderator)) {

				$open_for = fromDatetimeToTimestamp($room[OC_ROOM_END_DATE]) - fromDatetimeToTimestamp($room[OC_ROOM_START_DATE]);
				$distance = getArrayGap(fromDatetimeToTimestamp($room[OC_ROOM_START_DATE]), time());

				$cont = array();
				$cont[] = $room[OC_ROOM_NAME];
				$cont[] = $GLOBALS['regset']->databaseToRegional($room[OC_ROOM_START_DATE]).' ';
				if(($open_for / 60) > 60) {
					$cont[] = $GLOBALS['regset']->databaseToRegional($room[OC_ROOM_END_DATE]);
				} else {
					$cont[] = (int)($open_for / 60).$lang->def('_MINUTE_SYMBOL');
				}

				if ($room[OC_ROOM_BOOKABLE] == 1) {

					if ($is_moderator) {

						$room_sub=$cb->getRoomSubscriptions($room_id, "approved='0'");
						$to_approve=count($room_sub);

						if ($to_approve > 0) {
							$title=$lang->def('_MANAGE_SUBSCRIPTION').": ".$to_approve;
							$img="<img src=\"".getPathImage()."chat/booking_on.gif\" alt=\"".$title."\" title=\"".$title."\" />";
						}
						else {
							$title=$lang->def('_NO_NEW_SUBSCRIPTION');
							$img="<img src=\"".getPathImage()."chat/booking_off.gif\" alt=\"".$title."\" title=\"".$title."\" />";
						}
						$url="index.php?mn=chat&amp;&amp;pi=".getPI()."&amp;type=openconf&amp;op=managesub&amp;roomid=".$room_id;
						$cont[]="<a href=\"".$url."\">".$img."</a>\n";
					}
					else if ((isset($room_sub[$user_idst])) && ($room_sub[$user_idst]["approved"] == 1)) {
						$cont[]=$lang->def('_SUBSCRIPTION_APPROVED')."\n";
					}
					else if ((isset($room_sub[$user_idst])) && ($room_sub[$user_idst]["approved"] != 1)) {
						$cont[]=$lang->def('_WAITING_APPROVAL')."\n";
					}
					else {

						$remaining=$room[OC_ROOM_MAX_USER]-count($room_sub);
						$remaining_txt="(".$remaining." ".$lang->def('_REMAINING').")\n";

						if ($remaining > 0) {
							$url="index.php?mn=chat&amp;&amp;pi=".getPI()."&amp;type=openconf&amp;op=bookroom&amp;roomid=".$room_id;
							$cont[]="<a href=\"".$url."\">".$lang->def('_BOOK_ROOM')."</a>\n".$remaining_txt;
						}
						else
							$cont[]="<strike>".$lang->def('_BOOK_ROOM')."</strike> ".$remaining_txt;
					}
				} else {
					$cont[]="&nbsp;";
				}

				if ($is_moderator) {
					$title=$lang->def('_SET_ROOM_VIEW_PERM');
					$img="<img src=\"".getPathImage()."chat/user_perm.gif\" alt=\"".$title."\" title=\"".$title."\" />";
					$url="index.php?mn=chat&amp;&amp;pi=".getPI()."&amp;type=openconf&amp;op=setroomviewperm&amp;roomid=".$room_id;
					$cont[]="<a href=\"".$url."\">".$img."</a>\n";
					
					$cont[]='<a href="index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=modroom&amp;room_id='.$room_id.'">'
							.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD_ROOM').'" title="'.$lang->def('_MOD_ROOM').'" />'
							.'</a>';
					
					$cont[]='<a href="index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=delroom&amp;room_id='.$room_id.'">'
							.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL_ROOM').'" title="'.$lang->def('_DEL_ROOM').'" />'
							.'</a>';
					
				}

				$tbf->addBody($cont);
			}
		} // end while
		
		$GLOBALS['page']->add($tbf->getTable(), 'content');
	}
	if($is_moderator) {

		$role = 2;
		$can_open_room = $conference->canOpenRoom(date("Y-m-d H:i:s"), date("Y-m-d H:i:s", time() + 3600));
		
		if($can_open_room['errorcode'] == 0) {

			$GLOBALS['page']->add(
				'<p class="new_elem_link">'
				.'<a id="customroom" href="index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=createroom">'.$lang->def('_CANOPENNEWROOM').'</a>'
				.'</p>'
				, 'content');
			$GLOBALS['page']->add('<div class="no_float"></div>', 'content');
		} else {
			$GLOBALS['page']->add(getErrorUi($can_open_room['errormessage']), 'content');
		}
	}

	if ($GLOBALS["current_user"]->isAnonymous()) {

		$GLOBALS['page']->add($lang->def("_FULL_FEATURE_AFTER_LOGIN"), 'content');

	}

	$GLOBALS['page']->add('</div>', 'content');
}

function createroomOpenconference() {
	checkOpenconferencePerm(_IS_MODERATOR);
	$lang=& DoceboLanguage::createInstance('doceboconference', 'cms');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_scs'].'/lib/lib.openconference.php');

	$role = 1;
	$conference = new OpenConferenceManager();

	$conference->deleteOldRoom($_SESSION['idCourse'],
							'cms',
							date("Y-m-d H:i:s", time() - 1800) );

	if(isset($_POST['create_room'])) {

		$conference = new OpenConferenceManager();
		
		$start_date = $GLOBALS['regset']->regionalToDatabase($_POST['start_date'], 'date');
		$start_date = substr($start_date, 0, 10);

		$start_time = ( strlen($_POST['start_time']['hour']) == 1 ? '0' : '' ).$_POST['start_time']['hour'].':'
			.( strlen($_POST['start_time']['minute']) == 1 ? '0' : '' ).$_POST['start_time']['minute'].':00';

		if ((isset($_POST["bookable"])) && ($_POST["bookable"] == 1)) {
			$bookable=1;
			$capacity=(int)$_POST["capacity"];
		}
		else {
			$bookable=0;
			$capacity=(int)$_POST["capacity"];
		}


		$start_timestamp = fromDatetimeToTimestamp($start_date.' '.$start_time);
		$end_timestamp = $start_timestamp + ($_POST['session_hour']+1) * 3600;
		$end_date = date("Y-m-d H:i:s", $end_timestamp);
		$re_creation_room=$conference->openRoom($_SESSION['idCourse'], 'cms', $_POST['room_title'], $start_date.' '.$start_time,
		                                        $end_date, FALSE, FALSE, $bookable, $capacity, getLogUserId(), $_POST['layout']);
		if($re_creation_room['errorcode'] != 0) {

			jumpTo('index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference');
		}
		jumpTo('index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference');
	}

	$session_hours 			= range(1, 4);
	$start_time['hour'] 	= date('H');
	$start_time['minute'] 	= date('i');
	$start_date = importVar('start_date', false, date("Y-m-d H:i:s"));

	$GLOBALS['page']->add(
		getCmsTitleArea($lang->def('_OPENCONFERENCE_TITLE'), 'openconference')
		.'<div class="std_block">'

		.Form::openForm('createroom', 'index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=createroom')
		.Form::openElementSpace()

		.Form::getTextfield($lang->def('_ROOM_TITLE'), 	'room_title', 'room_title', 255, importVar('room_title', false, ''))
		.Form::getDatefield($lang->def('_START_DATE'), 	'start_date', 'start_date',
			$GLOBALS['regset']->databaseToRegional($start_date, 'date') )
		.Form::getLineBox(
			$lang->def('_AT_HOUR'),
			Form::getInputDropdown('', 'start_time_hour', 'start_time[hour]', range(0, 23),
				$start_time['hour'], '')
			.' : '.Form::getInputDropdown('', 'start_time_minute', 'start_time[minute]', range(0, 59),
				$start_time['minute'], '')
		)
		.Form::getDropdown($lang->def('_SESSION_HOUR'), 'session_hour', 'session_hour', $session_hours, importVar('session_hour', true, 0))
		
		.Form::getCheckbox($lang->def('_ROOM_BOOKABLE'), 'bookable', 'bookable', 1)
		.Form::getTextfield($lang->def('_ROOM_CAPACITY'), 'capacity', 'capacity', 11)
		.Form::getDropdown($lang->def('_LAYOUT_STYLE'), 'layout', 'layout', $conference->layoutSelection())
		.Form::closeElementSpace()

		.Form::openButtonSpace()
		.Form::getButton('create_room', 'create_room', $lang->def('_CREATE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>' , 'content');
}

function quickroomOpenconference() {
	checkOpenconferencePerm(_IS_MODERATOR);
	$lang=& DoceboLanguage::createInstance('doceboconference', 'cms');

	require_once($GLOBALS['where_scs'].'/lib/lib.openconference.php');

	$block_id=$_SESSION['idCourse'];
	$b_info=getBlockInfo($block_id);

	$conference = new OpenConferenceManager();

	// delete old room from database
	$conference->deleteOldRoom($block_id,
							'cms',
							date("Y-m-d H:i:s", time() - 1800) );

	$re_creation_room = $conference->openRoom(	$_SESSION['idCourse'],
												'cms',
												$b_info["title"],
												date("Y-m-d H:i:s"),
												date("Y-m-d H:i:s", time() + 3600));
	jumpTo('index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference');
}

function modroomOpenconference() {
	checkOpenconferencePerm(_IS_MODERATOR);
	$lang=& DoceboLanguage::createInstance('doceboconference', 'cms');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_scs'].'/lib/lib.openconference.php');

	$role = 1;
	$conference = new OpenConferenceManager();
	$room_id = importVar('room_id');
	$conference->deleteOldRoom($_SESSION['idCourse'],
							'cms',
							date("Y-m-d H:i:s", time() - 1800) );

	if(isset($_POST['create_room'])) {

		$start_date = $GLOBALS['regset']->regionalToDatabase($_POST['start_date'], 'date');
		$start_date = substr($start_date, 0, 10);

		$start_time = ( strlen($_POST['start_time']['hour']) == 1 ? '0' : '' ).$_POST['start_time']['hour'].':'
			.( strlen($_POST['start_time']['minute']) == 1 ? '0' : '' ).$_POST['start_time']['minute'].':00';

		if ((isset($_POST["bookable"])) && ($_POST["bookable"] == 1)) {
			$bookable=1;
			$capacity=(int)$_POST["capacity"];
		}
		else {
			$bookable=0;
			$capacity="";
		}

		$start_timestamp = fromDatetimeToTimestamp($start_date.' '.$start_time);
		$end_timestamp = $start_timestamp + ($_POST['session_hour']+1) * 3600;
		$end_date = date("Y-m-d H:i:s", $end_timestamp);
		$re_creation_room = $conference->updateRoom($room_id, $_SESSION['idCourse'], 'cms', $_POST['room_title'], $start_date.' '.$start_time,
		                                        $end_date, FALSE, FALSE, $bookable, $capacity, $_POST['layout']);
		if($re_creation_room['errorcode'] != 0) {

			jumpTo('index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference');
		}
		jumpTo('index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference');
	}
	
	$data = $conference->roomInfo($_SESSION['idCourse'], 'cms', $room_id);
	$session_hours 			= range(1, 4);

	$start_date = $GLOBALS['regset']->databaseToRegional($data[OC_ROOM_START_DATE], 'date');
	$start_time['hour'] 	= substr($data[OC_ROOM_START_DATE], 11, 2);
	$start_time['minute'] 	= substr($data[OC_ROOM_START_DATE], 14, 2);
	$endurance = ((substr($data[OC_ROOM_END_DATE], 11, 2) - $start_time['hour']) - 1);
	
	$GLOBALS['page']->add(
		getCmsTitleArea($lang->def('_OPENCONFERENCE_TITLE'), 'openconference')
		.'<div class="std_block">'

		.Form::openForm('createroom', 'index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=modroom')
		.Form::openElementSpace()

		.Form::getHidden('room_id', 'room_id', $room_id)
		
		.Form::getTextfield($lang->def('_ROOM_TITLE'), 	'room_title', 'room_title', 255, 
			importVar('room_title', false, $data[OC_ROOM_NAME]))
		.Form::getDatefield($lang->def('_START_DATE'), 	'start_date', 'start_date',
			$start_date )
		.Form::getLineBox(
			$lang->def('_AT_HOUR'),
			Form::getInputDropdown('', 'start_time_hour', 'start_time[hour]', range(0, 23),
				$start_time['hour'], '')
			.' : '.Form::getInputDropdown('', 'start_time_minute', 'start_time[minute]', range(0, 59),
				$start_time['minute'], '')
		)
		.Form::getDropdown($lang->def('_SESSION_HOUR'), 'session_hour', 'session_hour', $session_hours, importVar('session_hour', true, $endurance))

		.Form::getCheckbox($lang->def('_ROOM_BOOKABLE'), 'bookable', 'bookable', 1, importVar('bookable', false, $data[OC_ROOM_BOOKABLE]))
		.Form::getTextfield($lang->def('_ROOM_CAPACITY'), 'capacity', 'capacity', 11, importVar('capacity', false, $data[OC_ROOM_MAX_USER]))

		.Form::closeElementSpace()

		.Form::openButtonSpace()
		.Form::getButton('create_room', 'create_room', $lang->def('_CREATE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>' , 'content');
}

function delroomOpenconference() {
	checkOpenconferencePerm(_IS_MODERATOR);
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_scs'].'/lib/lib.openconference.php');
	
	$room_id = importVar('room_id');
	
	$conference = new OpenConferenceManager();
	
	if(isset($_POST['confirm'])) {
		if($re['errorcode'] == 0) jumpTo('index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference');
		else jumpTo('index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference');
	}
	
	$lang =& DoceboLanguage::createInstance('doceboconference', 'cms');
	$data = $conference->roomInfo($_SESSION['idCourse'], 'cms', $room_id);
	
	$title_page = array(
		'index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference' => $lang->def('_OPENCONFERENCE_TITLE'),
		$lang->def('_DEL_ROOM').': <b>'.$data[OC_ROOM_NAME].'</b>' 
	);
	// print page
	$GLOBALS['page']->add(
		getCmsTitleArea($title_page, 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference', $lang->def('_BACK'))
		
		.Form::openForm('form_del_eportfolio', 'index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=delroom')
		.Form::getHidden('room_id', 'room_id', $room_id)
		
		.getDeleteUi($lang->def('_ARE_YOU_SURE'), 
			'<b>'.$lang->def('_ROOM_TITLE').': </b> '.$data[OC_ROOM_NAME],
			false,
			'confirm',
			'undo' )
		.Form::closeForm()
		.'</div>'
	, 'content');
}

function bookroomOpenconference() {
	require_once($GLOBALS["where_scs"]."/lib/lib.booking.php");

	if ($GLOBALS['current_user']->isAnonymous()) {
		$res="";

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");
		$lang=& DoceboLanguage::createInstance('doceboconference', 'cms');

		$res.=	getCmsTitleArea($lang->def('_OPENCONFERENCE_TITLE'), 'openconference');
		$res.="<div class=\"std_block\">\n";
		$res.=$lang->def("_BOOKING_REQ_LOGIN");
		$res.="</div>\n";
		$out->add($res);
	} else {

		if ((isset($_GET["roomid"])) && ((int)$_GET["roomid"] > 0)) {
			$room_id=(int)$_GET["roomid"];

			$cb=new ChatBooking("openconference");
			$cb->bookRoom($GLOBALS["current_user"]->getIdSt(), $room_id);
		}

		jumpTo("index.php?mn=chat&amp;&amp;pi=".getPI()."&amp;type=openconf&amp;op=openconference");
	}
}


function getBookingTableOpenconference($approved, $room_id, $table_caption, $table_summary) {
	checkOpenconferencePerm(_IS_MODERATOR);
	require_once($GLOBALS["where_scs"]."/lib/lib.booking.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

	$res="";
	$cb=new ChatBooking("openconference");
	$lang=& DoceboLanguage::createInstance('doceboconference', 'cms');
	$user_lang=& DoceboLanguage::createInstance('admin_directory', 'framework');
	$form=new Form();

	$room_sub=$cb->getRoomSubscriptions($room_id, "approved='".(int)$approved."'");

	$tab = new TypeOne(0, $table_caption, $table_summary);
	$tab->setTableStyle('chat_room_booking');


	$cont_h=array(	"&nbsp;",
	              $user_lang->def('_USERNAME'),
	              $user_lang->def('_EMAIL'),
	              $user_lang->def('_DIRECTORY_FIRSTNAME'),
	              $user_lang->def('_LASTNAME'));


	if ($approved) {
		$img ="<img src=\"".getPathImage('fw')."standard/undo.gif\" alt=\"".$lang->def("_DISAPPROVE")."\" ";
		$img.="title=\"".$lang->def("_DISAPPROVE")."\" />";
	}
	else {
		$img ="<img src=\"".getPathImage('fw')."standard/flag.gif\" alt=\"".$lang->def("_APPROVE")."\" ";
		$img.="title=\"".$lang->def("_APPROVE")."\" />";
	}
	$cont_h[]=$img;

	$img ="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$cont_h[]=$img;

	$type_h=array('image', '', '', '', '', 'image', 'image');
	$tab->setColsStyle($type_h);
	$tab->addHead($cont_h);

	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	if (count($room_sub) > 0)
		$info=$acl_manager->getUsers(array_keys($room_sub));
	else
		$info=array();

	$checkbox_pfx=($approved ? "disapprove" : "approve");
	foreach ($info as $user_idst=>$user_info) {

		$cont=array();

		$nickname=$acl_manager->relativeId($user_info[ACL_INFO_USERID]);

		$show_details=(isset($_SESSION["chat_booking_user_details"][$user_idst]) ? TRUE : FALSE);
		if ($show_details) {
			$src=getPathImage('fw')."standard/less.gif";
			$alt=$lang->def("_HIDE_DETAILS").": ".$nickname;
			$id="hide_details_".$user_idst;
			$name="hide_details[".$user_idst."]";
		}
		else {
			$src=getPathImage('fw')."standard/more.gif";
			$alt=$lang->def("_SHOW_DETAILS").": ".$nickname;
			$id="show_details_".$user_idst;
			$name="show_details[".$user_idst."]";
		}
		$cont[]='<input type="image" src="'.$src.'" alt="'.$alt.'" title="'.$alt.'" id="'.$id.'" name="'.$name.'" />';

		$cont[]=$nickname;

		$cont[]=$user_info[ACL_INFO_EMAIL];
		$cont[]=$user_info[ACL_INFO_FIRSTNAME];
		$cont[]=$user_info[ACL_INFO_LASTNAME];


		$check_id=$checkbox_pfx."_".$user_idst;
		$check_name=$checkbox_pfx."[".$user_idst."]";
		$checked=(isset($_POST[$checkbox_pfx][$user_idst]) ? TRUE : FALSE);
		$cont[]=$form->getInputCheckbox($check_id, $check_name, $user_idst, 	$checked, '' ).
			'<label class="access-only" for="'.$check_id.'">'.$user_info[ACL_INFO_USERID].'</label>';

		$check_id="delete_".$user_idst;
		$check_name="delete[".$user_idst."]";
		$checked=(isset($_POST["delete"][$user_idst]) ? TRUE : FALSE);
		$cont[]=$form->getInputCheckbox($check_id, $check_name, $user_idst, 	$checked, '' ).
			'<label class="access-only" for="'.$check_id.'">'.$user_info[ACL_INFO_USERID].'</label>';

		$tab->addBody($cont);
		if($show_details) {
			$field = new FieldList();
			$tab->addBodyExpanded($field->playFieldsForUser( $user_idst, false, true ), 'user_specific_info');
		}
	}

	$res="";

	$suffix=($approved ? "_approved" : "_waiting");
	$url="index.php?mn=chat&amp;&amp;pi=".getPI()."&amp;type=openconf&amp;op=managesub&amp;roomid=".$room_id;
	$res.=$form->openForm("form".$suffix, $url);
	$res.=$tab->getTable();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save'.$suffix, 'save'.$suffix, $lang->def('_SAVE'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	return $res;
}


function manageSubscriptionOpenconference() {

	if ((isset($_GET["roomid"])) && ((int)$_GET["roomid"] > 0)) {
		$room_id=(int)$_GET["roomid"];
	}
	else {
		return "";
	}

	// ---------------------------------------------------------------------------
	// Checking / performing POST data actions
	// ---------------------------------------------------------------------------

	if ((isset($_POST["save_waiting"])) && (isset($_POST["approve"]))) {
		saveBooking($room_id, $_POST["approve"], "approve");
	}
	else if ((isset($_POST["save_approved"])) && (isset($_POST["disapprove"]))) {
		saveBooking($room_id, $_POST["disapprove"], "disapprove");
	}

	if ((isset($_POST["save_waiting"])) || (isset($_POST["save_approved"]))) {
		if (isset($_POST["delete"])) {
			deleteBooking($room_id, $_POST["delete"]);
		}
	}

	if (isset($_POST["hide_details"])) {
		unset($_SESSION["chat_booking_user_details"]);
	}
	if (isset($_POST["show_details"])) {
		$to_show=array_keys($_POST["show_details"]);
		$_SESSION["chat_booking_user_details"][$to_show[0]]=1;
	}

	// ---------------------------------------------------------------------------

	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance('doceboconference', 'cms');

	$tab1=getBookingTable(FALSE, $room_id, $lang->def('_PENDING_USERS_CAP'), $lang->def('_PENDING_USERS_SUM'));
	$tab2=getBookingTable(TRUE, $room_id, $lang->def('_APPROVED_USERS_CAP'), $lang->def('_APPROVED_USERS_SUM'));


	$res.=	getCmsTitleArea($lang->def('_OPENCONFERENCE_TITLE'), 'openconference');
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi('index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=openconference', $lang->def('_BACK'));
	$res.=$tab1;
	$res.=$tab2;
	$res.="</div>\n";
	$out->add($res);
}

function saveBookingOpenconference($room_id, $user_to_approve, $action) {
	checkOpenconferencePerm(_IS_MODERATOR);
	require_once($GLOBALS["where_scs"]."/lib/lib.booking.php");

	$cb=new ChatBooking("openconference");

	$approve=FALSE;
	switch($action) {
		case "approve": {
			$approve=TRUE;
		} break;
		case "disapprove": {
			$approve=FALSE;
		} break;
	}


	foreach($user_to_approve as $user_idst) {
		$cb->setApproved($user_idst, $room_id, $approve);
	}

}


function deleteBookingOpenconference($room_id, $to_delete) {
	checkOpenconferencePerm(_IS_MODERATOR);
	require_once($GLOBALS["where_scs"]."/lib/lib.booking.php");

	$cb=new ChatBooking("openconference");

	foreach($to_delete as $user_idst) {
		$cb->deleteBooking($user_idst, $room_id);
	}
}



function userBookingOpenconference() {
	checkOpenconferencePerm(_IS_MODERATOR);
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

	if ((isset($_GET["roomid"])) && ((int)$_GET["roomid"] > 0))
		$room_id=(int)$_GET["roomid"];
	else
		return "";

	if ((isset($_GET["user"])) && ((int)$_GET["user"] > 0))
		$user_idst=(int)$_GET["user"];
	else
		return "";


	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance('doceboconference', 'cms');
	$user_lang=& DoceboLanguage::createInstance('admin_directory', 'framework');
	$form=new Form();

	$acl=$GLOBALS["current_user"]->getAcl();
	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	$info=$acl_manager->getUsers(array($user_idst));
	$user_info=$info[$user_idst];

	$res.=$form->openForm('bookroom', 'index.php?mn=chat&amp;&amp;pi='.getPI().'&amp;type=openconf&amp;op=savebooking');
	$res.=$form->openElementSpace();

	$res.=$form->getLineBox($user_lang->def("_USERNAME"), $acl_manager->relativeId($user_info[ACL_INFO_USERID]));
	$res.=$form->getLineBox($user_lang->def("_DIRECTORY_FIRSTNAME"), $user_info[ACL_INFO_FIRSTNAME]);
	$res.=$form->getLineBox($user_lang->def("_LASTNAME"), $user_info[ACL_INFO_LASTNAME]);
	$res.=$form->getLineBox($user_lang->def("_EMAIL"), $user_info[ACL_INFO_EMAIL]);

	// --- Extra fields:
	$fl = new FieldList();

	$user_groups=$acl->getUserGroupsST($user_idst);
	$field_list=$fl->getFieldsFromIdst($user_groups);

	$field_id_arr=array_keys($field_list);
	$user_field_arr=$fl->showFieldForUserArr(array($user_idst), $field_id_arr);

	if ((isset($user_field_arr[$user_idst])) && (is_array($user_field_arr[$user_idst])))
 		$field_val=$user_field_arr[$user_idst];
	else
		$field_val=array();

	foreach ($field_val as $field_id=>$value) {
		$res.=$form->getLineBox($field_list[$field_id][FIELD_INFO_TRANSLATION], $value);
	}
	// --------------------

	$res.=$form->getHidden('room_id', 'room_id', $room_id);
	$res.=$form->getHidden('user_idst', 'user_idst', $user_idst);

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('do_approve', 'do_approve', $lang->def('_APPROVE_BOOKING'));
	$res.=$form->getButton('do_disapprove', 'do_disapprove', $lang->def('_DISAPPROVE_BOOKING'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	$res.=	getCmsTitleArea($lang->def('_OPENCONFERENCE_TITLE'), 'openconference');
	$res.="<div class=\"std_block\">\n";

	$res.="</div>\n";
	$out->add($res);
}



function setRoomViewPermOpenconference() {
	checkOpenconferencePerm(_IS_MODERATOR);

	if ((isset($_GET["roomid"])) && ((int)$_GET["roomid"] > 0))
		$room_id=(int)$_GET["roomid"];
	else
		return "";

	require_once($GLOBALS["where_scs"]."/lib/lib.roomperm.php");
	require_once($GLOBALS['where_framework']."/class.module/class.directory.php");
	$mdir=new Module_Directory();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance('doceboconference', 'cms');

	$roomperm=new RoomPermissions($room_id, "openconference");


	$res = getCmsTitleArea($lang->def('_OPENCONFERENCE_TITLE'), 'openconference');


	$back_url="index.php?mn=chat&amp;&amp;pi=".getPI()."&amp;type=openconf&amp;op=openconference";


	if( isset($_POST['okselector']) ) {

		$arr_selection=$mdir->getSelection($_POST);
		$arr_unselected=$mdir->getUnselected();

		$roomperm->addPerm("view", $arr_selection);
		$roomperm->removePerm("view", $arr_unselected);

		jumpTo(str_replace("&amp;", "&", $back_url));
	}
	else if( isset($_POST['cancelselector']) ) {
		jumpTo(str_replace("&amp;", "&", $back_url));
	}
	else {

		if( !isset($_GET['stayon']) ) {
			$all_perm = $roomperm->getAllPerm();
			if(isset($all_perm["view"])) $mdir->resetSelection($all_perm["view"]);
		}
		
		$acl_manager =& $GLOBALS['current_user']->getAclManager();

		$url="index.php?mn=chat&amp;&amp;pi=".getPI()."&amp;type=openconf&amp;op=setroomviewperm&amp;roomid=".$room_id;
		$mdir->setNFields(0);
		$mdir->show_group_selector=TRUE;
		$mdir->show_orgchart_selector=FALSE;
		
		$arr_idstGroup = $acl_manager->getGroupsIdstFromBasePath('/cms/course/'.(int)$_SESSION['idCourse'].'/subscribed/');
		$me = array(getLogUserId());
		$mdir->setUserFilter('exclude', $me);
		$mdir->setUserFilter('group',$arr_idstGroup);
		$mdir->setGroupFilter('path', '/cms/course/'.$_SESSION['idCourse'].'/group');
		
		$mdir->loadSelector($url,
			$lang->def( '_ROOM_VIEW_PERM' ), "", TRUE);
	}

}


function checkRoomPermOpenconference($perm_arr, $user_idst) {

	if ((!is_array($perm_arr)) || (count($perm_arr) < 1))
		$res=TRUE;
	else if (in_array($user_idst, $perm_arr))
		$res=TRUE;
	else
		$res=FALSE;

	return $res;
}

function openConferenceModuleOpenconference() {
	
	require_once($GLOBALS['where_scs'].'/lib/lib.openconference.php');
	
	VideoConferenceManager::jumpToRoom();
}

// ----------------------------------------------------------------------------

function checkOpenconferencePerm($mode, $return_value=FALSE) {

	if ($GLOBALS['current_user']->isAnonymous()) {
		$res=FALSE;
	}
	else {
		$block_id=$GLOBALS["pb"];
		$role_id="/cms/chat/openconf/block/".$block_id."/".$mode;

		$acl=$GLOBALS["current_user"]->getAcl();
		if ($acl->getRoleST($role_id))
			$res=$GLOBALS["current_user"]->matchUserRole($role_id);
		else
			$res=FALSE;
	}

	if ($return_value)
		return $res;
	elseif (!$res)
			die("You can't access!");
}

// ----------------------------------------------------------------------------


function openconferenceDispatch($op) {

	if(isset($_POST['undo'])) $op = 'openconference';
	switch($op) {
		case "quickroom" : {
			quickroomOpenconference();
		};
		case "openconference" : {
			openconference();
		};break;
		case "createroom" : {
			if(isset($_POST['undo']))
				openconference();
			else
				createroomOpenconference();
		};break;
		case "modroom": {
			modroomOpenconference();
		} break;
		case "delroom": {
			delroomOpenconference();
		} break;
		case "bookroom": {
			bookroomOpenconference();
		} break;
		case "managesub": {
			manageSubscriptionOpenconference();
		} break;
		case "userbooking": {
			userBookingOpenconference();
		} break;
		case "savebooking": {
			saveBookingOpenconference();
		} break;
		case "delbooking": {
			deleteBookingOpenconference();
		} break;
		case "setroomviewperm": {
			setRoomViewPermOpenconference();
		} break;
		case 'open_conference_module':
			openConferenceModuleOpenconference();
		break;
	}
}

?>