<?php // $Id: lib.php,v 1 2007/03/03 18:40:55 dfountain

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/lib/datalib.php');

//******************************************************************
// SQL FUNCTIONS 
//******************************************************************

function dfget_users_grades($id, $course){
	global $CFG;
	$sql = "SELECT gi.itemname, gg.finalgrade, gi.gradepass, gi.id, gi.itemtype, gi.itemmodule, gi.iteminstance
			FROM {$CFG->prefix}grade_items gi, {$CFG->prefix}grade_grades gg
			WHERE gg.itemid = gi.id
			AND gg.userid = $id
			AND gi.courseid = $course";
	return get_records_sql($sql);
}
function dfgrade_get_users_grades($id, $course, $prefixletter) {
    global $CFG;
	$sql = "SELECT ge.name, geg.grade, ge.id, 'gradebook' as source
	    FROM    {$CFG->prefix}grade_events ge,
		        {$CFG->prefix}grade_events_grades geg
		WHERE geg.event=ge.id
		        AND ge.course=$course
				AND geg.userid=$id";

	//get gradebook grades
	$tempgb = get_records_sql($sql);

	//get assignment grades
	$tempas = dfget_assignment_grades($course, $id, $prefixletter);

	//return all grades - check for NULL to avoid losing records in the array merge
	if ($tempgb == NULL){
		$temp = $tempas;
	}
	elseif ($tempas == NULL){
		$temp = $tempgb;
	}
	else{
		$temp = array_merge($tempgb, $tempas);
	}
	return $temp;
}

function dfget_grade_event_ids($prefixletter,$course) {
	global $CFG;
	//$sql = "SELECT name,id"
    //    . " FROM `";
	//$sql .="{$CFG->prefix}grade_events`"
    //    . " WHERE `course` =$course";
	//$sql .=' AND `name` LIKE CONVERT( _utf8 \'';
	//$sql .= "$prefixletter";
	//$sql .='\''
    //    . ' USING latin1 )'
    //    . ' COLLATE latin1_swedish_ci';
	
	$sql = "SELECT name, id, 'gradebook' as source
	    FROM    {$CFG->prefix}grade_events
		WHERE name LIKE '$prefixletter'
		AND	course=$course";

		$temp = get_records_sql($sql);
		return $temp;
}

function dfget_assignment_grades($courseid, $userid, $prefixletter){
	global $CFG;

	$sql= "SELECT a.name AS name, c.id AS id, s.grade AS grade, 'assignment' AS source
FROM {$CFG->prefix}assignment a, {$CFG->prefix}course_modules c, {$CFG->prefix}assignment_submissions s
WHERE a.course = $courseid
AND a.name LIKE '".$prefixletter."'
AND c.course =$courseid
AND c.instance = a.id
AND s.userid = $userid
AND s.assignment = a.id
AND c.module = '1'";
$temp=get_records_sql($sql);
	return $temp;
}


//******************************************************************
// END SQL FUNCTIONS 
//******************************************************************

function dfcoloured_table($df_grade_letter, $df_no_of_grades, $df_grades, $df_criteria_title, $dfpasscolor, $dffailcolor, $gradenameandids,$courseid,$suffix) {
    global $CFG, $USER;
	$dftemp=NULL;
	$px=NULL;
	if ($suffix = 0){$suffix=2;} //makes sure legacy blocks are configured 1,2,3...
	require_once("../config.php");
    require_once($CFG->dirroot.'/blocks/dfocrnational/lib.php');
	if ($suffix==2){
		$suffixarray = array("empty","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		}
	else{
		for($i=0;$i<21;$i++){
			$suffixarray[$i] = $i;
		}
		}
    $cid       = required_param('id');              // course id
	//get grade_event id's for this course with names with starting letter
	
	if ($df_no_of_grades > 10){
	    $colspan = round($df_no_of_grades/2);
	}
	else {
		$colspan = $df_no_of_grades;
	}
	$dftemp ="<table width=100% border=1 class=\"dftable\"><tr><td align=center colspan=".$colspan.">".$df_criteria_title."</td></tr>";
	for ($i=1; $i<=$df_no_of_grades;$i++){
	    //set background-color for pass/incomplete criteria
		$px = $df_grades[$df_grade_letter.$suffixarray[$i]];
		$source = NULL;
		$source = $gradenameandids[$df_grade_letter.$suffixarray[$i]]->source;
		if ($source == 'gradebook'){
			$gradeid = $gradenameandids[$df_grade_letter.$suffixarray[$i]]->id;
			$linktodescription = $CFG->wwwroot."/grade/index.php?id=".$cid."&action=add&geventid=".$gradeid;
		}
		else{
			//cmid from mdl_course_modules where name is prefix + suffix & course & module=1
			//this is needed to hyperlink to the appropriate assignment
			$sql="
				SELECT cm.id from {$CFG->prefix}course_modules cm, {$CFG->prefix}assignment a
				WHERE a.name = '".$df_grade_letter.$suffixarray[$i]."'
					AND a.course = '".$courseid."'
					AND cm.module = '1'
					AND cm.instance = a.id
				";
				$linkid = get_records_sql($sql);
				foreach($linkid as $value){ //poor programming skills - need to learn more about arrays (only one array with one entry
					$linkassignment = $value->id;
				};
				if($linkassignment >0){
					$linktodescription = $CFG->wwwroot."/mod/assignment/view.php?id=".$linkassignment;
				} 
				else{
					$linktodescription = $CFG->wwwroot."/blocks/dfocrnational/error.htm";
				}
		}
		
		//wrap over two lines if greater than ten items
		if ($i==10 || $i==18){
		    $dftemp .= "</tr><tr>";
		}
		//$templink = $dfgrade_event_ids[$df_grade_letter.$i];
		if ($px->grade == 1){
			//criteria passed
			    $dftemp .="<td align=center style=\"background-color:".$dfpasscolor."\"><a href=\"".$linktodescription."\" target=\"_blank\">".$suffixarray[$i]."</a></td>";
			}
			else {
			    $dftemp .="<td align=center style=\"background-color:".$dffailcolor."\"><a href=\"".$linktodescription."\" target=\"_blank\">".$suffixarray[$i]."</a></td>";
			}
	}
	$dftemp .="</tr></table>";
	return $dftemp;
}
?>
