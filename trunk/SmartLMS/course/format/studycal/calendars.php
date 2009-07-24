<?php
/**
 * Script run from course view page to allow selection of and display of
 * multiple course calendars for a user. Based upon existing format.php
 *
 * @copyright &copy; 2007 The Open University
 * @author D.A.Woolhead@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

define('WEEKSTOVIEW', 7); // Default weeks to view
define('MAXCALS', 3); // Maximum number of calendars to view

require_once(dirname(__FILE__).'/../../../config.php');
require_once(dirname(__FILE__).'/lib.php');

// Set pix path for extra images for study calendar plugin
$CFG->pixpathextra = $CFG->pixpath;
if (file_exists(dirname(__FILE__).'/pix')) {
    $CFG->pixpathextra = $CFG->wwwroot.'/course/format/studycal/pix';
}

$courseid = required_param('id', PARAM_INT);
$select = optional_param('select', array(), PARAM_INT);

// Make sure user is logged in
require_login();

// Make sure maximum courses not exceeded
if (count($select) > MAXCALS) {
    error(get_string('toomanycourses', 'format_studycal', MAXCALS), $_SERVER['PHP_SELF'].'?id='.$courseid);
}

// Toggle showallweeks option if necessary
global $USER;
if (($all = optional_param('all', -1, PARAM_INT)) != -1) {
    $USER->multcalweeks = $all;
}
$showallweeks = !empty($USER->multcalweeks) ? true : false;

// Get user courses
$mycourses = get_my_courses($USER->id, 'shortname ASC', 'id, shortname, format, numsections, visible', false);// derekw testing //

// Check user courses
if (empty($mycourses)) {
    error(get_string('metanopotentialcourses'));
}

if (isset($CFG->ousite)) {
    // Filter courses to hide from calendar views
    require_once(dirname(__FILE__).'/../../../local/lib.php');
    filter_hidden_courses($mycourses);

    // Check user courses again (just in case)
    if (empty($mycourses)) {
        error(get_string('metanopotentialcourses'));
    }
}

// Make courseid is a valid study calendar course
if (empty($mycourses[$courseid]) || $mycourses[$courseid]->format != 'studycal' || $mycourses[$courseid]->numsections < 1) {
    error(get_string('invalidcoursesettings', 'format_studycal'));
}

// Get/Build selected courses
$selected = empty($select) ? false : true;
$selectable = 0;
$max = empty($select) ? MAXCALS - 1 : count($select);
$content = '';
foreach ($mycourses as $id => $course) {
    if ($course->format == 'studycal' && $course->numsections >= 1) {
        $selectable++;
        $checked = false;
        if (in_array($id, $select)) {
            $checked = true;
        } else if (!$selected) {
            if ($id == $courseid || count($select) < $max) {
                $select[] = $id;
                $checked = true;
                if ($id == $courseid) {
                    $max++;
                }
            }
        }
        $content .= '<div class="coursecheckbox">';
        $content .= print_checkbox('select[]', $id, $checked, $course->shortname, 'Select to include in multiple calendar view', '', true);
        $content .= '</div>';
    }
}

// Define courses and study calendar arrays
$courses = array();
$studycals = array();
$trackprogress = array();

// Define default weeks to view and earliest start date variables
$weekstoview = WEEKSTOVIEW; // Consider max of courses->weekstoview;
$allstartdate = 9999999999;
$adjstartdate = false;

// Get courses and associated study calendars
for ($i = 0; $i < count($select); $i++) {

    // Get next course
    $courses[$i] = get_record('course', 'id', $select[$i]);

    // Get study calendar details for course
    $studycals[$i] = studycal_get_settings($select[$i]);

    // Store course study calendar start date (inc usual DST adj)
    $courses[$i]->calstartdate = strtotime(strftime('%d %b %Y', $studycals[$i]->startdateoffset + $courses[$i]->startdate + 7200)) + 7200;

    // Set earliest course study calendar start date
    if ($courses[$i]->calstartdate < $allstartdate) {
        $allstartdate = $courses[$i]->calstartdate;
    }

    // Set course study calendar start day
    $courses[$i]->calstartday = date('w', $courses[$i]->calstartdate);

    // Set flag to adjust course study calendar start date to a Saturday if days differ
    if ($i >= 1 && $courses[0]->calstartday != $courses[$i]->calstartday) {
        $adjstartdate = true;
    }
}

// Adjust earliest course study calendar start day to a Saturday if required
if ($adjstartdate && date('w', $allstartdate) != 6) {
    $allstartdate -= (date('w', $allstartdate) + 1)*86400;
    $allstartdate = strtotime(strftime('%d %b %Y', $allstartdate)) + 7200;
}

$strviewcals = get_string('viewcalendars', 'format_studycal');
$stryourcals = get_string('yourstudycalendars', 'format_studycal');
$navigation=array();
$navigation[]=array('name' => $stryourcals, 'link' => '', 'type' => 'studycal');
print_header($stryourcals, get_string('calendar','calendar'),
    build_navigation($navigation));

// Display list of courses
$strselectcals=get_string('selectcalendars','format_studycal');
$strshowselected=get_string('showselected','format_studycal');

print '<div class="studycal-format">';

if (count($select) < $selectable || $selectable >= 4) {
    print "<form method='get' action='".$_SERVER['PHP_SELF']."'>";
    print "<input type='hidden' name='id' value='{$courseid}' />";
    print $content;
 
    // Display edit button
    print '<div class="showbutton">';
    print "<input type='submit' value='$strshowselected' alt='$strselectcals' />";
    print '</div>';
    print "</form>";
}

// Display study calendar heading
$strstudycal = get_string('studycalendar','format_studycal');
$strview = get_string('view', 'format_studycal');
$strviewmultcals = get_string('viewmultiplecalendars', 'format_studycal');
$strviewcoursecals = get_string('viewcoursecalendars', 'format_studycal');

$strmultcals = "<div class='studycaltopright'><span class='studycalheadertext'>$strview</span> ";
$strmultcals .= "<img src='{$CFG->pixpathextra}/i/cal_combi_f2.gif' alt='' class='studycalimg'/>";

$date = usergetdate(time());
$dtmon = $date['mon'];
$dtyear = $date['year'];
$strmultcals .= "<a title='$strviewcoursecals' href='".htmlspecialchars("{$CFG->wwwroot}/calendar/view.php?view=month&course={$courseid}&cal_d=1&cal_m={$dtmon}&cal_y={$dtyear}")."'>".
                "<img src='{$CFG->pixpathextra}/i/cal_organ.gif' alt='$strviewcoursecals' class='studycalimg'/>".
                "</a>";

$strmultcals .= "</div>";
print "<h2 class='studycaltop'>$strmultcals$strstudycal</h2>";

$editing = false;// derekw testing - to be tidied up //

// Define variables only required once, not for all selected courses
$allsections = 0;

// Do what format.php does, but for all selected courses
for ($i = 0; $i < count($select); $i++) {

    // Set current section number for course
    $courses[$i]->cursection = 0 - floor(($courses[$i]->calstartdate - $allstartdate + 7200)/604800);

    // Set maximum number of sections to process
    if ($allsections < abs($courses[$i]->cursection) + $courses[$i]->numsections) {
        $allsections = abs($courses[$i]->cursection) + $courses[$i]->numsections;
    }

    $course = $courses[$i];
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $contexts[$i] = $context;

    // Get calendar setttings
    $studycal = $studycals[$i];

    // require sections (Code copied from course/view.php)
    get_all_mods($course->id, $arrmods[$i], $arrmodnames[$i], $arrmodnamesplural[$i], $arrmodnamesused[$i]);

    if (! $sections = get_all_sections($course->id)) {   // No sections found
        // Double-check to be extra sure
        if (! $section = get_record('course_sections', 'course', $course->id, 'section', 0)) {
            $section->course = $course->id;   // Create a default section.
            $section->section = 0;
            $section->visible = 1;
            $section->id = insert_record('course_sections', $section);
        }
        if (! $sections = get_all_sections($course->id) ) {      // Try again
            error('Error finding or creating section structures for this course');
        }
    }
    $arrsections[$i] = $sections;

    // Build list of section IDs to query for week-specific settings
    // (Array of sectionid => settings object)
    $sectionids='';
    foreach($sections as $section) {
        if($sectionids!=='') {
            $sectionids.=', ';
        }
        $sectionids.=$section->id;
    }
    $arrweeksettings[$i]=get_records_select('studycal_weeks',"sectionid IN ($sectionids)",'',
        'sectionid,groupwithsectionid,hidenumber,resetnumber,hidedate,title');

    // Get Moodle calendar entries
    $arrmoodleentries[$i] = get_moodle_calendar_entries($course->id);
    $moodleindex[$i]=0;

    // Different hack for adding tick boxes, avoiding duplicate labels
    // and just to avoid a trivial change to core course/lib.php
    $modinfo = unserialize($course->modinfo);
    if($modinfo) {
        foreach($modinfo as $cm => $mod) {
            if ($mod->mod == 'label') {
                $mod->extra = urlencode('<!--label cmid='.$cm.'-->'.urldecode($mod->extra));
            }
        }
        $courses[$i]->modinfo = serialize($modinfo);
    }

    $arrticked[$i]=array();
    $arrhideboxescm[$i]=array();
    $hideboxesevent[$i]=array();

    $trackprogress[$i]=has_capability('format/studycal:trackprogress',$context,null,false);
    if($trackprogress[$i]) {
        // Get the list of tickboxes to hide
        $rs=get_recordset_sql("
    SELECT
      coursemoduleid,eventid
    FROM
      {$CFG->prefix}studycal_hideboxes
    WHERE
      courseid={$course->id}");
        if(!$rs) {
            error('Failed to obtain list of hidden boxes');
        }
        while(!$rs->EOF) {
            if(!empty($rs->fields['coursemoduleid'])) {
                $arrhideboxescm[$i][$rs->fields['coursemoduleid']]=true;
            } else if(!empty($rs->fields['eventid'])) {
                $hideboxesevent[$i][$rs->fields['eventid']]=true;
            }
            $rs->MoveNext();
        }

        // Get the list of tickboxes the user has already ticked.
        if($trackprogress[$i]) {
            $rs=get_recordset_sql("
    SELECT
      coursemoduleid,eventid
    FROM
      {$CFG->prefix}studycal_ticks
    WHERE
      courseid={$course->id} AND userid={$USER->id}
            ");
            if(!$rs) {
                error('Failed to obtain tick list');
            }
            while(!$rs->EOF) {
                if(!is_null($rs->fields['coursemoduleid'])) {
                    $arrticked[$i]['cm'.$rs->fields['coursemoduleid']]=true;
                } else if(!is_null($rs->fields['eventid'])) {
                    $arrticked[$i]['e'.$rs->fields['eventid']]=true;
                }
                $rs->MoveNext();
            }
        }
    }
} // end for ($i = 0; $i < count($select); $i++)

print '<noscript><p class="studycalexplanation">'.get_string('checkboxnojs','format_studycal').'</p></noscript>';

// Now all the normal modules by week
// Everything below uses "section" terminology - each "section" is a week.

$weekdate = $allstartdate;
$weekdate += 7200;                 // Add two hours to avoid possible DST problems
$strftimedateshort = get_string('strftimedateshortest', 'format_studycal');
$strftimetime=get_string('strftimetime');
$strweek=get_string('week','format_studycal');
$timenow=time();
$oddrow=true;

// Work out start and end sections if not displaying all sections
$sectionstart = 1;
$sectionendall = $allsections;
if (!$showallweeks) {
    $weekstonow = floor(($timenow + 7200 - $allstartdate)/604800);
    if ($weekstonow > floor($weekstoview/2)) {
        $sectionstart = 1 + $weekstonow - floor($weekstoview/2);
    }
    if (($sectionstart + $weekstoview - 1) <= $allsections) {
        $sectionendall = $sectionstart + $weekstoview - 1;
    } else if ($allsections >= $weekstoview) {
        $sectionstart = $allsections - $weekstoview + 1;
    }
}

$contentchunks=array();
$currentchunk=0;
global $section;

print '<table cellspacing="4" class="sctable">';
print '<tr>';
$tdwidthcol1 = 4;
$tdwidth = floor((100 - $tdwidthcol1)/count($select));
print '<td class="filler" style="width: '.$tdwidthcol1.'%">&nbsp;</td>';
for ($i = 0; $i < count($select); $i++) {
    print '<th scope="col" class="content" style="width: '.$tdwidth.'%">'.
          '<a title="'.$courses[$i]->shortname.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$courses[$i]->id.'">'.
          $courses[$i]->shortname.'</a>'.
          '</th>';
    $courses[$i]->weeknumber = 1;
}
print '</tr>';

global $idx;
// Start from 1 to maintain sequence of weeks
for($xsection=1;$xsection<=$sectionendall;$xsection++) {

    // Check odd or even row
    $extraclasses=$oddrow?' oddrow':' evenrow';
    $oddrow=!$oddrow;

    // See if we're in that week
    if($timenow >= $weekdate && $timenow < ($weekdate + 604800)) {
        $currentchunk=count($contentchunks);
        $extraclasses.=' current';
    }

    // Start creating the week's content. Don't print directly because we
    // might only be displaying current weeks.
    $chunk = '<tr class="'.$extraclasses.'">';
    $chunk .= '<th scope="row" class="week" style="width: '.$tdwidthcol1.'%">';

    // We use server time to display week dates, avoiding inconsistencies that are
    // caused with userdate (can get 6-day weeks). For some reason, the date tends
    // to come with a zero at the front, so we get that out.
    $timedisplay=trim(userdate($weekdate,$strftimedateshort,666));
    $chunk.= $timedisplay;
    $chunk .= '</th>';
    global $COURSE;
    $oldcourse=$COURSE;

    for ($idx = 0; $idx < count($select); $idx++) {

        // Set current course section
        $section = ++$courses[$idx]->cursection;
        $course = $courses[$idx];
        $COURSE = $course;
        if ($section < 1 || $section > $course->numsections) {
            $chunk .= '<td class="nocontent" style="width: '.$tdwidth.'%">&nbsp;</td>';
        } else {

            $weeknumber = $course->weeknumber;
            $studycal = $studycals[$idx];
            $mods = $arrmods[$idx];
            $modnamesused = $arrmodnamesused[$idx];
            $sections = $arrsections[$idx];
            $weeksettings = $arrweeksettings[$idx];
            $moodleentries = $arrmoodleentries[$idx];

            // Create actual section if it's missing
            if (!empty($sections[$section])) {
                $thissection = $sections[$section];
            } else {
                unset($thissection);
                 $thissection->course = $course->id;   // Create a new week structure
                $thissection->section = $section;
                $thissection->summary = '';
                $thissection->visible = 1;
                if (!$thissection->id = insert_record('course_sections', $thissection)) {
                    notify('Error inserting new week!');
                }
            }

            // Get week settings
            if(isset($weeksettings[$thissection->id])) {
                $thisweek=$weeksettings[$thissection->id];
            } else {
                $thisweek=new StdClass;
            }

            // Check for grouped week - these aren't displayed because
            // they were shown with their parent week
            if(empty($thisweek->groupwithsectionid)) {

                // Count grouped weeks and hack it to include mods from other sections in this one
                $index=$section+1;
                while(!empty($sections[$index]) && !empty($weeksettings[$sections[$index]->id]->groupwithsectionid)
                    && $weeksettings[$sections[$index]->id]->groupwithsectionid==$thissection->id) {
                    if($sections[$index]->sequence) {
                        $thissection->sequence.=','.$sections[$index]->sequence;
                        $thissection->sequence=str_replace(',,',',',$thissection->sequence);
                    }
                    $index++;
                }
                $numweeks=$index-$section;
                $enddate=$weekdate + ($numweeks*604800);

                if($numweeks>1) {
                    $extraclasses.=' grouped';
                }

                // Initialise td chunk
                $tdchunk = '';

                // Build up heading
                $doneheading=false;

                // Reset week number if needed
                if(isset($thisweek->resetnumber) && !is_null($thisweek->resetnumber)) {
                    $weeknumber=$thisweek->resetnumber;
                }

                $weekrange = '';
                if(empty($thisweek->hidenumber) && empty($studycal->hidenumbers)) {
                    if($numweeks==1) {
                        $weekrange='Week '.$weeknumber;
                    } else {
                        $weekrange='Weeks '.$weeknumber.' &#x2022; '.($weeknumber+$numweeks-1);
                    }
                }

                if(isset($thisweek->title) && !is_null($thisweek->title)) {
                    if($doneheading) {
                        $tdchunk.='<div class="weekheader">';
                    } else {
                        $tdchunk.='<div class="weekheader">';
                    }
                    $tdchunk.='<span class="weektitle">'.htmlspecialchars($thisweek->title).'</span>';
                    $doneheading=true;
                }
                if($doneheading) {
                    $tdchunk.='</div>';
                }
                $tdchunk.= '<div>';

                ob_start();

                // frig to ignore editing mode
//                $USER->editingvalidated = true;
                $editingsave = $USER->editing;
                $USER->editing = false;

                print_section($course, $thissection, $mods, $modnamesused);

                // frig to undo ignore of editing mode
                $USER->editing = $editingsave;
//                $USER->editingvalidated = false;

                $sectionhtml=ob_get_contents();
                ob_end_clean();
                if($trackprogress || $editing) {
                    // Set up variables used as global (yuk) in studycal_insert_checkbox()
                    $ticked = $arrticked[$idx];
                    $hideboxescm = $arrhideboxescm[$idx];

                    global $doinglabels;
                    $doinglabels=true;
                    $sectionhtml=preg_replace_callback(
                        '/(<span class="dimmed_text"><h3>|<span class="dimmed_text">|<h3>|)(<!--label cmid=([0-9]+)-->)/',
                        'studycal_insert_checkbox',$sectionhtml);
                    $doinglabels=false;
                    $sectionhtml=preg_replace_callback(
                        '~(<li class="activity (?!label).*?">)(.*?<a [^>]*?view\.php\?id=([0-9]+).*?</li>)~s',
                        'studycal_insert_checkbox',$sectionhtml);
                }
                $tdchunk.=$sectionhtml;
                if($sectionhtml==='') {
                    $tdchunk.='&nbsp;';
                }

                $tdchunk.='</div>';

                $someentries=false;
                while($moodleindex[$idx] < count($moodleentries) && $moodleentries[$moodleindex[$idx]]->timestart < $enddate) {
                    $thisentry=$moodleentries[$moodleindex[$idx]];
                    if(!$someentries) {
                        $someentries=true;
                        $tdchunk.='<h4 class="accesshide">'.get_string('events','format_studycal').'</h4>';
                        $tdchunk.='<ul class="studycalentries">';
                    }

                    $tdchunk.='<li class="studycalevent">';
                    if($trackprogress || $editing) {
                        $hidethisbox=!empty($hideboxesevent[$thisentry->id]);
                        if(!$hidethisbox) {
                            $checked=isset($ticked['e'.$thisentry->id]) ? 'checked="checked" ': '';
                            $tdchunk.="
<div class='studycalcheckbox'>
<input type='checkbox' disabled='disabled' id='studycal_{$course->id}_e{$thisentry->id}' $checked/>
</div>";
                        }
                    }
                    $tdchunk.=htmlspecialchars($thisentry->name).' (';


                    // Dates other than midnight include time too
                    if(strtotime("0:00",$thisentry->timestart) != $thisentry->timestart) {
                        // When a specific time is given, we display in user time
                        $a->date=trim(userdate($thisentry->timestart, $strftimedateshort));
                        $a->time=userdate($thisentry->timestart,$strftimetime);
                        $datetime=get_string('datetime','format_studycal',$a);
                    } else {
                        // Get basic date. Use server date not local for date-only events.
                        $datetime=trim(userdate($thisentry->timestart,$strftimedateshort,666));
                    }

                    // Durations other than 0 are displayed
                    if($thisentry->timeduration>59) {
                        $left=$thisentry->timeduration;
                        $weeks=(int)($left/604800);
                        $left-=$weeks*604800;
                        $days=(int)($left/86400);
                        $left-=$days*86400;
                        $hours=(int)($left/3600);
                        $left-=$hours*3600;
                        $mins=(int)($left/60);

                        $duration='';
                        if($weeks) {
                            $duration.=get_string('weeks','format_studycal',$weeks).' ';
                        }
                        if($days) {
                            $duration.=get_string('days','format_studycal',$days).' ';
                        }
                        if($hours) {
                            $duration.=get_string('hours','format_studycal',$hours).' ';
                        }
                        if($mins) {
                            $duration.=get_string('mins','format_studycal',$mins).' ';
                        }

                        $a->datetime=$datetime;
                        $a->duration=$duration;
                        $overall=trim(get_string('duration','format_studycal',$a));

                    } else {
                        $overall=$datetime;
                    }

                    $tdchunk.=$overall.')</li>';

                    $moodleindex[$idx]++;
                }
                if($someentries) {
                    $tdchunk.='</ul>';
                }

                // 
                if($numweeks>1) {
                    if (($weeksbefore = $sectionstart - $xsection) >= 1 && ($weeksafter = $xsection + $numweeks - $sectionstart) >= 1) {
                        // Note: I know it may have rowspan = "1", but it doesn't break anything
                        $chunk .= '<td class="content" style="width: '.$tdwidth.'%" rowspan="'.$weeksbefore.'">'.$weekrange.$tdchunk.'</td>';
                        $weekrange .= ' ('.$timedisplay.')';
                        $sectionstartchunk[$idx] = '<td class="content" style="width: '.$tdwidth.'%" rowspan="'.$weeksafter.'">'.trim($weekrange).$tdchunk.'</td>';
                    } else {
                        $chunk .= '<td class="content" style="width: '.$tdwidth.'%" rowspan="'.$numweeks.'">'.$weekrange.$tdchunk.'</td>';
                    }
                } else {
                    $chunk .= '<td class="content" style="width: '.$tdwidth.'%">'.$weekrange.$tdchunk.'</td>';
                }
            } else { // end if(empty($thisweek->groupwithsectionid))
               if ($xsection == $sectionstart) {
                    if (!empty($sectionstartchunk[$idx])) {
                        $chunk .= $sectionstartchunk[$idx];
                    } else {
                        $chunk .= '<td class="content" style="width: '.$tdwidth.'%">'.'Error'.'</td>';
                    }
               }
            }
            $courses[$idx]->weeknumber = ++$weeknumber;
        } // end (!if ($section < 1 || $section > $course->numsections))
    } // end for ($i = 0; $i < count($select); $i++)
    $COURSE=$oldcourse;

    $chunk .= '</tr>';
    if($showallweeks) {
        print $chunk;
    } else {
        $contentchunks[]=$chunk;
    }
    $weekdate+=604800;
}

if(!$showallweeks) {
    $first=$currentchunk-(int)($weekstoview/2);
    if($first+$weekstoview > count($contentchunks)) {
        $first=count($contentchunks)-$weekstoview;
    }
    if($first<0) {
        $first=0;
    }
    for($i=$first;$i<$first+$weekstoview && $i<count($contentchunks);$i++) {
        print $contentchunks[$i];
    }
}
print '</table>';

// Calendar footer
$newall = $showallweeks ? 0 : 1;
$strshowlink=$newall ? get_string('showallweeks','format_studycal') : get_string('showcurrentweeks','format_studycal');
print '<div class="studycalbottom">';
print "<a class='showlink' href='".$_SERVER['PHP_SELF']."?id={$courseid}&amp;all=$newall";
for ($i = 0; $i < count($select); $i++) {
    print '&amp;select[]='.$select[$i];
}
print "'>$strshowlink</a>";
print '</div>';

// Write Javascript that manages the progress checkboxes
if($trackprogress && !$editing) {
    print_studycal_checkbox_js();
}

print '</div>'; // end print '<div class="format-studycal">';

print_footer();

?>