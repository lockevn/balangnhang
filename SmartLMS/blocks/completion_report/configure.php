
<?php  
/*
 * configure.php - part of block_completion_report
 *            - allows a course to be chosen from a dropdown menu of all available courses
 *            - configure rubrics using four different forms: assignment, quiz, resources, manual grade
 * created by Andrew Chow, of Lambda Solutions Inc., Vancouver, BC, Canada
 * http://www.lambdasolutions.net/ - andrew@lambdasolutions.net
 * based on block tutorial by Jon Papaioannou (pj@uom.gr) and the course front page from version 1.7
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
    
    // courseid - key parameter used in database access retrieved from dropdown menu listing of all courses
    // course id
    $courseid = optional_param('id', 0, PARAM_RAW); 
    
    if ( $courseid ==0 )
    {
        $courseid = optional_param('course', 0, PARAM_RAW);
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

<style>
/* style for percentage text boxes */
.percentage
{
    font-size: 12px;
    font-family: verdana;
    text-align: right;
}
/* style for message text displayed */
.message
{
    font-size: 12px;
    font-family: verdana;
    color: #000080;
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
/* 
    these styles go together with the JavaScript showHide function 
    to hide or show the FinalExam and FinalProject drop down menus 
*/
#lstQuizzes, #lstAssignments
{
  visibility:visible:
}
</style>
<script language="javascript" type="text/javascript">
function showHide(objSelect, strTarget)
{
    if ( objSelect.selectedIndex==0 ) {
        document.getElementById(strTarget).style.visibility = "visible";
    } else {
        document.getElementById(strTarget).style.visibility = "hidden";
    }
} // end of function showHide(objSelect, strTarget)
</script>
<table id="layout-table">
  <tr>
  <?php

    if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
        echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column">';


// Print Section

/* 
 * beginning of completion report configuration section
 */

/**
 * section to allow non-administrator and non-teachers to access demonstration course
 * which is specified in user defined string variables: $strServer, $strDemoCourse
 * which are defined in the associative array $arrServerDemo
 * MUST BE REMOVED in real production server
 */
/*
$arr_server_demo = array( 
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
    foreach ( $arr_server_demo as $str_server=>$str_demo_course)
    {
        if ( strpos($CFG->wwwroot, $str_server) > 0 )
        { 
            $courseid = get_field('course', 'id', 'fullname', $str_demo_course);
        }
    }
}
// end of section to allow guest access to demonstration course - remove up to this line
*/

if ( check_user_status( $courseid) )
{

    $strOutput = ''; // initialize output string, which is output at end of print section
    $scriptName = 'configure.php'; // this file name for use in form submission

    // initialize default variables-----------------------------------------
    // default assigment control variables
    $defaultBlnEnableAssignment = 'on'; // default checkbox state - on => assignment section required, off => assignment section omitted 
    $defaultIntPercentAssignment = '80';  // default textbox input - percentage threshold to meet completion requirement - tested for greater than or equal to
    $defaultStrApplyAssignmentGrade = 'FinalProject'; // default dropdown menu selection of two choices - FinalProject or AverageOfAllAssignments
    $defaultIntFinalProjectID = '0'; // default dropdown menu selection generated from database - listing all possible assignments as choice for FinalProject - not used if assignment grade is average of all assignments 
    
    // default quiz control variables
    $defaultBlnEnableQuiz = 'on'; // default checkbox state - on => quiz section required, off => quiz section omitted
    $defaultIntPercentQuiz = '80'; // default textbox input - percentage threshold to meet completion requirement - tested for greater than or equal to
    $defaultStrApplyPassingGrade = 'FinalExam'; // default dropdown menu selection of two choices - FinalExam or AverageOfAllQuizzes
    $defaultIntFinalExamID = '0'; // default dropdown menu selection generated from database - listing all possible quizzes as choice for FinalExam - not used if quiz grade is average of all quizzes
    
    // default resources control variables
    $defaultBlnEnableResource = 'on'; // default checkbox state - on => Resources section required, off => Resources section omitted
    $defaultStrCurrentRequiredResources = '';  // default list menu selection generated from database - listing all the resources as required
    
    // default manual grade control variables
    $defaultBlnEnableManual = 'on'; // default checkbox state - on => Manual Grade section required, off => Manual Grade section omitted
    $defaultIntPercentManual = '80'; // default textbox input - percentage threshold to meet completion requirement - tested for greater than or equal to

    // loading parameters from submitted forms

    // showing the configuration page

    // detects if submit button was pressed to display configuration form for the selected course 
    $strShowConfiguration = optional_param('showConfiguration', '', PARAM_RAW); 
    
    // submitted assignment section-----------------------------------------
    // reflects checkbox state - on => assignment section required, off => assignment section omitted
    $strEnableAssignment = optional_param('blnEnableAssignment', '', PARAM_RAW); 
    
    // from textbox input - percentage threshold to meet completion requirement - tested for greater than or equal to
    $intPercentAssignment = optional_param('intPercentAssignment', $defaultIntPercentAssignment, PARAM_INT); 
    
    // reflects dropdown menu selection of two choices - FinalProject or AverageOfAllAssignments
    $strApplyAssignmentGrade = optional_param('strApplyAssignmentGrade', $defaultStrApplyAssignmentGrade, PARAM_RAW); // 
    
    // reflects dropdown menu selection generated from database - listing all possible assignments as choice for FinalProject - not used if assignment grade is average of all assignments
    $assignmentFocus = optional_param('intFinalProjectID', 0, PARAM_INT); // 
    
    // detects if submit button pressed was for assignment section 
    $strSetAssignment = optional_param('setAssignment', '', PARAM_RAW); //
    //---------------------------------------------------------------------
    
    // submitted quiz section----------------------------------------------
    // reflects checkbox state - on => quiz section required, off => quiz section omitted
    $strEnableQuiz = optional_param('blnEnableQuiz', '', PARAM_RAW); // 
    
    // from textbox input - percentage threshold to meet completion requirement - tested for greater than or equal to
    $intPercentQuiz = optional_param('intPercentQuiz', $defaultIntPercentQuiz, PARAM_INT); // percentage required for completion:quiz, default = 80
    
    // reflects dropdown menu selection of two choices - FinalExam or AverageOfAllQuizzes
    $strApplyPassingGrade = optional_param('strApplyPassingGrade', $defaultStrApplyPassingGrade, PARAM_RAW); // 
    
    // reflects dropdown menu selection generated from database - listing all possible quizzes as choice for FinalExam - not used if quiz grade is average of all quizzes
    $quizFocus = optional_param('intFinalExamID', 0, PARAM_INT); // 
    
    // detects if submit button pressed was for quiz section 
    $strSetQuiz = optional_param('setQuiz', '', PARAM_RAW); //
    //----------------------------------------------------------------------
    
    // submitted resource section-------------------------------------------
    // reflects checkbox state - on => resources section required, off => resources section omitted
    $strEnableResource = optional_param('blnEnableResource', '', PARAM_RAW); // 
    
    // from list menu selection - listing all the resources as required
    $strCurrentRequiredResources = optional_param('strCurrentRequiredResources', '', PARAM_RAW );
    
    // from right-hand list menu selection - listing all the posssible resources to add as required
    $strAddingResources = optional_param('addingResources', '', PARAM_RAW );

    // from left-hand list menu selection - listing all the resources as currently required, but can be removed
    $strRemovingResources = optional_param('removingResources', '', PARAM_RAW );

    // submit buttom - tested to add items from $strAddingResources to $strCurrentRequiredResources
    $strSetAddItems = optional_param('setAddItems', '', PARAM_RAW); //

    // submit buttom - tested to remove items from $strRemovingResources from $strCurrentRequiredResources
    $strSetRemoveItems = optional_param('setRemoveItems', '', PARAM_RAW); //

    // detects if submit button pressed was for resources section - only updates the $strEnableResource variable since $strCurrentRequiredResources is updated by $strSetAddItems and $strSetRemoveItems
    $strSetResources = optional_param('setResources', '', PARAM_RAW); //
    //---------------------------------------------------------------------
    
    // submitted manual section--------------------------------------------
    // reflects checkbox state - on => resources section required, off => resources section omitted
    $strEnableManual = optional_param('blnEnableManual', '', PARAM_RAW); // 

    // from textbox input - percentage threshold to meet completion requirement - tested for greater than or equal to
    $intPercentManual = optional_param('intPercentManual', $defaultIntPercentManual, PARAM_INT); // percentage required for completion:quiz, default = 80

    // detects if submit button pressed was for manual grade section 
    $strSetManual = optional_param('setManual', '', PARAM_RAW); //
    //---------------------------------------------------------------------

    /*
     * end of control variable initialization section
     */
    
    /* 
     * creating form elements
     */
    
    // initialize arrays containing names of form elements to allow easy database access
    
    $arrAssignmentFieldNames = array(  'blnEnableAssignment', 'intPercentAssignment', 'strApplyAssignmentGrade', 'intFinalProjectID' );

    $arrQuizFieldNames = array( 'blnEnableQuiz', 'intPercentQuiz', 'strApplyPassingGrade', 'intFinalExamID' );

    $arrResourceFieldNames = array( 'blnEnableResource', 'strCurrentRequiredResources' );

    $arrManualFieldNames = array( 'blnEnableManual', 'intPercentManual' );
    
    $arrAssignmentChoices = array( 'FinalProject', 'AverageAllAssignments' ); 

    $arrQuizChoices = array( 'FinalExam', 'AverageAllQuizzes' );

    // create heading for the page containing the base category's full name - and display in H2 format    
    $strHeading =  get_field  ('course', 'fullname', 'category', '0');
    $strMessage = '<p class="test">' . get_string('savesetting', 'block_completion_report') . '</p>';
    $strOutput .= '<h2>' . $strHeading . '</h2>';
    
    /*
     *  saving submitted data to database before displaying anything on the page
     */ 
    
    // saving assignment section if submit button pressed
    if ( $strSetAssignment != '' )
    {

        $strSQL = 'update ' . $CFG->prefix . 'completion_configure set ';
        $strSQL .= ' blnEnableAssignment = '. ($strEnableAssignment==''?"'off'":"'on'" ) . ' ';
        $strSQL .= " , intPercentAssignment = '" . $intPercentAssignment . "' ";
        $strSQL .= ' , strApplyAssignmentGrade = "'. $strApplyAssignmentGrade . '" ';
        $strSQL .= ' , intFinalProjectID = '. $assignmentFocus . ' ';
        $strSQL .= ' where intCourseID = ' . $courseid . ' ';

        $return = execute_sql  ( $strSQL, false );
        if ( $return == true )
        {
            $strMessage .= '<p class="message">' . get_string('newassignmentsettingssaved', 'block_completion_report') . '.</p>';
        }
        else
        {
            $strMessage .= '<p class="message">' . get_string('assignmentsettingsnotsaved', 'block_completion_report') . '.</p>';
        }
    }
    // saving quiz section if submit button pressed
    else if ( $strSetQuiz != '' ) 
    {
        $strSQL = 'update ' . $CFG->prefix . 'completion_configure set ';
        $strSQL .= ' blnEnableQuiz = '. ($strEnableQuiz==''?"'off'":"'on'" ) . ' ';
        $strSQL .= " , intPercentQuiz = '" . $intPercentQuiz . "' ";
        $strSQL .= ' , strApplyPassingGrade = "'. $strApplyPassingGrade . '" ';
        $strSQL .= ' , intFinalExamID = '. $quizFocus . ' ';
        $strSQL .= ' where intCourseID = ' . $courseid . ' ';

        $return = execute_sql  ( $strSQL, false );
        if ( $return == true )
        {
            $strMessage .= '<p class="message">' . get_string('newquizsettingssaved', 'block_completion_report') . '.</p>';
        }
        else
        {
            $strMessage .= '<p class="message">' . get_string('quizsettingsnotsaved', 'block_completion_report') . '.</p>';
        }
    }
    // if resource section 'remove from requirements' submit button was pressed
    else if ( $strSetRemoveItems != '' )
    {
        if ($strRemovingResources!='') 
        {
            $arrResources = explode(',', $strCurrentRequiredResources);
            $arrNewList = array();
            foreach ($arrResources as $intResource )
            {
                if ( $intResource != $strRemovingResources )
                {
                    array_push ( $arrNewList, $intResource );
                }
            }
            $strCurrentRequiredResources = implode( ',', $arrNewList );
        }
        $strSQL = 'update ' . $CFG->prefix . 'completion_configure set ';
        $strSQL .= " strCurrentRequiredResources = '". $strCurrentRequiredResources . "' ";
        $strSQL .= ' where intCourseID = ' . $courseid . ' ';
        $return = execute_sql  ( $strSQL, false );

    }
    // if resource section 'add to requirements' submit button was pressed
    else if ( $strSetAddItems != '' )
    {
        if ($strCurrentRequiredResources=='')
        {
            $strCurrentRequiredResources = $strAddingResources;
        }
        else
        {        
            $strCurrentRequiredResources .= ',' . $strAddingResources;
        }
    
        $strSQL = 'update ' . $CFG->prefix . 'completion_configure set ';
        $strSQL .= " strCurrentRequiredResources = '". $strCurrentRequiredResources . "' ";
        $strSQL .= ' where intCourseID = ' . $courseid . ' ';
        $return = execute_sql  ( $strSQL, false );
    }
    // saving resource section if submit button pressed
    else if ( $strSetResources != '' )
    {
        $strSQL = 'update ' . $CFG->prefix . 'completion_configure set ';
        $strSQL .= ' blnEnableResource = '. ($strEnableResource==''?"'off'":"'on'" ) . ' ';
        $strSQL .= ' where intCourseID = ' . $courseid . ' ';
        $return = execute_sql  ( $strSQL, false );
        if ( $return == true )
        {
            $strMessage .= '<p class="message">' . get_string('newresourcessettingssaved', 'block_completion_report') . '.</p>';
        }
        else
        {
            $strMessage .= '<p class="message">' . get_string('resourcessettingsnotsaved', 'block_completion_report') . '.</p>';
        }
    }
    // saving manual grade section if submit button pressed
    else if ( $strSetManual != '' )
    {
        $strSQL = 'update ' . $CFG->prefix . 'completion_configure set ';
        $strSQL .= ' blnEnableManual = '. ($strEnableManual==''?"'off'":"'on'" ) . ' ';
        $strSQL .= " , intPercentManual = '" . $intPercentManual . "' ";
        $strSQL .= ' where intCourseID = ' . $courseid . ' ';
        $return = execute_sql  ( $strSQL, false );
        if ( $return == true )
        {
            $strMessage .= '<p class="message">' . get_string('newmanualgradesettingssaved', 'block_completion_report') . '.</p>';
        }
        else
        {
            $strMessage .= '<p class="message">' . get_string('manualsettingsnotsaved', 'block_completion_report') . '.</p>';
        }
    }
    
    /*
     * end of saving to database section 
     */ 
    
    // if no course was selected - i.e. page accessed for the first time
    if ( $courseid == 0 )
    {
    
       //  display all courses in drop down menu to allow choosing a course to configure requirement rubrics
        $strOutput .= '<form class="configurationForm" action="' . $scriptName . '" method="post"><table>'; 
        $objResult =  get_courses  ();
        $strOutput .= '<tr><td colspan="2">' . get_string( 'pleasechooseacourse', 'block_completion_report') . '</td></tr><tr><td><select style="width:250px;" name="course">'; 
        foreach ( $objResult as $objCourse )
        {
            $context = get_context_instance(CONTEXT_COURSE, $objCourse->id);
            $strRole = get_user_roles_in_context($USER->id, $context->id);
            $contextAdmin = get_context_instance(CONTEXT_SYSTEM);
            $strAdminRole = get_user_roles_in_context($USER->id, $contextAdmin->id);
//            $strMessage .= '<br />' . $objCourse->id . ': <pre>' . substr( $strRole, true ) . '</pre>';
            if ($objCourse->category !=0 && ( strpos($strRole, 'Teacher') > 0 || strpos( $strAdminRole, 'Administrator') > 0 )  )
            {
                $strOutput .= '<option class="complete" value="' . $objCourse->id . '">' . $objCourse->fullname . '</option>';
            }
        }
        $strOutput .= '</select></td>'; 
        $strOutput .= '<td colspan="2"><input type="submit" name="showConfiguration" value="' . get_string( 'configurecompletionrequirements', 'block_completion_report') . '" /></td></tr>'; 
        $strOutput .= '</table></form>'; 
//        echo $strMessage;
        echo $strOutput;
    
    }
    else // if a course was selected - display configuration rubrics in this order: assignment, quiz, resources, manual grade 
    {

        // display course heading
        $strResult =  get_field  ('course', 'fullname', 'id', $courseid);
        $strBackgroundColor = '#D3E0E7';  
        $strColor = 'black'; 
        $strLink = '<a href="report.php?course=' . $courseid . '">' .  get_string( 'displaycompletionreportforthiscourse', 'block_completion_report') . '</a>';
        $strOutput .= '<div style="text-align:right;padding:3px;font-size:smaller;"><b>' . $strLink . '</b></div>';
        $strOutput .= '<h3>' . get_string( 'configuringcoursecompletionrequirementrubrics', 'block_completion_report') . '</h3><h3><div style="background-color:' . $strBackgroundColor . ';color:' . $strColor . ';padding:3px;border:thin solid teal;font-size:larger;"><b>' . $strResult . '</b></div></h3>';
        $strOutput .= $strMessage;

        // check if course has record in completion table
        $strResult =  get_field  ('completion_configure', 'intCourseID', 'intCourseID', $courseid);

        // accessing course rubrics for the first time ever
        if ($strResult==null)
        {
            $strSQL = 'insert into ' . $CFG->prefix . 'completion_configure ( ';
            
            $strSQL .= implode ( ',', $arrAssignmentFieldNames ) . ',';
            $strSQL .= implode ( ',', $arrQuizFieldNames ) . ',';
            $strSQL .= implode ( ',', $arrResourceFieldNames ) . ',';
            $strSQL .= implode ( ',', $arrManualFieldNames ) . ',';
    
            $strSQL .= ' intCourseID ) values ( ';
            $strSQL .= "'" . $defaultBlnEnableAssignment . "', '" . $defaultIntPercentAssignment . "', '" . $defaultStrApplyAssignmentGrade . "', '" . $defaultIntFinalProjectID . "', '" ;
            $strSQL .= $defaultBlnEnableQuiz . "', '" . $defaultIntPercentQuiz . "', '" . $defaultStrApplyPassingGrade . "', '" . $defaultIntFinalExamID . "', '";
            $strSQL .= $defaultBlnEnableResource . "', '" . $defaultStrCurrentRequiredResources  . "', '";
            $strSQL .= $defaultBlnEnableManual . "', '" . $defaultIntPercentManual . "', '";
            $strSQL .= $courseid . "' ) ";
    
            $return = execute_sql  ( $strSQL, false );
        }

        // display assignment configuration form
        $strOutput .= '<form id="frmConfigureAssignment" class="configurationForm" action="' . $scriptName . '" method="post">';
        
        $strOutput .= '<input type="hidden" name="course" value="' . $courseid . '" />';
        foreach ( $arrAssignmentFieldNames as $strField )
        {
            $return = get_field( 'completion_configure', $strField, 'intCourseID', $courseid);
            switch ( $strField )
            {
                case 'blnEnableAssignment':
                    $strOutput .= get_string( 'assignmentcompletionisrequired', 'block_completion_report') . '<input type="checkbox" name="' .$strField . '" value="' .$strField . '" ' . strChecked($return) . ' />';
                    break;
                case 'intPercentAssignment':
                    $strOutput .= '<br />' . get_string( 'passinggradeforassignment', 'block_completion_report') . '<input class="percentage" size="3" maxlength="3" type="text" name="' .$strField . '" value="' . $return . '" />%';
                    break;
                case 'strApplyAssignmentGrade':
                    $strOutput .= '<br />' . get_string( 'gradeappliesto', 'block_completion_report') . '<select id="lstAssignmentGrade" onchange="showHide(this, ' . "'lstAssignments'" . ')" name="' . $strField . '">';
                    foreach ($arrAssignmentChoices as $strChoice )
                    {
                        $strChoice2 = ($strChoice=='FinalProject'? get_string( 'finalproject', 'block_completion_report') : get_string( 'averageofallassignments', 'block_completion_report') );
                        if ($return == $strChoice)
                        {
                            $strOutput .= '<option class="complete" selected="selected" value="' . $strChoice . '">' . $strChoice2 . '</option>';
                        } 
                        else
                        {
                            $strOutput .= '<option class="complete" value="' . $strChoice . '">' . $strChoice2 . '</option>';
                        } 
                    }
                    $strOutput .= '</select>';
                    break;
                case 'intFinalProjectID':
                    $strOutput .= '<div id="lstAssignments">' . get_string( 'choosefinalproject', 'block_completion_report') . '<select name="' . $strField . '">';
                    $arrAssignmentNames = get_records( 'assignment', 'course', $courseid, '', 'name,id' );
                    if ( $arrAssignmentNames )
                    {
                        foreach ($arrAssignmentNames as $objAssignment )
                        {
                            if ($return == $objAssignment->id)
                            {
                                $strOutput .= '<option class="complete" selected="selected" value="' . $objAssignment->id . '">' . $objAssignment->name . '</option>';
                            } 
                            else
                            {
                                $strOutput .= '<option class="complete" value="' . $objAssignment->id . '">' . $objAssignment->name . '</option>';
                            } 
                        }
                    }
                    else
                    {
                                $strOutput .= '<option class="complete" value=""></option>';
                    }
                    $strOutput .= '</select></div><script>objSelect=document.getElementById("lstAssignmentGrade");showHide(objSelect,"lstAssignments");</script>';
                    break;
            } // end of switch
        } // end of foreach
        $strOutput .= '<div style="text-align:right;"><input type="submit" name="setAssignment" value="' . get_string( 'saveassignmentsettings', 'block_completion_report') . '" /></div>';
        $strOutput .= '</form>';
    
    
        // display quiz configuration form
        $strOutput .= '<form id="frmConfigureQuiz" class="configurationForm" action="' . $scriptName . '" method="post">';
        $strOutput .= '<input type="hidden" name="course" value="' . $courseid . '" />';
    
        foreach ( $arrQuizFieldNames as $strField )
        {
            $return = get_field( 'completion_configure', $strField, 'intCourseID', $courseid);
            switch ( $strField )
            {
                case 'blnEnableQuiz':
                    $strOutput .= get_string( 'quizcompletionisrequired', 'block_completion_report') . '<input type="checkbox" name="' .$strField . '" value="' .$strField . '" ' . strChecked($return) . ' />';
                    break;
                case 'intPercentQuiz':
                    $strOutput .= '<br />' . get_string( 'passinggradeforquizzes', 'block_completion_report') . '<input class="percentage" size="3" maxlength="3" type="text" name="' .$strField . '" value="' . $return . '" />%';
                    break;
                case 'strApplyPassingGrade':
                    $strOutput .= '<br />' . get_string( 'gradeappliesto', 'block_completion_report') . '<select id="lstQuizGrade" onchange="showHide(this, ' . "'lstQuizzes'" . ')" name="' . $strField . '">';
                    foreach ($arrQuizChoices as $strChoice )
                    {
                        $strChoice2 = ($strChoice=='FinalExam'? get_string( 'finalexam', 'block_completion_report') : get_string( 'averageofallquizzes', 'block_completion_report') );
                        if ($return == $strChoice)
                        {
                            $strOutput .= '<option class="complete" selected="selected" value="' . $strChoice . '">' . $strChoice2 . '</option>'; 
                        } 
                        else 
                        {
                            $strOutput .= '<option class="complete" value="' . $strChoice . '">' . $strChoice2 . '</option>';
                        } 
                    } // end of foreach
                    $strOutput .= '</select>';
                    break;
                case 'intFinalExamID':
                    $strOutput .= '<div id="lstQuizzes">' . get_string( 'choosefinalexam', 'block_completion_report') . '<select name="' . $strField . '">';
                    $arrQuizNames = get_records( 'quiz', 'course', $courseid, '', 'name,id' );
                    if ( $arrQuizNames ) 
                    {
                        foreach ($arrQuizNames as $objQuiz )
                        {
                            if ($return == $objQuiz->id)
                            {
                                $strOutput .= '<option class="complete" selected="selected" value="' . $objQuiz->id . '">' . $objQuiz->name . '</option>';
                            } 
                            else
                            {
                                $strOutput .= '<option class="complete" value="' . $objQuiz->id . '">' . $objQuiz->name . '</option>';
                            } 
                        }
                    }
                    else
                    {
                        $strOutput .= '<option class="complete" value=""></option>';
                    }
                    $strOutput .= '</select></div><script>objSelect=document.getElementById("lstQuizGrade");showHide(objSelect,"lstQuizzes");</script>';
                    break;
            } // end of switch
        } // end of foreach
        $strOutput .= '<div style="text-align:right;"><input type="submit" name="setQuiz" value="' . get_string( 'savequizsettings', 'block_completion_report') . '" /></div>';
        $strOutput .= '</form>';
    
        // resource configuration
        $strOutput .= '<form class="configurationForm" action="' . $scriptName . '" method="post">';
        $strOutput .= '<input type="hidden" name="course" value="' . $courseid . '" />';

        foreach ( $arrResourceFieldNames as $strField )
        {
            $return = get_field( 'completion_configure', $strField, 'intCourseID', $courseid);
            $strOther = ($return == null ? ' ' : 'and id not in ( '. $return .') ' );
            $strAnother = ($return == null ? ' and id in (  ) ' : 'and id in ( '. $return .') ' );
            if (substr($strField, 0, 3)=='bln' )
            {
                $strOutput .= get_string( 'quizcompletionisrequired', 'block_completion_report') . '<input type="checkbox" name="' . $strField . '" value="' . $strField . '" ' . strChecked($return) . ' />';
            }
            else if (substr($strField, 0, 3)=='str' )
            {
                $strOutput .=  '<table>';
    
                // required
                $arrRequiredResources =   get_records_select  ('resource', 'course = ' . $courseid . ' ' . $strAnother , 'id', 'name,id');
    
                // available
                $arrAvailableResources =   get_records_select  ('resource', 'course = ' . $courseid . ' ' . $strOther , 'id', 'name,id');
                $countRequiredResources = sizeof($arrRequiredResources);
                
                $countAvailableResources = sizeof($arrAvailableResources);
    
                $sizeSelect = ($countRequiredResources>$countAvailableResources?$countRequiredResources:$countAvailableResources) ;
                $strOutput .= '<tr>';
                $strOutput .= '<th align="left">' . get_string( 'listofrequiredresources', 'block_completion_report') . '</th>';
                $strOutput .= '<th align="left">' . get_string( 'listofresourcesnotrequired', 'block_completion_report') . '</th>';
                $strOutput .= '</tr>';
    
                $strOutput .= '<tr><td align="right">';
                $strOutput .= '<input type="hidden" name="strCurrentRequiredResources" value="' . $return . '" />';
                $strOutput .= '</td>';
    
                $strOutput .= '<td></td></tr>';            

                // left box
                $strOutput .=  '<td valign="top"><select style="width:100%;" name="removingResources" size="'. $sizeSelect .'">';
                if ($arrRequiredResources)
                {
                	foreach ( $arrRequiredResources as $objField )
                	{
                    	$strOutput .=  '<option class="complete" value="' . $objField->id . '">' . $objField->name . '</option>';
                	}
                }
                $strOutput .=  '</select><input type="submit" name="setRemoveItems" value="' . get_string( 'removefromrequirements', 'block_completion_report') . '" /></td>';
                // right box
                $strOutput .=  '<td><select style="width:100%;" name="addingResources" size="'. $sizeSelect .'">';
                if ($arrAvailableResources)
                {
                	foreach ( $arrAvailableResources as $objField )
                	{
                    	$strOutput .=  '<option class="complete" value="' . $objField->id . '">' . $objField->name . '</option>';
                	}
                }
                $strOutput .=  '</select><input type="submit" name="setAddItems" value="' . get_string( 'addtorequirements', 'block_completion_report') . '" /></td>';
                $strOutput .=  '</tr></table>';
            } // end of if (substr($strField, 0, 3)=='str' )
        } // end of foreach
        $strOutput .= '<div style="text-align:right;"><input type="submit" name="setResources" value="' . get_string( 'saveresourcessettings', 'block_completion_report') . '" /></div>';
        $strOutput .= '</form>';
    
        // manual grade configuration
        $strOutput .= '<form class="configurationForm" action="' . $scriptName . '" method="post">';
        $strOutput .= '<input type="hidden" name="course" value="' . $courseid . '" />';
        foreach ( $arrManualFieldNames as $strField )
        {
            $return = get_field( 'completion_configure', $strField, 'intCourseID', $courseid);
            if (substr($strField, 0, 3)=='bln' )
                $strOutput .= get_string( 'manualgradeisapplied', 'block_completion_report') . '<input type="checkbox" name="' .$strField . '" value="' .$strField . '" ' . strChecked($return) . ' />';
            else if (substr($strField, 0, 3)=='int' )
                $strOutput .= '<br />' .  get_string( 'passinggrade', 'block_completion_report') . '<input class="percentage" size="3" maxlength="3" type="text" name="' .$strField . '" value="' . $return . '" />%';
        }
    
        $strOutput .= '<div style="text-align:right;"><input type="submit" name="setManual" value="' . get_string( 'savemanualgradesettings', 'block_completion_report') . '" /></div>';
        $strOutput .= '</form>';
    
        echo $strOutput;
    }
} 
else // if not administrator, redirect to login page - likely someone accessing browser history after admin timeout or logout
{
    echo '<script>location.href="../../login/index.php"</script>';   
}

/*
 * end of completion report configuration section
 */
    
/* 
 * end of Print Section - continue with original code to output the page
 */ 

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
