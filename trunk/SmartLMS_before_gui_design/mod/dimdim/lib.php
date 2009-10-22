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

/// Library of functions and constants for module dimdim

require_once($CFG->libdir.'/pagelib.php');

if (!isset($CFG->dimdim_refresh_room)) {
    set_config("dimdim_refresh_room", 5);
}
if (!isset($CFG->dimdim_refresh_userlist)) {
    set_config("dimdim_refresh_userlist", 10);
}
if (!isset($CFG->dimdim_old_ping)) {
    set_config("dimdim_old_ping", 35);
}
if (!isset($CFG->dimdim_method)) {
    set_config("dimdim_method", "header_js");
}
if (!isset($CFG->dimdim_serverhost)) {
    set_config("dimdim_serverhost", "<your_dimdim_url>");
}
if (!isset($CFG->dimdim_serverip)) {
    set_config("dimdim_serverip", '127.0.0.1');
}
if (!isset($CFG->dimdim_serverport)) {
    set_config("dimdim_serverport", 80);
}
if (!isset($CFG->dimdim_servermax)) {
    set_config("dimdim_servermax", 100);
}

// The HTML head for the message window to start with (<!-- nix --> is used to get some browsers starting with output
$dimdim_HTMLHEAD = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head></head>\n<body bgcolor=\"#FFFFFF\">\n\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n";

// The HTML head for the message window to start with (with js scrolling)
$dimdim_HTMLHEAD_JS = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><script language=\"JavaScript\">\n<!--\nfunction move()\n{\nif (scroll_active) window.scroll(1,400000);\nwindow.setTimeout(\"move()\",100);\n}\nscroll_active = true;\nmove();\n//-->\n</script>\n</head>\n<body bgcolor=\"#FFFFFF\" onBlur=\"scroll_active = true\" onFocus=\"scroll_active = false\">\n\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n";

// The HTML code for standard empty pages (e.g. if a user was kicked out)
$dimdim_HTMLHEAD_OUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>You are out!</title></head><body bgcolor=\"#FFFFFF\"></body></html>";

// The HTML head for the message input page
$dimdim_HTMLHEAD_MSGINPUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title></head><body bgcolor=\"#FFFFFF\">";

// The HTML code for the message input page, with JavaScript
$dimdim_HTMLHEAD_MSGINPUT_JS = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title>\n<script language=\"Javascript\">\n<!--\nscroll_active = true;\nfunction empty_field_and_submit()\n{\ndocument.fdummy.arsc_message.value=document.f.arsc_message.value;\ndocument.fdummy.submit();\ndocument.f.arsc_message.focus();\ndocument.f.arsc_message.select();\nreturn false;\n}\n// -->\n</script>\n</head><body bgcolor=\"#FFFFFF\" OnLoad=\"document.f.arsc_message.focus();document.f.arsc_message.select();\">";

// Dummy data that gets output to the browser as needed, in order to make it show output
$dimdim_DUMMY_DATA = "<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n";

function dimdim_add_instance($dimdim) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    $dimdim->timemodified = time();

    $dimdim->dimdimtime = make_timestamp($dimdim->dimdimyear, $dimdim->dimdimmonth, $dimdim->dimdimday,
                                     $dimdim->dimdimhour, $dimdim->dimdimminute);

    if ($returnid = insert_record('dimdim', $dimdim)) {

        $event = NULL;
        $event->name        = $dimdim->name;
        $event->description = $dimdim->intro;
        $event->courseid    = $dimdim->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'dimdim';
        $event->instance    = $returnid;
        $event->eventtype   = $dimdim->schedule;
        $event->timestart   = $dimdim->dimdimtime;
        $event->timeduration = 0;

        add_event($event);
    }

    return $returnid;
}


function dimdim_update_instance($dimdim) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.

    $dimdim->timemodified = time();
    $dimdim->id = $dimdim->instance;

    $dimdim->dimdimtime = make_timestamp($dimdim->dimdimyear, $dimdim->dimdimmonth, $dimdim->dimdimday,
                                     $dimdim->dimdimhour, $dimdim->dimdimminute);

    if ($returnid = update_record('dimdim', $dimdim)) {

        $event = NULL;

        if ($event->id = get_field('event', 'id', 'modulename', 'dimdim', 'instance', $dimdim->id)) {

            $event->name        = $dimdim->name;
            $event->description = $dimdim->intro;
            $event->timestart   = $dimdim->dimdimtime;

            update_event($event);
        }
    }

    return $returnid;
}


function dimdim_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $dimdim = get_record('dimdim', 'id', $id)) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records('dimdim', 'id', $dimdim->id)) {
        $result = false;
    }
    if (! delete_records('dimdim_messages', 'dimdimid', $dimdim->id)) {
        $result = false;
    }
    if (! delete_records('dimdim_users', 'dimdimid', $dimdim->id)) {
        $result = false;
    }

    $pagetypes = page_import_types('mod/dimdim/');
    foreach($pagetypes as $pagetype) {
        if(!delete_records('block_instance', 'pageid', $dimdim->id, 'pagetype', $pagetype)) {
            $result = false;
        }
    }

    if (! delete_records('event', 'modulename', 'dimdim', 'instance', $dimdim->id)) {
        $result = false;
    }

    return $result;
}

function dimdim_user_outline($course, $user, $mod, $dimdim) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    $return = NULL;
    return $return;
}

function dimdim_user_complete($course, $user, $mod, $dimdim) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    return true;
}

function dimdim_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a date, prints a summary of all dimdim rooms
/// that currently have people in them.
/// This function is called from course/lib.php: print_recent_activity()

    global $CFG;

    $timeold = time() - $CFG->dimdim_old_ping;

    $lastpingsearch = ($CFG->dimdim_method == 'sockets') ? '': 'AND cu.lastping > \''.$timeold.'\'';

    if (!$dimdimusers = get_records_sql("SELECT u.id, cu.dimdimid, u.firstname, u.lastname
                                        FROM {$CFG->prefix}dimdim_users as cu,
                                             {$CFG->prefix}dimdim as ch,
                                             {$CFG->prefix}user as u
                                       WHERE cu.userid = u.id
                                         AND cu.dimdimid = ch.id $lastpingsearch
                                         AND ch.course = '$course->id'
                                       ORDER BY cu.dimdimid ASC") ) {
        return false;
    }

    $isteacher = isteacher($course->id);

    $outputstarted = false;
    $current = 0;
    foreach ($dimdimusers as $dimdimuser) {
        if ($current != $dimdimuser->dimdimid) {
            if ($current) {
                echo '</ul></div>';  // room
                $current = 0;
            }
            if ($dimdim = get_record('dimdim', 'id', $dimdimuser->dimdimid)) {
                if (!($isteacher or instance_is_visible('dimdim', $dimdim))) {  // dimdim hidden to students
                    continue;
                }
                if (!$outputstarted) {
                    print_headline(get_string('currentdimdims', 'dimdim').':');
                    $outputstarted = true;
                }
                echo '<div class="room"><p class="head"><a href="'.$CFG->wwwroot.'/mod/dimdim/view.php?c='.$dimdim->id.'">'.format_string($dimdim->name,true).'</a></p><ul>';
            }
            $current = $dimdimuser->dimdimid;
        }
        $fullname = fullname($dimdimuser, $isteacher);
        echo '<li class="info name">'.$fullname.'</li>';
    }

    if ($current) {
        echo '</ul></div>';  // room
    }

    return true;
}


function dimdim_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    dimdim_update_dimdim_times();

    dimdim_delete_old_users();

    /// Delete old messages
    if ($dimdims = get_records('dimdim')) {
        foreach ($dimdims as $dimdim) {
            if ($dimdim->keepdays) {
                $timeold = time() - ($dimdim->keepdays * 24 * 3600);
                delete_records_select("dimdim_messages", "dimdimid = '$dimdim->id' AND timestamp < '$timeold'");
            }
        }
    }

    return true;
}

function dimdim_get_participants($dimdimid, $groupid=0) {
//Returns the users with data in one dimdim
//(users with records in dimdim_messages, students)

    global $CFG;

    if ($groupid) {
        $groupselect = " AND (c.groupid='$groupid' OR c.groupid='0')";
    } else {
        $groupselect = "";
    }

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}dimdim_messages c
                                 WHERE c.dimdimid = '$dimdimid' $groupselect
                                   AND u.id = c.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}

function dimdim_refresh_events($courseid = 0) {
// This standard function will check all instances of this module
// and make sure there are up-to-date events created for each of them.
// If courseid = 0, then every dimdim event in the site is checked, else
// only dimdim events belonging to the course specified are checked.
// This function is used, in its new format, by restore_refresh_events()

    if ($courseid) {
        if (! $dimdims = get_records('dimdim', "course", $courseid)) {
            return true;
        }
    } else {
        if (! $dimdims = get_records('dimdim')) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'dimdim');

    foreach ($dimdims as $dimdim) {
        $event = NULL;
        $event->name        = addslashes($dimdim->name);
        $event->description = addslashes($dimdim->intro);
        $event->timestart   = $dimdim->dimdimtime;

        if ($event->id = get_field('event', 'id', 'modulename', 'dimdim', 'instance', $dimdim->id)) {
            update_event($event);

        } else {
            $event->courseid    = $dimdim->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'dimdim';
            $event->instance    = $dimdim->id;
            $event->eventtype   = $dimdim->schedule;
            $event->timeduration = 0;
            $event->visible     = get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $dimdim->id);

            add_event($event);
        }
    }
    return true;
}

function dimdim_force_language($lang) {
/// This function prepares moodle to operate in given language
/// usable when $nomoodlecookie = true;
/// BEWARE: there must be no $course, $USER or $SESSION
    global $CFG;

    if(!empty($CFG->courselang)) {
        unset($CFG->courselang);
    }
    if(!empty($CFG->locale)) {
        unset($CFG->locale);
    }
    $CFG->lang = $lang;
    moodle_setlocale();
}

//////////////////////////////////////////////////////////////////////
/// Functions that require some SQL

function dimdim_get_users($dimdimid, $groupid=0) {

    global $CFG;

    if ($groupid) {
        $groupselect = " AND (c.groupid='$groupid' OR c.groupid='0')";
    } else {
        $groupselect = "";
    }

    return get_records_sql("SELECT DISTINCT u.id, u.firstname, u.lastname, u.picture, c.lastmessageping, c.firstping
                              FROM {$CFG->prefix}dimdim_users c,
                                   {$CFG->prefix}user u
                             WHERE c.dimdimid = '$dimdimid'
                               AND u.id = c.userid $groupselect
                             ORDER BY c.firstping ASC");
}

function dimdim_get_latest_message($dimdimid, $groupid=0) {
/// Efficient way to extract just the latest message
/// Uses ADOdb directly instead of get_record_sql()
/// because the LIMIT command causes problems with
/// the developer debugging in there.

    global $db, $CFG;

    if ($groupid) {
        $groupselect = " AND (groupid='$groupid' OR groupid='0')";
    } else {
        $groupselect = "";
    }

    if (!$rs = $db->Execute("SELECT *
                               FROM {$CFG->prefix}dimdim_messages
                              WHERE dimdimid = '$dimdimid' $groupselect
                           ORDER BY timestamp DESC LIMIT 1")) {
        return false;
    }
    if ($rs->RecordCount() == 1) {
        return (object)$rs->fields;
    } else {
        return false;                 // Found no records
    }
}


//////////////////////////////////////////////////////////////////////
// login if not already logged in

function dimdim_login_user($dimdimid, $version, $groupid, $course) {
    global $USER;
    if (($version != 'sockets') and $dimdimuser = get_record_select('dimdim_users', "dimdimid='$dimdimid' AND userid='$USER->id' AND groupid='$groupid'")) {
        $dimdimuser->version  = $version;
        $dimdimuser->ip       = getremoteaddr();
        $dimdimuser->lastping = time();
        $dimdimuser->lang     = current_language();

        if (($dimdimuser->course != $course->id)
         or ($dimdimuser->userid != $USER->id)) {
            return false;
        }
        if (!update_record('dimdim_users', $dimdimuser)) {
            return false;
        }
    } else {
        $dimdimuser->dimdimid   = $dimdimid;
        $dimdimuser->userid   = $USER->id;
        $dimdimuser->groupid  = $groupid;
        $dimdimuser->version  = $version;
        $dimdimuser->ip       = getremoteaddr();
        $dimdimuser->lastping = $dimdimuser->firstping = $dimdimuser->lastmessageping = time();
        $dimdimuser->sid      = random_string(32);
        $dimdimuser->course   = $course->id; //caching - needed for current_language too
        $dimdimuser->lang     = current_language(); //caching - to resource intensive to find out later

        if (!insert_record('dimdim_users', $dimdimuser)) {
            return false;
        }

        if ($version == 'sockets') {
            // do not send 'enter' message, dimdimd will do it
        } else {
            $message->dimdimid    = $dimdimuser->dimdimid;
            $message->userid    = $dimdimuser->userid;
            $message->groupid   = $groupid;
            $message->message   = 'enter';
            $message->system    = 1;
            $message->timestamp = time();

            if (!insert_record('dimdim_messages', $message)) {
                error('Could not insert a dimdim message!');
            }
        }
    }

    return $dimdimuser->sid;
}

function dimdim_delete_old_users() {
// Delete the old and in the way

    global $CFG;

    $timeold = time() - $CFG->dimdim_old_ping;

    $query = "lastping < '$timeold'";

    if ($oldusers = get_records_select('dimdim_users', $query) ) {
        delete_records_select('dimdim_users', $query);
        foreach ($oldusers as $olduser) {
            $message->dimdimid    = $olduser->dimdimid;
            $message->userid    = $olduser->userid;
            $message->groupid   = $olduser->groupid;
            $message->message   = 'exit';
            $message->system    = 1;
            $message->timestamp = time();

            if (!insert_record('dimdim_messages', $message)) {
                error('Could not insert a dimdim message!');
            }
        }
    }
}


function dimdim_update_dimdim_times($dimdimid=0) {
/// Updates dimdim records so that the next dimdim time is correct

    $timenow = time();
    if ($dimdimid) {
        if (!$dimdims[] = get_record_select('dimdim', "id = '$dimdimid' AND dimdimtime <= '$timenow' AND schedule > '0'")) {
            return;
        }
    } else {
        if (!$dimdims = get_records_select('dimdim', "dimdimtime <= '$timenow' AND schedule > '0'")) {
            return;
        }
    }

    foreach ($dimdims as $dimdim) {
        unset($dimdim->name);
        unset($dimdim->intro);
        switch ($dimdim->schedule) {
            case 1: // Single event - turn off schedule and disable
                    $dimdim->dimdimtime = 0;
                    $dimdim->schedule = 0;
                    break;
            case 2: // Repeat daily
                    $dimdim->dimdimtime += 24 * 3600;
                    break;
            case 3: // Repeat weekly
                    $dimdim->dimdimtime += 7 * 24 * 3600;
                    break;
        }
        update_record('dimdim', $dimdim);

        $event = NULL;           // Update calendar too
        if ($event->id = get_field('event', 'id', 'modulename', 'dimdim', 'instance', $dimdim->id)) {
            $event->timestart   = $dimdim->dimdimtime;
            update_event($event);
        }
    }
}


function dimdim_format_message_manually($message, $courseid, $sender, $currentuser, $dimdim_lastrow=NULL) {
    global $CFG, $USER;

    $output = New stdClass;
    $output->beep = false;       // by default
    $output->refreshusers = false; // by default

    // Use get_user_timezone() to find the correct timezone for displaying this message:
    // It's either the current user's timezone or else decided by some Moodle config setting
    // First, "reset" $USER->timezone (which could have been set by a previous call to here)
    // because otherwise the value for the previous $currentuser will take precedence over $CFG->timezone
    $USER->timezone = 99;
    $tz = get_user_timezone($currentuser->timezone);

    // Before formatting the message time string, set $USER->timezone to the above.
    // This will allow dst_offset_on (called by userdate) to work correctly, otherwise the
    // message times appear off because DST is not taken into account when it should be.
    $USER->timezone = $tz;
    $message->strtime = userdate($message->timestamp, get_string('strftimemessage', 'dimdim'), $tz);

    $message->picture = print_user_picture($sender->id, 0, $sender->picture, false, true, false);
    if ($courseid) {
        $message->picture = "<a target=\"_new\" href=\"$CFG->wwwroot/user/view.php?id=$sender->id&amp;course=$courseid\">$message->picture</a>";
    }

    //Calculate the row class
    if ($dimdim_lastrow !== NULL) {
        $rowclass = ' class="r'.$dimdim_lastrow.'" ';
    } else {
        $rowclass = '';
    }

    // Start processing the message

    if(!empty($message->system)) {
        // System event
        $output->text = $message->strtime.': '.get_string('message'.$message->message, 'dimdim', fullname($sender));
        $output->html  = '<table class="dimdim-event"><tr'.$rowclass.'><td class="picture">'.$message->picture.'</td><td class="text">';
        $output->html .= '<span class="event">'.$output->text.'</span></td></tr></table>';

        if($message->message == 'exit' or $message->message == 'enter') {
            $output->refreshusers = true; //force user panel refresh ASAP
        }

        return $output;
    }

    // It's not a system event

    $text = $message->message;

    /// Parse the text to clean and filter it

    $options->para = false;
    $text = format_text($text, FORMAT_MOODLE, $options, $courseid);

    // And now check for special cases

    if (substr($text, 0, 5) == 'beep ') {
        /// It's a beep!
        $beepwho = trim(substr($text, 5));

        if ($beepwho == 'all') {   // everyone
            $outinfo = $message->strtime.': '.get_string('messagebeepseveryone', 'dimdim', fullname($sender));
            $outmain = '';
            $output->beep = true;  // (eventually this should be set to
                                   //  to a filename uploaded by the user)

        } else if ($beepwho == $currentuser->id) {  // current user
            $outinfo = $message->strtime.': '.get_string('messagebeepsyou', 'dimdim', fullname($sender));
            $outmain = '';
            $output->beep = true;

        } else {
            return false;
        }
    } else if (substr($text, 0, 1) == ':') {              /// It's an MOO emote
        $outinfo = $message->strtime;
        $outmain = $sender->firstname.' '.substr($text, 1);

    } else if (substr($text, 0, 1) == '/') {     /// It's a user command

        if (substr($text, 0, 4) == "/me ") {
            $outinfo = $message->strtime;
            $outmain = $sender->firstname.' '.substr($text, 4);
        } else {
            $outinfo = $message->strtime;
            $outmain = $text;
        }

    } else {                                          /// It's a normal message
        $outinfo = $message->strtime.' '.$sender->firstname;
        $outmain = $text;
    }

    /// Format the message as a small table

    $output->text  = strip_tags($outinfo.': '.$outmain);

    $output->html  = "<table class=\"dimdim-message\"><tr$rowclass><td class=\"picture\">$message->picture</td><td class=\"text\">";
    $output->html .= "<span class=\"title\">$outinfo</span>";
    if ($outmain) {
        $output->html .= ": $outmain";
    }
    $output->html .= "</td></tr></table>";

    return $output;
}

function dimdim_format_message($message, $courseid, $currentuser, $dimdim_lastrow=NULL) {
/// Given a message object full of information, this function
/// formats it appropriately into text and html, then
/// returns the formatted data.

    if (!$user = get_record("user", "id", $message->userid)) {
        return "Error finding user id = $message->userid";
    }

    return dimdim_format_message_manually($message, $courseid, $user, $currentuser, $dimdim_lastrow);

}

if (!function_exists('ob_get_clean')) {
/// Compatibility function for PHP < 4.3.0
    function ob_get_clean() {
        $cont = ob_get_contents();
        if ($cont !== false) {
            ob_end_clean();
            return $cont;
        } else {
            return $cont;
        }
    }
}

function dimdim_context($dimdim) {
    //TODO: add some $cm caching if needed
    if (is_object($dimdim)) {
        $dimdim = $dimdim->id;
    }
    if (! $cm = get_coursemodule_from_instance('dimdim', $dimdim)) {
        error('Course Module ID was incorrect');
    }

    return get_context_instance(CONTEXT_MODULE, $cm->id);
}

function dimdim_is_teacher($dimdim, $userid=NULL) {
    return has_capability('mod/dimdim:manage', dimdim_context($dimdim), $userid);
}

?>
