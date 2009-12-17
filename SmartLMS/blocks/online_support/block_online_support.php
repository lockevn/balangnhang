<?php

require_once $CFG->libdir . '/memcached.class.php';

class block_online_support extends block_base {
	function init() {
		$this->title = get_string('block_online_support_title', 'block_online_support');
		$this->version = 2009121700;
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
        $str .= '<div style="overflow: auto; display: block; overflow-x: no; overflow-y: scroll; height: 150px; width:218px; border: 1px solid #ccc; padding-left: 10px;">';
        $str .= '<p>';
        $str .= get_strings('block_online_support_headline', 'block_online_support');
        $str .= '</p>';
        
        $teachers = get_list_teacher();
        foreach($teachers as $teacher) {
        	$str .= '<p><a href="http://localhost/message/discussion.php?id='.$teacher['id'].'" target="_blank">'.$teacher['name'].'</a></p>';
        }
        
        $str .= '</div>';
        $this->content->text = $str;
        
        $this->content->footer = '';               

        return $this->content;
    }
    
    function get_list_teacher() {
    	$teachers = array(array('id'=> 1, 'name'=>'Demo'));
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