<?php

/*
 **************************************************************************
 *                                                                        *
 *               DDDDD   iii                 dd  iii                      *
 *               DD  DD      mm mm mmmm      dd      mm mm mmmm           *
 *               DD   DD iii mmm  mm  mm  ddddd  iii mmm  mm  mm          *
 *               DD   DD iii mmm  mm  mm dd  dd  iii mmm  mm  mm          *
 *               DDDDDD  iii mmm  mm  mm  ddddd  iii mmm  mm  mm          *
 *                                                                        *
 *																		  *
 *                  Visit us at http://www.dimdim.com                     *
 *                  The Friendly Open Source Web Meeting                  *
 **************************************************************************
 **************************************************************************
 * NOTICE OF COPYRIGHT													  *
 *																		  *
 * Copyright (C) 2007													  *
 *																		  *
 * This program is free software; you can redistribute it and/or modify   *
 * it under the terms of the GNU General Public License as published by   *
 * the Free Software Foundation; either version 2 of the License, or      *
 * (at your option) any later version.					                  *
 *                                                                        *
 * This program is distributed in the hope that it will be useful,        *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 * GNU General Public License for more details:                           *
 *                                                                        *
 *          http://www.gnu.org/copyleft/gpl.html                          *
 * 													                      *
 * 															              *
 *                                                                        *
 **************************************************************************
 */

/// This page prints a particular instance of dimdim

    require_once('../../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once('pagelib.php');
	require_once('dimdim_javascript.php');

    $id          = optional_param('id', 0, PARAM_INT);
    $c           = optional_param('c', 0, PARAM_INT);
    $edit        = optional_param('edit', '');

    function link_to_dimdimpopup_window ($url, $name='popup', $linkname='click here',
	                               $height=400, $width=500, $title='Popup window', $options='none', $return=false) {

	    global $CFG;

	    if ($options == 'none') {
	        $options = 'menubar=0,location=0,scrollbars,resizable,width='. $width .',height='. $height;
	    }
	    $fullscreen = 0;

	    $link = '<a target="'. $name .'" title="'. $title .'" href="' . $url . '"'.
	           "onclick=\"return dimdimopenpopup('$url', '$name', '$options', $fullscreen);\">$linkname</a>";
	    if ($return) {
	        return $link;
	    } else {
	        echo $link;
	    }
}

    if ($id) {
        if (! $cm = get_record('course_modules', 'id', $id)) {
            error('Course Module ID was incorrect');
        }

        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }

        dimdim_update_dimdim_times($cm->instance);

        if (! $dimdim = get_record('dimdim', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }

    } else {
        dimdim_update_dimdim_times($c);

        if (! $dimdim = get_record('dimdim', 'id', $c)) {
            error('Course module is incorrect');
        }
        if (! $course = get_record('course', 'id', $dimdim->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('dimdim', $dimdim->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    }

    require_course_login($course, true, $cm);

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    add_to_log($course->id, 'dimdim', 'view', "view.php?id=$cm->id", $dimdim->id, $cm->id);

// Initialize $PAGE, compute blocks

    $PAGE       = page_create_instance($dimdim->id);
    $pageblocks = blocks_setup($PAGE);
    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

/// Print the page header

    $strnextsession  = get_string('nextsession', 'dimdim');

    if (!empty($edit) && $PAGE->user_allowed_editing()) {
        if ($edit == 'on') {
            $USER->editing = true;
        } else if ($edit == 'off') {
            $USER->editing = false;
        }
    }

    $PAGE->print_header($course->shortname.': %fullname%');

    echo '<table id="layout-table"><tr>';

    if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column">';

    print_heading(format_string($dimdim->name));

/// Check to see if groups are being used here
    $groupmode = groupmode($course, $cm);
    $currentgroup = setup_and_print_groups($course, $groupmode, "view.php?id=$cm->id");


    if ($currentgroup) {
        $groupselect = " AND groupid = '$currentgroup'";
        $groupparam = "&amp;groupid=$currentgroup";
    } else {
        $groupselect = "";
        $groupparam = "";
    }

	$ip = $CFG->dimdim_serverhost;
	$port = $CFG->dimdim_serverport;
	$email = $USER->email;
	$displayname = $USER->firstname." ".$USER->lastname;
	$confname = "$dimdim->name";


	$lobbyvalue = get_field('dimdim','lobby','id',$dimdim->id);
	if($lobbyvalue)
	{
	$lobby = 'true';
	}
	else
	{
	$lobby = 'false';
	}

	$interntoll = get_field('dimdim','interntoll','id',$dimdim->id);
	$moderatorpasscode = get_field('dimdim','moderatorpasscode','id',$dimdim->id);
	$attendeepasscode = get_field('dimdim','attendeepasscode','id',$dimdim->id);
	$meetingkey = get_field('dimdim','meetingkey','id',$dimdim->id);
	$hostkey = get_field('dimdim','hostkey','id',$dimdim->id);

	$privatechatvalue = get_field('dimdim','privatechat','id',$dimdim->id);
		if($privatechatvalue)
		{
		$privatechat = 'true';
		}
		else
		{
		$privatechat = 'false';
	}


	$publicchatvalue = get_field('dimdim','publicchat','id',$dimdim->id);
		if($publicchatvalue)
		{
		$publicchat = 'true';
		}
		else
		{
		$publicchat = 'false';
	}


	$screencastvalue = get_field('dimdim','screencast','id',$dimdim->id);
		if($screencastvalue)
		{
		$screencast = 'true';
		}
		else
		{
		$screencast = 'false';
	}


	$whiteboardvalue = get_field('dimdim','whiteboard','id',$dimdim->id);
		if($whiteboardvalue)
		{
		$whiteboard = 'true';
		}
		else
		{
		$whiteboard = 'false';
	}

	$participantlistvalue = get_field('dimdim','participantlist','id',$dimdim->id);
		if($participantlistvalue)
		{
		$participantlist = 'true';
		}
		else
		{
		$participantlist = 'false';
	}


	$displaydialinfovalue = get_field('dimdim','displaydialinfo','id',$dimdim->id);
		if($displaydialinfovalue)
		{
		$displaydialinfo = 'true';
		}
		else
		{
		$displaydialinfo = 'false';
	}

	$assistantenabledvalue = get_field('dimdim','assistantenabled','id',$dimdim->id);
		if($assistantenabledvalue)
		{
		$assistantenabled = 'true';
		}
		else
		{
		$assistantenabled = 'false';
		}

	$handsfreeonloadvalue = get_field('dimdim','handsfreeonload','id',$dimdim->id);
		if($displaydialinfovalue)
		{
		    $handsfreeonload = 'true';
		}
		else
		{
		    $handsfreeonload = 'false';
		}

	$assignmikeonjoinvalue = get_field('dimdim','assignmikeonjoin','id',$dimdim->id);
		if($assignmikeonjoinvalue)
		{
		    $assignmikeonjoin = 'true';
		}
		else
		{
		    $assignmikeonjoin = 'false';
		}

	$allowattendeeinvitevalue = get_field('dimdim','allowattendeeinvite','id',$dimdim->id);
		if($allowattendeeinvitevalue)
		{
		    $allowattendeeinvite = 'true';
		}
		else
		{
		    $allowattendeeinvite = 'false';
		}

	$featuredocsharevalue = get_field('dimdim','featuredocshare','id',$dimdim->id);
		if($featuredocsharevalue)
		{
		    $featuredocshare = 'true';
		}
		else
		{
		    $featuredocshare = 'false';
		}

	$featurecobsharevalue = get_field('dimdim','featurecobshare','id',$dimdim->id);
		if($featurecobsharevalue)
		{
		    $featurecobshare = 'true';
		}
		else
		{
		    $featurecobshare = 'false';
		}

	$featurerecordingvalue = get_field('dimdim','featurerecording','id',$dimdim->id);
		if($featurerecordingvalue)
		{
		    $featurerecording = 'true';
		}
		else
		{
		    $featurerecording = 'false';
		}


	$networkprof = get_field('dimdim','networkprofile','id',$dimdim->id) + 1;


	$meethrsvalue = get_field('dimdim','meetinghours','id',$dimdim->id);

	if ($meethrsvalue == 0)
		$meethrs = 1;
		else if ($meethrsvalue == 1)
		$meethrs = 2;
		else if ($meethrsvalue == 2)
		$meethrs = 3;
		else if ($meethrsvalue == 3)
		$meethrs = 4;
		else if ($meethrsvalue == 4)
		$meethrs = 5;

	$meetmin = 0;

	$maxparticipantsvalue = get_field('dimdim','maxparticipants','id',$dimdim->id);

	if ($maxparticipantsvalue == 0)
	$maxpart = 5;
	else if ($maxparticipantsvalue == 1)
	$maxpart = 10;
	else if ($maxparticipantsvalue == 2)
	$maxpart = 15;
	else if ($maxparticipantsvalue == 3)
	$maxpart = 20;
	else
	$maxpart = 20;

	$potentialpresenter = "null";

	$audioorvideo = get_field('dimdim','audiovideosettings','id',$dimdim->id);

	if($audioorvideo == 0)
	{
	$meetaudio = "audio";
	}
	else if ($audioorvideo == 1)
	{
	$meetaudio = "av";
	}
	else if ($audioorvideo == 2)
	{
	$meetaudio = "videochat";
	}
	else if ($audioorvideo == 3)
	{
	$meetaudio = "disabled";
	}
	else
	{
	$meetaudio = "av";
	}

	$meetattendees = " ";
	$meetmike = get_field('dimdim','maxmikes','id',$dimdim->id);

	$returnurl = $CFG->wwwroot;
	$formload = 'true';

	$dbtime = get_field('event','timestart','name',$dimdim->name);
	$now = time();

	if((($dbtime - 900) <= $now ) && ($now <= ($dbtime + 7200)))
	{
		if (!isguestuser() and isloggedin()) {
		        print_simple_box_start('center');
		        if ($PAGE->user_allowed_editing())
		        {
		    		$confkey = get_field('dimdim','confkey','id',$dimdim->id);
		    	    $url = "http://".$ip.":".$port."/dimdim/html/envcheck/connect.action?action=host&"."email=".$email."&confKey=".$confkey."&displayName=".$displayname."&confName=".$confname."&lobby=".$lobby."&networkProfile=".$networkprof."&meetingHours=".$meethrs."&meetingMinutes=".$meetmin."&maxParticipants=".$maxpart."&presenterAV=".$meetaudio."&attendees=".$meetattendees."&maxAttendeeMikes=".$meetmike."&returnUrl=".$returnurl."&whiteboardEnabled=".$whiteboard."&screenShareEnabled=".$screencast."&privateChatEnabled=".$privatechat."&publicChatEnabled=".$publicchat."&participantListEnabled=".$participantlist."&internToll=".$interntoll."&moderatorPassCode=".$moderatorpasscode."attendeePassCode=".$attendeepasscode."&presenterPwd=".$hostkey."&attendeePwd=".$meetingkey."&dialInfoVisible=".$displaydialinfo."&assistantEnabled=".$assistantenabled."&assignMikeOnJoin=".$assignmikeonjoin."&handsFreeOnLoad=".$handsfreeonload."&allowAttendeeInvites=".$allowattendeeinvite."&featureRecording=".$featurerecording."&featureCob=".$featurecobshare."&featureDoc=".$featuredocshare;
		    	    //print $url;
		    	    link_to_dimdimpopup_window($url," ","Click here to Start Meeting", 500, 700, get_string('modulename', 'dimdim'));
	        	}
	        	else {
					$joinkey = get_field('dimdim','confkey','id',$dimdim->id);
				    $url = "http://".$ip.":".$port."/dimdim/html/envcheck/connect.action?action=join&"."email=".$email."&confKey=".$joinkey."&displayName=".$displayname."&attendeePwd=".$meetingkey;
				    //print $url;
					link_to_dimdimpopup_window($url, " ", "Click here to Join Meeting", 500, 700, get_string('modulename', 'dimdim'));
					echo "<br /><br />You can join the meeting once the teacher has started the meeting";
					}
		        print_simple_box_end();
		       }
	    else {
		        $wwwroot = $CFG->wwwroot.'/login/index.php';
		        if (!empty($CFG->loginhttps)) {
		            $wwwroot = str_replace('http','https', $wwwroot);
		        }

		        notice_yesno(get_string('noguests', 'dimdim').'<br /><br />'.get_string('liketologin'),
		                     $wwwroot, $_SERVER['HTTP_REFERER']);
		        print_footer($course);
		        exit;
        }
	}
	$difference = $now - $dbtime;
	$difference=-$difference;
	$days = floor($difference/86400);
	$difference = $difference - ($days*86400);
	$hours = floor($difference/3600);
	$difference = $difference - ($hours*3600);
	$minutes = floor($difference/60);
	$difference = $difference - ($minutes*60);
	$seconds = $difference;

	$output = "after $days Days, $hours Hours, $minutes Minutes and $seconds Seconds,";
	$outputwithoutdays = "after $hours Hours, $minutes Minutes and $seconds Seconds,";

	if(($now < ($dbtime - 900)))
	{
		if($days == 0)
		{
		print_simple_box_start('center');
		if(dimdim_is_teacher($dimdim))
			echo "This meeting has not started yet. This meeting is scheduled to start ".$outputwithoutdays." \"".$displayname."\" will start the meeting";
		else
			echo "This meeting has not started yet. This meeting is scheduled to start ".$outputwithoutdays." \"".$displayname."\" will be able to join the meeting";
		}
		else
		{
		print_simple_box_start('center');
		if(dimdim_is_teacher($dimdim))
				echo "This meeting has not started yet. This meeting is scheduled to start ".$output." \"".$displayname."\" will start the meeting";
			else
				echo "This meeting has not started yet. This meeting is scheduled to start ".$output." \"".$displayname."\" will be able to join the meeting";
		}
	}

	if($now > ($dbtime + 7200))
	{
		print_simple_box_start('center');
		echo "This meeting occured in the past and has finished";

	}


    if ($dimdim->dimdimtime and $dimdim->schedule) {  // A dimdim is scheduled
        echo "<p align=\"center\">$strnextsession: ".userdate($dimdim->dimdimtime).' ('.usertimezone($USER->timezone).')</p>';
    } else {
        echo '<br />';
    }

    if ($dimdim->intro) {
        print_simple_box(format_text($dimdim->intro), 'center', '70%', '', 5, 'generalbox', 'intro');
        echo '<br />';
    }

/// Finish the page
    echo '</td></tr></table>';

    print_footer($course);

?>
