<?php
/**
 * Script run from course view page to combine or split apart weeks
 * in the study calendar.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

require_once('../../../config.php');
require_once('../../lib.php');

$courseid=required_param('course',PARAM_INT);
$sectionindex=required_param('section',PARAM_INT);
$combine=required_param('combine',PARAM_INT);

if(!confirm_sesskey()) {
    error('Session key not valid');
}
if($_SERVER['REQUEST_METHOD']!=='POST') {
    error('This script requires POST requests');
}

// Require login and editing mode (also checks manageactivities)
require_login($courseid);
if(!isediting($courseid)) {
    error('You are not currently editing this course');
}

// Check calendar isn't locked
$lock=get_field('studycal','lock','courseid',$courseid);
if($lock) {
    error('The calendar structure is locked and cannot be edited');
} 

// Get all sections and check section index is valid
$sections=get_all_sections($courseid);
if($sectionindex > count($sections) || $sectionindex < 1) {
    error('Invalid section index'); 
}
$thissection=$sections[$sectionindex];

// Get week settings for this section
$thisweek=get_record('studycal_weeks','sectionid',$thissection->id);
$iscombinedchild=$thisweek && $thisweek->groupwithsectionid;

if($combine && !$iscombinedchild) {
    if($sectionindex==1) {
        error('Cannot combine first week');
    }
    
    $previousweek=get_record('studycal_weeks','sectionid',$sections[$sectionindex-1]->id);
    if($previousweek && $previousweek->groupwithsectionid) {
        // If previous week is also grouped, group this with the same thing
        $newgroupwithsectionid=$previousweek->groupwithsectionid;        
    } else {
        // Otherwise group with previous week
        $newgroupwithsectionid=$sections[$sectionindex-1]->id;               
    }
    
    // Create record if needed...
    if(!$thisweek) {
        // This doesn't use insert_record because the table has no id field.
        if(!execute_sql(
            "INSERT INTO {$CFG->prefix}studycal_weeks(sectionid,groupwithsectionid) 
            VALUES({$thissection->id},$newgroupwithsectionid)")) {
            error('Failed to create settings for week in database');
        }
    } else {
        if(!set_field('studycal_weeks','groupwithsectionid',$newgroupwithsectionid,'sectionid',$thissection->id)) {
            error('Failed to update settings for week in database');
        }
    }
    
    // Update any records that were combined with this one
    for($later=$sectionindex+1;$later<count($sections);$later++) {
        $nextweek=get_record('studycal_weeks','sectionid',$sections[$later]->id);
        if(!$nextweek || ($nextweek->groupwithsectionid != $sections[$sectionindex]->id) ) {
            break;
        }
        if(!set_field('studycal_weeks','groupwithsectionid',$newgroupwithsectionid,'sectionid',$sections[$later]->id)) {
            error('Failed to update settings for week in database');
        }
    }
    
    // Find the new sectionindex
    for($sectionindex=1;$sectionindex<count($sections);$sectionindex++) {
        if($sections[$sectionindex]->id==$newgroupwithsectionid) {
            break;
        }
    }
} else if(!$combine) {
    if($iscombinedchild) {
        error('When splitting weeks, the section= parameter must specify the first week in combined group');
    }
    $done=0;
    for($index=$sectionindex+1;$index<count($sections);$index++) {
        $thatweek=get_record('studycal_weeks','sectionid',$sections[$index]->id);
        if(!$thatweek || ($thatweek->groupwithsectionid!=$thissection->id)) {
            break;
        }
        if(!$thatweek->hidenumber && is_null($thatweek->resetnumber)) {
            // If record is otherwise default, delete it
            if(!delete_records('studycal_weeks','sectionid',$thatweek->sectionid)) {
                error('Failed to delete record for combined week');
            }
        } else {
            // Otherwise keep the record but null the group with bit
            if(!execute_sql(
                "UPDATE {$CFG->prefix}studycal_weeks SET groupwithsectionid=NULL WHERE sectionid={$thatweek->sectionid}")) {
                error('Failed to update record for combined week'); 
            } 
        }
        $done++;
    }    
    if(!$done) {
        error('The selected week was not combined');
    }    
} else {
    error('Week is already in requested state');
}

redirect("../../view.php?id=$courseid#section-$sectionindex");
?>