<?php


   /* - - - - - - - - - - - - - - - - - - - - - - - 
      block_course_standing.php
      created by Fridolin Wild (WU-Wien)
      for ODG on Mar 18, 2007
   */

   require_once($CFG->dirroot.'/mod/assignment/lib.php');

   class block_course_standing extends block_base {

      function init () {
         global $CFG;
	 $this->title = get_string('blockname', 'block_course_standing');
	 $this->version = 2007031700;
	 $this->config->maxchars = 12;
	 if (!$CFG->bcs_badrangemaximum)
		 set_config("bcs_badrangemaximum", "50");
	 if (!$CFG->bcs_okrangeminimum)
		 set_config("bcs_okrangeminimum", "70");
	 if (!$CFG->bcs_goodrangeminimum)
		 set_config("bcs_goodrangeminimum", "90");
	 $this->config_save($this->config);
      } // function init()

      function has_config() {
         return true;
      } // function has_config()

      function get_content() {

         global $db, $USER, $CFG, $config;

         if ($this->content !== NULL) {
	    return $this->content;
         }
	  
	 $this->content = new stdClass;

	 // get courses of the current user
	 $courses = get_my_courses($USER->id);
         if ((empty($courses) || !is_array($courses) || count($courses) == 0)) {
            $this->content->text .= get_string('no courses', 'block_course_standing');
         } else {

            // get assignments of the current user in his courses
            if (!$assignments = get_all_instances_in_courses('assignment',$courses)) {
               $this->content->text .= get_string('no assignments', 'block_course_standing');
	    } else {

                $sort_order_cs = optional_param( 'sort_order_cs','ASC', PARAM_RAW );

                $this->content->text .= '<div style="overflow:auto;Overflow-x:hidden;Overflow-y:scroll">';
                $this->content->text .= '<table border="0" cellspacing="2" cellpadding="0" align="center">';
		$this->content->text .= '<tr><td align="right">';
		$this->content->text .= '<a href="'.$CFG->pagepath.'?sort_order_cs='. ($sort_order_cs == 'ASC' ? 'DESC':'ASC')  .'"'.
		                        ' style="font-size:10px;color:#666666;">'. get_string('reverse','block_course_standing') .'</a>';
		$this->content->text .= '</td></tr>';
                $n = 0;
		$strict = 0;

		// get the id's of the already graded assignments
		$graded = $grade = array();
		$submissions = get_records('assignment_submissions', 'userid', $USER->id);
		if ($submissions) {
		   foreach ($submissions as $submission ) {
		      if ($submission->teacher!=0 && $submission->grade != -1) { // submitted and graded
		         $graded[]= $submission->assignment;
			 $grade[$submission->assignment]= $submission->grade;
                      }
		   } // foreach submission
		} // if submissions
                
                if (empty($graded)) {
                    $this->content->text .= '<tr><td>'.get_string('no grades', 'block_course_standing').'</td></tr>';
		}

                // sort by timedue, so extract timedues with key, then multisort
		$b = array();
		foreach ($assignments as $key => $assignment) {
		   $b[$key] = $grade[$assignment->id];
		}
		if ($sort_order_cs == 'DESC') {
		   array_multisort($b, SORT_DESC, $assignments);
		} else {
	           array_multisort($b, SORT_ASC, $assignments);
		}

		// now: print assignment by assignment
		$achtotal = 0;
		$achmaxtotal = 0;
                if (!empty($grade)) {
		foreach ($assignments as $assignment) {
                    
                    // display only when already graded
                    if ( in_array($assignment->id, $graded, true) ) {

                       // fetch the grade
		       $mygrade = $grade[$assignment->id];
                       
		       // truncate assignment title if too long
                       if (strlen($assignment->name) > $this->config->maxchars ) 
		          $name = substr($assignment->name,0,$this->config->maxchars)."..."; else $name = $assignment->name;
                       
		       // display name and a bar with the percentage achieved
		       $achmax = 100;
		       $ach = round(100*$mygrade/$assignment->grade);
		       $achtotal += $ach;
		       $achmaxtotal += 100;
		       if ($ach<$CFG->bcs_badrangemaximum) $colour = '#CC3300'; 
		       else if ($ach<$CFG->bcs_okrangeminimum) $colour = '#996600'; 
		       else if ($ach<$CFG->bcs_goodrangeminimum) $colour='#666600'; 
		       else $colour='#009900';
                       $due = '<table border="1" cellspacing="0" cellpadding="0" style="'.
		              'border:1px solid #666666;height:4px;"><tr style="height:4px;"><td style="font-size:4px;width:'.$ach.
			      'px;background-color:'.$colour.';"> </td><td style="height:4px;font-size:1px;width:'.
			      ($achmax-$ach).'px"> </td></tr></table>';
		       $colour = '#666666';

                       $this->content->text .= '<tr style="font-size:12px;color:'.$colour.';padding:1px;">' .
                                               '<td align="left" style="border-bottom:3px dotted #999999;">' .
                                               '<a '. ($assignment->visible ? '':' class="dimmed" '). 'href="'. $CFG->wwwroot .
		                               '/mod/assignment/view.php?id='. $assignment->coursemodule .
			  	               '" style="color:'. $colour .';font-weight:bold;">'.
                                               $name . ' ('.$ach.'%)</a>'.
                                               '<br/><div style="height:4px;"></div>'. $due .
                                               '<div style="height:4px;"></div>'.
	                                       '</td></tr>';
   	               $n++;

		    } // if graded 

		} // foreach assignments
		} // if not empty grade
                
                $this->content->text .= '</table></div>';

	    } // if assignments
            
            if (!empty($grade)) $this->content->footer = '<div align="center" style="font-size:10px;color:#666666;">'. $n .
	                             ' '. get_string('grades','block_course_standing') .', '.round(100*$achtotal/$achmaxtotal).'%'.
				     '</div>';
	    else
	    	$this->content->footer = '';

	 } // if courses

         return $this->content;

      } // function get_content()

      function specialisation() {
         $this->maxchars = $this->config->maxchars;
      }

      function instance_allow_config() {
         return true;
      } // function instance_allow_config()

      function config_save($data) {
         foreach ($data as $name => $value) {
            set_config($name, $value);
         }
         return true;
      } // function config_save()

      function applicable_formats() {
         return array('my'=>true);
      } // function applicable_formats()

   } // class block_course_standing

?>
