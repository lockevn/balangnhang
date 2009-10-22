<?php // $Id: format.php,v 1.5 2009/05/07 16:12:46 mchurch Exp $
      // Display the whole course as "topics" made of of modules
      // In fact, this is very similar to the "weeks" format, in that
      // each "topic" is actually a week.  The main difference is that
      // the dates aren't printed - it's just an aesthetic thing for
      // courses that aren't so rigidly defined by time.
      // Included from "view.php"

    require_once($CFG->libdir.'/ajax/ajaxlib.php');
    require_once($CFG->dirroot.'/mod/forum/lib.php');

    require_once($CFG->dirroot.'/course/format/'.$course->format.'/course_format.class.php');
    require_once($CFG->dirroot.'/course/format/'.$course->format.'/course_format_fn.class.php');
    require_once($CFG->dirroot.'/course/format/'.$course->format.'/lib.php');
    require_once($CFG->dirroot.'/course/format/'.$course->format.'/modulelib.php');

    $cobject = new course_format_fn($course);

    $course = $cobject->course;

    /// Handle any extra arguments
    $cobject->handle_extra_actions();

    /// Add any extra module information to our module structures.
    $cobject->add_extra_module_info();

    $selected_week = optional_param('selected_week', -1, PARAM_INT);

    // Bounds for block widths
    // more flexible for theme designers taken from theme config.php
    $lmin = (empty($THEME->block_l_min_width)) ? 100 : $THEME->block_l_min_width;
    $lmax = (empty($THEME->block_l_max_width)) ? 210 : $THEME->block_l_max_width;
    $rmin = (empty($THEME->block_r_min_width)) ? 100 : $THEME->block_r_min_width;
    $rmax = (empty($THEME->block_r_max_width)) ? 210 : $THEME->block_r_max_width;

    define('BLOCK_L_MIN_WIDTH', $lmin);
    define('BLOCK_L_MAX_WIDTH', $lmax);
    define('BLOCK_R_MIN_WIDTH', $rmin);
    define('BLOCK_R_MAX_WIDTH', $rmax);

    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]),
                                            BLOCK_R_MAX_WIDTH);

    /// G8 - Store the selected_week in a Session variable for each course reached.
    /// A selected week above 999 is a tab range change...
    $tabrange = 0;
    if ($selected_week > 999) {
        $tabrange = $selected_week;
        $selected_week = $SESSION->G8_selected_week[$course->id];
        list($tablow, $tabhigh, $selected_week) = $cobject->get_week_info($tabrange, $selected_week);
    } else if ($selected_week > -1) {
        $SESSION->G8_selected_week[$course->id] = $selected_week;
    }
    else if (isset($SESSION->G8_selected_week[$course->id])) {
        $selected_week = $SESSION->G8_selected_week[$course->id];
    }
    else {
        $SESSION->G8_selected_week[$course->id] = $selected_week;
    }

    $cobject->context = get_context_instance(CONTEXT_COURSE, $course->id);

    if (($marker >=0) && has_capability('moodle/course:setcurrentsection', $cobject->context) && confirm_sesskey()) {
        $course->marker = $marker;
        if (! set_field("course", "marker", $marker, "id", $course->id)) {
            error("Could not mark that topic for this course");
        }
    }

    $streditsummary   = get_string("editsummary");
    $stradd           = get_string("add");
    $stractivities    = get_string("activities");
    $strshowalltopics = get_string("showalltopics");
    $strtopic         = get_string("topic");
    $strgroups       = get_string("groups");
    $strgroupmy      = get_string("groupmy");
    $editing          = $PAGE->user_is_editing();

    if ($editing) {
        $strstudents = moodle_strtolower($course->students);
        $strtopichide = get_string("topichide", "", $strstudents);
        $strtopicshow = get_string("topicshow", "", $strstudents);
        $strmarkthistopic = get_string("markthistopic");
        $strmarkedthistopic = get_string("markedthistopic");
        $strmoveup = get_string("moveup");
        $strmovedown = get_string("movedown");
    }

    $isediting = isediting($course->id);
    $isteacher = has_capability('moodle/grade:viewall', $cobject->context);

/// Add the selected_week to the course object (so it can be used elsewhere).
    $course->selected_week = $selected_week;

/// Layout the whole page as three big columns.
    echo '<table id="layout-table" cellspacing="0"><tr valign="top">';

    /// If we are using mandatory activities, until they are completed only show them.
    if (!empty($course->usemandatory) && !$cobject->all_mandatory_completed($course->id, $cobject->mods)) {
    /// Start main column
        echo '<td id="middle-column" align="center">';

        echo '<table class="weeks" width="*" cellpadding="8">';
        echo "<tr>";
        echo "<td valign=top class=\"fnweeklycontent\" width=\"100%\">";
        $cobject->print_mandatory_section();
        echo '</td></tr></table>';
        echo '</td>';

    } else {

    /// The left column ...

        if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
            echo '<td style="vertical-align: top; width: '.$preferred_width_left.'px;" id="left-column">';
            blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
            echo '</td>';
        }

    /// Start main column
        echo '<td id="middle-column">';

//        /// Added a center blocks position
//        if (blocks_have_content($pageblocks, BLOCK_POS_CENTER) || $editing) {
//            blocks_print_group($PAGE, $pageblocks, BLOCK_POS_CENTER);
//        }

        echo '<table class="weeks" width="100%">';

    /// If currently moving a file then show the current clipboard
        if (ismoving($course->id)) {
            $stractivityclipboard = strip_tags(get_string('activityclipboard', '', addslashes($USER->activitycopyname)));
            $strcancel= get_string('cancel');
            echo '<tr class="clipboard">';
            echo '<td colspan="3">';
            echo $stractivityclipboard.'&nbsp;&nbsp;(<a href="mod.php?cancelcopy=true&amp;sesskey='.$USER->sesskey.'">'.$strcancel.'</a>)';
            echo '</td>';
            echo '</tr>';
        }


    /// Print Section 0 with general activities

        $section = 0;
        $cobjectsection = $sections[$section];

        if (!empty($course->showsection0) && ($cobjectsection->summary || $cobjectsection->sequence || $isediting)) {

            echo "<tr id=\"section-0\" class=\"section main\">";
            echo '<td colspan="3" align="center" width="100%" id="fnsection0" class="content">';
            if (empty($course->sec0title)) {
                $course->sec0title = '';
            }
            if ($isediting) {
                if (empty($_GET['edittitle']) || ($_GET['edittitle'] != 'sec0')) {
                    echo $course->sec0title;
                    $path = $CFG->wwwroot.'/course';
                    if (empty($THEME->custompix)) {
                        $pixpath = $path.'/../pix';
                    } else {
                        $pixpath = $path.'/../theme/'.$CFG->theme.'/pix';
                    }
                    echo ' <a title="'.get_string('edit').'" href="'.$CFG->wwwroot.'/course/view.php?id='.
                         $course->id.'&amp;edittitle=sec0"><img src="'.$pixpath.'/t/edit.gif" /></a>';
                } else if ($_GET['edittitle'] == 'sec0') {
                    echo '<form name="editsec0title" method="post" '.
                         'action="'.$CFG->wwwroot.'/course/format/fn/mod.php">'.
                         '<input name="id" type="hidden" value="'.$course->id.'" />'.
                         '<input name="sec0title" type="text" size="20" value="'.$course->sec0title.'" />'.
                         '<input style="font-size: 8pt; margin: 0 0 0 2px; padding: 0 0 0 0;" type="submit" '.
                         'value="ok" title="Save">'.
                         '</form>';
                } else {
                    echo $course->sec0title;
                }
            } else {
                echo $course->sec0title;
            }
            echo '</td></tr>';

            echo '<tr id="section-0" class="section main">';
            echo '<td class="left side">&nbsp;</td>';
            echo '<td class="content">';

            echo '<div class="summary">';

//            echo "<tr>";
//            echo "<td nowrap class=\"fnweeklyside\" valign=top width=20>&nbsp;</td>";
//            echo "<td valign=top class=\"fnweeklycontent\" width=\"100%\">";

            echo '<table cellspacing="0" cellpadding="0" border="0" align="center">'
                .'<tr><td>';

            $summaryformatoptions->noclean = true;
            echo format_text($cobjectsection->summary, FORMAT_HTML, $summaryformatoptions);

            if ($isediting) {
                echo " <a title=\"$streditsummary\" ".
                     " href=\"editsection.php?id=$cobjectsection->id\"><img height=11 width=11 src=\"$CFG->pixpath/t/edit.gif\" ".
                     " border=0 alt=\"$streditsummary\"></a><br />";
            }

            echo '<br clear="all">';

            /// If showannouncements is off, remove the news forum from the mod list.
            if (empty($cobject->course->showannouncements)) {
                $news = forum_get_course_forum($course->id, 'news');
                $modnum = get_field('modules', 'id', 'name', 'forum');
                foreach ($mods as $key => $mod) {
                    if (($mod->module == $modnum) && ($mod->instance == $news->id)) {
                        unset($mods[$key]);
                        break;
                    }
                }
            }

            $cobject->print_section_fn($cobjectsection);

            if ($isediting) {
                $cobject->print_section_add_menus($section);
            }

            echo '</td></tr></table>';

            echo '</td>';
            echo '<td class="right side">&nbsp;</td>';
            echo '</tr>';
//            echo "</td>";
//            echo "<td nowrap class=\"fnweeklyside\" valign=top align=center width=10>";
//            echo "&nbsp;</td></tr>";
            if ($course->numsections > 0) {
                echo "<tr><td colspan=3><img src=\"../pix/spacer.gif\" width=1 height=1></td></tr>";
            } else {
                echo "<tr height=\"20\"><td colspan=\"3\"></td></tr>";
            }
        }
        echo '</table>';

        if (empty($course->showonlysection0)) {

        /// Now all the weekly sections
            $timenow = time();
            $weekdate = $course->startdate;    // this should be 0:00 Monday of that week
            $weekdate += 7200;                 // Add two hours to avoid possible DST problems
            $section = 1;
            $weekofseconds = 604800;
            $course->enddate = $course->startdate + ($weekofseconds * $course->numsections);
            $sectionmenu = array();

        //  Calculate the current week based on today's date and the starting date of the course.
            $currentweek = ($timenow > $course->startdate) ?
                            (int)((($timenow - $course->startdate) / $weekofseconds)+1) : 0;
            $currentweek = min($currentweek, $course->numsections);

            $strftimedateshort = " ".get_string("strftimedateshort");

        /// If the selected_week variable is 0, all weeks are selected.
            if ($selected_week == -1 && $currentweek == 0) {
                $selected_week = 0;
                $section = $selected_week;
                $numsections = $course->numsections;
            }
            else if ($selected_week == -1) {
                if (isteacher($course->id) ||
                    (!empty($course->activitytracking) && ($selected_week = $cobject->first_unfinished_section()) === false)) {
                    $selected_week = $currentweek;
                }
                $selected_week = ($selected_week > $currentweek) ? $currentweek : $selected_week;
                $section = $selected_week;
                $numsections = MAX($section, 1);
            }
            else if ($selected_week != 0) {
                /// Teachers can select a future week; students can't.
                if (($selected_week > $currentweek) && !$isteacher) {
                    $section = $currentweek;
                } else {
                    $section = $selected_week;
                }
                $numsections = $section;
            }
            else {
                $numsections = $course->numsections;
            }

            $selected_week = ($selected_week < 0) ? 1 : $selected_week;

            // If the course has been set to more than zero sections, display normal.
            if ($course->numsections > 0) {
                /// Forcing a style here, seems to be the only way to force a zero bottom margin...
                if (!empty($course->mainheading)) {
                    $strmainheading = $course->mainheading;
                } else {
                    $strmainheading = get_string('defaultmainheading', 'format_fn');
                }
                print_heading_block($strmainheading, 'fnoutlineheadingblock" style="margin-bottom:0;');
                if ($selected_week > 0 && !$isediting) {
                    echo '<table class="topicsoutline" border="0" cellpadding="8" cellspacing="0" width="100%">
            <tr><td valign=top class="fntopicsoutlinecontent fnsectionouter" width="100%">
            <!-- Tabbed section container -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
                    ';
                    if ($course->numsections > 1) {
                        echo '
                <!-- Tabs -->
                <tr>
                    <td width="100%">
                        ';
                        echo $cobject->print_weekly_activities_bar($selected_week, $tabrange);
    //                    echo $cobject->print_weekly_activities_bar_2($selected_week);
                        echo '
                    </td>
                </tr>
                <!-- Tabs -->
                        ';
                    }
                    echo '
                <!-- Selected Tab Content -->
                <tr>
                    <!-- This cell holds the same colour as the selected tab. -->
                    <td width="100%" class="fnweeklynavselected">
                        <!-- This table creates a selected colour box around the content -->
                        <table width="100%" cellpadding="5" cellspacing="0" border="0">
                            <tr>
                                <td>
                        <table width="100%" cellpadding="1" cellspacing="0" border="0">
                            <tr>
                                <td>
                    ';
                } else if ($course->numsections > 1) {
                    echo '<table class="topicsoutline" border="0" cellpadding="8" cellspacing="0" width="100%">';

                    echo '<tr>';
                    echo '<td valign="top" class="fntopicsoutlinecontent fnsectionouter" width="100%">';
                    echo $cobject->print_weekly_activities_bar($selected_week, $tabrange);
    //                echo $cobject->print_weekly_activities_bar_2($selected_week);
                    echo '</td>';
                    echo '</tr>';

                    echo '</table>';
                }

                if (isset($course->topicheading) && !empty($course->topicheading)) {
                    $heading_prefix = $course->topicheading;
                } else {
                    $heading_prefix = 'Week ';
                }
            } else {
                $section = 1;
                $numsections = 1;
                $weekdate = 0;
                $heading_prefix = 'Section ';
            }

        /// Now all the normal modules by topic
        /// Everything below uses "section" terminology - each "section" is a topic.

            if ($section <= 0) $section = 1;
            while (($course->numsections > 0) && ($section <= $numsections)) {

                echo '<table class="topicsoutline" border="0" cellpadding="4" cellspacing="0" width="100%">';

                if (!empty($sections[$section])) {
                    $cobjectsection = $sections[$section];

                } else {
                    unset($cobjectsection);
                    $cobjectsection->course = $course->id;   // Create a new section structure
                    $cobjectsection->section = $section;
                    $cobjectsection->summary = "";
                    $cobjectsection->visible = 1;
                    if (!$cobjectsection->id = insert_record("course_sections", $cobjectsection)) {
                        notify("Error inserting new topic!");
                    }
                }

                $showsection = ($isteacher || ($cobjectsection->visible && ($timenow > $weekdate)));

                if ($showsection) {

                    $currenttopic = ($course->marker == $section);
        //            if (!$cobjectsection->visible || ($timenow < $weekdate) || ($selected_week > $currentweek)) {
                    if (!$cobjectsection->visible || ($selected_week > $currentweek)) {
                        $colorsides = "class=\"fntopicsoutlinesidehidden\"";
                        $colormain  = "class=\"fntopicsoutlinecontenthidden\"";
                    } else if ($currenttopic) {
                        $colorsides = "class=\"fntopicsoutlinesidehighlight\"";
                        $colormain  = "class=\"fntopicsoutlinecontenthighlight\"";
                    } else {
                        $colorsides = "class=\"fntopicsoutlineside\"";
                        $colormain  = "class=\"fntopicsoutlinecontent fntopicsoutlineinner\"";
                    }

                    if ($selected_week <= 0 || $isediting) {
                        echo '<tr><td colspan="3" '.$colorsides.' align="center">';
                        echo $heading_prefix.$section;
                        echo '</td></tr>';

                        echo "<tr>";
                        echo '<td nowrap '.$colorsides.' valign="top" width="20">&nbsp;</td>';
                    } else {
                        echo "<tr>";
                    }

                    if (!$isteacher and !$cobjectsection->visible) {   // Hidden for students
                        echo "<td valign=top align=center $colormain width=\"100%\">";
                        echo get_string("notavailable");
                        echo "</td>";
                    } else {
                        echo "<td valign=top $colormain width=\"100%\">";

                        if (isset($cobject->course->expforumsec) && ($cobject->course->expforumsec == $cobjectsection->section)) {
                            echo '<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%">'
                                .'<tr><td>';
                        } else {
                            echo '<table cellspacing="0" cellpadding="0" border="0" align="center">'
                                .'<tr><td>';
                        }

                        echo format_text($cobjectsection->summary, FORMAT_HTML);

                        if ($isediting) {
                            echo " <a title=\"$streditsummary\" href=editsection.php?id=$cobjectsection->id>".
                                 "<img src=\"$CFG->pixpath/t/edit.gif\" border=0 height=11 width=11></a><br />";
                        }

                        echo '<br clear="all">';

        //                $mandatorypopup = print_section_local($course, $cobjectsection, $mods, $modnamesused);
                        $cobject->print_section_fn($cobjectsection);

                        if ($isediting) {
                            $cobject->print_section_add_menus($section);
                        }

                        echo '</td></tr></table>';

                        echo "</td>";
                    }

                    if ($selected_week <= 0 || $isediting) {
                        echo '<td nowrap '.$colorsides.' valign="top" align="center" width="20">';
                        echo "<font size=1>";
                    }

                    if ($isediting) {
                        if ($course->marker == $section) {  // Show the "light globe" on/off
                            echo "<a href=\"view.php?id=$course->id&marker=0&amp;sesskey=$USER->sesskey\" title=\"$strmarkedthistopic\">".
                                 "<img src=\"$CFG->pixpath/i/marked.gif\" vspace=3 height=16 width=16 border=0></a><br />";
                        } else {
                            echo "<a href=\"view.php?id=$course->id&marker=$section&amp;sesskey=$USER->sesskey\" title=\"$strmarkthistopic\">".
                                 "<img src=\"$CFG->pixpath/i/marker.gif\" vspace=3 height=16 width=16 border=0></a><br />";
                        }

                        if ($cobjectsection->visible) {        // Show the hide/show eye
                            echo "<a href=\"view.php?id=$course->id&hide=$section&amp;sesskey=$USER->sesskey\" title=\"$strtopichide\">".
                                 "<img src=\"$CFG->pixpath/i/hide.gif\" vspace=3 height=16 width=16 border=0></a><br />";
                        } else {
                            echo "<a href=\"view.php?id=$course->id&show=$section&amp;sesskey=$USER->sesskey\" title=\"$strtopicshow\">".
                                 "<img src=\"$CFG->pixpath/i/show.gif\" vspace=3 height=16 width=16 border=0></a><br />";
                        }

                        if ($section > 1) {                       // Add a arrow to move section up
                            echo "<a href=\"view.php?id=$course->id&section=$section&move=-1&amp;sesskey=$USER->sesskey\" title=\"$strmoveup\">".
                                 "<img src=\"$CFG->pixpath/t/up.gif\" vspace=3 height=11 width=11 border=0></a><br />";
                        }

                        if ($section < $course->numsections) {    // Add a arrow to move section down
                            echo "<a href=\"view.php?id=$course->id&section=$section&move=1&amp;sesskey=$USER->sesskey\" title=\"$strmovedown\">".
                                 "<img src=\"$CFG->pixpath/t/down.gif\" vspace=3 height=11 width=11 border=0></a><br />";
                        }

                    }

                    if ($selected_week <= 0 || $isediting) {
                        echo "</td>";
                    }
                    echo "</tr>";

                    if ($selected_week <= 0 || $isediting) {
                        echo '<tr><td colspan="3" '.$colorsides.' align="center">';
                        echo '&nbsp;';
                        echo '</td></tr>';

                        echo "<tr><td colspan=3><img src=\"../pix/spacer.gif\" width=1 height=1></td></tr>";
                    }

                    $weekdate += ($weekofseconds);
                }

                echo '</table>';

                $section++;
            }

            if ($selected_week > 0 && !$isediting) {
                    echo '
                                </td>
                            </tr>
                        </table>
                        <!-- This table creates a selected colour box around the content -->
                    </td>
                    <!-- This cell holds the same colour as the selected tab. -->
                </tr>
                <!-- Selected Tab Content -->
            </table>
                    </td>
                    <!-- This cell holds the same colour as the selected tab. -->
                </tr>
                <!-- Selected Tab Content -->
            </table>
            <!-- Tabbed section container -->
            </td></tr></table>
            <br><br>
            <!-- Tabbed section container -->
                    ';
            }
        }

        if (!empty($sectionmenu)) {
            echo '<div align="center" class="jumpmenu">';
            echo popup_form($CFG->wwwroot.'/course/view.php?id='.$course->id.'&', $sectionmenu,
                       'sectionmenu', '', get_string('jumpto'), '', '', true);
            echo '</div>';
        }

        echo "</td>";

        // The right column
        if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing) {
            echo '<td style="vertical-align: top; width: '.$preferred_width_right.'px;" id="right-column">';
            blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
            echo '</td>';
        }

    }

    echo "</tr>\n";
    echo "</table>\n";
?>
