<?php
/**
 * Restore for studycal course format
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

global $CFG;
require_once(dirname(__FILE__).'/lib.php');

function studycal_restore_format_data($restore,$data) {
    
    $courseid=$restore->course_id;
    
    try {
        // This is the studycal backup format version, included
        // for future convenience. (Yes there is an overall
        // backup version anyway but studycal might change
        // independently.) At present it is not used.
        $toplevel=$data['FORMATDATA']['#'];
        $version=$toplevel['BACKUP_VERSION']['0']['#'];
        if($version!=1) {
            throw new Exception('Studycal: unsupported version');
        }
        
        // Wipe any existing study calendar information
        // Multiple restores to existing course, deleting it first
        if ($restore->deleting) {
            $course = new stdClass;
            $course->id = $courseid;
            studycal_wipe($course);
        }

        // Set up calendar basic data
        studycal_init_settings_relative($courseid,
            $toplevel['STARTDATEOFFSET']['0']['#'],
            $toplevel['HIDENUMBERS']['0']['#'],
            $toplevel['WEEKSTOVIEW']['0']['#'],
            $toplevel['LOCK']['0']['#']);
            
        // Add per-week data
        $weeks = !empty($toplevel['WEEKS']['0']['#']['WEEK']) ? $toplevel['WEEKS']['0']['#']['WEEK'] : array();
        foreach($weeks as $week) {
            $section=$week['#']['SECTION']['0']['#'];
            $hidenumber=$week['#']['HIDENUMBER']['0']['#'];
            $hidedate=$week['#']['HIDEDATE']['0']['#'];
            if(isset($week['#']['GROUPWITHSECTION']['0']['#'])) {
                $groupwithsection=$week['#']['GROUPWITHSECTION']['0']['#'];
            } else {
                $groupwithsection=null;
            }
            if(isset($week['#']['RESETNUMBER']['0']['#'])) {
                $resetnumber=$week['#']['RESETNUMBER']['0']['#'];
            } else {
                $resetnumber=null;
            }
            if(isset($week['#']['TITLE']['0']['#'])) {
                $title=$week['#']['TITLE']['0']['#'];
            } else {
                $title=null;
            }
            
            studycal_init_week_settings_without_objects(
                $courseid,$section,$groupwithsection,
                $hidenumber,$hidedate,$resetnumber,$title);
        }
        
        // Add hidebox data
        $hidecmids = !empty($toplevel['HIDEBOXES']['0']['#']['COURSEMODULEID']) ? $toplevel['HIDEBOXES']['0']['#']['COURSEMODULEID'] : array();
        foreach($hidecmids as $hidecm) {
            $backupdata=backup_getid(
                $restore->backup_unique_code,"course_modules",$hidecm['#']);
            if($backupdata) { // Maybe the module wasn't included in backup                
                $coursemoduleid=$backupdata->new_id;
                studycal_set_hide_box($courseid,$coursemoduleid,0,true);
            }
        }
        $hideeventids = !empty($toplevel['HIDEBOXES']['0']['#']['EVENTID']) ? $toplevel['HIDEBOXES']['0']['#']['EVENTID'] : array();
        foreach($hideeventids as $hideev) {
            $backupdata=backup_getid(
                $restore->backup_unique_code,"event",$hideev['#']);
            if($backupdata) {
                $eventid=$backupdata->new_id;
                studycal_set_hide_box($courseid,0,$eventid,true);
            }
        }
        
        // Record imported labels
        $importeds = !empty($toplevel['IMPORTED']['0']['#']['ACTIVITY']) ? $toplevel['IMPORTED']['0']['#']['ACTIVITY'] : array();
        foreach($importeds as $imported) {
            $backupdata=backup_getid(
                $restore->backup_unique_code,"course_modules",
                $imported['#']['COURSEMODULEID']['0']['#']);
            if($backupdata) { // Maybe the module wasn't included in backup                
                $coursemoduleid=$backupdata->new_id;
                $col=$imported['#']['COL']['0']['#'];
                studycal_mark_imported($courseid,$coursemoduleid,$col);
            }
        }
        
        // Record ticks
        $users = !empty($toplevel['TICKS']['0']['#']['USER']) ? $toplevel['TICKS']['0']['#']['USER'] : array();
        foreach($users as $user) {
            $backupid=backup_getid(
                $restore->backup_unique_code,"user",
                $user['#']['ID']['0']['#']);
            if(!$backupid) {
                // Can probably happen if we stored ticks from users who weren't in course
                continue;
            }
            $userid=$backupid->new_id;
            $coursemodules=explode(',',trim($user['#']['COURSEMODULES']['0']['#']));
            if($coursemodules===false || (count($coursemodules)==1 && $coursemodules[0]==='')) {
                $coursemodules=array();
            }
            $events=explode(',',trim($user['#']['EVENTS']['0']['#']));
            if($events===false || (count($events)==1 && $events[0]==='') ) {
                $events=array();
            }
            foreach($coursemodules as $cm) {
                $backupdata=backup_getid(
                    $restore->backup_unique_code,"course_modules",$cm);
                if($backupdata) { // Maybe the module wasn't included in backup                
                    $coursemoduleid=$backupdata->new_id;
                    studycal_set_ticked($courseid,$userid,$coursemoduleid,0,true);
                }
            }
            foreach($events as $ev) {
                $eventid=backup_getid(
                    $restore->backup_unique_code,"event",$ev)->new_id;
                studycal_set_ticked($courseid,$userid,0,$eventid,true);
            }
        }
        
        return true;
    } catch(Exception $e) {
        if(!defined('RESTORE_SILENTLY')) {
            notify($e->getMessage().' ('.$e->getCode().')');
        }
        return false;
    }
    
    
}

?>