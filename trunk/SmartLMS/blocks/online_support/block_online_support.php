<?php

require_once $CFG->libdir . '/memcached.class.php';

class block_online_support extends block_base {
	function init() {
		$this->title = get_string('block_online_support_title', 'block_online_support');
		$this->version = 2009121700;
	}
	
	function specialization() {
		global $COURSE;
        if($this->instance->pagetype == PAGE_COURSE_VIEW && $COURSE->id != SITEID) {
            $this->title = get_string('block_online_support_title', 'block_online_support');
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
        
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        if(has_capability('mod/smartcom:realtimesupported', $context)) {
        	$teachers = $this->get_support_teachers();
        	if(empty($teachers)) {
        		$this->content->text = get_string('block_online_support_no_teacher_online', 'block_online_support');
        	} else {
	        	$str = "";
		        $str .= '<div style="display: block; width:200px; padding-left: 10px;">';
		        foreach($teachers as $teacher) { 	
		        	$str .= "<div style=\"padding: 5px 0;\"><a href=\"#\" onclick = \"window.open('" .$CFG->wwwroot. "/message/discussion.php?id=$teacher->id','Chat', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,width=470,height=400'); return false;\" target = \"_blank\"><img align=\"absmiddle\" src=\"".$CFG->wwwroot."/user/pix.php?file=/$teacher->id/f1.jpg\" width=\"100\" height=\"100\" /></a>&nbsp;&nbsp;<a class = \"courseRB\" href=\"#\" onclick = \"window.open('" .$CFG->wwwroot. "/message/discussion.php?id=$teacher->id','Chat', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,width=470,height=400'); return false;\" target = \"_blank\">$teacher->username</a></div>";
		        }
		        $str .= '</div>';
		        $this->content->text = $str;
        	}
        } else {
        	$this->content->text = get_string('block_online_support_no_teacher_online', 'block_online_support');
        }
        
        
        $this->content->footer = '';               

        return $this->content;
    }
    
    function get_support_teachers() {
    	global $CFG, $COURSE;
    	$timetoshowusers = 300; //Seconds default
		if (isset($CFG->block_online_users_timetosee)) {
			$timetoshowusers = $CFG->block_online_users_timetosee * 60;
		}
		
		$timefrom = 100 * floor((time()-$timetoshowusers) / 100); // Round to nearest 100 seconds for better query cache
    	$timefrom = 0;
		$context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
    	$teachers = get_users_by_capability($context, 'mod/smartcom:sendnotification', 'u.id id, u.username username', '', '', '', '', '', false);
    	$result = array();
    	foreach($teachers as $teacher) {
    		$sql = 'SELECT max(timeaccess) as timeaccess FROM ' . $CFG->prefix . 'user_lastaccess ul WHERE ul.userid = ' . $teacher->id . ' LIMIT 1';
    		$results = get_records_sql($sql);
    		if(!empty($results)) {
    			foreach($results as $rs){
    				if($rs->timeaccess > $timefrom) {
    					$result[] = $teacher;
    				}
    			}
    		}
    	}
    	return $result;
    }
    
	function preferred_width() {
        return 210;
    }
    
	function applicable_formats() {
        return array('all' => true);
    }
    
	function instance_allow_multiple() {
        return true;
    }
}