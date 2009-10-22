
<?php  
/*
 * participant.php - part of block_completion_report
 *            - this page is displayed only when a link is click from the report.php page
 *            - the link is part of the name of a user displayed in the report
 *            - this page displays only the user's results and allows the manual grade to be changed
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
    if ( $courseid==0 )
    {
        $courseid = $COURSE->id;    
    }

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

<style >
.pageheading
{
}
.coursename
{
}
.listItem
{
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
.subForm
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
 * beginning of participant report section
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

    $scriptName = 'participant.php'; // this file name for use in form submission

/*
 *     $arrReportColumns = array (
    'id', 'userid', 'userfirstname', 'userlastname', 
    'intaverageQuiz', 'intGradeFinalExam', 'intaverageAssignment', 'intGradeFinalProject', 'intGradeManual', 
    'strCompletedResources', 'courseid', 'strCompletionStatus' );
    */

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

    $setManual = optional_param('setManual', '', PARAM_RAW); 
    $intGradeManual = optional_param('intGradeManual', 0, PARAM_RAW); 

    // form elements

    // create heading for the page containing the base category's full name - and display in H2 format    
    $strHeading =  get_field  ('course', 'fullname', 'category', '0');
    
    $strOutput0 = '<h2>' . $strHeading . '</h2>';

    // saving submitted data to database
    
    if ( $setManual != '' )
    {
        $strSQL = 'update ' . $CFG->prefix . 'completion_report set ';
        $strSQL .= " intGradeManual = '" . $intGradeManual . "' ";
        $strSQL .= ' where userid = ' . $userid . ' ';
        $return = execute_sql  ( $strSQL, false );
    } // end of if ( $setManual != '' )

    // if no user specified in the URL or form submission (i.e. if user access the page from browser history or typed in URL)
    if ( $userid == 0 )
    {
    
        // display all courses in drop down menu 
        $strOutput .= '<form class="subForm" action="' . $scriptName . '" method="post"><table>'; 
    
        $objResult =  get_courses  ();
        $strOutput .= '<tr><td>' . get_string( 'course', 'block_completion_report') . ':</td><td><select style="width:250px;" name="course">'; 
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
    
    
        // display all users in drop down menu 
    
        $objResult =  get_users_confirmed  ();
        $strOutput .= '<tr><td>' . get_string( 'participants', 'block_completion_report') . ':</td><td><select style="width:250px;" name="user">'; 
        foreach ( $objResult as $objUser )
        {
            //if ($objCourse->category !=0 )
            $strOutput .= '<option class="complete" value="' . $objUser->id . '">' . $objUser->firstname . ' '. $objUser->lastname . '</option>';
        }
        $strOutput .= '</select></td></tr>'; 
     
        $strOutput .= '<tr><td colspan="2"><input type="submit" name="showParticipant" value="' . get_string( 'showcompletionreportforthisuser', 'block_completion_report') . '" /></td></tr>'; 
    
        $strOutput .= '</table></form>'; 
    
        echo $strOutput;
    
    } // end of if ( $userid == 0 )
    else // if user specified in the URL or form submission
    {
        // displays link to configure.php page using the text string 'Configure Requirement Rubrics for this course'
        $strBackgroundColor = '#D3E0E7';
        $strColor = '#000000'; 
        $strLink1 = '<a title="' . get_string( 'configurerequirementrubricsforthiscourse', 'block_completion_report') . '" href="configure.php?course=' . $courseid . '">' . get_string( 'configurerequirementrubricsforthiscourse', 'block_completion_report') . '</a>';
        $strOutput0 .= '<h3><div style="text-align:right;font-size:smaller;"><b>' . $strLink1 . '</b></div></h3>';
       
        $strFirstName =  get_field  ('user', 'firstname', 'id', $userid);
        $strLastName =  get_field  ('user', 'lastname', 'id', $userid);
        $strOutput0 .= '<h3>' . get_string( 'completionreportfor', 'block_completion_report') . '&nbsp;';
        $strOutput0 .= $strFirstName . '&nbsp;' . $strLastName . '&nbsp;' . get_string( 'inthecourse', 'block_completion_report') . ',&nbsp;';
        
        // check if course has record in completion_configure table
        $strResult =  get_field  ('completion_configure', 'intCourseID', 'intCourseID', $courseid);
    
        if ($strResult!=null)
        {
            // displays link to report.php using the course name
            $strCourseName =  get_field  ('course', 'fullname', 'id', $courseid );
            $strLink2 = '<a title="' . get_string( 'completionreportfor', 'block_completion_report') . $strCourseName . '" href="report.php?course=' . $courseid . '">' . $strCourseName . '</a>';
            $strOutput0 .= '&nbsp;' . $strLink2 . '</h3>';

            // check if user has record in completion_report table
            $strResult =  get_field  ('completion_report', 'userid', 'userid', $userid, 'courseid', $courseid );
            if ($strResult==null)
            {
                $strSQL = 'insert into ' . $CFG->prefix . 'completion_report ( ';
                $strSQL .= 'userid, userfirstname, userlastname, '; 
                $strSQL .= 'courseid, strCompletionStatus ';
        
                $strSQL .= ' ) values ( ';
                $strSQL .= "'" . $userid . "', '" . $strFirstName . "', '" . $strLastName  . "', "; 
                $strSQL .= "'" . $courseid . "', " . "''"  . " ) ";
    
                $return = execute_sql  ( $strSQL, false );
            } // end of if ($strResult==null)- first time users
            //----------------------------------------------------------------------
        
            // assignment section---------------------------------------------------
            $blnEnableAssignment = get_field( 'completion_configure', 'blnEnableAssignment', 'intCourseID', $courseid);
            // check if assigment is required
            if ( $blnEnableAssignment == 'on' )
            {
                // retrieve required passing grade from database    
                $intPercentRequiredAssignment = round ( get_field( 'completion_configure', 'intPercentAssignment', 'intCourseID', $courseid), 1);
    
                // either FinalProject or AverageAllAssignments
                $strApplyAssignmentGrade = get_field( 'completion_configure', 'strApplyAssignmentGrade', 'intCourseID', $courseid);
    
                // final project id
                $intFinalProjectID = get_field( 'completion_configure', 'intFinalProjectID', 'intCourseID', $courseid);
                if ( $intFinalProjectID != 0 )
                {
                    $strFinalProjectName = get_field( 'assignment', 'name', 'id', $intFinalProjectID);

                    // get assignment grades of FinalProject
                    $strSQL = 'SELECT s.grade  FROM ' . $CFG->prefix . 'assignment_submissions s, ' . $CFG->prefix . 'assignment a ';
                    $strSQL .= 'WHERE a.course = ' . $courseid . ' AND s.assignment = a.id and a.id=' . $intFinalProjectID . ' AND s.userid= ' . $userid . '; ';
                    $intFinalProjectGrade =  round ( get_field_sql ( $strSQL, false ), 1 );
                }
                else
                {
                    $strFinalProjectName = '';
                    $intFinalProjectGrade = 0;
                }
    
                // get sum of attempted assignments and sum of assignment grades
                $strSQL = 'SELECT a.id FROM ' . $CFG->prefix . 'assignment a, ' . $CFG->prefix . 'assignment_submissions aa ';
                $strSQL .= 'WHERE a.course = ' . $courseid . ' AND aa.assignment = a.id AND aa.userid = ' . $userid . ';';
                $arrAssignments =  get_fieldset_sql ( $strSQL );
    
                $sumAssignmentGrade = 0;
                $sumAchievedAssignmentGrade = 0;
                $arrAssignmentGrades = array();

                if ( $arrAssignments )
                {
                    foreach ( $arrAssignments as $intAssignmentID )
                    {
                        // final project grade
                        if ($intAssignmentID==$intFinalProjectID )
                        {
                            $achievedFinalProjectGrade = round( get_field( 'assignment_submissions', 'grade', 'assignment', $intFinalProjectID, 'userid', $userid), 1 );
                            $intGradeFinalProject = $achievedFinalProjectGrade / get_field( 'assignment', 'grade', 'id', $intAssignmentID) * 100;
                        }
        
                        $AchievedAssignmentGrade = round( get_field( 'assignment_submissions', 'grade', 'assignment', $intAssignmentID, 'userid', $userid), 1 );
                        $sumAchievedAssignmentGrade += $AchievedAssignmentGrade;
                        $AssignmentGrade = round( get_field( 'assignment', 'grade', 'id', $intAssignmentID), 1 );
    
    
                        $arrAssignment = array('id'=>$intAssignmentID, 'grade'=>$AchievedAssignmentGrade);
                        array_push ($arrAssignmentGrades, $arrAssignment);
                        $sumAssignmentGrade += $AssignmentGrade;
                    }
                }
                else
                {
                    $intGradeFinalProject = -1;
                    $AchievedAssignmentGrade = -1;
                    $sumAssignmentGrade = -1;
                }
                
                // saving grade to completion_report
                if ( $sumAssignmentGrade )
                {
                    $intAverageAssingmentGrade = round( $sumAchievedAssignmentGrade / $sumAssignmentGrade * 100, 1 );
                }
                else
                {
                    $intAverageAssingmentGrade = 0;
                }
    
                // set passing criteria depending on $strApplyAssignmentGrade
                $achievedGrade = round ( ($strApplyAssignmentGrade=='FinalProject'?$intGradeFinalProject:$intAverageAssingmentGrade), 1 );
    
                // output
                $strAssignmentsCompletionStatus = ($achievedGrade>=$intPercentRequiredAssignment?'Completed':'Incomplete');
                $strOutput .= '<div class="subForm"><b>' . get_string( 'assignmentscompletionstatus', 'block_completion_report') . ': ' . $strAssignmentsCompletionStatus . '</b>';
    
                $strOutput .= '<br />' . get_string( 'passinggraderequired', 'block_completion_report') . ': ' . round( $intPercentRequiredAssignment, 1 ) . ' % ' . get_string('for', 'block_completion_report') . '&nbsp;' . ($strApplyAssignmentGrade=='FinalProject'?get_string('thefinalprojectnamed', 'block_completion_report'). '&nbsp;'.$strFinalProjectName:'Average of All Assignments');
    
                $strOutput .= '<br />' . get_string( 'averagegradesfromallassignments', 'block_completion_report') . ':&nbsp;';
                $strOutputSub = '<ol>';
                $intCount = 0; 
                $intAverageGrade = 0;
                foreach ($arrAssignmentGrades as $index=>$objassignment)
                {
                    $intCount++;
                    ++$index;
                    $AssignmentPercentage = ($objassignment['grade']>=0?(100 * $objassignment['grade'] / get_field( 'assignment', 'grade', 'id', $objassignment['id']). ' %'):get_string( 'notgraded', 'block_completion_report'));
                    $strOutputSub .= '<li> ' . '&nbsp;' . get_field( 'assignment', 'name', 'id', $objassignment['id']) .  ': ' . round( $AssignmentPercentage, 1 ) . '%</li>';
                    if ( $objassignment['id']== $intFinalProjectID )
                    {
                        $achievedGrade = round ( $AssignmentPercentage, 1 );
                    }
                    if ( $AssignmentPercentage>=0 )
                    {
                        $intAverageGrade += $AssignmentPercentage;
                    }
                }
                if ( $intCount!=0 )
                {
                    $strOutput .= round( $intAverageGrade / $intCount, 1 ) . '%' . $strOutputSub . '</ol>';
                }
                else
                {
                    $strOutput .= get_string( 'notgraded', 'block_completion_report') . '&nbsp;%' . $strOutputSub . '</ol>';
                }
                $strOutput .= $strApplyAssignmentGrade=='FinalProject'?get_string( 'finalproject', 'block_completion_report' ):get_string( 'averageofallassignments', 'block_completion_report' );
                $achievedGrade = ($achievedGrade>=0?round($achievedGrade, 1):get_string( 'notgraded', 'block_completion_report'));
                $strOutput .= '<br />' . get_string( 'currentgrade', 'block_completion_report') . ': ' . ' ' . $achievedGrade . ' %';
                $strOutput .= '</div>';
            } // end if ($blnEnableAssignment=='on')
            //----------------------------------------------------------------------
        
        
            // quiz configuration---------------------------------------------------
            $blnEnableQuiz = get_field( 'completion_configure', 'blnEnableQuiz', 'intCourseID', $courseid);
            // check if assigment is required
            if ( $blnEnableQuiz == 'on' )
            {
                // required passing grade    
                $intPercentQuiz = round ( get_field( 'completion_configure', 'intPercentQuiz', 'intCourseID', $courseid), 1 );
    
                // either FinalExam or AverageAllQuizzes
                $strApplyPassingGrade = get_field( 'completion_configure', 'strApplyPassingGrade', 'intCourseID', $courseid);
    
                // final Exam id
                $intFinalExamID = get_field( 'completion_configure', 'intFinalExamID', 'intCourseID', $courseid);
                $strFinalExamName = get_field( 'quiz', 'name', 'id', $intFinalExamID);
                    
                // get array of id of attempted quizzes 
                $strSQL = 'SELECT q.id FROM ' . $CFG->prefix . 'quiz q, ' . $CFG->prefix . 'quiz_attempts qa ';
                $strSQL .= 'WHERE q.course = ' . $courseid . ' AND qa.quiz = q.id AND qa.userid = ' . $userid . ' ;';
                $arrQuizzes =  get_fieldset_sql ( $strSQL );

                // get achieved final Exam grade
                $intAchievedFinalExamGrade = round( get_field( 'quiz_attempts', 'sumgrades', 'quiz', $intFinalExamID, 'userid', $userid), 1);
                
                // get maximum final Exam grade
                $intFinalExamMaxGrade = round( get_field( 'quiz', 'sumgrades', 'id', $intFinalExamID), 1 );

                $intFinalExamPercentage = round( $intFinalExamGrade/$intGradeFinalExam, 1 );

                $sumQuizGrade = 0;
                $sumAchievedQuizGrade = 0;
                $arrQuizGrades = array();
                
                if ( $arrQuizzes )
                {
                    foreach ( $arrQuizzes as $intQuizID )
                    {
                        /* final exam grade
                        if ($intQuizID==$intFinalExamID )
                        {
                            $intAchievedFinalExamGrade = get_field( 'quiz_attempts', 'sumgrades', 'quiz', $intFinalExamID, 'userid', $userid);
                            $intGradeFinalExam = round ( $intAchievedFinalExamGrade / get_field( 'quiz', 'sumgrades', 'id', $intQuizID) * 100, 1 );
                        }
                        */
                        
                        $AchievedQuizGrade = round ( get_field( 'quiz_attempts', 'sumgrades', 'quiz', $intQuizID, 'userid', $userid), 1 );
                        $sumAchievedQuizGrade += $AchievedQuizGrade;
                        
                        $arrQuiz = array('id'=>$intQuizID, 'grade'=>$AchievedQuizGrade);
                        array_push ($arrQuizGrades, $arrQuiz);
                        $sumQuizGrade += get_field( 'quiz', 'sumgrades', 'id', $intQuizID);
                    }
 
                    // saving grade to completion_report
                    if ( $sumQuizGrade > 0 )
                    {
                        $intAverageQuiz = round( $sumAchievedQuizGrade / $sumQuizGrade * 100, 1 );
                    }
                    else
                    {
                        $intAverageQuiz = 0;
                    }
        
                    // set passing criteria depending on $strApplyPassingGrade
                    $achievedGrade = round( ($strApplyPassingGrade=='FinalExam'?$intGradeFinalExam:$intAverageQuiz), 1);
                }
                else
                {
                    // saving grade to completion_report
                    $intAverageQuiz = -1;
        
                    // set passing criteria depending on $strApplyPassingGrade
                    $achievedGrade = -1;
                }

                
    
                // output
                $strQuizzesCompletionStatus = ($achievedGrade>=$intPercentQuiz?'Completed':'Incomplete');
                $strOutput .= '<div class="subForm"><b>' . get_string( 'quizzescompletionstatus', 'block_completion_report') . ': ' . $strQuizzesCompletionStatus . '</b>';
    
                $strOutput .= '<br />' . get_string( 'passinggraderequired', 'block_completion_report') . ': ' . round( $intPercentQuiz, 1 ) . ' % ' . get_string( 'for', 'block_completion_report') . ' ' . ($strApplyPassingGrade=='FinalExam'? get_string( 'finalexam', 'block_completion_report') . '&nbsp;'.$strFinalExamName:get_string( 'averageofallquizzes', 'block_completion_report'));
    
                $strOutput .= '<br />' . get_string( 'averagegradesfromallquizzes', 'block_completion_report') . ':&nbsp;';
                $strOutputSub = '<ol>';
                $intCount = 0; 
                $intAverageQuiz = 0;

                if ( $arrQuizGrades )
                {
                    foreach ($arrQuizGrades as $index=>$objquiz)
                    {
                        $intCount++;
                        $QuizPercentage = ($objquiz['grade']>=0?(100 * $objquiz['grade'] / get_field( 'quiz', 'sumgrades', 'id', $objquiz['id']). ' %'):'not graded');
                        $strOutputSub .= '<li>' . '&nbsp;' . get_field( 'quiz', 'name', 'id', $objquiz['id']) .  ': ' . round( $QuizPercentage, 1 ) . '%</li>' ;
                        if ( $objquiz['id']== $intFinalExamID )
                        {
                            $achievedGrade = $QuizPercentage;
                        }
                        $intAverageQuiz += $QuizPercentage;
                    }
                }
                else
                {
                    $QuizPercentage = -1;
                    $achievedGrade = -1;
                    $intAverageQuiz = -1;
                }
                if ( $intCount > 0 )
                {
                    $intAverageQuiz = $intAverageQuiz / $intCount;
                    $strOutput .= round( $intAverageQuiz, 1 ) . '%' . $strOutputSub . '</ol>';
                }
                else
                {
                    $strOutput .= get_string( 'notgraded', 'block_completion_report' ) . $strOutputSub . '</ol>';
                }
                $strOutput .= $strApplyPassingGrade=='FinalExam'?get_string( 'finalexam', 'block_completion_report' ):get_string( 'averageofallquizzes', 'block_completion_report' );
                $strOutput .= '<br />' . get_string( 'currentgrade', 'block_completion_report') . ': ' . round( ($strApplyPassingGrade=='FinalExam'?$intGradeFinalExam:$intAverageQuiz), 1) . ' %';
    
                $strOutput .= '</div>';
            } // end if (  $blnEnableQuiz == 'on' )
            //----------------------------------------------------------------------
    
            // check if Resource is required----------------------------------------
            $blnEnableResource =  get_field  ('completion_configure', 'blnEnableResource', 'intCourseID', $courseid);
            if ($blnEnableResource=='on')
            {
                $strOutputCurrentCompletedResources ='';
                // required resources    
                $strCurrentRequiredResources = get_field( 'completion_configure', 'strCurrentRequiredResources', 'intCourseID', $courseid);
                $strOther = ($strCurrentRequiredResources == null ? ' ' : 'and id not in ( '. $strCurrentRequiredResources .') ' );
                $arrRequiredResources =   get_records_select  ('resource', 'course = ' . $courseid . '  and id in (' . $strCurrentRequiredResources . ')' , 'id', 'name,id');
    
                // available
                $arrAvailableResources =   get_records_select  ('resource', 'course = ' . $courseid . ' ' . $strOther , 'id', 'name,id');
    
                // completed
                $strSQL = 'SELECT distinct r.id '; //, l.course, module, info, action, url 
                $strSQL .= 'FROM ' . $CFG->prefix . 'log l, ' . $CFG->prefix . 'resource r ';
                $strSQL .= 'WHERE userid = ' . $userid . " AND module='resource' and r.course= " . $courseid . " and r.id = l.info ";
                $strSQL .= " and r.id in ( " . $strCurrentRequiredResources . " ); ";
    
                $arrCurrentCompletedResources =  get_fieldset_sql ( $strSQL );
                if ( $arrCurrentCompletedResources )
                {
                    $strCurrentCompletedResources =  implode(',',$arrCurrentCompletedResources );
                }
                else
                {
                    $strCurrentCompletedResources = '';
                }
    
                $countRequiredResources = sizeof($arrRequiredResources);
                $countAvailableResources = sizeof($arrAvailableResources);
                $countCompletedResources = sizeof($arrCurrentCompletedResources);
    
                $sizeSelect = ($countRequiredResources>$countAvailableResources?$countRequiredResources:$countAvailableResources) ;
                
                $strOther = ($strCurrentCompletedResources==''?' ':(" WHERE r.id in ( " . $strCurrentCompletedResources . " ); ") );
    
                // output
                $strSQL = 'SELECT name '; //, l.course, module, info, action, url 
                $strSQL .= ' FROM ' . $CFG->prefix . 'resource r ';
                $strSQL .= $strOther;
                $arrOutputCurrentCompletedResources =  get_fieldset_sql ( $strSQL );
                if ( $arrOutputCurrentCompletedResources )
                {
                    $strOutputCurrentCompletedResources = '<ul>';
                    foreach ( $arrOutputCurrentCompletedResources as $strCompleteResource )
                    {
                       $strOutputCurrentCompletedResources .= '<li>' . $strCompleteResource . '</li>';
                    }
                    $strOutputCurrentCompletedResources .= '</ul>';
                }
    
                $strSQL = 'SELECT name '; //, l.course, module, info, action, url 
                $strSQL .= ' FROM ' . $CFG->prefix . 'resource r ';
                $strSQL .= " WHERE r.id in ( " . $strCurrentRequiredResources . " ); ";
                $arrOutputCurrentRequiredResources =  get_fieldset_sql ( $strSQL );
                $strOutputCurrentRequiredResources = '<ul>';
                foreach ( $arrOutputCurrentRequiredResources as $strCurrentResource )
                {
                   $strOutputCurrentRequiredResources .= '<li>' . $strCurrentResource . '</li>';
                }
                $strOutputCurrentRequiredResources .= '</ul>';
    
                $strResourcesCompletionStatus = ($countRequiredResources==$countCompletedResources?'Completed':'Incomplete');
    
                $strOutput .= '<div class="subForm"><b>' . get_string( 'resourcescompletionstatus', 'block_completion_report') . ': ' . $strResourcesCompletionStatus . '</b>';
    
                $strOutput .= '<br /><i>' . get_string( 'resourcesrequired', 'block_completion_report') . ':</i> ' . ($strCurrentRequiredResources!=''?$strOutputCurrentRequiredResources:'none required') ;
    
                $strOutput .= '<br /><i>' . get_string( 'resourcescompleted', 'block_completion_report') . ':</i> ' . ($strCurrentCompletedResources!=''?$strOutputCurrentCompletedResources:'none completed') ;
    
                $strOutput .= '</div>';
    
            } // end if ($blnEnableResource=='on')
            //----------------------------------------------------------------------
    
            // manual grade configuration-------------------------------------------
            $blnEnableManual = get_field( 'completion_configure', 'blnEnableManual', 'intCourseID', $courseid);
    
            if ( $blnEnableManual == 'on' )
            {
                $strOutput .= '<form  class="subForm" action="' . $scriptName . '" method="post">';
                $strOutput .= '<input type="hidden" name="course" value="' . $courseid . '" />';
                $strOutput .= '<input type="hidden" name="user" value="' . $userid . '" />';
    
                $intPercentManual = get_field( 'completion_configure', 'intPercentManual', 'intCourseID', $courseid);
                $achievedGrade = get_field( 'completion_report', 'intGradeManual', 'userid', $userid, 'courseid', $courseid);
                
                $strManualGradeCompletionStatus = ($achievedGrade>=$intPercentManual?'Completed':'Incomplete');
                $strOutput .= '<b>' . get_string( 'manualgradecompletionstatus', 'block_completion_report') . ': ' . $strManualGradeCompletionStatus . '</b>';
    
                $strOutput .= '<br />' . get_string( 'passinggraderequired', 'block_completion_report') . ': ' . $intPercentManual . ' %';
    
                $strOutput .= '<br />' . get_string( 'currentgrade', 'block_completion_report') . ': <input type="text" size="3" maxlength="3" name="intGradeManual" value="' .$achievedGrade . '" />%';
            
                $strOutput .= '&nbsp;&nbsp;<input type="submit" name="setManual" value="' . get_string( 'changecurrentgrade', 'block_completion_report') . '" />';
                $strOutput .= '</form>';
            } // end of if ( $blnEnableManual == 'on' )  
            //----------------------------------------------------------------------
        
            // course completion status---------------------------------------------
            $strCompletionStatus = get_field( 'completion_report', 'strCompletionStatus', 'courseid', $courseid, 'userid', $userid );
    
            if (
                ($blnEnableManual  == 'on' &&  $strManualGradeCompletionStatus == get_string( 'incomplete', 'block_completion_report')) ||
                ($blnEnableResource  == 'on' &&  $strResourcesCompletionStatus == get_string( 'incomplete', 'block_completion_report')) ||
                ($blnEnableQuiz  == 'on' &&  $strQuizzesCompletionStatus == get_string( 'incomplete', 'block_completion_report')) ||
                ($blnEnableAssignment == 'on' && $strAssignmentsCompletionStatus == get_string( 'incomplete', 'block_completion_report')) 
                )
                {
                    $strCompletionStatus = get_string( 'incomplete', 'block_completion_report');
                } 
                else 
                {
                    $strCompletionStatus = get_string( 'completed', 'block_completion_report');
                }
            // check $status for Assingment Quiz Resource Manual and set new CompletionStatus

            $strOutput2 = '<div class="subForm" style="padding:8px;margin:0px;"><b style="font-size:larger;">' . get_string( 'coursecompletionstatussummary', 'block_completion_report') . ': ' . $strCompletionStatus . '</b>';
            if ($blnEnableAssignment=='on')
            {
                $strOutput2 .= '<br />' . get_string( 'assignmentscompletionstatus', 'block_completion_report') . ': ' . ($strAssignmentsCompletionStatus==get_string( 'incomplete', 'block_completion_report')?$strIncomplete :$strCompleted);
            }
            if ($blnEnableQuiz=='on')
            {
                $strOutput2 .= '<br />' . get_string( 'quizzescompletionstatus', 'block_completion_report') . ': ' . ($strQuizzesCompletionStatus==get_string( 'incomplete', 'block_completion_report')?$strIncomplete :$strCompleted);
            }
            if ($blnEnableResource=='on')
            {
                $strOutput2 .= '<br />' . get_string( 'resourcescompletionstatus', 'block_completion_report') . ': ' . ($strResourcesCompletionStatus==get_string( 'incomplete', 'block_completion_report')?$strIncomplete :$strCompleted);
            }
            if ($blnEnableManual=='on')
            {
                $strOutput2 .= '<br />' . get_string( 'manualgradecompletionstatus', 'block_completion_report') . ': ' . ($strManualGradeCompletionStatus==get_string( 'incomplete', 'block_completion_report')?$strIncomplete :$strCompleted);
            }

            $strSQL = 'update ' . $CFG->prefix . 'completion_report set ';
            $strSQL .= " strCompletionStatus = '" . get_string( 'completed', 'block_completion_report') . "' ";
            $strSQL .= ' where courseid = ' . $courseid . ' ';
            $strSQL .= ' and userid = ' . $userid . ' ';
            $return = execute_sql  ( $strSQL, false );

            $strOutput2 .= '</div>' . $strOutput;
            //----------------------------------------------------------------------

            // output         
            echo $strOutput0 . $strOutput2;

        } // end of if ($strResult!=null) - course exists
    } // end of if () - user exists
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
