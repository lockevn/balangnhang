<?php //$Id: block_dfocrnational.php,v 1 2007/03/03 20:09:57 defacer Exp $
//Uses the gradebookplus that allows the addition of non-activity grading to specify criteria grading system.
//3 levels permitted. Grades set in the gradebook where 1 = successful completion and anything else does not.
//Each level requres the criteria group to be given names with the same starting letters and finishing with
//incremental values, eg P1, P2, P3, P4; M1, M2, M3etc.
class block_dfocrnational extends block_base {
    function init() {
        $this->title = get_string('block_title', 'block_dfocrnational');
        $this->version = 2008101001;
  	}
function instance_allow_multiple() {
    return true;
}

function get_content() {
	global $CFG, $USER, $COURSE;
	$courseid = $COURSE->id;
	$userid = $USER->id;
	//context and capabilities
	if ($courseid){
		$context = get_context_instance(CONTEXT_COURSE, $courseid);
	} else {
		$context = get_context_instance(CONTEXT_SYSTEM,SITEID);
	}
	//
	if($this->content !== NULL){
		return $this->content;
	}
    $this->content = new stdClass;
    $this->content->footer = '';
	require_once("../config.php");
	require_once("../lib/datalib.php");
	require_once($CFG->dirroot.'/blocks/dfocrnational/lib.php');

	$this->content->text =$COURSE->id;
	if(has_capability('moodle/course:manageactivities',$context,$userid,false)){
		//a user with editing ability - show the summary of the block set up
		$this->content->text ="A pupil will see a grid set out according to the configuration";
	} else {
		//set up alternative suffix arrays 0:alphabetical, 1:numerical
		$suffixarray[0] = array("empty","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		for($i=1;$i<21;$i++){
			$suffixarray[1][$i]=$i;
		}
		//no editing ability - they have grades so we show them here
		$grades = dfget_users_grades($USER->id, $COURSE->id);
		$text="";
		for ($i=1; $i<=$this->config->numberoflevels;$i++){
			if($this->config->display{$i} == 1){
			//heading of table per level 
			//number of criteria (max 20) spread over two rows if >10
			if($this->config->numberofcriteria{$i}>10){
				$colspan = round($this->config->numberofcriteria{$i}/2);
			}else{
				$colspan = $this->config->numberofcriteria{$i};
			}
			//construct heading row:
			$descrip = $this->config->descrip{$i};
			$text .= "<table width=\"100%\" align=\"center\"><tr><td colspan=\"{$colspan}\" align=\"center\">$descrip</td></tr><tr>";
			//loop through the critria creating a cell for each criteria				
			$colorsuccess = $this->config->colorsuccess{$i};
			$colorincomplete = $this->config->colorincomplete{$i};
			for ($j=1;$j<=$this->config->numberofcriteria{$i};$j++){
				//determine if criteria has been completed or not and set color of cell
				$gradename = $this->config->begin{$i}.$suffixarray[$this->config->suffix{$i}][$j];
				if($grades[$gradename]==NULL){
					$colorresult =$colorincomplete;
				}else{
					$finalgrade = $grades[$gradename]->finalgrade;
					$passgrade = $grades[$gradename]->gradepass;
					if($finalgrade>=$passgrade){
						$colorresult = $colorsuccess;
					}else{
						$colorresult = $colorincomplete;
					}
				}
				//form a link to the assignments URL - manual grade items need a new page developing
				$urlmanual ="";
				$sql = "SELECT id, itemtype, itemmodule, iteminstance, itemname FROM {$CFG->prefix}grade_items WHERE itemname='$gradename' AND courseid='$courseid'";
				$graderecord = get_record_sql($sql);
				//temp
				//print_object($graderecord);
				//
				$gradeid = $graderecord->id;
				if($graderecord == NULL){
					$urlmanual = "{$CFG->wwwroot}/blocks/dfocrnational/nogradeset.htm\" target=\"_blank\"";
				}else{
					if($graderecord->itemtype == 'manual'){
						$urlmanual = "{$CFG->wwwroot}/blocks/dfocrnational/manualgrade.php?giid=$gradeid\" target=\"_blank\"";
					}else{
						$iteminstance = $graderecord->iteminstance;
						$sql = "SELECT cm.id FROM {$CFG->prefix}course_modules cm WHERE cm.instance = $iteminstance AND cm.idnumber != 'NULL'";
						$cmid = get_record_sql($sql);
						$urlmanual="{$CFG->wwwroot}/{$graderecord->itemtype}/{$graderecord->itemmodule}/view.php?id={$cmid->id}\"";
					}
				}
				$k = $this->config->suffix{$i};
				$label=$suffixarray[$k][$j];
				$text .="<td align=\"center\" bgcolor=\"$colorresult\"><a href=\"$urlmanual>$label</a></td>";
			}
			$text .= "</tr>";
		}else{
			$text .= "<table>";
		}
		$text .="</table>";
		$this->content->text = $text;
		}
	}
    return $this->content;
}

function instance_allow_config() {
    return true;
}
function specialization() {
    $this->title = $this->config->title;
}
function has_config() {
    return true;
}
function config_save($data) {
    // Default behavior: save all variables as $CFG properties
    foreach ($data as $name => $value) {
        set_config($name, $value);
    }
    return true;
}
}
?>