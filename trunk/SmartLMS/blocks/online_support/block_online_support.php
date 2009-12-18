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
        /*$str .= '<script type="text/javascript">';
		$str .= '	function onBrowse(url) {';
		$str .= '    var lx = (screen.width - 470) / 2;';
		$str .= '    var tx = (screen.height - 400) / 2;';
		
		$str .= '    var settings = "toolbar=no,";';
		$str .= '    settings += " location=no,";';
		$str .= '    settings += " directories=no,";';
		$str .= '    settings += " status=no,";';
		$str .= '    settings += " menubar=no,";';
		$str .= '    settings += " scrollbars=no,";';
		$str .= '    settings += " resizable=no,";';
		$str .= '    settings += " width=470,";';
		$str .= '    settings += " height=400,";';
		
		$str .= '    var newwin = window.open(url,"",""+ settings +" left="+ lx +", top="+ tx +"");';
		$str .= '    return false;';
		$str .= '}';
		$str .= '</script>';*/
		 
        $str .= '<div style="display: block; width:200px; margin-left:10px; padding-left: 10px;">';
        
        $str .= '<p>Demo online</p>';
        $str .= '<p><a href="#" onclick = "window.open(\'http://localhost/message/discussion.php?id=22\',\'Chat\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,width=470,height=400\'); return false;" target="_blank">Chat with teacher</a></p>';
        
        //$teachers = get_list_teacher();
        //foreach($teachers as $teacher) {
        //	$str .= '<a href="http://localhost/message/discussion.php?id='.$teacher['id'].'" target="_blank">'.$teacher['name'].'</a><br/>';
        //}
        
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