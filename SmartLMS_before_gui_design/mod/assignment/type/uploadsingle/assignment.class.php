<?php // $Id: assignment.class.php,v 1.33.2.5 2008/04/08 03:02:49 scyrma Exp $
	
	require_once($CFG->libdir.'/blocklib.php');
	require_once($CFG->dirroot.'/mod/assignment/type/uploadsingle/pagelib.php');	
	        
    //require_once($CFG->dirroot.'/mod/quiz/pagelib.php');
/**
 * Extend the base assignment class for assignments where you upload a single file
 *
 */
class assignment_uploadsingle extends assignment_base {


    function print_student_answer($userid, $return=false){
           global $CFG, $USER;

        $filearea = $this->file_area_name($userid);

        $output = '';

        if ($basedir = $this->file_area($userid)) {
            if ($files = get_directory_list($basedir)) {
                require_once($CFG->libdir.'/filelib.php');
                foreach ($files as $key => $file) {

                    $icon = mimeinfo('icon', $file);
                    $ffurl = get_file_url("$filearea/$file");

                    //died right here
                    //require_once($ffurl);
                    $output = '<img src="'.$CFG->pixpath.'/f/'.$icon.'" class="icon" alt="'.$icon.'" />'.
                            '<a href="'.$ffurl.'" >'.$file.'</a><br />';
                }
            }
        }

        $output = '<div class="files">'.$output.'</div>';
        return $output;
    }

    function assignment_uploadsingle($cmid='staticonly', $assignment=NULL, $cm=NULL, $course=NULL) {
        parent::assignment_base($cmid, $assignment, $cm, $course);
        $this->type = 'uploadsingle';
    }

    function view() {

        global $USER;
        global $CFG;

        $context = get_context_instance(CONTEXT_MODULE,$this->cm->id);
        require_capability('mod/assignment:view', $context);

        add_to_log($this->course->id, "assignment", "view", "view.php?id={$this->cm->id}", $this->assignment->id, $this->cm->id);

        $this->view_header();
        
         // Initialize $PAGE, compute blocks    
	    $PAGE       = page_create_instance($this->assignment->id);
	    $pageblocks = blocks_setup($PAGE, BLOCKS_PINNED_BOTH);
	    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);
	    /*end of danhut modified*/	
	    echo '<table id="layout-table"><tr>';
	
	    if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
	        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
	        print_container_start();
	        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
	        print_container_end();
	        echo '</td>';
	    }
	    echo '<td id="middle-column">';	    
	    	     
		$menu = navmenu($this->course, $this->cm);
		echo $menu;
		groups_print_activity_menu($this->cm, 'view.php?id=' . $this->cm->id);
        echo '<div class="reportlink">'.$this->submittedlink().'</div>';
        echo '<div class="clearer"></div>';
		/*end of danhut added*/
        $this->view_intro();

        $this->view_dates();

        $filecount = $this->count_user_files($USER->id);

        if ($submission = $this->get_submission()) {
            if ($submission->timemarked) {
                $this->view_feedback();
            }
            if ($filecount) {
                print_simple_box($this->print_user_files($USER->id, true), 'center');
            }
        }

        if (has_capability('mod/assignment:submit', $context)  && $this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            $this->view_upload_form();
        }
        
        /*danhut added*/
        
        print_container_end();
	    echo $menu;
	    echo '</td>';
	    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 210);    
		if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $PAGE->user_is_editing())) {
	        echo '<td style="width: '.$blocks_preferred_width.'px;" id="right-column">';
	        print_container_start();
	        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
	        print_container_end();
	        echo '</td>';
	    }
	    
	    echo '</tr></table>';
	    /*end of danhut added*/

        $this->view_footer();
    }
    



    function view_upload_form() {
        global $CFG;
        $struploadafile = get_string("uploadafile");

        $maxbytes = $this->assignment->maxbytes == 0 ? $this->course->maxbytes : $this->assignment->maxbytes;
        $strmaxsize = get_string('maxsize', '', display_size($maxbytes));

        echo '<div style="text-align:center">';
        echo '<form enctype="multipart/form-data" method="post" '.
             "action=\"$CFG->wwwroot/mod/assignment/upload.php\">";
        echo '<fieldset class="invisiblefieldset">';
        echo "<p>$struploadafile ($strmaxsize)</p>";
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        require_once($CFG->libdir.'/uploadlib.php');
        upload_print_form_fragment(1,array('newfile'),false,null,0,$this->assignment->maxbytes,false);
        echo '<input type="submit" name="save" value="'.get_string('uploadthisfile').'" />';
        echo '</fieldset>';
        echo '</form>';
        echo '</div>';
    }


    function upload() {

        global $CFG, $USER;

        require_capability('mod/assignment:submit', get_context_instance(CONTEXT_MODULE, $this->cm->id));

        $this->view_header(get_string('upload'));

        $filecount = $this->count_user_files($USER->id);
        $submission = $this->get_submission($USER->id);
        if ($this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            if ($submission = $this->get_submission($USER->id)) {
                //TODO: change later to ">= 0", to prevent resubmission when graded 0
                if (($submission->grade > 0) and !$this->assignment->resubmit) {
                    notify(get_string('alreadygraded', 'assignment'));
                }
            }

            $dir = $this->file_area_name($USER->id);

            require_once($CFG->dirroot.'/lib/uploadlib.php');
            $um = new upload_manager('newfile',true,false,$this->course,false,$this->assignment->maxbytes);
            if ($um->process_file_uploads($dir)) {
                $newfile_name = $um->get_new_filename();
                if ($submission) {
                    $submission->timemodified = time();
                    $submission->numfiles     = 1;
                    $submission->submissioncomment = addslashes($submission->submissioncomment);
                    unset($submission->data1);  // Don't need to update this.
                    unset($submission->data2);  // Don't need to update this.
                    if (update_record("assignment_submissions", $submission)) {
                        add_to_log($this->course->id, 'assignment', 'upload',
                                'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                        $submission = $this->get_submission($USER->id);
                        $this->update_grade($submission);
                        $this->email_teachers($submission);
                        print_heading(get_string('uploadedfile'));
                    } else {
                        notify(get_string("uploadfailnoupdate", "assignment"));
                    }
                } else {
                    $newsubmission = $this->prepare_new_submission($USER->id);
                    $newsubmission->timemodified = time();
                    $newsubmission->numfiles = 1;
                    if (insert_record('assignment_submissions', $newsubmission)) {
                        add_to_log($this->course->id, 'assignment', 'upload',
                                'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                        $submission = $this->get_submission($USER->id);
                        $this->update_grade($submission);
                        $this->email_teachers($newsubmission);
                        print_heading(get_string('uploadedfile'));
                    } else {
                        notify(get_string("uploadnotregistered", "assignment", $newfile_name) );
                    }
                }
            }
        } else {
            notify(get_string("uploaderror", "assignment")); //submitting not allowed!
        }

        print_continue('view.php?id='.$this->cm->id);

        $this->view_footer();
    }

    function setup_elements(&$mform) {
        global $CFG, $COURSE;

        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));

        $mform->addElement('select', 'resubmit', get_string("allowresubmit", "assignment"), $ynoptions);
        $mform->setHelpButton('resubmit', array('resubmit', get_string('allowresubmit', 'assignment'), 'assignment'));
        $mform->setDefault('resubmit', 0);

        $mform->addElement('select', 'emailteachers', get_string("emailteachers", "assignment"), $ynoptions);
        $mform->setHelpButton('emailteachers', array('emailteachers', get_string('emailteachers', 'assignment'), 'assignment'));
        $mform->setDefault('emailteachers', 0);

        $choices = get_max_upload_sizes($CFG->maxbytes, $COURSE->maxbytes);
        $choices[0] = get_string('courseuploadlimit') . ' ('.display_size($COURSE->maxbytes).')';
        $mform->addElement('select', 'maxbytes', get_string('maximumsize', 'assignment'), $choices);
        $mform->setDefault('maxbytes', $CFG->assignment_maxbytes);

    }

}

?>
