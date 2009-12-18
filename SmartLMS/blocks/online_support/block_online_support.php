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
        
        $str = "";
        $str .= '<div style="display: block; width:200px; padding-left: 10px;">';
        $teachers = $this->get_support_teachers();
        foreach($teachers as $teacher) {    	
        	$str .= "<div style=\"padding: 5px 0;\"><a href=\"#\" onclick = \"window.open('" .$CFG->wwwroot. "/message/discussion.php?id=22','Chat', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,width=470,height=400'); return false;\" target = \"_blank\"><img align=\"absmiddle\" src=\"".$CFG->wwwroot."/user/pix.php?file=/$teacher->id/f1.jpg\" width=\"100\" height=\"100\" /></a>&nbsp;&nbsp;<a class = \"courseRB\" href=\"#\" onclick = \"window.open('" .$CFG->wwwroot. "/message/discussion.php?id=22','Chat', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,width=470,height=400'); return false;\" target = \"_blank\">$teacher->username</a></div>";
        }
        $str .= '</div>';
        $this->content->text = $str;
        
        $this->content->footer = '';               

        return $this->content;
        
        
        $this->content->text = $str;
        
        $this->content->footer = '';               

        return $this->content;
    }
    
    function get_support_teachers() {
    	global $CFG;
    	$sql = "SELECT u.id id, u.username username
				FROM " . $CFG->prefix . "user u 
				WHERE u.id = 22";
    	$results = get_records_sql($sql);
    	
    	$teachers = array();
    	if(!empty($results)) {
    		foreach($results as $result) {
    			$teachers[] = $result;
    		}
    	}
    	
    	return $teachers;
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