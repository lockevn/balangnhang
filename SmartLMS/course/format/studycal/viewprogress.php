<?php
/**
 * For tutors and course staff. Displays progress of students through course.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

require_once('../../../config.php');
require_once('lib.php');
require_once('../../lib.php');

$courseid=required_param('course',PARAM_INT);
$groupid=optional_param('group',0,PARAM_INT);
$startfrom=optional_param('startfrom',0,PARAM_INT);
if($startfrom<0) {
    $startfrom=0;
}

// Maximum number of users to show per page
$maxusers=50;

$course=get_record('course','id',$courseid);
if(!$course) {
    error('Course does not exist');
}

// Check login to course
require_login($courseid);
$context = get_context_instance(CONTEXT_COURSE, $courseid);

// Check permission to view
if($groupid) {
    $group=get_record('groups','id',$groupid);
    if(!$group) {
        error('Group does not exist');
    }
    $strprogressfor=get_string('forgroup','format_studycal',$group->name);
    require_capability('format/studycal:viewgroupprogress',$context);
    $mygroups=mygroupid($courseid);
    if(!$mygroups || !is_array($mygroups) || !in_array($groupid,$mygroups)) {
        error('You do not belong to the requested group');
    }    
} else {
    require_capability('format/studycal:viewallprogress',$context);
    $strprogressfor=get_string('forcourse','format_studycal');
}


$strviewprogress=get_string('viewprogressfor','format_studycal',$strprogressfor);
$navigation=array();
$navigation[]=array('name' => $strviewprogress, 'link' => '', 'type' => 'studycal');
print_header($strviewprogress, "$course->fullname",
    build_navigation($navigation));
        
// Get list of users on course or in group who could enter progress
$users=get_users_by_capability($context,
    'format/studycal:trackprogress','','lastname,firstname',
    $startfrom,$maxusers+1,$groupid==0?'':$groupid,'',false);
if(!$users) {
    error('No users on this course have the capability to track their progress.');
}
$moreusers=false;
if(count($users) > $maxusers) {
    $moreusers=true;
    foreach($users as $lastkey=>$lastuser) {
    }
    unset($users[$lastkey]);
}

// Get list of users with some ticks
global $userswithticks;
$userswithticks=get_records_sql("
SELECT 
  DISTINCT userid,1
FROM
  {$CFG->prefix}studycal_ticks
WHERE
  courseid=$courseid");
  
function tickclass($user) {
    global $userswithticks;
    return (!empty($userswithticks[$user->id])) ? 'hasticks' : 'noticks';
}

// Get the list of tickboxes to hide
$rs=get_recordset_sql("
SELECT
  coursemoduleid,eventid
FROM
  {$CFG->prefix}studycal_hideboxes
WHERE
  courseid={$courseid}");
if(!$rs) {
    error('Failed to obtain list of hidden boxes');
}
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

print "
<table>
<tr>
<td colspan='2' rowspan='2'>";

if($groupid && count($mygroups)>1) {
    print "
<form action='viewprogress.php' method='get'>
  <input type='hidden' name='course' value='$courseid' />";
    print get_string('group').':';
    print "
  <select name='group'>";
  
    foreach($mygroups as $thisgroupid) {
        $thisgroupname=htmlspecialchars(get_field('groups','name','id',$thisgroupid));
        $selected=$thisgroupid==$groupid ? " selected='selected'" : "";
        print "<option value='$thisgroupid'$selected>$thisgroupname</option>";
    }
  
    print "
  </select>
  <input type='submit' value='Change' />
</form>";
}
if($startfrom > 0 || $moreusers) {
    print "Showing ".($startfrom+1)." to ".($startfrom+1+$maxusers).".<br />"; 
}
if($startfrom > 0) {
    $newstartfrom=($startfrom-$maxusers==0) ? '' : '&amp;startfrom='.($startfrom-$maxusers);    
    $group=$groupid ? "&amp;group=$groupid" : "";
    print "<a href='viewprogress.php?course=$courseid$newstartfrom$group'>&larr; Previous $maxusers</a> ";        
} 
if($moreusers) {
    $newstartfrom=$startfrom+$maxusers;
    $group=$groupid ? "&amp;group=$groupid" : "";
    print "<a href='viewprogress.php?course=$courseid&amp;startfrom=$newstartfrom$group'>Next $maxusers &rarr;</a> ";        
}
print '&nbsp;';
print "
</td>
<th scope='row' style='text-align:right'>First name</th>";
foreach($users as $user) {
    print '<td class="'.tickclass($user).'">'.htmlspecialchars($user->firstname).'</td>';
}
print "
</tr>
<tr>
<th scope='row' style='text-align:right'>Last name</th>";
foreach($users as $user) {
    print '<td class="'.tickclass($user).'">'.htmlspecialchars($user->lastname).'</td>';
}
print "</tr>
<tr>
<th scope='col'>Week</th><th scope='col'>Date</th><th scope='col'>Item</th>";
foreach($users as $user) {
    print '<td class="'.tickclass($user).'"><small>'.htmlspecialchars($user->username).'</small></td>';    
}
print "</tr>";

// Get list of modules on course (in calendar sections)
$coursemodules=get_records('course_modules','course',$courseid);
$modules=get_records('modules');
//$sections=get_records('course_sections','course',$courseid,'section');

// This horrible mess is a butchered version of the code from format.php.
// It has been changed to print a humungous table.
if (! $sections = get_all_sections($course->id)) {   
    error('No sections found');
}
// Get calendar setttings
$studycal=studycal_get_settings($course->id);

// Get Moodle calendar entries
$usergroups=($groupid ? ",$groupid" : '');
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
$moodleentries=$moodleentries ? array_values($moodleentries) : array();
$moodleindex=0;

// Build list of section IDs to query for week-specific settings 
// (Array of sectionid => settings object)
$sectionids='';
foreach($sections as $section) {
    if($sectionids!=='') {
        $sectionids.=',';
    } 
    $sectionids.=$section->id;    
}
$weeksettings=get_records_select('studycal_weeks',"sectionid IN ($sectionids)",'',
    'sectionid,groupwithsectionid,hidenumber,resetnumber,hidedate,title');

$weekdate = $studycal->startdateoffset+$course->startdate;  // this should be 0:00 Monday of that week
$weekdate += 7200;                 // Add two hours to avoid possible DST problems
$strftimedateshort = get_string('strftimedateshort');
$strftimetime=get_string('strftimetime');
$weeknumber=1;
$now=time();

$oddrow='odd';
for($section=1;$section<=$course->numsections;$section++) {
    
    if(!isset($sections[$section])) {
        error('Missing section');
    }    
    $thissection = $sections[$section];
    
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

        
        // See if we're in that week
        if($now >= $weekdate && $now < $enddate) {
            $current=' current';
        } else {
            $current='';
        }
        
        // Check odd or even row
        $oddrow=($oddrow=='odd' ? 'even' : 'odd');
        print "<tr class='$oddrow$current'>";
        
        // Get list of items in row
        if($thissection->sequence=='') {
            $items=array();
        } else {
            $items=explode(',',$thissection->sequence);
        }
        
        // Get list of Moodle calendar entries in row
        $entries=array();
        while($moodleindex < count($moodleentries) && $moodleentries[$moodleindex]->timestart < $enddate) {
            $thisentry=$moodleentries[$moodleindex];
            $entries[]=$thisentry;                                  
            $moodleindex++;            
        }
        
        $rowspan=count($items)+count($entries);
        if($rowspan==0) {
            $rowspan=1;
        }

        // Week number cell
        print "<td rowspan='$rowspan'>";        
            
        // Reset week number if needed
        if(isset($thisweek->resetnumber) && !is_null($thisweek->resetnumber)) {
            $weeknumber=$thisweek->resetnumber;
        }

        $doneweeknumber=false;   
        if(empty($thisweek->hidenumber) && empty($studycal->hidenumbers)) {
            print $weeknumber;
            $doneweeknumber=true;
        }
        if(isset($thisweek->title) && !is_null($thisweek->title)) {
            if($doneweeknumber) {
                print ' &#x2022; ';
            }
            print htmlspecialchars($thisweek->title);
            $doneweeknumber=true;
        }
        if(!$doneweeknumber) {
            print "&nbsp;";
        }
        
        // Date cell
        print "</td><td rowspan='$rowspan'>";        
        if(empty($thisweek->hidedate)) {
            print userdate($weekdate, $strftimedateshort);
        } else {
            print "&nbsp;";
        }
        print "</td>";
        
        if(count($items)+count($entries)==0) {
            print "<td><td colspan='".(count($users)+1)."'>&nbsp;</td>";
        } else {
            // Item cell
            for($i=0;$i<$rowspan;$i++) {
                print "<td>";            
                if($i<count($items)) {
                    $item=$items[$i];
                    
                    // Get item name (ugh)
                    $moduleid=$coursemodules[$item]->module;
                    if(!$moduleid) {
                        error('Missing module ID');
                    }
                    $name=get_field($modules[$moduleid]->name,'name','id',$coursemodules[$item]->instance);
                    print $name;
                    $hidethisbox=!empty($hideboxescm[$item]);
                    
                    // Get ticks
                    $ticks=get_records_sql("
SELECT 
    userid,1 
FROM 
    {$CFG->prefix}studycal_ticks 
WHERE  
    courseid=$courseid AND coursemoduleid=$item");                    
                } else {
                    $entry=$entries[$i-count($items)];
                    print htmlspecialchars($entry->name);
                    $hidethisbox=!empty($hideboxesevent[$entry->id]);
                    
                    // Get ticks
                    $ticks=get_records_sql("
SELECT 
    userid,1 
FROM 
    {$CFG->prefix}studycal_ticks 
WHERE  
    courseid=$courseid AND eventid={$entry->id}");                    
                }
                print "</td>";
                if(!is_array($ticks)) {
                    $ticks=array();
                }
                
                // Now loop through all users seeing if they have it ticked
                foreach($users as $user) {
                    if($hidethisbox) {
                        print "<td>&nbsp;</td>";
                    } else if(!empty($ticks[$user->id])) {
                        print "<td class='yes ".tickclass($user)."'>&#x2611;</td>";
                    } else {
                        print "<td class='no ".tickclass($user)."'>&#x2610;</td>";
                    }
                }
                
                if($i<$rowspan-1) {
                    $oddrow=($oddrow=='odd' ? 'even' : 'odd');
                    print "</tr><tr class='$oddrow$current'>";
                }
            }
        }
        
        print "</tr>";
    }        
    $weekdate+=604800;
    $weeknumber++;
}

print '</table>';
 
print_footer();
?>