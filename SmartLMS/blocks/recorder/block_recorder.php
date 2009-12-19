<?php

class block_recorder extends block_base {
function init() {
		$this->title = get_string('block_recorder_title', 'block_recorder');
		$this->version = 2009121900;
	}
	
	function specialization() {
		
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
        $str .= '<applet id="applet" archive="'.$CFG->wwwroot.'/mod/nanogong/nanogong.jar" code="gong.NanoGong" width="180" height="40"></applet>';
        $str .= '<span><a href="#">'. get_string('block_recorder_jre_need', 'block_recorder') . '</a></span>';
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