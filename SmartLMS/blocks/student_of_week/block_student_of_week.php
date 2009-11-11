<?php //$Id: block_course_summary.php,v 1.26.2.2 2008/03/03 11:41:02 moodler Exp $

require_once $CFG->libdir . '/memcached.class.php';
require_once $CFG->dirroot .'/smartcom/util/memcachedutil.php';


class block_student_of_week extends block_base {
    function init() {
        $this->title = get_string('pagedescription', 'block_student_of_week');
        $this->version = 2009101509;
    }

    function specialization() {
        global $COURSE;
        if($this->instance->pagetype == PAGE_COURSE_VIEW && $COURSE->id != SITEID) {
            $this->title = get_string('pagedescription', 'block_student_of_week');
        }
    }

    function get_content() {
        global $CFG, $COURSE;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return '';
        }

        $this->content = new stdClass();
        $options = new object();
        $options->noclean = true;    // Don't clean Javascripts etc
        $studentList = $this->retrieveTopStudentList();
        $str = "";
        $str .= '<div style="overflow: auto; display: block; overflow-x: no; overflow-y: scroll; height: 150px; width:218px; border: 1px solid #ccc; padding-left: 10px;"><marquee scrolldelay="120" onmouseover="this.stop();" onmouseout="this.start();" direction="up">';
        foreach($studentList as $student) {    
                	
        	$str .= "<div style=\"padding: 5px 0;\"><img align=\"absmiddle\" src=\"".$CFG->wwwroot."/user/pix.php?file=/$student->id/f1.jpg\" width=\"100\" height=\"100\" />&nbsp;&nbsp;<a class='courseRB' href='$CFG->wwwroot/user/view.php?id=$student->id&course=1'>$student->username</a></div>";
        }
        $str .= '</marquee></div>';
        $this->content->text = $str;
        
        $this->content->footer = '';               

        return $this->content;
    }

//    function hide_header() {
//        return true;
//    }

    function preferred_width() {
        return 210;
    }
    

    
    function retrieveTopStudentList() {
    	global $CFG;
    	if($CFG->cachetype === 'memcached') {
    		$memcached = new memcached();
    		if($memcached === false) {
    			return false;
    		}
    		$studentList = $memcached->get(MemcachedUtil::$STUDENT_OF_WEEK_KEY);
    		if(!empty($studentList)) {
    			//cache hits
    			return $studentList;
    		}
    	}
    	$week = $this->get_start_and_end_date_from_week(date('W'));    	
    	$sql = "SELECT qg.userid id, avg(qg.grade/q.sumgrades) avg_, u.username username
				FROM " . $CFG->prefix ."quiz_grades qg, mdl_quiz q,  mdl_user u 
				WHERE qg.quiz=q.id AND u.id=qg.userid 
				AND qg.timemodified > " .$week['start_timestamp'] . " AND qg.timemodified < " .$week['end_timestamp'] .
				" GROUP BY qg.userid
				ORDER BY avg_ DESC
				LIMIT 10";
    	
    	$results = get_records_sql($sql);
    	$studentList = array();
    	if(!empty($results)) {
    		foreach($results as $result) {
    			$studentList[] = $result;
    		}
    	}
    	if(!empty($memcached)) {
    		//$memcached->set(MemcachedUtil::$STUDENT_OF_WEEK_KEY, $studentList, 24*3600);
    		$memcached->set(MemcachedUtil::$STUDENT_OF_WEEK_KEY, $studentList, 10);
    	}
    	return $studentList;    	    	   
    }
    
    function get_start_and_end_date_from_week ($w)
    {
    	$y = date("Y", time());
    	$o = 6; // week starts from sunday by default

    	$days = ($w - 1) * 7 + $o;

    	$firstdayofyear = getdate(mktime(0,0,0,1,1,$y));
    	if ($firstdayofyear["wday"] == 0) $firstdayofyear["wday"] += 7;
    	# in getdate, Sunday is 0 instead of 7
    	$firstmonday = getdate(mktime(0,0,0,1,1-$firstdayofyear["wday"]+1,$y));
    	$calcdate = getdate(mktime(0,0,0,$firstmonday["mon"], $firstmonday["mday"]+$days,$firstmonday["year"]));

    	$sday = $calcdate["mday"];
    	$smonth = $calcdate["mon"];
    	$syear = $calcdate["year"];
    	 
    	/*0 hiểu sao bị tính quá mất 7 ngày, nên tạm đảo ngược lại*/ 
//    	$timestamp['start_timestamp'] =  mktime(0, 0, 0, $smonth, $sday, $syear);
//    	$timestamp['end_timestamp'] =  $timestamp['start_timestamp'] + (60*60*24*7);
		$timestamp['end_timestamp'] =  mktime(0, 0, 0, $smonth, $sday, $syear);
    	$timestamp['start_timestamp'] =  $timestamp['end_timestamp'] - (60*60*24*7);

    	return $timestamp;

    }
    
function applicable_formats() {
        return array('all' => true);
    }
    
function instance_allow_multiple() {
        return true;
    }
     

}

?>
