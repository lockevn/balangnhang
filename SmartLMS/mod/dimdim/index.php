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

    require_once('../../config.php');
    require_once('lib.php');

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = get_record('course', 'id', $id)) {
        error('Course ID is incorrect');
    }

    require_course_login($course);

    add_to_log($course->id, 'dimdim', 'view all', "index.php?id=$course->id", '');


/// Get all required strings

    $strdimdims = get_string('modulenameplural', "dimdim");
    $strdimdim  = get_string('modulename', 'dimdim');


/// Print the header

    print_header_simple($strdimdims, '', $strdimdims, '', '', true, '', navmenu($course));

/// Get all the appropriate data

    if (! $dimdims = get_all_instances_in_course('dimdim', $course)) {
        notice('There are no dimdims', "../../course/view.php?id=$course->id");
        die();
    }

/// Print the list of instances (your module will probably extend this)

    $timenow  = time();
    $strname  = get_string('name');
    $strweek  = get_string('week');
    $strtopic = get_string('topic');

    if ($course->format == 'weeks') {
        $table->head  = array ($strweek, $strname);
        $table->align = array ('center', 'left');
    } else if ($course->format == 'topics') {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ('center', 'left', 'left', 'left');
    } else {
        $table->head  = array ($strname);
        $table->align = array ('left', 'left', 'left');
    }

    $currentsection = '';
    foreach ($dimdims as $dimdim) {
        if (!$dimdim->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$dimdim->coursemodule\">".format_string($dimdim->name,true)."</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$dimdim->coursemodule\">".format_string($dimdim->name,true)."</a>";
        }
        $printsection = '';
        if ($dimdim->section !== $currentsection) {
            if ($dimdim->section) {
                $printsection = $dimdim->section;
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $dimdim->section;
        }
        if ($course->format == 'weeks' or $course->format == 'topics') {
            $table->data[] = array ($printsection, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo '<br />';

    print_table($table);

/// Finish the page

    print_footer($course);

?>
