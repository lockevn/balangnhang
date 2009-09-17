
<?php  
/*
 * report.php - part of block_completion_report
 *            - allows a course to be chosen from a dropdown menu of all available courses
 *            - displays all users enrolled in the chosen course, listing their individually achieved grade according
 *              to the configured rubrics using the configure page
 * created by Andrew Chow, of Lambda Solutions Inc., Vancouver, BC, Canada
 * http://www.lambdasolutions.net/ - andrew@lambdasolutions.net
 * based on block tutorial by Jon Papaioannou (pj@uom.gr) and the front page from version 1.7
 * with all the French translation files in /lang/fr_utf8/ created by Valery Fremaux at http://www.ethnoinformatique.fr/
 */ 

/// Bounds for block widths on this page
	define('BLOCK_L_MIN_WIDTH', 160);
	define('BLOCK_L_MAX_WIDTH', 210);
	define('BLOCK_R_MIN_WIDTH', 160);
	define('BLOCK_R_MAX_WIDTH', 210);

	require_once('../../config.php');
	require_once($CFG->dirroot .'/lib/datalib.php');
	require_once($CFG->dirroot .'/course/lib.php');
	require_once($CFG->dirroot .'/lib/blocklib.php');
	require_once('lib.php');

	// user id
	$userid = optional_param('userid', 0, PARAM_RAW); 
	if ( $userid ==0 )
	{
		$userid = optional_param('user', 0, PARAM_RAW);
	} 
	
	// course id
	$courseid = optional_param('id', 0, PARAM_RAW); 
	
	if ( $courseid ==0 )
	{
		$courseid = optional_param('course', 0, PARAM_RAW);
	} 


// function to output HTML string for checkbox status


	if ($CFG->forcelogin) {
		require_login();
	}

	if ($CFG->rolesactive) { // if already using roles system
		if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
			if (moodle_needs_upgrading()) {
				redirect($CFG->wwwroot .'/'. $CFG->admin .'/index.php');
			}
		} else if (!empty($CFG->mymoodleredirect)) {    // Redirect logged-in users to My Moodle overview if required
			if (isloggedin()) {
				redirect($CFG->wwwroot .'/my/index.php');
			}
		}
	} else { // if upgrading from 1.6 or below
		if (isadmin() && moodle_needs_upgrading()) {
			redirect($CFG->wwwroot .'/'. $CFG->admin .'/index.php');
		}
	}


	if (get_moodle_cookie() == '') {   
		set_moodle_cookie('nobody');   // To help search for cookies on login page
	}

	if (!empty($USER->id)) {
		add_to_log(SITEID, 'course', 'view', 'view.php?id='.SITEID, SITEID);
	}

	if (empty($CFG->langmenu)) {
		$langmenu = '';
	} else {
		$currlang = current_language();
		$langs = get_list_of_languages();
		$langmenu = popup_form ($CFG->wwwroot .'/index.php?lang=', $langs, 'chooselang', $currlang, '', '', '', true);
	}
	if ($courseid>0)
	{
		$PAGE       = page_create_object(PAGE_COURSE_VIEW, $courseid);
	}
	else
	{
		$PAGE       = page_create_object(PAGE_COURSE_VIEW, SITEID);
	}

	$pageblocks = blocks_setup($PAGE);
	$editing    = $PAGE->user_is_editing();
	$preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),  
											BLOCK_L_MAX_WIDTH);
	$preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 
											BLOCK_R_MAX_WIDTH);
	print_header(strip_tags($SITE->fullname), $SITE->fullname, 'Reports', '',
				 '<meta name="description" content="'. s(strip_tags($SITE->summary)) .'" />',
				 true, '', user_login_string($SITE));

?>

<style>
/* style for percentage text boxes */
.percentage
{
	font-size: 12px;
	font-family: verdana;
	text-align: right;
}
/* style for options in dropdown menu */
option
{
	font-size: 12px;
	font-family: Arial Narrow;
}
/* style for options in dropdown menu */
.complete
{
	font-size: 100%;
	font-family: Arial;
}
/* style for configuration forms */
.configurationForm
{
	border: thin solid #000080; 
	background-color:#EEEEEE;
	padding: 5px;
	margin:10px;
}
</style>
<table id="layout-table">
  <tr>
  <?php

	if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
		echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
		blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
		echo '</td>';
	}

	echo '<td id="middle-column">';


/// Print Section

/* 
 * beginning of completion report section
 */
/**
 * section to allow non-administrator and non-teachers to access demonstration course
 * which is specified in user defined string variables: $strServer, $strDemoCourse
 * which are defined in the associative array $arrServerDemo
 * MUST BE REMOVED in real production server
 */
 /*
$arrServerDemo = array( 
						'lambdamoodle'=>'Moodle Community', 
						'localhost'=>'New Test Course' 
					   ); 
if (
	  !(  
		has_capability('moodle/legacy:admin') || 
		has_capability('moodle/legacy:teacher') || 
		has_capability('moodle/legacy:editingteacher')
		) 
	)
{
	foreach ( $arrServerDemo as $strServer=>$strDemoCourse)
	{
		if ( strpos($CFG->wwwroot, $strServer) > 0 )
		{ 
			$courseid = get_field('course', 'id', 'fullname', $strDemoCourse);
		}
	}
}*/
// end of section to allow guest access to demonstration course - remove up to this line

if ( check_user_status() )
{

	$strOutput = ''; // initialize output string, which is output at end of print section

	$scriptName = 'report.php'; // this file name for use in form submission

	$arrReportColumns = array (
	'id', 'userid', 'userfirstname', 'userlastname', 
	'intaverageQuiz', 'intGradeFinalExam', 'intaverageAssignment', 'intGradeFinalProject', 'intGradeManual', 
	'strCompletedResources', 'courseid', 'strCompletionStatus' );

	// string to be displayed when one of the sections are not required
	$strManualGradeCompletionStatus = get_string( 'notrequired', 'block_completion_report');
	$strResourcesCompletionStatus = get_string( 'notrequired', 'block_completion_report');
	$strQuizzesCompletionStatus = get_string( 'notrequired', 'block_completion_report');
	$strAssignmentsCompletionStatus = get_string( 'notrequired', 'block_completion_report');
	$strCourseCompletionStatus = get_string( 'notrequired', 'block_completion_report');
	
	// string to be displayed when one of the sections has been completed or is incomplete
	$strCompleted = '<span style="color: #006600;">' . get_string( 'completed', 'block_completion_report') . '</span>';
	$strIncomplete = '<span style="color: #cc0000;">' . get_string( 'incomplete', 'block_completion_report') . '</span>';
	
	// parameters from submitted forms
	
	
	// form elements
  
	// create heading for the page containing the base category's full name - and display in H2 format    
	$strHeading =  get_field  ('course', 'fullname', 'category', '0');
	$strOutput .= '<h2>' . $strHeading . '</h2>';
	
	
	// saving submitted data to database
	
	if ( $courseid == 0 )
	{
	//  display all courses in drop down menu 
	   $strOutput .= '<form class="configurationForm" action="' . $scriptName . '" method="post"><table>'; 
	
	   $objResult =  get_courses  ();
	   $strOutput .= '<tr><td>' . get_string( 'choosecoursedisplayreport', 'block_completion_report') . '</td><td><select style="width:250px;" name="course">'; 
	   foreach ( $objResult as $objCourse )
	   {
			$context = get_context_instance(CONTEXT_COURSE, $objCourse->id);
			$strRole = get_user_roles_in_context($USER->id, $context->id);
			$contextAdmin = get_context_instance(CONTEXT_SYSTEM);
			$strAdminRole = get_user_roles_in_context($USER->id, $contextAdmin->id);
			if ($objCourse->category !=0 && ( strpos($strRole, 'Teacher') > 0 || strpos( $strAdminRole, 'Administrator') > 0 )  )
			{
				$strOutput .= '<option class="complete" value="' . $objCourse->id . '">' . $objCourse->fullname . '</option>';
			}
	   }
	   $strOutput .= '</select></td>'; 
	 
	   $strOutput .= '<tr>'; 
	
	   $strOutput .= '<tr><td colspan="2"><input type="submit" name="showParticipant" value="' . get_string( 'showcompletionreportforselectedcourse', 'block_completion_report') . '" /></td></tr>'; 
	
	   $strOutput .= '</table></form>'; 
	
	   echo $strOutput;
	
	}
	else // if a course was selected - display completion report 
	{
		// display course heading
		$strResult =  get_field  ('course', 'fullname', 'id', $courseid);
		$strBackgroundColor = '#D3E0E7';
		$strColor = 'black'; 
		$strLink = '<a href="smartcom_configure.php?course=' . $courseid . '">' . get_string( 'configurerequirementrubricsforthiscourse', 'block_completion_report') . '</a>';
		$strOutput .= '<div style="text-align:right;padding:3px;font-size:smaller;"><b>' . $strLink . '</b></div>';
		$strOutput .= '<h3>' . get_string( 'completionreportforthecourse', 'block_completion_report') . '</h3>';
		$strOutput .= '<h3><div style="background-color:' . $strBackgroundColor . ';color:' . $strColor . ';padding:3px;border:thin solid teal;font-size:larger;"><b>' . $strResult . '</b></div></h3>';


		//  display all users in the report 
		$objUsers =  get_users_confirmed (true, '', false, '', 'firstname ASC', '', '', '', '', '*');
		
		// initialize display strings
		$strParticipant = ''; 
		$strAssignment = ''; 
		$strQuiz = ''; 
		$strResource = ''; 
		$strManual = ''; 
		$strCompletion = ''; 
	
		/*
		 * retrieve Rubric from configuration database table which were stored using the forms in the configure.php page
		 */
		// submitted assignment section-----------------------------------------
		// reflects checkbox state - on => assignment section required, off => assignment section omitted
		$blnEnableAssignment = get_field( 'completion_configure', 'blnEnableAssignment', 'intCourseID', $courseid);

		// from textbox input - percentage threshold to meet completion requirement - tested for greater than or equal to
		$intPercentAssignment = get_field( 'completion_configure', 'intPercentAssignment', 'intCourseID', $courseid);

		// reflects dropdown menu selection of two choices - FinalProject or AverageOfAllAssignments
		$strApplyAssignmentGrade = get_field( 'completion_configure', 'strApplyAssignmentGrade', 'intCourseID', $courseid);

		// reflects dropdown menu selection generated from database - list the choice for FinalProject - not used if assignment grade is average of all assignments
		$intFinalProjectID = get_field( 'completion_configure', 'intFinalProjectID', 'intCourseID', $courseid);
		$ProjectName = get_field( 'assignment', 'name', 'id', $intFinalProjectID);
		//---------------------------------------------------------------------
		
		// submitted quiz section----------------------------------------------
		// reflects checkbox state - on => quiz section required, off => quiz section omitted
		$blnEnableQuiz = get_field( 'completion_configure', 'blnEnableQuiz', 'intCourseID', $courseid);

		// from textbox input - percentage threshold to meet completion requirement - tested for greater than or equal to
		$intPercentQuiz = get_field( 'completion_configure', 'intPercentQuiz', 'intCourseID', $courseid);

		// reflects dropdown menu selection of two choices - FinalExam or AverageOfAllQuizzes
		$strApplyPassingGrade = get_field( 'completion_configure', 'strApplyPassingGrade', 'intCourseID', $courseid);

		// reflects dropdown menu selection generated from database - list the choice for FinalExam - not used if quiz grade is average of all quizzes
		$intFinalExamID = get_field( 'completion_configure', 'intFinalExamID', 'intCourseID', $courseid);

		// stores the name of the chosen FinalExam - not used if quiz grade is average of all quizzes
		$ExamName = get_field( 'quiz', 'name', 'id', $intFinalExamID);
		//----------------------------------------------------------------------
	
		// submitted resource section-------------------------------------------
		// reflects checkbox state - on => resources section required, off => resources section omitted
		$blnEnableResource =  get_field  ('completion_configure', 'blnEnableResource', 'intCourseID', $courseid);

		// from list menu selection - listing all the resources as required
		$strCurrentRequiredResources = get_field( 'completion_configure', 'strCurrentRequiredResources', 'intCourseID', $courseid);

		if ( trim($strCurrentRequiredResources)=='' ) {
			$intRequiredResources = 0;
			$arrRequiredResources =  null;
		} else {
		
			// from list menu selection - listing all the resources NOT required
			$strOther = ($strCurrentRequiredResources == null ? ' ' : ' and id not in ( '. $strCurrentRequiredResources .') ' );
			$strAnother = ($strCurrentRequiredResources == null ? ' ' : ' and id in ( '. $strCurrentRequiredResources .') ' );
			// retrieves from database array of required resource to output size of array later, displays number of required resources
			$arrRequiredResources =   get_records_select  ('resource', 'course = ' . $courseid . $strAnother , 'id', 'name,id');
			$intRequiredResources =  count( $arrRequiredResources);
		}
		// string for displaying link to pop up required resource window - displaying the names of the required resources
		$url = $CFG->wwwroot . "/blocks/completion_report/required_resources.php?course=" . $courseid;
		$popupname = 'requiredresourcepopup';
		$strLink = get_string( 'numberofrequiredresources', 'block_completion_report');
		$strText = get_string( 'clicktoseelistofrequiredresources', 'block_completion_report');
		$strOutput2 = link_to_popup_window ($url, $popupname, $strLink, 400, 700, $strText, 'none', true);
		//---------------------------------------------------------------------
	
		// submitted manual section--------------------------------------------
		// reflects checkbox state - on => resources section required, off => resources section omitted
		$blnEnableManual =  get_field  ('completion_configure', 'blnEnableManual', 'intCourseID', $courseid);
		// from textbox input - percentage threshold to meet completion requirement - tested for greater than or equal to
		$intPercentManual = get_field( 'completion_configure', 'intPercentManual', 'intCourseID', $courseid);
		//---------------------------------------------------------------------

		/*
		 * end of control variable initialization section
		 */
	   
		/* 
		 * creating table containing report
		 */
		$strHeading = '<table border="1"><tr><th>' . get_string( 'participant', 'block_completion_report') . '</th>';
	
		if ( $blnEnableAssignment=='on' )
		{
			$strHeading .= '<th>' . ($strApplyAssignmentGrade=='FinalProject'? ( get_string( 'finalproject', 'block_completion_report') . ':<br /><i>'.$ProjectName.'</i>'):get_string( 'averageofallassignments', 'block_completion_report')) . '<br />' . get_string( 'passinggrade', 'block_completion_report') .  $intPercentAssignment . '%</th>';
		}

		if ( $blnEnableQuiz=='on' )
		{
			$strHeading .= '<th>' . ($strApplyPassingGrade=='FinalExam'?get_string( 'finalexam', 'block_completion_report') . ':<br /><i>'.$ExamName.'</i>':get_string( 'averageofallquizzes', 'block_completion_report')) . '<br />' . get_string( 'passinggrade', 'block_completion_report') . $intPercentQuiz . '%</th>';
		}

		if ( $blnEnableResource=='on' )
		{
			$strHeading .= '<th>' . $strOutput2 . '<br />' . $intRequiredResources .  '</th>';
		}
			
		if ( $blnEnableManual=='on' )
		{
			$strHeading .= '<th>' . get_string( 'manualgradepassinggrade', 'block_completion_report') . $intPercentManual . '%</th>';
		}
		
		$strHeading .= '<th>' . get_string( 'completionstatus', 'block_completion_report') . '</th></tr>';
		
		$strOutput .= $strHeading;
		
		
		$intUserCount = 0; 
		foreach ($objUsers as $objUser )
		{

			$context = get_context_instance(CONTEXT_COURSE, $courseid);
			$strRole = get_user_roles_in_context($objUser->id, $context);// HACKED: LockeVN: 			$strRole = get_user_roles_in_context($objUser->id, $context->id);
			
			if ( strpos($strRole, 'Student') > 0 )
			{

				$intUserCount++;
				$strParticipant = ''; 
				$strAssignment = ''; 
				$strQuiz = ''; 
				$strResource = ''; 
				$strManual = ''; 
				$strCompletion = ''; 
				$strOutputRow = '<tr>';
				$userid = $objUser->id;
		
				$strFirstName =  get_field  ('user', 'firstname', 'id', $userid);
				$strLastName =  get_field  ('user', 'lastname', 'id', $userid);
			
				$strParticipant .= '<td><a href="participant.php?id=' . $courseid . '&userid=' . $userid . '">' . $strFirstName . '</a>&nbsp;';
				$strParticipant .= '<a href="participant.php?id=' . $courseid . '&userid=' . $userid . '">' . $strLastName . '</a></td>';
				$strOutputRow .= $strParticipant;
		
				// check if user has record in completion_report table
				$strResult =  get_field  ('completion_report', 'userid', 'userid', $userid, 'courseid', $courseid );
				if ($strResult==null)
				{
			
					$strSQL = 'insert into ' . $CFG->prefix . 'completion_report ( ';
						
					$strSQL .= 'userid, userfirstname, userlastname, '; 
					$strSQL .= 'courseid, strCompletionStatus ';
				
					$strSQL .= ' ) values ( ';
					$strSQL .= "'" . $userid . "', '" . $strFirstName . "', '" . $strLastName  . "', "; 
					$strSQL .= "'" . $courseid . "', " . "'" . get_string( 'incomplete', 'block_completion_report') . "'"  . " ) ";
			
					//$strOutput .= $strSQL;
					$return = execute_sql  ( $strSQL, false );
				}
				
				// assignment section
				$blnEnableAssignment = get_field( 'completion_configure', 'blnEnableAssignment', 'intCourseID', $courseid);
				// check if assigment is required
				if ( $blnEnableAssignment == 'on' )
				{
					// required passing grade    
					$intPercentAssignment = get_field( 'completion_configure', 'intPercentAssignment', 'intCourseID', $courseid);
			
					// either FinalProject or AverageAllAssignments
					$strApplyAssignmentGrade = get_field( 'completion_configure', 'strApplyAssignmentGrade', 'intCourseID', $courseid);
			
					// final project id
					$intFinalProjectID = get_field( 'completion_configure', 'intFinalProjectID', 'intCourseID', $courseid);
			
					// need sum of attempted assignments and sum of assignment grades
					$strSQL = 'SELECT a.id FROM ' . $CFG->prefix . 'assignment a, ' . $CFG->prefix . 'assignment_submissions aa ';
					$strSQL .= 'WHERE a.course = ' . $courseid . ' AND aa.assignment = a.id AND aa.userid = ' . $userid . ';';
					$arrAssignments =  get_fieldset_sql ( $strSQL );
			
					$sumAssignmentGrade = 0;
					$sumAchievedAssignmentGrade = 0;
					$arrAssignmentGrades = array();
					$intGradeFinalProject = 0;
					
					if ( $arrAssignments )
					{
						foreach ( $arrAssignments as $intAssignmentID )
						{
							// final project grade
							if ($intAssignmentID==$intFinalProjectID )
							{
								$achievedFinalProjectGrade = get_field( 'assignment_submissions', 'grade', 'assignment', $intFinalProjectID, 'userid', $userid);
								$intGradeFinalProject = $achievedFinalProjectGrade / get_field( 'assignment', 'grade', 'id', $intAssignmentID) * 100;
							}
				
							$AchievedAssignmentGrade = get_field( 'assignment_submissions', 'grade', 'assignment', $intAssignmentID, 'userid', $userid);
							$sumAchievedAssignmentGrade += $AchievedAssignmentGrade;
							$AssignmentGrade = get_field( 'assignment', 'grade', 'id', $intAssignmentID);
							$arrAssignment = array('id'=>$intAssignmentID, 'grade'=>$AchievedAssignmentGrade);
							array_push ($arrAssignmentGrades, $arrAssignment);
							$sumAssignmentGrade += $AssignmentGrade;
						}
					}
						
					// saving grade to completion_report
					if ( $sumAssignmentGrade>0)
					{
						$intAverageAssingmentGrade = round( $sumAchievedAssignmentGrade / $sumAssignmentGrade * 100 );
					}
					else
					{
						$intAverageAssingmentGrade = 0;
					}
			
					// set passing criteria depending on $strApplyAssignmentGrade
					$achievedGrade = ($strApplyAssignmentGrade=='FinalProject'?$intGradeFinalProject:$intAverageAssingmentGrade);
					$achievedGrade = ($achievedGrade>0?$achievedGrade:get_string( 'notgraded', 'block_completion_report'));
			
					// output
					if ($achievedGrade>=$intPercentAssignment && $achievedGrade!=get_string( 'notgraded', 'block_completion_report'))
					{
						$strAssignmentsCompletionStatus = $strCompleted;
						$blnAssignmentsCompletionStatus = true;
					}
					else
					{
						$strAssignmentsCompletionStatus = $strIncomplete;
						$blnAssignmentsCompletionStatus = false;
					}
			
					$strAssignment .= '<table width="100%" border="1"><tr><th colspan="2" bgcolor="silver">' . $strAssignmentsCompletionStatus . '</th></tr>';
					$strAssignment .= '<tr>'; 
					$strAssignment .= '<td align="center">' . ' ' .$achievedGrade . '&nbsp;%</td></tr></table>';
					foreach ($arrAssignmentGrades as $index=>$objassignment)
					{
						$AssignmentPercentage = ($objassignment['grade']>0?(100 * $objassignment['grade'] / get_field( 'assignment', 'grade', 'id', $objassignment['id']). ' %'):'not graded');
					}
			
					$strOutputRow .= '<td valign="top">' . $strAssignment . '</td>';
				} // end if ($blnEnableAssignment=='on')
			
			
				// quiz configuration
				$blnEnableQuiz = get_field( 'completion_configure', 'blnEnableQuiz', 'intCourseID', $courseid);
				// check if assigment is required
				if ( $blnEnableQuiz == 'on' )
				{
					// required passing grade    
					$intPercentQuiz = get_field( 'completion_configure', 'intPercentQuiz', 'intCourseID', $courseid);
		
					// either FinalExam or AverageAllQuizzes
					$strApplyPassingGrade = get_field( 'completion_configure', 'strApplyPassingGrade', 'intCourseID', $courseid);
		
					$arrQuizGrades = array();
					if ($strApplyPassingGrade=='FinalExam')
					{
						// final Exam id
						$intFinalExamID = get_field( 'completion_configure', 'intFinalExamID', 'intCourseID', $courseid);
			
						// final Exam grade
						$achievedFinalExamGrade = get_field( 'quiz_attempts', 'sumgrades', 'quiz', $intFinalExamID, 'userid', $userid);
						$FinalExamGrade = get_field( 'quiz', 'sumgrades', 'id', $intFinalExamID);
						if ( $FinalExamGrade>0 )
						{
							$intGradeFinalExam = $achievedFinalExamGrade / $FinalExamGrade;
						}
						else
						{
							$intGradeFinalExam = 0;
						}
						$achievedGrade = $intGradeFinalExam * 100;
					}
					else
					{
						// need sum of attempted quizzes and sum of quiz grade
						$strSQL = 'SELECT q.id FROM ' . $CFG->prefix . 'quiz q, ' . $CFG->prefix . 'quiz_attempts qa ';
						$strSQL .= 'WHERE q.course = ' . $courseid . ' AND qa.quiz = q.id AND qa.userid = ' . $userid . ' ;';
						$arrQuizzes =  get_fieldset_sql ( $strSQL );
						$sumQuizGrade = 0;
						$sumAchievedQuizGrade = 0;
						if ( $arrQuizzes )
						{
							foreach ( $arrQuizzes as $intQuizID )
							{
								$AchievedQuizGrade = get_field( 'quiz_attempts', 'sumgrades', 'quiz', $intQuizID, 'userid', $userid);
								$sumAchievedQuizGrade += $AchievedQuizGrade;
								$arrQuiz = array('id'=>$intQuizID, 'grade'=>$AchievedQuizGrade);
								array_push ($arrQuizGrades, $arrQuiz);
								$sumQuizGrade += get_field( 'quiz', 'sumgrades', 'id', $intQuizID);
							}
						}
						else
						{
							$AchievedQuizGrade = 0;
							$sumAchievedQuizGrade = 0;
							$sumQuizGrade = 0;
						}
						if ( $sumAchievedQuizGrade > 0 )
						{
							$achievedGrade = round( $sumAchievedQuizGrade / $sumAchievedQuizGrade * 100 );
						}
						else
						{
							$achievedGrade = 0;
						}
					} // end of if ($strApplyPassingGrade=='FinalExam')
					
					// output
					if ($achievedGrade>=$intPercentQuiz)
					{
						$strQuizzesCompletionStatus = $strCompleted;
						$blnQuizzesCompletionStatus = true;
					}
					else
					{
						$strQuizzesCompletionStatus = $strIncomplete;
						$blnQuizzesCompletionStatus = false;
					}
		
					$strQuiz .= '<table width="100%" border="1"><tr><th colspan="2" bgcolor="silver">' . $strQuizzesCompletionStatus . '</th></tr>';
					$strQuiz .= '<tr>';
					$strQuiz .= '<td align="center">' . round($achievedGrade*100)/100 . '&nbsp;%</td></tr></table>';
					if ( $arrQuizGrades )
					{
						foreach ($arrQuizGrades as $index=>$objquiz)
						{
							$QuizPercentage = ($objquiz['grade']>0?(100 * $objquiz['grade'] / get_field( 'quiz', 'sumgrades', 'id', $objquiz['id']). ' %'): get_string( 'notgraded', 'block_completion_report'));
						}
					}
		
					$strOutputRow .= '<td valign="top">' . $strQuiz . '</td>';
				} // end if (  $blnEnableQuiz == 'on' )
		
				// check if Resource is required 
				$blnEnableResource =  get_field  ('completion_configure', 'blnEnableResource', 'intCourseID', $courseid);
				if ($blnEnableResource=='on')
				{
					$strOutputCurrentCompletedResources ='';
					// required resources    
					$strCurrentRequiredResources = get_field( 'completion_configure', 'strCurrentRequiredResources', 'intCourseID', $courseid);
					if ($strCurrentRequiredResources != null )
					{
						$strOtherAvailableResources = 'and id not in ( '. $strCurrentRequiredResources .') ';
						$arrRequiredResources =   get_records_select  ('resource', 'course = ' . $courseid . ' ' . $strAnother , 'id', 'name,id');
					}
					else
					{
						$strOtherAvailableResources = ' ';
						$arrRequiredResources =  array ();
					}
		
					// available
					$arrAvailableResources =   get_records_select  ('resource', 'course = ' . $courseid . ' ' . $strOtherAvailableResources , 'id', 'name,id');
		
					// completed
					$strSQL = 'SELECT distinct l.info ';  
					$strSQL .= 'FROM ' . $CFG->prefix . 'log l, ' . $CFG->prefix . 'resource r ';
					$strSQL .= 'WHERE userid = ' . $userid . " AND module='resource' and r.course= " . $courseid . " and r.id = l.info ";
					$strSQL .= " and r.id in ( " . $strCurrentRequiredResources . " ); ";
		
					$arrCurrentCompletedResources =  get_fieldset_sql ( $strSQL );
					if ( empty($arrCurrentCompletedResources) )
					{
						$strCurrentCompletedResources =  '';
					}
					else
					{
						$strCurrentCompletedResources =  implode(',',$arrCurrentCompletedResources );
					}
					if ( empty($arrRequiredResources) )
					{
						$countRequiredResources = 0;
					}
					else
					{
						$countRequiredResources = sizeof($arrRequiredResources);
					}
					if ( empty($arrAvailableResources) )
					{
						$countAvailableResources = 0;
					}
					else
					{
						$countAvailableResources = sizeof($arrAvailableResources);
					}
					if ( empty($arrCurrentCompletedResources) )
					{
						$countCompletedResources = 0;
					}
					else
					{
						$countCompletedResources = sizeof($arrCurrentCompletedResources);
					}
					$sizeSelect = ($countRequiredResources>$countAvailableResources?$countRequiredResources:$countAvailableResources) ;
		
					// output
					if ( $strCurrentCompletedResources != null )
					{
	
						$strSQL = 'SELECT name '; 
						$strSQL .= ' FROM ' . $CFG->prefix . 'resource r ';
						$strSQL .= " WHERE r.id in ( " . $strCurrentCompletedResources . " ); ";
						$arrOutputCurrentCompletedResources =  get_fieldset_sql ( $strSQL );
						$strOutputCurrentCompletedResources = '<ul>';
						
						if ( $arrOutputCurrentCompletedResources )
						{
							foreach ( $arrOutputCurrentCompletedResources as $strCompleteResource )
							{
							   $strOutputCurrentCompletedResources .= '<li>' . $strCompleteResource . '</li>';
							}
						}
						$strOutputCurrentCompletedResources .= '</ul>';
					}
					else
					{
						$strOutputCurrentCompletedResources = ' ';
						$arrOutputCurrentCompletedResources =  array ();
					}
	
					if ($strCurrentRequiredResources != null )
					{
						$strSQL = 'SELECT name '; 
						$strSQL .= ' FROM ' . $CFG->prefix . 'resource r ';
						$strSQL .= " WHERE r.id in ( " . $strCurrentRequiredResources . " ); ";
						$arrOutputCurrentRequiredResources =  get_fieldset_sql ( $strSQL );
						
						$strOutputCurrentRequiredResources = '<ul>';
						foreach ( $arrOutputCurrentRequiredResources as $strRequiredResource )
						{
						   $strOutputCurrentRequiredResources .= '<li>' . $strRequiredResource . '</li>';
						}
						$strOutputCurrentRequiredResources .= '</ul>';
	
					}
					else
					{
						$strOutputCurrentRequiredResources = ' ';
						$arrOutputCurrentRequiredResources =  array ();
					}
		
	
		
					if ($countRequiredResources==$countCompletedResources)
					{
						$strResourcesCompletionStatus = $strCompleted;
						$blnResourcesCompletionStatus = true;
					}
					else
					{
						$strResourcesCompletionStatus = $strIncomplete;
						$blnResourcesCompletionStatus = false;
					}
	
					// link to popup window to display Accessed Resources    
					$url = $CFG->wwwroot . "/blocks/completion_report/accessed_resources.php?course=" . $courseid . '&user=' . $userid;
					$popupname = 'accessedresourcepopup';
					$popupoptions = 'menubar=0,location=0,scrollbars,resizable,width=700,height=500,left=500,top=0';
					$strOutput3 = link_to_popup_window ($url, $popupname, get_string( 'numberofrequiredresourcesaccessed', 'block_completion_report'), 700, 700, 'Click here to see list of Accessed Resources', $popupoptions, true);
		
					$strResource .= '<table width="100%" border="1"><tr><th bgcolor="silver" colspan="2">' . $strResourcesCompletionStatus . '</th></tr>';
					$strResource .= '<tr><td align="center"><a href="">' . $strOutput3 . '</a>:<br />' . ($strCurrentCompletedResources!=''?count($arrOutputCurrentCompletedResources):0)  . '</td></tr></table>';
		
					$strOutputRow .= '<td valign="top">' . $strResource . '</td>';
				} // end if resource
		
				// manual grade configuration
				$blnEnableManual = get_field( 'completion_configure', 'blnEnableManual', 'intCourseID', $courseid);
		
				if ( $blnEnableManual == 'on' )
				{
					$intPercentManual = get_field( 'completion_configure', 'intPercentManual', 'intCourseID', $courseid);
					$achievedGrade = get_field( 'completion_report', 'intGradeManual', 'userid', $userid, 'courseid', $courseid);
					
					if ($achievedGrade>=$intPercentManual)
					{
						$strManualGradeCompletionStatus = $strCompleted;
						$blnManualGradeCompletionStatus = true;
					}
					else
					{
						$strManualGradeCompletionStatus = $strIncomplete;
						$blnManualGradeCompletionStatus = false;
					}
					$strManual .= '<table width="100%" border="1">';
					$strManual .= '<tr><th colspan="2" bgcolor="silver">' . $strManualGradeCompletionStatus . '</th></tr>';
					$strManual .= '<tr><td>' .  get_string( 'achievedgrade', 'block_completion_report') . '</td><td>' .$achievedGrade . '%</td></tr></table>';
					$strOutputRow .= '<td valign="top">' . $strManual . '</td>';
				} // end of if ( $blnEnableManual == 'on' )  
			
				// course completion status		
				$strCompletionStatus = get_field('completion_report', 'strCompletionStatus', 'courseid', $courseid, 'userid', $userid );
				$strCompletionStatus = strtolower($strCompletionStatus);
		
				if ( $strCompletionStatus != 'complete' )
				{
					if (
						($blnEnableManual  == 'on' &&  $blnManualGradeCompletionStatus == false) ||
						($blnEnableResource  == 'on' &&  $blnResourcesCompletionStatus == false) ||
						($blnEnableQuiz  == 'on' &&  $blnQuizzesCompletionStatus == false) ||
						($blnEnableAssignment == 'on' && $blnAssignmentsCompletionStatus == false) 
						)
					{
						$strCompletionStatus = 'Incomplete'; // HACKED: lockevn: $strCompletionStatus = $strIncomplete;
					} 
					else 
					{
						$strCompletionStatus = 'complete'; // HACKED: lockevn: $strCompletionStatus = $strCompleted;
					}
		
					$strSQL = 'update ' . $CFG->prefix . 'completion_report set ';
					$strSQL .= ' strCompletionStatus = "' . $strCompletionStatus . '" ';
					$strSQL .= ' where courseid = ' . $courseid . ' ';
					$strSQL .= ' and userid = ' . $userid . ' ';
					$return = execute_sql($strSQL, false);
		
					$strCompletion = '<table>';
					$strCompletion .= '<tr><td bgcolor="silver"><b>' . get_string( 'coursecompletion', 'block_completion_report') . ':</b></td><td bgcolor="silver"><b>' . $strCompletionStatus . '</b></td><tr>';
					if ($blnEnableAssignment=='on')
					{
						$strCompletion .= '<tr><td><b>' . get_string( 'assignments', 'block_completion_report') . ':</b></td><td>' . $strAssignmentsCompletionStatus  . '</td><tr>';
					}
					if ($blnEnableQuiz=='on')
					{
						$strCompletion .= '<tr><td><b>' . get_string( 'quizzes', 'block_completion_report') . ':</b></td><td>' . $strQuizzesCompletionStatus  . '</td><tr>';
					}
					if ($blnEnableResource=='on')
					{
						$strCompletion .= '<tr><td><b>' . get_string( 'resources', 'block_completion_report') . ':</b></td><td>' . $strResourcesCompletionStatus . '</td><tr>';
					}
					if ($blnEnableManual=='on')
					{
						$strCompletion .= '<tr><td><b>' . get_string( 'manualgrade', 'block_completion_report') . ':</b></td><td>' . $strManualGradeCompletionStatus  . '</td><tr>';
					}
		
					$strCompletion .= '</table>';
		
					$strOutputRow .= '<td valign="top">' . $strCompletion . '</td>';
		
				} // end of  if ( $strCompletionStatus != 'complete' )
			
				$strOutput .= $strOutputRow . '</tr>';
			} // end of if user is student in course

	
	   } // end of foreach ($objUsers as $objUser)    
	   echo $strOutput .'</table>';
	
	} // end of if ( $courseid == 0 )

}
else  // if not administrator, redirect to login page - likely someone accessing browser history after admin timeout or logout
{
 echo '<script>location.href="../../login/index.php"</script>';   
}

/*
 * end of completion report configuration section
 */

/// end of Print Section

	echo '</td>';

	// The right column
	if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing || has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
		echo '<td style="width: '.$preferred_width_right.'px;" id="right-column">';
		if (has_capability('moodle/course:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
			echo '<div align="center">'.update_course_icon($SITE->id).'</div>';
			echo '<br />';
		}
		blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
		echo '</td>';
	}

?>

  </tr>
</table>

<?php
	print_footer('home');     // Please do not modify this line
?>
