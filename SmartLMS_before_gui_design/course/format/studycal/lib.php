<?php
/**
 * Shared calendar functions.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */
 
require_once(dirname(__FILE__).'/exceptions.php');

/**
 * Gets study calendar settings for a course. If not defined, uses 
 * defaults.
 * @param int $courseid Course ID
 * @return object An object with ->hidenumbers, ->lock, ->weekstoview, and ->startdateoffset,
 *   and with ->defaultrecord non-empty if this didn't really come from db.
 */
function studycal_get_settings($courseid) {
    if(!($studycal=get_record('studycal','courseid',$courseid))) {
        // Use default settings
        $studycal=new StdClass;
        $studycal->hidenumbers=0;
        $studycal->lock=0;
        $studycal->weekstoview=3;
        $studycal->startdateoffset=0;
        $studycal->defaultrecord=true;
    }
    return $studycal;     
}

/**
 * Wipes entire study calendar details for course including calendar settings,
 * week settings, and actually deleting any imported content.
 * @param object $course Moodle course object
 * @throws Exception In case of failure
 */
function studycal_wipe($course) {
    global $CFG;
    
    // Delete calendar settings
    if(!execute_sql("DELETE FROM {$CFG->prefix}studycal WHERE courseid={$course->id}",false)) {
        throw new Exception('Error deleting calendar settings',EXN_STUDYCAL_LIB_WIPE);
    }
    if(!execute_sql("
DELETE FROM {$CFG->prefix}studycal_weeks WHERE sectionid IN (
  SELECT id FROM {$CFG->prefix}course_sections WHERE course={$course->id})",false)) {
        throw new Exception('Error deleting week settings',EXN_STUDYCAL_LIB_WIPE);
    }

    // Get list of entries that need deleting 
    $cms=get_records_sql("
SELECT 
  cm.* 
FROM 
  {$CFG->prefix}studycal_imported i 
  INNER JOIN {$CFG->prefix}course_modules cm ON i.coursemoduleid=cm.id
WHERE
  i.courseid={$course->id}
");
    if(!$cms) {
        $cms=array();
    }
if (!empty($cms)) {
    foreach($cms as $cm) {
        // Remove from section
        if(!delete_mod_from_section($cm->id,$cm->section)) {
            throw new Exception('Error removing module from section',EXN_STUDYCAL_LIB_WIPE);
        }
        
        // Delete entry
        if(!delete_course_module($cm->id)) {
            throw new Exception('Error deleting module reference',EXN_STUDYCAL_LIB_WIPE);
        }
        // Delete label
        if(!delete_records('label','id',$cm->instance)) {
            throw new Exception('Error deleting label',EXN_STUDYCAL_LIB_WIPE);
        }
    } 
}
    
    // Clear list of entries
    if(!execute_sql("DELETE FROM {$CFG->prefix}studycal_imported WHERE courseid={$course->id}",false)) {
        throw new Exception('Error clearing imported item records',EXN_STUDYCAL_LIB_WIPE);
    } 
}

/**
 * Sets the number of sections for a course, including creating section objects
 * if needed.
 * @param object $course Moodle course entry object
 * @param int $numsections Number of sections desired
 * @return array Array of section objects numbered 1..$numsections (also includes section 0)
 */
function studycal_set_num_sections($course,$numsections) {
    // Adjust number of sections if needed
    if($course->numsections!=$numsections) {
        if(!set_field('course','numsections',$numsections,'id',$course->id)) {
            throw new Exception('Failed to set number of sections',EXN_STUDYCAL_LIB_SECTIONS);
        }        
    }
    
    // Get sections and create if not present
    $sections=get_all_sections($course->id);
    if(!$sections) {
        // It returns false if there are no sections even when there's not an error!
        // So we have no way to detect an error. Nice.
        $sections=array();
    }
    for($i=1;$i<=$numsections;$i++) {
        if(!isset($sections[$i])) {
            $sections[$i]=new StdClass;
            $sections[$i]->course = $course->id;
            $sections[$i]->section = $i;
            $sections[$i]->summary = ''; // Studycal doesn't show summaries anyhow
            $sections[$i]->visible = 1; // Studycal ignores this too
            if (!$sections[$i]->id = insert_record('course_sections', $sections[$i])) {
                throw new Exception('Error inserting new section record',EXN_STUDYCAL_LIB_SECTIONS);
            }            
        }
    }
    
    return $sections;
}

/**
 * Initialises settings if needed.
 * @param object $course Moodle course object
 * @param int $startdate Actual date of start of calendar.
 */
function studycal_init_settings($course,$startdate) {
    global $CFG;

    $relative=$startdate-$course->startdate;
    if($relative!=0) {
        if(!execute_sql("
INSERT INTO {$CFG->prefix}studycal(courseid,startdateoffset) 
VALUES({$course->id},$relative)",false)) {
            throw new Exception('Error creating calendar settings',EXN_STUDYCAL_LIB_SETTINGS);
        }        
    }
}

/**
 * Initialises settings if needed using relative date.
 * @param int $courseid Course ID
 * @param int $startdateoffset Start date relative to course start
 * @param int $hidenumbers 0 = show week numbers, 1 = hide all week numbers
 * @param int $weekstoview Number of weeks to show by default
 * @param int $lock 0 = unlocked, 1 = locked
 */
function studycal_init_settings_relative($courseid,$startdateoffset=0,$hidenumbers=0,$weekstoview=3,$lock=0) {
    global $CFG;
    
    if($startdateoffset==0 && $hidenumbers==0 && $weekstoview==3 && $lock==0) {
        return;        
    } 
    if(!execute_sql("
INSERT INTO {$CFG->prefix}studycal(courseid,startdateoffset,hidenumbers,weekstoview,lock) 
VALUES({$courseid},$startdateoffset,$hidenumbers,$weekstoview,$lock)",false)) {
        throw new Exception('Error creating calendar settings',EXN_STUDYCAL_LIB_SETTINGS);
    }        
}

/**
 * Initialises week settings.
 * @param object $course Moodle course object
 * @param object $section Section object
 * @param bool $hidenumber True to hide week number
 * @param int $resetnumber Null if not resetting number, a value otherwise
 * @param string $title Null if not setting title, a value otherwise 
 */
function studycal_init_week_settings($course,$section,$hidenumber,$resetnumber,$title) {
    global $CFG;

    $sqltitle=is_null($title) ? 'NULL' : "'".addslashes($title)."'";
    $sqlhidenumber=$hidenumber ? 1 : 0;
    $sqlresetnumber=is_null($resetnumber) ? 'NULL' : (int)$resetnumber; 
    
    if(!execute_sql("
INSERT INTO {$CFG->prefix}studycal_weeks(sectionid,hidenumber,resetnumber,title) 
VALUES({$section->id},$sqlhidenumber,$sqlresetnumber,$sqltitle)",false)) {
        throw new Exception('Error creating week settings',EXN_STUDYCAL_LIB_WEEKSETTINGS);
    }   
}

/**
 * Finds section ID given number.
 * @param int $courseid Moodle course ID
 * @param int $sectionnumber Section number
 * @return int Section ID
 * @throws Exception If section does not exist in database
 */
function studycal_find_section($courseid,$sectionnumber) {
    global $CFG;
    $rs=get_recordset_sql("
SELECT id 
FROM {$CFG->prefix}course_sections
WHERE course=$courseid AND section=$sectionnumber");
    if($rs->EOF) {
        throw new Exception('Error finding required section',EXN_STUDYCAL_LIB_SECTION);
    }
    return $rs->fields['id'];
}

/**
 * Initialises week settings.
 * @param int $courseid Moodle course ID
 * @param int $sectionnumber Section number
 * @param int $groupwithsectionnumber Section number for grouping, null if not grouped
 * @param bool $hidenumber True to hide week number
 * @param int $resetnumber Null if not resetting number, a value otherwise
 * @param string $title Null if not setting title, a value otherwise 
 */
function studycal_init_week_settings_without_objects($courseid,$sectionnumber,
    $groupwithsectionnumber,$hidenumber,$hidedate,$resetnumber,$title) {
    global $CFG;

    $sqltitle=is_null($title) ? 'NULL' : "'".addslashes($title)."'";
    $sqlhidenumber=$hidenumber ? 1 : 0;
    $sqlresetnumber=is_null($resetnumber) ? 'NULL' : (int)$resetnumber;
    
    $sectionid=studycal_find_section($courseid,$sectionnumber);  
    
    if(is_null($groupwithsectionnumber)) {
        $groupwithsectionid='NULL';
    } else {
        $groupwithsectionid=studycal_find_section($courseid,$groupwithsectionnumber);
    }
    
    if(!execute_sql("
INSERT INTO {$CFG->prefix}studycal_weeks(sectionid,groupwithsectionid,hidenumber,resetnumber,title) 
VALUES($sectionid,$groupwithsectionid,$sqlhidenumber,$sqlresetnumber,$sqltitle)",false)) {
        throw new Exception('Error creating week settings',EXN_STUDYCAL_LIB_WEEKSETTINGS);
    }   
}

/**
 * Adds entry to a week of the calendar as a Moodle label.
 * @param object $course Moodle course object
 * @param object &$section Section object
 * @param int $col Column number
 * @param string $header Header for column
 * @param string $entry Text of entry
 */
function studycal_add_entry($course,&$section,$col,$header,$entry) {
    global $CFG;
    
    if($header) {
        $title="<span class='studycalheader'>$header</span>: <span class='studycalentry'>$entry</span>";
    } else {
        $title="<span class='studycalentry'>$entry</span>";
    }
    $title=addslashes($title);
    
    // Set up label object
    $label=new StdClass;
    $label->course=$course->id;
    $label->name=shorten_text($title);
    $label->content=$title;
    $label->displaytype=1; // For some reason 'text' is 1 while 'heading' is 0.
    if(!($label->id=insert_record('label',$label))) {
        throw new Exception('Error creating entry label',EXN_STUDYCAL_LIB_ENTRY);
    }
    
    // Find label module ID
    if(!($moduleid=get_field('modules','id','name','label'))) {
        throw new Exception('Error finding label module',EXN_STUDYCAL_LIB_ENTRY);
    }
    
    // Put it in course
    $cm=new StdClass;
    $cm->course=$course->id;
    $cm->module=$moduleid;
    $cm->instance=$label->id;
    $cm->section=$section->id;
    $cm->visible=1;
    if(!($cm->id=add_course_module($cm))) {
        throw new Exception('Error creating entry reference',EXN_STUDYCAL_LIB_ENTRY);
    }
    
    // Add it to section    
    $cm->coursemodule=$cm->id;
    $cm->section=$section->section;
    if(!add_mod_to_section($cm)) {
        throw new Exception('Error updating section sequence',EXN_STUDYCAL_LIB_ENTRY);
    }
    
    // Record that we made it
    studycal_mark_imported($course->id,$cm->id,$col);
}

/**
 * Sets on (or off) hiding of a tickbox.
 * @param int $courseid Moodle course ID
 * @param int $coursemoduleid=0 Coursemodule defining tickbox, or 0 if event
 * @param int $eventid=0 Event defining tickbox, or 0 if coursemodule
 * @param bool $hide True to hide, false to reveal 
 */
function studycal_set_hide_box($courseid,$coursemoduleid,$eventid,$hide) {
    global $CFG;
    
    if(!$coursemoduleid && !$eventid) {
        throw new Exception('Must specify either coursemodule or event',EXN_STUDYCAL_LIB_HIDEINVAL);
    }

    if($coursemoduleid) {
        $instancename='coursemoduleid';
        $instanceid=$coursemoduleid;
    } else {
        $instancename='eventid';
        $instanceid=$eventid;    
    }
    
    if($hide) {    
        // Check if it's already ticked
        $rs=get_recordset_sql("
SELECT 
  COUNT(*) AS cnt
FROM 
  {$CFG->prefix}studycal_hideboxes
WHERE
  courseid=$courseid AND $instancename=$instanceid");
        if(!$rs) {
            throw new Exception('Failed to check for existing concealment',EXN_STUDYCAL_LIB_HIDEDB);
        }
        if($rs->fields['cnt']==0) {
            // Only do anything if it's not already there.   
            if(!execute_sql("
INSERT INTO
  {$CFG->prefix}studycal_hideboxes (courseid,$instancename)
VALUES
  ($courseid,$instanceid)",false)) {
                throw new Exception('Failed to add concealer to database',EXN_STUDYCAL_LIB_HIDEDB);
            }
        }   
    } else {
        if(!execute_sql("
DELETE FROM 
  {$CFG->prefix}studycal_hideboxes 
WHERE courseid=$courseid AND $instancename=$instanceid",false)) {
            throw new Exception('Failed to delete concealer from database',EXN_STUDYCAL_LIB_HIDEDB);
        }
    }
}

/**
 * Adds a line to the studycal_imported table.
 * @param int $courseid Moodle course ID
 * @param int $coursemoduleid Course-module ID
 * @param int $col Column number
 */
function studycal_mark_imported($courseid,$coursemoduleid,$col) {
    global $CFG;
    if(!execute_sql("
INSERT INTO {$CFG->prefix}studycal_imported(courseid,coursemoduleid,col) 
VALUES($courseid,$coursemoduleid,$col)",false)) {
        throw new Exception('Error creating entry import marker',EXN_STUDYCAL_LIB_MARKIMPORTED);
    }   
}

/**
 * Sets the user ticked flag.
 * @param int $courseid Moodle course ID
 * @param int $userid Moodle user ID
 * @param int $coursemoduleid Course-module ID, or 0 if eventid is set
 * @param int $eventid Event ID, or 0 if coursemodule ID is set
 * @param int $ticked True if box should be ticked, false otherwise
 */
function studycal_set_ticked($courseid,$userid,$coursemoduleid,$eventid,$ticked) {
    global $CFG;
    if($coursemoduleid) {
        $instancename='coursemoduleid';
        $instanceid=$coursemoduleid;
    } else if($eventid) {
        $instancename='eventid';
        $instanceid=$eventid;
    } else {
        throw new Exception('Must specify either coursemodule or event',EXN_STUDYCAL_LIB_TICKINVAL);
    }
    
    if($ticked) {
        // Check if it's already ticked
        $rs=get_recordset_sql("
SELECT 
  COUNT(*) AS cnt
FROM 
  {$CFG->prefix}studycal_ticks
WHERE
  courseid=$courseid 
  AND userid=$userid 
  AND $instancename=$instanceid");
        if(!$rs) {
            throw new Exception('Failed to check for existing tick',EXN_STUDYCAL_LIB_TICKDB);
        }
        if($rs->fields['cnt']==0) {
            // Only do anything if it's not already there.   
            if(!execute_sql("
INSERT INTO
  {$CFG->prefix}studycal_ticks (courseid,userid,$instancename)
VALUES
  ($courseid,$userid,$instanceid)",false)) {
                throw new Exception('Failed to add tick to database',EXN_STUDYCAL_LIB_TICKDB);
            }
        }   
    } else {
        if(!execute_sql("
DELETE FROM 
  {$CFG->prefix}studycal_ticks 
WHERE 
  courseid=$courseid 
  AND userid=$userid 
  AND $instancename=$instanceid",false)) {
            throw new Exception('Failed to delete tick from database',EXN_STUDYCAL_LIB_TICKDB);
        }
    }
}

/**
 * Get Moodle calendar entries
 * @param int $courseid Moodle course ID
 * @return array Array of moodle calendar entries
 */
function get_moodle_calendar_entries($courseid) {
    global $CFG;
    $usergrouparray=mygroupid($courseid);
    if($usergrouparray) {
        $usergroups=','.implode(',',$usergrouparray);
    } else {
        $usergroups='';
    }
    $moodleentries=get_records_sql("
SELECT 
  id,name,format,timestart,timeduration 
FROM 
  {$CFG->prefix}event 
WHERE 
  courseid={$courseid} 
  AND visible=1 
  AND groupid IN (0$usergroups) 
ORDER BY timestart");
    return $moodleentries ? array_values($moodleentries) : array();
}

// Here's the function that does the horrible replace.
function studycal_insert_checkbox($matches) {
    global $ticked,$editing,$CFG,$COURSE,$hideboxescm,$section,$doinglabels;

    if(empty($matches[3])) {
        return '[[Error finding activity!]]';
    }

    $cm = $matches[3];
    $sesskey=sesskey();
    $hidethisbox=!empty($hideboxescm[$cm]);
    if($editing) {
        $hide=$hidethisbox ? 0 : 1;
        $hideshow=$hide ? 'hide' : 'show';
        return ($doinglabels?'':$matches[1])."
<div class='studycalcheckbox'>
<form action='format/studycal/hidebox.php' method='post'>
<input type='hidden' name='sesskey' value='$sesskey' />
<input type='hidden' name='section' value='$section' />
<input type='hidden' name='course' value='{$COURSE->id}' />
<input type='hidden' name='coursemodule' value='$cm' />
<input type='hidden' name='hide' value='$hide' />
<input type='image' src='{$CFG->pixpath}/t/$hideshow.gif' />
</form>".
($hidethisbox ? '' : "<input type='checkbox' disabled='disabled'/>").
"</div>
".($doinglabels?$matches[1]:$matches[2]);
    } else if(!$hidethisbox) {
        $checked=isset($ticked['cm'.$cm]) ? 'checked="checked" ' : '';
        return ($doinglabels?'':$matches[1])."
<div class='studycalcheckbox'>
<input type='checkbox' disabled='disabled' id='studycal_{$COURSE->id}_cm$cm' class='studycalcheckbox' $checked/>
</div>
".($doinglabels?$matches[1]:$matches[2]);
    } else {
        return $doinglabels ? '' : $matches[1].$matches[2];
    }
}

function print_studycal_checkbox_js() {
		global $CFG;
    require_js(array('yui_yahoo','yui_event','yui_connection'));
    print "
<script type='text/javascript'>
function handleResponse(o) {
    if(o.responseText!='OK') {
        alert('An error occurred when attempting to save your tick mark.\\n\\n('+o.responseText+'.)');
    }
}
function handleFailure(o) {
    alert('An error occurred when attempting to connect to our server. The tick mark will not be saved.\\n\\n('+
        o.status+','+o.statusText+')');
}

function checkboxfunction(checkbox) {
    return function() {
        YAHOO.util.Connect.asyncRequest('POST','{$CFG->wwwroot}/course/format/studycal/tick.php',
            {success:handleResponse,failure:handleFailure},
            'request='+checkbox.id.replace('studycal_','')+'_'+(checkbox.checked ? '1' : '0'));    
    };
}

function studycal_init() {
    if(!document.getElementsByTagName) { // Old browser
        return;
    }
    var inputs=document.getElementsByTagName('input');
    for(var i=0;!(i>=inputs.length);i++) {
        if(!(/^studycal_[0-9]+_(e|cm)[0-9]+$/.test(inputs[i].id))) {
            continue;
        }
        inputs[i].disabled=false;
        inputs[i].onclick=checkboxfunction(inputs[i]);
    }
}

studycal_init();
</script>
    ";
}

?>