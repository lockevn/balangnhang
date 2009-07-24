<?php 
/**
 * Course format that displays a study calendar, integrating Moodle calendar
 * items along with course activities located in specific weeks.
 * 
 * Originally based on the weekscss format.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 */

require_once($CFG->dirroot.'/course/format/studycal/lib.php');

$streditsummary  = get_string('editsummary');
$stradd          = get_string('add');
$stractivities   = get_string('activities');
$strshowallweeks = get_string('showallweeks');
$strweek         = get_string('week');
$strgroups       = get_string('groups');
$strgroupmy      = get_string('groupmy');
$editing         = $PAGE->user_is_editing();

$strstudycal = get_string('studycalendar','format_studycal');
$sesskey=sesskey();

$context = get_context_instance(CONTEXT_COURSE, $course->id);


// Internet Explorer min-width fix. (See theme/standard/styles_layout.css: min-width for Firefox.)
// Window width: 800px, Firefox 763px, IE 752px. (Window width: 640px, Firefox 602px, IE 588px.)    
?>



<!--[if IE]>
  <style type="text/css">
  .weekscss-format { width: expression(document.body.clientWidth < 800 ? "752px" : "auto"); }
  </style>
<![endif]-->
<?php
// Set pix path for extra images for study calendar plugin
$CFG->pixpathextra = $CFG->pixpath;
if (file_exists(dirname(__FILE__).'/pix')) {
    $CFG->pixpathextra = $CFG->wwwroot.'/course/format/studycal/pix';
}

// Layout the whole page as three big columns (was, id="layout-table")
print '<div class="studycal-format'.($editing ? ' editing' : '').'">';

// The left column ...
if(($hasleft=blocks_have_content($pageblocks, BLOCK_POS_LEFT)) || $editing) {
    print '<div id="left-column">';
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    print '</div>';
}

// The right column, BEFORE the middle-column.
if (($hasright=blocks_have_content($pageblocks, BLOCK_POS_RIGHT)) || $editing) {
    print '<div id="right-column">';
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
    print '</div>';
}

// Start main column
$classes=$hasleft ? 'has-left-column ' : '';
$classes.=$hasright ? 'has-right-column ' : '';
$classes=trim($classes);
if($classes) {
    print '<div id="middle-column" class="'.$classes.'">';
} else {    
    print '<div id="middle-column">';
}
print skip_main_destination();

// If currently moving a file then show the current clipboard
if (ismoving($course->id)) {
    $stractivityclipboard = strip_tags(get_string('activityclipboard', '', addslashes($USER->activitycopyname)));
    $strcancel= get_string('cancel');
    print '<div class="clipboard">';
    print $stractivityclipboard.'&nbsp;&nbsp;(<a href="mod.php?cancelcopy=true&amp;sesskey='.$USER->sesskey.'">'.$strcancel.'</a>)';
    print "</div>\n";
}

// Print Section 0 with general activities

$section = 0;
$thissection = $sections[$section];

if ($thissection->summary or $thissection->sequence or $editing) {

    print '<div id="section-0" class="section main" >';
    print '<div class="content">';
    
    print '<div class="summary">';
    $summaryformatoptions->noclean = true;
    print format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);

    if ($editing) {
        print '<p><a title="'.$streditsummary.'" '.
             ' href="editsection.php?id='.$thissection->id.'"><img src="'.$CFG->pixpath.'/t/edit.gif" '.
             ' class="icon edit" alt="'.$streditsummary.'" /></a></p>';
    }
    print '</div>';
    
    print_section($course, $thissection, $mods, $modnamesused);

    if ($editing) {
        print_section_add_menus($course, $section, $modnames);
    }

    print '</div>';
    print "</div>\n";
}

// Don't show anything when there are no extra weeks...
if($course->numsections>0) {

// Get 'all weeks' option from user session
$showallweeks = !empty($USER->allweeks[$course->id]);

// Get calendar setttings
$studycal=studycal_get_settings($course->id);

// Build list of section IDs to query for week-specific settings 
// (Array of sectionid => settings object)
$sectionids='';
foreach($sections as $section) {
    if($sectionids!=='') {
        $sectionids .= ', ';
    } 
    $sectionids.=$section->id;    
}
$weeksettings=get_records_select('studycal_weeks',"sectionid IN ($sectionids)",'',
    'sectionid,groupwithsectionid,hidenumber,resetnumber,hidedate,title');

$showcontrolicons=$editing && empty($studycal->lock) 
  && has_capability('format/studycal:manage',$context);

// Calendar heading
// Check if calendar views should be hidden
if (isset($CFG->ousitexxx) && get_field('oucoursefeatures', 'hidefromcalendarviews', 'courseid', $course->id) == 1) {
    $strmultcals = '';
} else {

// Check number of courses for user
$mycourses = get_my_courses($USER->id, 'shortname ASC', 'id, shortname, format, visible', false);

// Check user courses
$strview = get_string('view', 'format_studycal');
$strmultcals = "<div class='studycaltopright'><span class='studycalheadertext'>$strview</span> ";
if(!empty($mycourses)) {
    // Count courses that are the new study calendar format
    $i = 0;
    while ((list($myid, $mycourse) = each($mycourses)) && $i < 2) {
        if ($mycourse->format == 'studycal') {
            $i++;
        }
    } 
    if ($i >= 2) {
        $strviewmultcals = get_string('viewmultiplecalendars', 'format_studycal');
        $strmultcals .= "<a title='$strviewmultcals' href='{$CFG->wwwroot}/course/format/studycal/calendars.php?id={$course->id}'>".
                       "<img src='{$CFG->pixpathextra}/i/cal_combi.gif' alt='$strviewmultcals' class='studycalimg'/>".
                       "</a>";
    }
}
$strviewcoursecals = get_string('viewcoursecalendars', 'format_studycal');
$date = usergetdate(time());
$dtmon = $date['mon'];
$dtyear = $date['year'];
$strmultcals .= "<a title='$strviewcoursecals' href='".htmlspecialchars("{$CFG->wwwroot}/calendar/view.php?view=month&course={$course->id}&cal_d=1&cal_m={$dtmon}&cal_y={$dtyear}")."'>".
                "<img src='{$CFG->pixpathextra}/i/cal_organ.gif' alt='$strviewcoursecals' class='studycalimg'/>".
                "</a>";
$strmultcals .= "</div>";
}

if($editing) {    
    print "<h2 class='studycaltop'>$strmultcals<span class='studycaltopleft'>";    
    if($showcontrolicons) {
        $strcalendaredit=get_string('editcalendarsettings','format_studycal');
        print
"<form method='get' action='{$CFG->wwwroot}/course/format/studycal/edit.php'>".
"<input type='hidden' name='course' value='{$course->id}' />".
"<input type='image' src='{$CFG->pixpath}/i/edit.gif' alt='$strcalendaredit' />".
"</form> ";
    }
    if($canlock=has_capability('format/studycal:lock',$context)) {
        print 
"<form method='post' action='{$CFG->wwwroot}/course/format/studycal/lock.php'>".
"<input type='hidden' name='sesskey' value='$sesskey' />".
"<input type='hidden' name='course' value='{$course->id}' />";
        if($studycal->lock) {
            $strcalendarlocked=get_string('calendarlocked','format_studycal');
            print
"<input type='hidden' name='lock' value='0' />".
"<input type='image' src='{$CFG->pixpathextra}/i/locked.gif' alt='$strcalendarlocked' />";
        } else {
            $strcalendarunlocked=get_string('calendarunlocked','format_studycal');
            print
"<input type='hidden' name='lock' value='1' />".
"<input type='image' src='{$CFG->pixpathextra}/i/unlocked.gif' alt='$strcalendarunlocked' />";
        }
        print "</form>";
    } else if(has_capability('format/studycal:manage',$context)) {
        if($studycal->lock) {
            $un='';
            $alt=get_string('calendarlocked','format_studycal');
        } else {
            $un='un';
            $alt=get_string('calendarunlocked','format_studycal');
        }
        print "<img src='{$CFG->pixpathextra}/i/{$un}locked.gif' alt='$alt' />";
    }
    
    print "</span>$strstudycal</h2>";
} else {
    print "<h2 class='studycaltop'>$strmultcals$strstudycal</h2>";
}

// Get Moodle calendar entries
$moodleentries = get_moodle_calendar_entries($course->id);
$moodleindex=0;

// Different hack for adding tick boxes, avoiding duplicate labels
// and just to avoid a trivial change to core course/lib.php
$modinfo = unserialize((string)$course->modinfo);
if (!empty($modinfo)) {
    $hack = false;
    foreach($modinfo as $cm => $mod) {
        if ($mod->mod == 'label') {
            $mod->extra = urlencode('<!--label cmid='.$cm.'-->'.urldecode($mod->extra));
            $hack = true;
        }
    }
    if ($hack) {
        $course->modinfo = serialize($modinfo);
    }
}

$trackprogress=has_capability('format/studycal:trackprogress',$context,null,false);
if($trackprogress || $editing) {
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
    global $hideboxescm;
    $hideboxescm=array();
    $hideboxesevent=array();
    while(!$rs->EOF) {
        if(!empty($rs->fields['coursemoduleid'])) {
            $hideboxescm[$rs->fields['coursemoduleid']]=true;
        } else if(!empty($rs->fields['eventid'])) {
            $hideboxesevent[$rs->fields['eventid']]=true;
        }
        $rs->MoveNext();
    }
    
    // Get the list of tickboxes the user has already ticked.
    global $ticked;
    $ticked=array();
    if($trackprogress) {
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
                $ticked['cm'.$rs->fields['coursemoduleid']]=true;
            } else if(!is_null($rs->fields['eventid'])) {
                $ticked['e'.$rs->fields['eventid']]=true;
            } 
            $rs->MoveNext();
        }
    }

    if(!$editing) {
        if(count($ticked) < 2) {
            print '<p class="studycalexplanation">'.get_string('checkboxexplanation','format_studycal').'</p>';
        }
        print '<noscript><p class="studycalexplanation">'.get_string('checkboxnojs','format_studycal').'</p></noscript>';
    }
}         

// Now all the normal modules by week
// Everything below uses "section" terminology - each "section" is a week.


print "<ul class='studycalweeks'>";


$timenow = time();
$weekdate = $studycal->startdateoffset+$course->startdate;  // this should be 0:00 Monday of that week
$weekdate += 7200;                 // Add two hours to avoid possible DST problems
$strftimedateshort = get_string('strftimedateshort');
$strftimetime=get_string('strftimetime');
$strweek=get_string('week','format_studycal');
$weeknumber=1;
$now=time();
$oddrow=true;

$contentchunks=array();
$currentchunk=0;
global $section;
for($section=1;$section<=$course->numsections;$section++) {
    
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

        // Check odd or even row        
        $extraclasses=$oddrow?' oddrow':' evenrow';
        $oddrow=!$oddrow;
        
        // See if we're in that week
        $strthisweek='';
        if($now >= $weekdate && $now < $enddate) {
            $currentchunk=count($contentchunks);
            $extraclasses.=' current';
            $strthisweek=get_string('thisweek','format_studycal');
        }
        if($numweeks>1) {
            $extraclasses.=' grouped';
        }
            
        // Start creating the week's content. Don't print directly because we
        // might only be displaying current weeks.
        $chunk="<li class='studycalsection$extraclasses' id='section-$section'><div class='studycalleft'>";
        
        // Reset week number if needed
        if(isset($thisweek->resetnumber) && !is_null($thisweek->resetnumber)) {
            $weeknumber=$thisweek->resetnumber;
        }
        
        if(empty($thisweek->hidenumber) && empty($studycal->hidenumbers)) {
            if($numweeks==1) {
                $weekrange=$weeknumber;
            } else {
                $weekrange=$weeknumber.'<div class="studycalweekdivider">&#x2022;</div>'.($weeknumber+$numweeks-1);
            }
            $chunk.="<h3 class='weeknum'><span class='accesshide'>$strthisweek$strweek </span>$weekrange</h3>";
        }
        
        if($showcontrolicons) {
            $strcombineweeks=get_string('combineweeks','format_studycal');
            $strsplitweeks=get_string('splitweeks','format_studycal');
            $streditweeks=get_string('editweeksettings','format_studycal');            
            $chunk.="<div class='controlicons'>";
            $chunk.=
"<form method='get' action='{$CFG->wwwroot}/course/format/studycal/editweek.php'>".
"<input type='hidden' name='course' value='{$course->id}' />".
"<input type='hidden' name='section' value='$section' />".
"<input type='image' src='{$CFG->pixpath}/i/edit.gif' alt='$streditweeks' />".
" </form>";
            if($section>1) {
                $chunk.= 
"<form method='post' action='{$CFG->wwwroot}/course/format/studycal/combineweeks.php'>".
"<input type='hidden' name='sesskey' value='$sesskey' />".
"<input type='hidden' name='course' value='{$course->id}' />".
"<input type='hidden' name='section' value='$section' />".
"<input type='hidden' name='combine' value='1' />".
"<input type='image' src='{$CFG->pixpathextra}/i/combineweeks.gif' alt='$strcombineweeks' />".
" </form>";
            }
            if(($numweeks)>1) {
                $chunk.= 
"<form method='post' action='{$CFG->wwwroot}/course/format/studycal/combineweeks.php'>".
"<input type='hidden' name='sesskey' value='$sesskey' />".
"<input type='hidden' name='course' value='{$course->id}' />".
"<input type='hidden' name='section' value='$section' />".
"<input type='hidden' name='combine' value='0' />".
"<input type='image' src='{$CFG->pixpathextra}/i/splitweeks.gif' alt='$strsplitweeks' />".
" </form>";
            }
            $chunk.= '</div>';
        }
        
        $chunk.= '</div><div class="studycalcontent"><div class="studycalcontentdeco1"><div class="studycalcontentdeco2">';
        
        // Build up heading
        $doneheading=false;
        if(empty($thisweek->hidedate)) {
            // We use server time to display week dates, avoiding inconsistencies that are
            // caused with userdate (can get 6-day weeks). For some reason, the date tends 
            // to come with a zero at the front, so we get that out.
            $timedisplay=trim(userdate($weekdate,$strftimedateshort,666));
            $chunk.= '<div class="weekheader"><span class="weekdate">'.$timedisplay.'</span>';
            $doneheading=true;
        }        
        if(isset($thisweek->title) && !is_null($thisweek->title)) {
            if($doneheading) {
                $chunk.=' &#x2022; ';
            } else {
                $chunk.='<div class="weekheader">';
            }
            $chunk.='<span class="weektitle">'.htmlspecialchars($thisweek->title).'</span>';
            $doneheading=true;
        }
        if($doneheading) {
            $chunk.='</div>';
        }
        $chunk.= '<div class="studycalactivities">';
            
        ob_start();
        print_section($course, $thissection, $mods, $modnamesused);
        if ($editing) {
            print_section_add_menus($course, $section, $modnames);
        }
        $sectionhtml=ob_get_contents();
        ob_end_clean();
        if(($trackprogress || $editing) && !ismoving($course->id)) {
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
        $chunk.=$sectionhtml;
        if($sectionhtml==='') {
            $chunk.='&nbsp;';
        }
        
        $chunk.='</div>';
        
        $someentries=false;
        while($moodleindex < count($moodleentries) && $moodleentries[$moodleindex]->timestart < $enddate) {
            $thisentry=$moodleentries[$moodleindex];
            if(!$someentries) {
                $someentries=true;
                $chunk.='<h4 class="accesshide">'.get_string('events','format_studycal').'</h4>';
                $chunk.='<ul class="studycalentries">';
            }
            
            $chunk.='<li class="studycalevent">';
            if($trackprogress || $editing) {
                $hidethisbox=!empty($hideboxesevent[$thisentry->id]);                                
                if($editing) {
                    $hide=!$hidethisbox;
                    $hideshow=$hide ? 'hide' : 'show';
                    $chunk.="
<div class='studycalcheckbox'>
<form action='format/studycal/hidebox.php' method='post'>
<input type='hidden' name='sesskey' value='$sesskey' />
<input type='hidden' name='section' value='$section' />
<input type='hidden' name='course' value='{$course->id}' />
<input type='hidden' name='event' value='{$thisentry->id}' />
<input type='hidden' name='hide' value='$hide' />
<input type='image' src='{$CFG->pixpath}/t/$hideshow.gif' />
</form>".
($hidethisbox ? '' : "<input type='checkbox' disabled='disabled'/>").
"</div>";
                } else if(!$hidethisbox) {
                    $checked=isset($ticked['e'.$thisentry->id]) ? 'checked="checked" ': '';
                    $chunk.="
<div class='studycalcheckbox'>
<input type='checkbox' disabled='disabled' id='studycal_{$course->id}_e{$thisentry->id}' class='studycalcheckbox' $checked/>
</div>";
                }
            }
            $chunk.=htmlspecialchars($thisentry->name).' (';
            
            
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
                    $duration.=($weeks==1 ? get_string('week','format_studycal') : 
                        get_string('weeks','format_studycal',$weeks)).' ';
                }
                if($days) {
                    $duration.=($days==1 ? get_string('day','format_studycal') :
                        get_string('days','format_studycal',$days)).' ';
                }
                if($hours) {
                    $duration.=($hours==1 ? get_string('hour','format_studycal') :
                        get_string('hours','format_studycal',$hours)).' ';
                }
                if($mins) {
                    $duration.=($mins==1 ? get_string('min','format_studycal') :
                        get_string('mins','format_studycal',$mins)).' ';
                }
                
                $a->datetime=$datetime;
                $a->duration=$duration;
                $overall=trim(get_string('duration','format_studycal',$a));
                
            } else {
                $overall=$datetime;
            }
                                  
            $chunk.=$overall.')</li>';
            
            $moodleindex++;            
        }
        if($someentries) {
            $chunk.='</ul>';
        }
        
        $chunk.='</div></div></div></li>';
        if($showallweeks) {
            print $chunk;
        } else {
            $contentchunks[]=$chunk;
        }        
    }    
    $weekdate+=604800;
    $weeknumber++;
}

if(!$showallweeks) {
    $first=$currentchunk-(int)($studycal->weekstoview/2);
    if($first+$studycal->weekstoview > count($contentchunks)) {
        $first=count($contentchunks)-$studycal->weekstoview;
    }
    if($first<0) {
        $first=0;
    }
    for($i=$first;$i<$first+$studycal->weekstoview && $i<count($contentchunks);$i++) {
        print $contentchunks[$i];
    }
}

print '</ul>';

// Write Javascript that manages the progress checkboxes
if($trackprogress && !$editing) {
    print_studycal_checkbox_js();
}

// Show links to view other people's progress
$viewallprogress=has_capability('format/studycal:viewallprogress',$context);
$myfirstgroup=mygroupid($course->id);
if(is_array($myfirstgroup) && count($myfirstgroup)>0) {
    $myfirstgroup=$myfirstgroup[0];
} else {
    $myfirstgroup=false;
}
$viewgroupprogress=has_capability('format/studycal:viewgroupprogress',$context) && $myfirstgroup;
if($viewallprogress || $viewgroupprogress) {
    print '<div class="viewprogress">';
    print '<h3>'.get_string('viewprogress','format_studycal').'</h3>';
    print '<ul>';
    if($viewallprogress) {
        print "<li><a href='format/studycal/viewprogress.php?course={$course->id}'>".
            get_string('course','format_studycal').'</a></li>';
    } 
    if($viewgroupprogress) {
        print "<li><a href='format/studycal/viewprogress.php?course={$course->id}&amp;group={$myfirstgroup}'>".
            get_string('group','format_studycal').'</a></li>';
    }
    print '</ul></div>';
}

// Calendar footer
$newall = $showallweeks ? 0 : 1;
$strshowlink=$newall ? get_string('showallweeks','format_studycal') : get_string('showcurrentweeks','format_studycal');
print "<div class='studycalbottom'>
<a href='{$CFG->wwwroot}/course/format/studycal/setweeks.php?course={$course->id}&amp;all=$newall'>$strshowlink</a>
</div>";
}

print '</div>';

print '</div>';
print '<div class="clearer"></div>';

?>