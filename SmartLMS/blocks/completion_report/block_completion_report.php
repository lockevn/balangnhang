<?php //$Id: block_completion_report.php,v 1.13 2006/12/20

class block_completion_report extends block_base {
    var $user;
    var $cfg;
    var $course;
    function init( ) {
        $this->title = get_string('blockname', 'block_completion_report'); 
        $this->version = 2006012200;
    }

    function get_content( ) 
    {  
	    if ($this->content !== NULL) {
		    return $this->content;
	    } 
        $str_output = '';
        $str_footer = '';

        global $USER, $CFG;
        $userid = $USER->id;
        if ( $userid != 0 && $userid !=1 )
        {
            $str_footer = '';
            $str_output = '';
            if ( $this->courseid > 1 ) {
                $str_course = '?course=' . $this->courseid;
            } else {
                $str_course = '';
            }
            //$str_course = '';   // turn off context dependent feature for now until bug fix
            $context = get_context_instance(CONTEXT_COURSE, $this->course->id );
            /*danhut modified*/
            if(!empty($context)) {
	            $str_role = get_user_roles_in_context($USER->id, $context);
	        
	            if (
	//                    strpos( $str_role, 'Student') < 0 && 
	                    ( 
	//                        strpos($CFG->wwwroot, 'lambdamoodle') > 0 || 
	//                        strpos($CFG->wwwroot, 'localhost') > 0  || 
	                        strpos($str_role, 'Teacher') > 0 || 
	                        strpos( $str_role, 'Administrator') > 0 || 
							has_capability('moodle/legacy:admin', $context) ||
							has_capability('moodle/legacy:teacher', $context) ||
							has_capability('moodle/legacy:editingteacher', $context ) 
	                    )
	                )
	            {
	                $str_output .= "<img title=\"" . get_string('completionreport', 'block_completion_report') . "\" src=\"" . $CFG->wwwroot . "/blocks/" . $this->name() . "/pix/complete.gif\" />";
	                $str_output .= "<b>" . get_string('completionreport', 'block_completion_report') .  "</b>";
	        
	                $str_output .= helpbutton('help', get_string('helpwithcompletionreport', 'block_completion_report'), 'block_completion_report', true, false, '', true);
	                $str_output .= "<br />&nbsp;&nbsp;&nbsp;<b><a title='" . get_string('configurerequirementrubrics', 'block_completion_report') .  "' href='" . $CFG->wwwroot . "/blocks/" . $this->name() . "/configure.php" . $str_course .  "'>";
	                $str_output .= get_string('configurerequirementrubrics', 'block_completion_report') .  "</a><br />"; 
	                $str_output .= "&nbsp;&nbsp;&nbsp;<b><a title='" . get_string('generatereport', 'block_completion_report') .  "' href='" . $CFG->wwwroot . "/blocks/" . $this->name() . "/report.php" . $str_course . "'>" . get_string('generatereport', 'block_completion_report') .  "</a></b>";
	         
//	                    $bln_video_reports = get_field('completion_video','id', '', '');
//	                if ( $bln_video_reports ) {
//	                    $str_output .= "<hr />&nbsp;&nbsp;&nbsp;<b><a title='Video and Policy Report' href='" . $CFG->wwwroot . "/blocks/" . $this->name() . "/videopolicyreport.php'>Video&nbsp;and&nbsp;Policy&nbsp;Report</a></b>";
//	                }
	                
	            } // if check_capability
            }
        } // end if $userid = 0 
  
        $this->content = new stdClass;
        $this->content->text = $str_output;
        $this->content->footer = $str_footer;
        return $this->content;
    } // end of function get_content

    // obsolete if instance_allow_multiple() is set to true
    function instance_allow_config ( )   
    {
        return false;   
    }

    function specialization ( )   
    {
        global $CFG, $USER, $COURSE;
        $this->user = $USER;
        $this->cfg = $CFG;
        $this->course = $COURSE;
        $this->courseid = $COURSE->id;
    }
    
    // when set to true, this makes instance_allow_config() obsolete
    function instance_allow_multiple ( )   
    {
        return false;   
    }
    
    function has_config ( )   
    {
        return false;   
    }
    
    function hide_header( ) 
    {
        return true;
    }

    function preferred_width( ) 
    {
        return 360;
    }
    
    function html_attributes( )   
    {
        return array (  'style'=>'border:single solid black;' );
    }
    
    function applicable_formats( )  
    {
        return array('all' => true);
    }

} // end of class definition

?>
