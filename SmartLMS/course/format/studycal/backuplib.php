<?php
/**
 * Backup function for study calendar data.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

require_once(dirname(__FILE__).'/lib.php');

function studycal_backup_format_data($bf,$preferences) {
	global $CFG;
    @include_once(dirname(__FILE__).'/local/xml_backup.php');
    if (!class_exists('xml_backup')) {
        require_once($CFG->dirroot.'/local/xml_backup.php');
    }
	$xb=new xml_backup($bf,$preferences,4);
	
	try {
		// Output a version code just in case
		$xb->tag_full('BACKUP_VERSION',1);
		
		// Output overall calendar settings
		$studycal=studycal_get_settings($xb->get_courseid());
		$xb->tag_full('STARTDATEOFFSET',$studycal->startdateoffset);
		$xb->tag_full('HIDENUMBERS',$studycal->hidenumbers);
		$xb->tag_full('WEEKSTOVIEW',$studycal->weekstoview);
        $xb->tag_full('LOCK',$studycal->lock);
		
		// Get list of weeks w/ data to turn section IDs into numbers 
		$rs=get_recordset_sql("
SELECT
    cs.id, cs.section, sw.* 
FROM
    {$CFG->prefix}course_sections cs 
    LEFT JOIN {$CFG->prefix}studycal_weeks sw ON sw.sectionid=cs.id 
WHERE
    cs.course={$xb->get_courseid()}
ORDER BY
    cs.section");		
		if(!$rs) {
			debugging('Failed to query for sections');
			return false;
		}
		$weeks=recordset_to_array($rs);
		if(!$weeks) {
			$weeks=array(); 
		}
		
		// Output per-week settings
		$xb->tag_start('WEEKS');		
		foreach($weeks as $week) {
			// Only store the weeks we have data for!
			if($week->sectionid) {
				$xb->tag_start('WEEK');
				$xb->tag_full('SECTION',$week->section);
				if($week->groupwithsectionid) {
					$xb->tag_full('GROUPWITHSECTION',$weeks[$week->groupwithsectionid]->section);
				}
				$xb->tag_full('HIDENUMBER',$week->hidenumber);
				$xb->tag_full('HIDEDATE',$week->hidedate);
				if(!is_null($week->resetnumber)) {
					$xb->tag_full('RESETNUMBER',$week->resetnumber);
				}
				if(!is_null($week->title)) {
					$xb->tag_full('TITLE',$week->title);
				}				
				$xb->tag_end('WEEK');
			}
		}		
		$xb->tag_end('WEEKS');
        
        // Get list of hidden checkboxes (but only those referring to events
        // or coursemodules that actually exist)
        $rs=get_recordset_sql("
SELECT
    hb.id,cm.id AS coursemoduleid,e.id AS eventid
FROM
    {$CFG->prefix}studycal_hideboxes hb
    LEFT JOIN {$CFG->prefix}event e ON hb.eventid=e.id
    LEFT JOIN {$CFG->prefix}course_modules cm ON hb.coursemoduleid=cm.id 
WHERE
    hb.courseid={$xb->get_courseid()}
ORDER BY
    cm.id,e.id");        
        if(!$rs) {
            debugging('Failed to query for hidden checkboxes');
            return false;
        }
        $hiddenboxes=recordset_to_array($rs);
        if(!$hiddenboxes) {
            $hiddenboxes=array(); 
        }

        // Output list
        $xb->tag_start('HIDEBOXES');        
        foreach($hiddenboxes as $hidebox) {
            if($hidebox->coursemoduleid) {
                $xb->tag_full('COURSEMODULEID',$hidebox->coursemoduleid);
            }
            if($hidebox->eventid) {
                $xb->tag_full('EVENTID',$hidebox->eventid);
            }
        }       
        $xb->tag_end('HIDEBOXES');
		
		// Get imported label list. This query tries to find only
		// the labels which are still relevant i.e. haven't been 
		// deleted or something.
		$rs=get_recordset_sql("
SELECT
    si.coursemoduleid,si.col 
FROM
    {$CFG->prefix}studycal_imported si 
    INNER JOIN {$CFG->prefix}course_modules cm ON si.coursemoduleid=cm.id AND si.courseid=cm.course 
WHERE
    si.courseid={$xb->get_courseid()}
ORDER BY
    cm.id");		
		if(!$rs) {
			debugging('Failed to query for imported labels');
			return false;
		}
		$imported=recordset_to_array($rs);
		if(!$imported) {
			$imported=array(); 
		}
		
		// Output list
		$xb->tag_start('IMPORTED');
		foreach($imported as $import) {
			$xb->tag_start('ACTIVITY');
			$xb->tag_full('COURSEMODULEID',$import->coursemoduleid);
			$xb->tag_full('COL',$import->col);
			$xb->tag_end('ACTIVITY');
		}
		$xb->tag_end('IMPORTED');
		
		// If backup includes user data, also store the ticks table
        $xb->tag_start('TICKS');
		if($preferences->backup_users!=2) {
            // Query for ticks
            $rs=get_recordset_sql("
SELECT
    userid,coursemoduleid,eventid 
FROM
    {$CFG->prefix}studycal_ticks  
WHERE
    courseid={$xb->get_courseid()}
ORDER BY
    userid,coursemoduleid,eventid");        
            if(!$rs) {
                debugging('Failed to query for user ticks');
                return false;
            }
            $currentuser=-1;
            $currentcm=array();
            $currentevent=array();
            while(true) {
                if($rs->EOF || $currentuser!=$rs->fields['userid']) {
                    if($currentuser!=-1) {
                        $xb->tag_start('USER');
                        $xb->tag_full('ID',$currentuser);
                        $xb->tag_full('COURSEMODULES',implode(',',$currentcm));
                        $xb->tag_full('EVENTS',implode(',',$currentevent));
                        $xb->tag_end('USER');
                    }            
                    if($rs->EOF) {
                        break;
                    }
                    $currentuser=$rs->EOF ? -1 : $rs->fields['userid'];
                    $currentcm=array();
                    $currentevent=array();                    
                }
                
                if(!is_null($rs->fields['coursemoduleid'])) {
                    $currentcm[]=$rs->fields['coursemoduleid'];
                }
                if(!is_null($rs->fields['eventid'])) {
                    $currentevent[]=$rs->fields['eventid'];
                }
                
                $rs->MoveNext();
            }
		}
        $xb->tag_end('TICKS');
		
		return true;
	} catch(Exception $e) {
		debugging('Exception '.$e->getCode().': '.$e->getMessage());
		return false;
	}
}
?>
