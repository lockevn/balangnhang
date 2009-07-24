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

/// This page prints reports and info about dimdims

    require_once('../../config.php');
    require_once('lib.php');

    $id            = required_param('id', PARAM_INT);
    $start         = optional_param('start', 0, PARAM_INT);   // Start of period
    $end           = optional_param('end', 0, PARAM_INT);     // End of period
    $deletesession = optional_param('deletesession', 0, PARAM_BOOL);
    $confirmdelete = optional_param('confirmdelete', 0, PARAM_BOOL);

    if (! $cm = get_record('course_modules', 'id', $id)) {
        error('Course Module ID was incorrect');
    }
    if (! $dimdim = get_record('dimdim', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }
    if (! $course = get_record('course', 'id', $dimdim->course)) {
        error('Course is misconfigured');
    }

    require_login($course->id, false, $cm);

    $isteacher     = isteacher($course->id);
    $isteacheredit = isteacheredit($course->id);

    if (isguest() or (!$isteacher and !$dimdim->studentlogs)) {
        error('You can not view these dimdim reports');
    }

    add_to_log($course->id, 'dimdim', 'report', "report.php?id=$cm->id", $dimdim->id, $cm->id);

    $strdimdims         = get_string('modulenameplural', "dimdim");
    $strdimdim          = get_string('modulename', 'dimdim');
    $strdimdimreport    = get_string('dimdimreport', 'dimdim');
    $strseesession    = get_string('seesession', 'dimdim');
    $strdeletesession = get_string('deletesession', 'dimdim');


/// Print a session if one has been specified

    if ($start and $end and !$confirmdelete) {   // Show a full transcript

        print_header_simple(format_string($dimdim->name).": $strdimdimreport", '',
                     "<a href=\"index.php?id=$course->id\">$strdimdims</a> ->
                     <a href=\"view.php?id=$cm->id\">".format_string($dimdim->name,true)."</a> ->
                     <a href=\"report.php?id=$cm->id\">$strdimdimreport</a>",
                      '', '', true, '', navmenu($course, $cm));

    /// Check to see if groups are being used here
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id");
        } else {
            $currentgroup = false;
        }

        if (!empty($currentgroup)) {
            $groupselect = " AND groupid = '$currentgroup'";
        } else {
            $groupselect = "";
        }

        if ($deletesession and $isteacheredit) {
            notice_yesno(get_string('deletesessionsure', 'dimdim'),
                         "report.php?id=$cm->id&amp;deletesession=1&amp;confirmdelete=1&amp;start=$start&amp;end=$end&amp;sesskey=$USER->sesskey",
                         "report.php?id=$cm->id");
        }

        if (!$messages = get_records_select('dimdim_messages', "dimdimid = $dimdim->id AND
                                                              timestamp >= '$start' AND
                                                              timestamp <= '$end' $groupselect", "timestamp ASC")) {
            print_heading(get_string('nomessages', 'dimdim'));

        } else {
            echo '<p align="center">'.userdate($start).' --> '. userdate($end).'</p>';

            print_simple_box_start('center');
            foreach ($messages as $message) {  // We are walking FORWARDS through messages
                $formatmessage = dimdim_format_message($message, $course->id, $USER);
                echo $formatmessage->html;
            }
            print_simple_box_end('center');
        }

        if (!$deletesession or !$isteacheredit) {
            print_continue("report.php?id=$cm->id");
        }

        print_footer($course);
        exit;
    }


/// Print the Sessions display

    print_header_simple(format_string($dimdim->name).": $strdimdimreport", '',
                 "<a href=\"index.php?id=$course->id\">$strdimdims</a> ->
                 <a href=\"view.php?id=$cm->id\">".format_string($dimdim->name,true)."</a> -> $strdimdimreport",
                  '', '', true, '', navmenu($course, $cm));

    print_heading(format_string($dimdim->name).': '.get_string('sessions', 'dimdim'));


/// Check to see if groups are being used here
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id");
    } else {
        $currentgroup = false;
    }

    if (!empty($currentgroup)) {
        $groupselect = " AND groupid = '$currentgroup'";
    } else {
        $groupselect = "";
    }

/// Delete a session if one has been specified

    if ($deletesession and $isteacheredit and $confirmdelete and $start and $end and confirm_sesskey()) {
        delete_records_select('dimdim_messages', "dimdimid = $dimdim->id AND
                                            timestamp >= '$start' AND
                                            timestamp <= '$end' $groupselect");
        $strdeleted  = get_string('deleted');
        notify("$strdeleted: ".userdate($start).' --> '. userdate($end));
        unset($deletesession);
    }


/// Get the messages

    if (empty($messages)) {   /// May have already got them above
        if (!$messages = get_records_select('dimdim_messages', "dimdimid = '$dimdim->id' $groupselect", "timestamp DESC")) {
            print_heading(get_string('nomessages', 'dimdim'));
            print_footer($course);
            exit;
        }
    }

/// Show all the sessions

    $sessiongap = 5 * 60;    // 5 minutes silence means a new session
    $sessionend = 0;
    $sessionstart   = 0;
    $sessionusers = array();
    $lasttime   = 0;

    $messagesleft = count($messages);

    foreach ($messages as $message) {  // We are walking BACKWARDS through the messages

        $messagesleft --;              // Countdown

        if (!$lasttime) {
            $lasttime = $message->timestamp;
        }
        if (!$sessionend) {
            $sessionend = $message->timestamp;
        }
        if ((($lasttime - $message->timestamp) < $sessiongap) and $messagesleft) {  // Same session
            if ($message->userid and !$message->system) {       // Remember user and count messages
                if (empty($sessionusers[$message->userid])) {
                    $sessionusers[$message->userid] = 1;
                } else {
                    $sessionusers[$message->userid] ++;
                }
            }
        } else {
            $sessionstart = $lasttime;

            if ($sessionend - $sessionstart > 60 and count($sessionusers) > 1) {

                echo '<p align="center">'.userdate($sessionstart).' --> '. userdate($sessionend).'</p>';

                print_simple_box_start('center');

                arsort($sessionusers);
                foreach ($sessionusers as $sessionuser => $usermessagecount) {
                    if ($user = get_record('user', 'id', $sessionuser)) {
                        print_user_picture($user->id, $course->id, $user->picture);
                        echo '&nbsp;'.fullname($user, $isteacher);
                        echo "&nbsp;($usermessagecount)<br />";
                    }
                }

                echo '<p align="right">';
                echo "<a href=\"report.php?id=$cm->id&amp;start=$sessionstart&amp;end=$sessionend\">$strseesession</a>";
                if ($isteacheredit) {
                    echo "<br /><a href=\"report.php?id=$cm->id&amp;start=$sessionstart&amp;end=$sessionend&amp;deletesession=1\">$strdeletesession</a>";
                }
                echo '</p>';
                print_simple_box_end();
            }

            $sessionend = $message->timestamp;
            $sessionusers = array();
            $sessionusers[$message->userid] = 1;
        }
        $lasttime = $message->timestamp;
    }

/// Finish the page
    print_footer($course);

?>
