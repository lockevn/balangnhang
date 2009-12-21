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
        	$str = "";
	        $str .= '<div style="display: block; width:200px; padding-left: 10px;">';
	        $teachers = $this->get_support_teachers();
	        foreach($teachers as $teacher) {    	
	        	$str .= "<div style=\"padding: 5px 0;\"><a href=\"#\" onclick = \"window.open('" .$CFG->wwwroot. "/message/discussion.php?id=$teacher->id','Chat', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,width=470,height=400'); return false;\" target = \"_blank\"><img align=\"absmiddle\" src=\"".$CFG->wwwroot."/user/pix.php?file=/$teacher->id/f1.jpg\" width=\"100\" height=\"100\" /></a>&nbsp;&nbsp;<a class = \"courseRB\" href=\"#\" onclick = \"window.open('" .$CFG->wwwroot. "/message/discussion.php?id=$teacher->id','Chat', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,width=470,height=400'); return false;\" target = \"_blank\">$teacher->username</a></div>";
	        }
	        $str .= '</div>';
	        $this->content->text = $str;
        } else {
        	$this->content->text = '';
        }
        
        
        $this->content->footer = '';               

        return $this->content;
        
        
        $this->content->text = $str;
        
        $this->content->footer = '';               

        return $this->content;
    }
    
    function get_support_teachers() {
    	global $CFG, $COURSE;
    	if($CFG->cachetype === 'memcached') {
    		$memcached = new memcached();
    		if($memcached === false) {
    			return false;
    		}
    		$teachers = $memcached->get(MemcachedUtil::$TEACHER_SUPPORT_KEY);
    		if(!empty($teachers)) {
    			//cache hits
    			return $teachers;
    		}
    	}
    	$context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
    	$teachers = get_users_by_capability($context, 'mod/smartcom:sendnotification', 'u.id id, u.username username', '', '', '', '', '', false);
    	
    	if(!empty($memcached)) {
    		$memcached->set(MemcachedUtil::$TEACHER_SUPPORT_KEY, $teachers, 1);
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