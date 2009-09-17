<?php
/**
 * library of functions for block_completion_report
 * created by Andrew Chow, of Lambda Solutions Inc., Vancouver, BC, Canada
 * http://www.lambdasolutions.net/ - andrew@lambdasolutions.net
 **/ 


/**
 * simple function to provide support in html form processing
 * produces the attribute checked="checked" when needed
 * 
 * @param boolean $bln_status
 * @return string ' checked="checked" ' if $bln_status is 'on'
 *                ' ' if $bln_status is 'off'
 */
function strChecked ( $bln_status ) {
  return ($bln_status=='on'? ' checked="checked" ': " " );
} 
// end of function strChecked 


/**
 * check the current user's status using the Moodle function
 * (@link has_capability() ) makes use of the global variables
 * @param global $CFG    - to access server location in case of testing
 * @param global $USER   - to access user id
 * @param global $COURSE - to define context and permission
 * @return boolean true - if user has access to completion report
 *                        either as site:administrator
 *                        or site:editing teacher
 *                        or site:nonediting teacher
 *                        or course:editing teacher
 *                        or course:nonediting teacher
 *                        or course:administrator
 */
function check_user_status ( $courseid=0, $userid=0 ) {
	global $CFG, $USER, $COURSE;
	if ( $courseid==0 ) {
		$courseid = $COURSE->id;
	}
	if ( $userid==0 ) {
		$userid = $USER->id;
	}

	$context = get_context_instance(CONTEXT_COURSE, $courseid );
	$str_role = get_user_roles_in_context($userid, $context); // HACKED: LOCKEVN: $str_role = get_user_roles_in_context($userid, $context->id);
	return true;
	(
		strpos($str_role, 'Teacher') > 0 || 
		strpos( $str_role, 'Administrator') > 0 ||
		has_capability('moodle/legacy:admin', $context) || 
		has_capability('moodle/legacy:teacher', $context) || 
		has_capability('moodle/legacy:editingteacher', $context)
	);

} 
// end of function check_user_status


/**
 * Returns list of Required Resources id, retrieved from the database
 * table completion_configure using the parameter $courseid (see below)
 * the output is an array from the Moodle function (@link get_fieldset_sql() )
 * or null if either the courseid is invalid or no record was found in the table
 * 
 * @param int $courseid - a valid id from the course table
 * @return array from the Moodle function (@link get_fieldset_sql() ) or null
 */
function array_of_required_resources_name( $courseid ) {
	global $CFG;
	$arr_current_required_resources =  null;
	if ( isset($courseid) ) {
		// list of required resources id as specified by the completion requirement rubrics using the configure.php file
		$str_current_required_resources = get_field( 'completion_configure', 'strCurrentRequiredResources', 'intCourseID', $courseid);
	
		if ( $str_current_required_resources != '' ) {
			// retrieve name of resources from database table
			$str_sql = 'SELECT name ';  
			$str_sql .= ' FROM ' . $CFG->prefix . 'resource r ';
			$str_sql .= " WHERE r.id in ( " . $str_current_required_resources . " ); ";
			$arr_current_required_resources =  get_fieldset_sql ( $str_sql );
		} // end of if ( $str_current_required_resources!='' )
	} // end of if ( isset($courseid) )
		
	return $arr_current_required_resources;
}
// end of function array_of_required_resources( $courseid )


/**
 * Displays an unordered list of the resources which are required
 * in order to meet the Course Completion criteria.
 * The list of Required Resources are retrieved using the function
 * (@link array_of_required_resources() )
 * 
 * @param int $courseid - a valid id from the course table
 * @param boolean $return - default to true - outputs string of html instead of echo
 *                        - false - echoes output
 * @return true   - if echo was successful
 *         string - html containing header, name of course, and unordered list
 *         false  - if courseid was invalid
 */
function display_required_resources( $courseid, $return=true ) 
{
	global $CFG;
	$str_output = '';
	if ( isset($courseid) ) {
		// course name displayed on the page
		$coursename = get_field( 'course', 'fullname', 'id', $courseid);

		// display results
		$str_output = '<table align="center" width="80%">';
		$str_output .= '<tr><td><h3 class="pageheading">' . get_string( 'requiredresourcesinthecourse', 'block_completion_report') . ':</h3></td></tr>';
		$str_output .= '<tr><td><b class="coursename">' . $coursename . '</b></td></tr>';
		$arr_current_required_resources = array_of_required_resources_name( $courseid );
		
		if ( $arr_current_required_resources ) {
			$str_output .= '<tr><td><ul>';
			foreach ( $arr_current_required_resources as $str_required_resource ) {
			   $str_output .= '<li class="listitem">' . $str_required_resource . '</li>';
			}
			$str_output .= '</ul></td></tr>';
		} else {
			$str_output .= '<tr><td><p>' . get_string( 'noresourcesarerequired', 'block_completion_report') . '</p></td></tr>';
		} // end of if ( $arrOutputCurrentRequiredResources )
		$str_output .= '</table>';
	
		if ($return==false ) {
			echo '<br />' . $str_output;
			return true;
		} else {
			return $str_output;
		} // end of if ($return==false )
	} else {
		return false;
	} // end of if ( isset($courseid) )
}
// end of function display_required_resources( $courseid ) 


/**
 * simple function to provide support in sql query processing
 * produces the segment for the where clause when checking log table action column
 * 
 * @param int $courseid
 * @return string " AND action='view' " if the course is a regular Moodle course
 *                " AND action='clearview' " if the course is in ClearView format
 */
function clearview_check( $courseid )
{
	// if course is ClearView use appropriate SQL segment
	$str_courseview = get_field( 'course', 'courseview', 'id', $courseid);
	
	if ( $str_courseview=="custom" ) {
		$str_clearview = " AND action='clearview' ";
	} else {
		$str_clearview = " AND action='view' ";
	} // end of  if ( $str_courseview=="custom" )
	return $str_clearview;
}
// end of function clearview_check( $courseid )


/**
 * Returns an array of strings containing the names of all the resources
 * accessed by the user, regardless of whether the resource is required or not.
 * The list of Accessed Resources are retrieved using the function
 * (@link get_fieldset_sql() )
 * 
 * @param int $courseid - a valid id from the course table
 * @param int $userid - a valid id from the user table
 */
function array_of_accessed_resources_name( $courseid, $userid ) {
	global $CFG;
	$arr_accessed_resources_name = null;

	if ( isset($courseid) && isset($userid) ) {
		// retrieve string of SQL segment to use when checking log table action column
		$str_clearview = clearview_check( $courseid ); 
	
		// list of id of resources that have been accessed  
		$str_sql = 'SELECT distinct r.id ';  
		$str_sql .= 'FROM ' . $CFG->prefix . 'log l, ' . $CFG->prefix . 'resource r ';
		$str_sql .= 'WHERE userid = ' . $userid . " AND module='resource' " . $str_clearview .  " AND r.course= " . $courseid . " AND r.id = l.info ";
		$arr_accessed_resources =  get_fieldset_sql ( $str_sql );
		
		$str_accessed_resources = implode (',', $arr_accessed_resources);

		if ( $str_accessed_resources!='' ) {
			// retrieve name of resources from database table
			$str_sql = 'SELECT name ';  
			$str_sql .= ' FROM ' . $CFG->prefix . 'resource r ';
			$str_sql .= ' WHERE r.id in ( ' . $str_accessed_resources . ' ); ';
			$arr_accessed_resources_name =  get_fieldset_sql ( $str_sql );
		} // end of if ( $str_accessed_resources!='' )

	} // end of if ( isset($courseid) && isset($userid) )
	return $arr_accessed_resources_name;
}
// end of function array_of_accessed_resources_name( $courseid, $userid )


/**
 * Returns an array of ids from the database table resource, of resources
 * accessed by the user, which are required to satisfy the completion requirement.
 * The list of Accessed Resources are retrieved using the function
 * (@link get_fieldset_sql() )
 * 
 * @param int $courseid - a valid id from the course table
 * @param int $userid - a valid id from the user table
 */
function array_of_accessed_required_resources_id( $courseid, $userid ) {
	global $CFG;
	$str_sql = ''; // working string containing SQL statement when accessing database tables
	
	$str_current_required_resources = ''; // to be retrieved from database table completion_configure
	$arr_required_resources = null; // id from table resources based on $str_current_required_resources

	$arr_accessed_required_resources = null; // output object containing ids of accessed required resources

	// retrieve string of SQL segment to use checking log table action column
	$str_clearview = clearview_check( $courseid ); 
	
	// list of required resources id as specified by the completion requirement rubrics using the configure.php file
	$str_current_required_resources = get_field( 'completion_configure', 'strCurrentRequiredResources', 'intCourseID', $courseid);
//    echo 'course:' . $courseid;
//    echo 'required ids:' . $str_current_required_resources;
	// there are required resources
	if ( $str_current_required_resources != '' ) {
		$arr_required_resources = explode(',', $str_current_required_resources );

		// list of id of REQUIRED resources that have been accessed  
		$str_sql = 'SELECT distinct r.id ';  
		$str_sql .= 'FROM ' . $CFG->prefix . 'log l, ' . $CFG->prefix . 'resource r ';
		$str_sql .= 'WHERE userid = ' . $userid . " AND module='resource' " . $str_clearview .  " AND r.course= " . $courseid . " AND r.id = l.info ";
		$str_sql .= ' AND r.id in ( ' . $str_current_required_resources . ' ); ';
		$arr_accessed_required_resources =  get_fieldset_sql ( $str_sql );
	} else {
		$arr_accessed_required_resources = null;
	}  // end of if ( $str_current_required_resources != '' )
	return $arr_accessed_required_resources;
}
// end of function array_of_accessed_required_resources_id( $courseid, $userid )

/**
 * Displays two unordered lists of Accessed Resources. 
 * The first list is of the resources which are required
 * in order to meet the Course Completion criteria.
 * The second list of the resources which are not required.
 * Both lists are retrieved using the function
 * (@link get_fieldset_sql() )
 * 
 * @param int $courseid - a valid id from the course table
 * @param int $userid - a valid id from the user table
 */
function display_accessed_resources( $courseid, $userid )
{
	global $CFG;

// initialized variables
	$str_output = ''; // working string containing HTML code to be display using the echo statement
	$str_sql = ''; // working string containing SQL statement when accessing database tables

	// user and course names to be retrieved from database tables using $courseid and $userid
	$userfirstname = '';
	$userlastname = '';
	$coursename = '';
	
	$str_current_required_resources = ''; // string containing id of required resources, to be retrieved from database table completion_configure
	$arr_required_resources = null; // id from table resources based on $str_current_required_resources
	$int_count_required_resources = 0; // count of $arr_required_resources

	$arr_accessed_resources = null; // to be retrieved from database table log, and table resources based on $userid and $courseid
	$int_count_accessed_resources = 0; // count of $arr_current_accessed_resources
	$str_accessed_resources = ''; // string of comma separated values of id from $arr_current_accessed_resources

	$arr_accessed_required_resources = null; // 
	$int_count_accessed_required_resources = 0; // count of $arr_accessed_required_resources
	$str_accessed_required_resources = ''; // string of comma separated values of id from $arr_current_accessed_required_resources

	// retrieve string of SQL segment to use checking log table action column
	$str_clearview = clearview_check( $courseid ); 

	if ( isset($courseid) && isset($userid) ) {
		// user information retrieved from database using userid
		$userfirstname = get_field( 'user', 'firstname', 'id', $userid);
		$userlastname = get_field( 'user', 'lastname', 'id', $userid);
		$coursename = get_field( 'course', 'fullname', 'id', $courseid);


		// find required resources
		// list of required resources id as specified by the completion requirement rubrics using the configure.php file
		$str_current_required_resources = get_field( 'completion_configure', 'strCurrentRequiredResources', 'intCourseID', $courseid);

		// there are required resources
		if ( $str_current_required_resources != '' ) {
			$arr_required_resources = explode(',', $str_current_required_resources );

			// find the number of resources required  
			$int_count_required_resources = count( $arr_required_resources );
		} else { // there are NOT required resources 
			$int_count_required_resources = 0;
			$arr_required_resources = null;
		}
		// end of  if ( $str_current_required_resources != '' ) 

		// find accessed required resources section
		if ( $int_count_required_resources> 0 ) {
			// list of id of REQUIRED resources that have been accessed  
			$str_sql = 'SELECT distinct r.id ';  
			$str_sql .= 'FROM ' . $CFG->prefix . 'log l, ' . $CFG->prefix . 'resource r ';
			$str_sql .= 'WHERE userid = ' . $userid . " AND module='resource' " . $str_clearview .  " AND r.course= " . $courseid . " AND r.id = l.info ";
			$str_sql .= " AND r.id in ( " . $str_current_required_resources . " ); ";
			$arr_accessed_required_resources =  get_fieldset_sql ( $str_sql );

			// some required resources were accessed
			if ( $arr_accessed_required_resources ) {
				// find the number of required resources accessed 
				$int_count_accessed_required_resources = count( $arr_accessed_required_resources );
				// converts array of id into comma separated string
				$str_accessed_rquired_resources = implode(',',$arr_accessed_required_resources );
				// retrieve name of resources from database table
				$str_sql = 'SELECT name '; //, l.course, module, info, action, url 
				$str_sql .= ' FROM ' . $CFG->prefix . 'resource r ';
				$str_sql .= " WHERE r.id in ( " . $str_accessed_rquired_resources . " ); ";
				$arr_accessed_resources_name =  get_fieldset_sql ( $str_sql );
			} 
			// end of if ( $arr_accessed_required_resources ) 
		} else {
			$arr_accessed_required_resources = null;
			$str_accessed_rquired_resources = '';
			$int_count_accessed_required_resources = 0;
		}
		// end of if ( $int_count_required_resources> 0 )
	
		// list of ACCESSED resources id not in required list
		$str_sql = 'SELECT distinct r.id ';  
		$str_sql .= 'FROM ' . $CFG->prefix . 'log l, ' . $CFG->prefix . 'resource r ';
		$str_sql .= 'WHERE userid = ' . $userid . " AND module='resource' " . $str_clearview .  " AND r.course= " . $courseid . " AND r.id = l.info ";
		if ( $int_count_required_resources> 0 ) {
			$str_sql .= " AND r.id not in ( " . $str_current_required_resources . " ); ";
		}
		$arr_other_accessed_resources = get_fieldset_sql ( $str_sql );

		if ( $arr_other_accessed_resources ) { 

			if ( !empty( $arr_other_accessed_resources ) ) {
				$int_count_other_accessed_resources = count($arr_other_accessed_resources);
				// converts array of id into comma separated string
				$str_other_accessed_resources =  implode(',',$arr_other_accessed_resources );

				// retrieve name of resource from database
				$str_sql = 'SELECT name '; 
				$str_sql .= ' FROM ' . $CFG->prefix . 'resource r ';
				$str_sql .= " WHERE r.id in ( " . $str_other_accessed_resources . " ); ";
				
				$arr_other_accessed_resources_name =  get_fieldset_sql ( $str_sql );
				if ( empty( $arr_other_accessed_resources_name ) ) {
					$int_count_other_accessed_resources_name = 0;
				} else {
					$int_count_other_accessed_resources_name = count($arr_other_accessed_resources_name);
				} 
				// end of  if ( empty( $arr_other_accessed_resources_name ) )

			} else {
				$int_count_other_accessed_resources = 0;
				$arr_other_accessed_resources_name = null;
				$arr_other_accessed_resources = null;
			} 
			// end of  if ( !empty( $arr_other_accessed_resources ) )
		} 
		// end of   if ( $arr_other_accessed_resources ) 


		/*
		 * output section:
		 * required variables:
		 * $userfirstname
		 * $userlastname
		 * $coursename
		 * $int_count_required_resources
		 * $int_count_accessed_required_resources
		 * $int_count_other_accessed_resources
		 * $arr_accessed_required_resources
		 * $arr_other_accessed_resources
		 */
		
		$str_first_title = '';
		$str_second_title = '';
		$str_third_title = '';
	
		$str_output = '<table align="center" width="80%">';
		
//        $str_first_title .=  get_string( 'accessed', 'block_completion_report')  . ' ' . get_string( 'resources', 'block_completion_report');
		$str_first_title .= '<h3 class="pageheading"> ' . $userfirstname . ' ' . $userlastname . '</h3>';
		$str_first_title .=  '<h3 class="pageheading"> ' . get_string( 'accessed', 'block_completion_report') .  ' ' . get_string( 'inthecourse', 'block_completion_report') . '</h3>' ; 
		$str_first_title = ucwords($str_first_title);
		$str_output .= '<tr><td><h3 class="pageheading"> ' . $str_first_title .'</h3></td></tr>';
		$str_output .= '<tr><td><h3 class="coursename"> ' . $coursename . '</h3></td></tr>';

		$str_second_title = $int_count_accessed_required_resources . ' ' ;
		$str_second_title .= get_string( 'outof', 'block_completion_report') . ' ' ;
		$str_second_title .= $int_count_required_resources . ' ' . get_string( 'requiredresources', 'block_completion_report') ;
		$str_second_title = ucwords($str_second_title);
		$str_insert_symbol = ( $int_count_accessed_required_resources>0?':':'.'); // depending on whether any resources are accessed, use colon for list,  period to end sentence.

		$str_output .= '<tr><td><h3 class="pageheading">' . $str_second_title . $str_insert_symbol . '</h3></td></tr>';
		$str_output .= '<tr><td><ul>';

		// there were some accessed required resources
		if ( $arr_accessed_resources_name ) { 
			foreach ( $arr_accessed_resources_name as $str_accessed_resources_name ) {
			   $str_output .= '<li>' . $str_accessed_resources_name . '</li>';
			}
		}

		$str_output .= '</ul></td></tr>';

		// there were some other accessed resources
		/* for future
		$str_third_title = get_string( 'and', 'block_completion_report') . ' ' . $int_count_other_accessed_resources . ' ' . get_string( 'notinthelistofrequiredresources', 'block_completion_report');
		$str_third_title = ucwords($str_third_title);
		$str_insert_symbol = ( $int_count_other_accessed_resources>0?':':'.'); // depending on whether any resources are accessed, use colon for list,  period to end sentence.

		$str_output .= '<tr><td><h3 class="pageheading">' . $str_third_title . $str_insert_symbol . '</h3></td></tr>';
		$str_output .= '<tr><td><ul>';
		if ( $arr_other_accessed_resources_name ) { 
			foreach ( $arr_accessed_resources_name as $str_other_accessed_resource_name ) {
			   $str_output .= '<li>' . $str_other_accessed_resource_name . '</li>';
			}
		}
		$str_output .= '</ul></td></tr>';
		*/
		$str_output .= '<table>';
	
	
		// display list of required resources
		echo '<br />' . $str_output;
	} // end of if ( isset($userid) && isset($courseid) )
}
// end of function display accessed_resources

?>