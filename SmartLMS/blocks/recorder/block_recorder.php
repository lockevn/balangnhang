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
        ?>
        <script>
        var interval = 0;
        function appletCheck() {
            if(document.applet) {
		        if(!document.applet.isActive()) {
		        	jQuery("#jre_download").html("<a href=\"<?php echo $CFG->wwwroot ?>/blocks/recorder/jre_6.17.exe\"><?php echo get_string('block_recorder_jre_need', 'block_recorder')?></a>");
	                return;
	            } else {
	            	//jQuery("#jre_download").html("<a href=\"<?php echo $CFG->wwwroot ?>/blocks/recorder/jre_6.17.exe\"><?php echo get_string('block_recorder_jre_need', 'block_recorder')?></a>");
	               return;
	            }
            } else {
            	setTimeout("appletCheck();", 1000);
            }
        }
        (function timedAppletCheck(){
            interval = setTimeout("appletCheck();", 1000);
        })();
        
        </script>
        <?php 
        $str .= '<br/><span id = "jre_download"></span>';
        $str .= '</div>';
        
        $this->content->text = $str;
        
        $this->content->footer = '';               

        return $this->content;
        
        
        $this->content->text = $str;
        
        $this->content->footer = '';               

        return $this->content;
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