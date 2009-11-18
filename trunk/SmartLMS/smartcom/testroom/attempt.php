<?php  // $Id: attempt.php,v 1.131.2.15 2009/02/17 07:21:51 tjhunt Exp $
/**
 * This page prints a particular instance of quiz
 *
 * @author Martin Dougiamas and many others. This has recently been completely
 *         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
 *         the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

    require_once("../../config.php");
    require_once($CFG->dirroot.'/mod/quiz/locallib.php');
    require_once($CFG->dirroot.'/mod/quiz/quiz_attempt_pagelib.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once($CFG->dirroot.'/smartcom/util/uiutil.php');
    require_once($CFG->dirroot.'/smartcom/util/courseutil.php');

    // remember the current time as the time any responses were submitted
    // (so as to make sure students don't get penalized for slow processing on this page)
    $timestamp = time();

    // Get submitted parameters.
    $id = optional_param('id', 0, PARAM_INT);               // Course Module ID
    $q = optional_param('q', 0, PARAM_INT);                 // or quiz ID
    $page = optional_param('page', 0, PARAM_INT);
    $questionids = optional_param('questionids', '');
    $finishattempt = optional_param('finishattempt', 0, PARAM_BOOL);
    $timeup = optional_param('timeup', 0, PARAM_BOOL); // True if form was submitted by timer.
    $forcenew = optional_param('forcenew', false, PARAM_BOOL); // Teacher has requested new preview

    if ($id) {
        if (! $cm = get_coursemodule_from_id('quiz', $id)) {
            error("There is no coursemodule with id $id");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("The quiz with id $cm->instance corresponding to this coursemodule $id is missing");
        }
    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("There is no quiz with id $q");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("The course with id $quiz->course that the quiz with id $q belongs to is missing");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("The course module for the quiz with id $q is missing");
        }
    }
    
    /*check user đã hoàn thành quiz này trong session login hiện tại chưa, nếu rồi, gán finish=1*/
    if((isset($SESSION->completedQuizIdArr) 
    	&& !empty($SESSION->completedQuizIdArr) 
    	&& in_array($quiz->id, $SESSION->completedQuizIdArr))
    	|| $timeup) {
    	$finishattempt = 1;
    }
    
    /*đánh dấu page hiện tại của quiz đang làm*/
    if(!isset($SESSION->currentQuizPageArr) 
    	|| empty($SESSION->currentQuizPageArr)) {
    	$currentQuizPageArr = array();	     	    	
    }
    else {
    	$currentQuizPageArr = $SESSION->currentQuizPageArr;
    }
    
    $currentQuizPageArr[$quiz->id] = $page;
    $SESSION->currentQuizPageArr = $currentQuizPageArr;
    	       
    require_login($course->id, false, $cm);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $cm->course); // course context
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $ispreviewing = has_capability('mod/quiz:preview', $context);

    /// Get number for the next or unfinished attempt
    if(!$attemptnumber = (int)get_field_sql('SELECT MAX(attempt)+1 FROM ' .
            "{$CFG->prefix}quiz_attempts WHERE quiz = '{$quiz->id}' AND " .
            "userid = '{$USER->id}' AND timefinish > 0 AND preview != 1")) {
    	$attemptnumber = 1;
    }

    $strquizzes = get_string("modulenameplural", "quiz");
    $popup = $quiz->popup && !$ispreviewing; // Controls whether this is shown in a javascript-protected window.

    /// Check number of attempts
    $numberofpreviousattempts = count_records_select('quiz_attempts', "quiz = '{$quiz->id}' AND " .
        		"userid = '{$USER->id}' AND timefinish > 0 AND preview != 1");



    $attempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id);

    $newattempt = false;
    if (!$attempt || ((time() - $attempt->timestart) > $quiz->timelimit * 60 )) {
    	// Delete any previous preview attempts belonging to this user.
    	if ($oldattempts = get_records_select('quiz_attempts', "quiz = '$quiz->id'
    	AND userid = '$USER->id' AND preview = 1")) {
    		foreach ($oldattempts as $oldattempt) {
    			quiz_delete_attempt($oldattempt, $quiz);
    		}
    	}
    	$newattempt = true;
    	// Start a new attempt and initialize the question sessions
    	$attempt = quiz_create_attempt($quiz, $attemptnumber);
    	// If this is an attempt by a teacher mark it as a preview
    	if ($ispreviewing) {
    		$attempt->preview = 1;
    	}
    	// Save the attempt
    	if (!$attempt->id = insert_record('quiz_attempts', $attempt)) {
    		error('Could not create new attempt');
    	}
    	// make log entries
    	if ($ispreviewing) {
    		add_to_log($course->id, 'quiz', 'preview',
                           "attempt.php?id=$cm->id",
                           "$quiz->id", $cm->id);
    	} else {
    		add_to_log($course->id, 'quiz', 'attempt',
                           "review.php?attempt=$attempt->id",
                           "$quiz->id", $cm->id);
    	}
    }

    if (!$attempt->timestart) { // shouldn't really happen, just for robustness
    	debugging('timestart was not set for this attempt. That should be impossible.', DEBUG_DEVELOPER);
    	$attempt->timestart = $timestamp - 1;
    }

    /// Load all the questions and states needed by this script

    // list of questions needed by page
    $pagelist = quiz_questions_on_page($attempt->layout, $page);

    if ($newattempt) {
    	$questionlist = quiz_questions_in_quiz($attempt->layout);
    } else {
    	$questionlist = $pagelist;
    }

    // add all questions that are on the submitted form
    if ($questionids) {
    	$questionlist .= ','.$questionids;
    }

    if (!$questionlist) {
    	print_error('noquestionsfound', 'quiz', 'view.php?q='.$quiz->id);
    }

    $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
		           "  FROM {$CFG->prefix}question q,".
		           "       {$CFG->prefix}quiz_question_instances i".
		           " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
		           "   AND q.id IN ($questionlist)";

    // Load the questions
    if (!$questions = get_records_sql($sql)) {
    	print_error('noquestionsfound', 'quiz', 'view.php?q='.$quiz->id);
    }

    // Load the question type specific information
    if (!get_question_options($questions)) {
    	error('Could not load question options');
    }

    // If the new attempt is to be based on a previous attempt find its id
    $lastattemptid = false;
    if ($newattempt and $attempt->attempt > 1 and $quiz->attemptonlast and !$attempt->preview) {
    	// Find the previous attempt
    	if (!$lastattemptid = get_field('quiz_attempts', 'uniqueid', 'quiz', $attempt->quiz, 'userid', $attempt->userid, 'attempt', $attempt->attempt-1)) {
    		error('Could not find previous attempt to build on');
    	}
    }

    // Restore the question sessions to their most recent states
    // creating new sessions where required
    if (!$states = get_question_states($questions, $quiz, $attempt, $lastattemptid)) {
    	error('Could not restore question sessions');
    }

    // Save all the newly created states
    if ($newattempt) {
    	foreach ($questions as $i => $question) {
    		save_question_session($questions[$i], $states[$i]);
    	}
    }

    /// Process form data /////////////////////////////////////////////////

    if ($responses = data_submitted() and empty($responses->quizpassword)) {

    	// set the default event. This can be overruled by individual buttons.
    	$event = (array_key_exists('markall', $responses)) ? QUESTION_EVENTSUBMIT :
    	($finishattempt ? QUESTION_EVENTCLOSE : QUESTION_EVENTSAVE);

    	// Unset any variables we know are not responses
    	unset($responses->id);
    	unset($responses->q);
    	unset($responses->oldpage);
    	unset($responses->newpage);
    	unset($responses->review);
    	unset($responses->questionids);
    	unset($responses->saveattempt); // responses get saved anway
    	unset($responses->finishattempt); // same as $finishattempt
    	unset($responses->markall);
    	unset($responses->forcenewattempt);

    	// extract responses
    	// $actions is an array indexed by the questions ids
    	$actions = question_extract_responses($questions, $responses, $event);

    	// Process each question in turn

    	$questionidarray = explode(',', $questionids);
    	$success = true;
    	foreach($questionidarray as $i) {
    		if (!isset($actions[$i])) {
    			$actions[$i]->responses = array('' => '');
    			$actions[$i]->event = QUESTION_EVENTOPEN;
    		}
    		$actions[$i]->timestamp = $timestamp;
    		if (question_process_responses($questions[$i], $states[$i], $actions[$i], $quiz, $attempt)) {
    			save_question_session($questions[$i], $states[$i]);
    		} else {
    			$success = false;
    		}
    	}

    	if (!$success) {
    		$pagebit = '';
    		if ($page) {
    			$pagebit = '&amp;page=' . $page;
    		}
    		print_error('errorprocessingresponses', 'question',
    		$CFG->wwwroot . '/smartcom/testroom/attempt.php?q=' . $quiz->id . $pagebit);
    	}

    	$attempt->timemodified = $timestamp;

    	// We have now finished processing form data
    }

/// Finish attempt if requested
    if ($finishattempt) {

        // Set the attempt to be finished        
        $attempt->timefinish = $timestamp;

        // load all the questions
        $closequestionlist = quiz_questions_in_quiz($attempt->layout);
        $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
               "  FROM {$CFG->prefix}question q,".
               "       {$CFG->prefix}quiz_question_instances i".
               " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
               "   AND q.id IN ($closequestionlist)";
        if (!$closequestions = get_records_sql($sql)) {
            error('Questions missing');
        }

        // Load the question type specific information
        if (!get_question_options($closequestions)) {
            error('Could not load question options');
        }

        // Restore the question sessions
        if (!$closestates = get_question_states($closequestions, $quiz, $attempt)) {
            error('Could not restore question sessions');
        }

        $success = true;
        foreach($closequestions as $key => $question) {
            $action->event = QUESTION_EVENTCLOSE;
            $action->responses = $closestates[$key]->responses;
            $action->timestamp = $closestates[$key]->timestamp;
            
            if (question_process_responses($question, $closestates[$key], $action, $quiz, $attempt)) {
                save_question_session($question, $closestates[$key]);
            } else {
                $success = false;
            }
        }

        if (!$success) {
            $pagebit = '';
            if ($page) {
                $pagebit = '&amp;page=' . $page;
            }
            print_error('errorprocessingresponses', 'question',
                    $CFG->wwwroot . '/smartcom/testroom/attempt.php?q=' . $quiz->id . $pagebit);
        }

        add_to_log($course->id, 'quiz', 'close attempt',
                           "review.php?attempt=$attempt->id",
                           "$quiz->id", $cm->id);
        /*danhut: lưu attempt id vào session*/
        global $SESSION;

        if(!isset($SESSION->attemptIdArr) || empty($SESSION->attemptIdArr)) {
        	$attemptIdArr = array();
        } else {
        	$attemptIdArr = $SESSION->attemptIdArr;
        }
        if(!in_array($quiz->id, array_keys($attemptIdArr))) {
        	$attemptIdArr[$quiz->id] = $attempt->id;
        }
        $SESSION->attemptIdArr = $attemptIdArr;
        /*đánh dấu đã hoàn thành quiz này, để 0 thể quay về đc bằng 1 attempt khác trong cùng 1 session*/
        if(!isset($SESSION->completedQuizIdArr) || empty($SESSION->completedQuizIdArr)) {
        	$completedQuizIdArr = array();
        } else {
        	$completedQuizIdArr = $SESSION->completedQuizIdArr;
        }
        if(!in_array($quiz->id, $completedQuizIdArr)) {
        	$completedQuizIdArr[] = $quiz->id;
        }
        $SESSION->completedQuizIdArr = $completedQuizIdArr;
    	    	
    }
    

/// Update the quiz attempt and the overall grade for the quiz
    if ($responses || $finishattempt) {
        if (!update_record('quiz_attempts', $attempt)) {
            error('Failed to save the current quiz attempt!');
        }
        if (($attempt->attempt > 1 || $attempt->timefinish > 0) and !$attempt->preview) {
            quiz_save_best_grade($quiz);
        }
    }


    if ($finishattempt) {
        if (!empty($SESSION->passwordcheckedquizzes[$quiz->id])) {
            unset($SESSION->passwordcheckedquizzes[$quiz->id]);
        }
    
        $navlinks = navmenu($course, $cm, 'self', true);
        if(!empty($navlinks['nextLink'])) {
        	redirect($navlinks['nextLink']);
        } else {
        	/*nếu không có next resource -> quiz cuối trong bài test: redirect sang trang kết quả*/
        	redirect($CFG->wwwroot.'/smartcom/testroom/review.php');
        }
				
    }

// Now is the right time to check the open and close times.
    if (!$ispreviewing && ($timestamp < $quiz->timeopen || ($quiz->timeclose && $timestamp > $quiz->timeclose))) {
        print_error('notavailable', 'quiz', "view.php?id={$cm->id}");
    }

/// Print the quiz page ////////////////////////////////////////////////////////

 
    
    // Print the page header
    require_js($CFG->wwwroot . '/mod/quiz/quiz.js');
    $pagequestions = explode(',', $pagelist);
    $headtags = get_html_head_contributions($pagequestions, $questions, $states);

    $strupdatemodule = has_capability('moodle/course:manageactivities', $coursecontext)
    ? update_module_button($cm->id, $course->id, get_string('modulename', 'quiz'))
    : "";
    $navigation = build_navigation("", $cm);
    print_header_simple(format_string($quiz->name), "", $navigation, "", $headtags, true, $strupdatemodule);

    echo '
        <table align="center" cellpadding="0" cellspacing="0" style="width: 1024px !important;" border="0">
            <tr>
                <td valign="top" width="5px"><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BG1_L.jpg" /></td>
                <td valign="top">';
    /*danhut added*/
    echo '<table align="center" width="100%" id="layout-table" class="' . $quiz->lotype. ' smartlms-table-wrapper"><tr>';
    echo '<td id="middle-column">';
    
    print_container_start();
    /*danhut: print activity list của lesson nếu 0 phải test room*/
    if($course->format != 'testroom') {
    $activityArr = getLessonActivitiesFromLOId($COURSE->id, $cm->id, $quiz->lotype);
    if(!empty($activityArr)) {
        printSectionActivities($activityArr, $COURSE->id, $cm->id, $USER->id);
    }
    }
//    $menu = navmenu($course, $cm);
//    echo $menu;
    /*end of added*/
    
    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

    // Print the quiz name heading and tabs for teacher, etc.
//    if ($ispreviewing) {
//        $currenttab = 'preview';
//        include('tabs.php');
//
//        print_heading(get_string('previewquiz', 'quiz', format_string($quiz->name)));
//        unset($buttonoptions);
//        $buttonoptions['q'] = $quiz->id;
//        $buttonoptions['forcenew'] = true;
//        echo '<div class="controls">';
//        print_single_button($CFG->wwwroot.'/smartcom/testroom/attempt.php', $buttonoptions, get_string('startagain', 'quiz'));
//        echo '</div>';
//    /// Notices about restrictions that would affect students.
//        if ($quiz->popup) {
//            notify(get_string('popupnotice', 'quiz'));
//        }
//        if ($timestamp < $quiz->timeopen || ($quiz->timeclose && $timestamp > $quiz->timeclose)) {
//            notify(get_string('notavailabletostudents', 'quiz'));
//        }
//        if ($quiz->subnet && !address_in_subnet(getremoteaddr(), $quiz->subnet)) {
//            notify(get_string('subnetnotice', 'quiz'));
//        }
//    } else {
//        if ($quiz->attempts != 1 && $quiz->lotype != "test") {
//            print_heading(format_string($quiz->name).' - '.$strattemptnum);
//        } else {
            print_heading(format_string($quiz->name));
//        }
       
//    }
    
    // Start the form
    $quiz->thispageurl = $CFG->wwwroot . '/smartcom/testroom/attempt.php?q=' . s($quiz->id) . '&amp;page=' . s($page);
    
    $quiz->cmid = $cm->id;
    echo '<form id="responseform" method="post" action="', $quiz->thispageurl . '" enctype="multipart/form-data"' .
            ' onclick="this.autocomplete=\'off\'" onkeypress="return check_enter(event);" accept-charset="utf-8">', "\n";
    if($quiz->timelimit > 0) {
        // Make sure javascript is enabled for time limited quizzes
        ?>
        <script type="text/javascript">
            // Do nothing, but you have to have a script tag before a noscript tag.
        </script>
        <noscript>
        <div>
        <?php print_heading(get_string('noscript', 'quiz')); ?>
        </div>
        </noscript>
        <?php
    }
    echo '<div>';

/// Print the navigation panel if required
    $numpages = quiz_number_of_pages($attempt->layout);
    /*danhut modified: print page index / total page*/
//    if ($numpages > 1 && ($event == QUESTION_EVENTSUBMIT || $quiz->type == 'test')) {
        echo '<div class="paging pagingbar">';
        echo '<span class="title smartlms-title" >' . get_string('page') . ' ' . ($page + 1) . '/' . $numpages . '</span>';
        echo '</div>';
//    }
    // ECHO TIME
    // If the quiz has a time limit, or if we are close to the close time, include a floating timer.
    $showtimer = false;
    $timerstartvalue = 999999999999;
    if ($quiz->timeclose) {
        $timerstartvalue = min($timerstartvalue, $quiz->timeclose - time());
        $showtimer = $timerstartvalue < 60*60; // Show the timer if we are less than 60 mins from the deadline.
    }
    if ($quiz->timelimit > 0 && !has_capability('mod/quiz:ignoretimelimits', $context, NULL, false)) {
        $timerstartvalue = min($timerstartvalue, $attempt->timestart + $quiz->timelimit*60- time());
        $showtimer = true;
    }
    if ($showtimer && (!$ispreviewing || $timerstartvalue > 0)) {
        $timerstartvalue = max($timerstartvalue, 1); // Make sure it starts just above zero.
        require($CFG->dirroot.'/mod/quiz/jstimer.php');
    }
/// Print all the questions
    $number = quiz_first_questionnumber($attempt->layout, $pagelist);
    foreach ($pagequestions as $i) {
        $options = quiz_get_renderoptions($quiz->review, $states[$i]);
        // Print the question
        print_question($questions[$i], $states[$i], $number, $quiz, $options);
        save_question_session($questions[$i], $states[$i]);
        $number += $questions[$i]->length;
    }

/// Print the submit buttons

    $strconfirmattempt = addslashes(get_string("confirmclose", "quiz"));
    $onclick = "return confirm('$strconfirmattempt')";
    
    
        
    echo "<div class=\"submitbtns mdl-align $quiz->lotype\">\n";
    /*danhut added: print previous page link if required*/
    if ($page > 0) {
        // Print previous link
        $strprev = get_string('previouspage', 'quiz');        
         echo '&nbsp;<input type="button" src ="' . $CFG->pixpath.'/a/l_breadcrumb.gif" value="' . $strprev .'" class="quiz_page_previous cls_button" onclick="javascript:navigate(' . ($page - 1) . ');"/>&nbsp;';
        
    }     

    /*danhut added: only display submit All button at the last page of the quiz*/
    if($page == $numpages - 1) {
        echo "<input class=\"cls_button\" type=\"submit\" name=\"finishattempt\" value=\"".get_string("finishattempt", "quiz")."\" onclick=\"$onclick\" />\n";
    }
    
    /*danhut added: print next page link if required*/
    if ($page < $numpages - 1) {
        // Print next link
        $strnext = get_string('nextpage', 'quiz');
//        echo '&nbsp;<a class="quiz_page_next" href="javascript:navigate(' . ($page + 1) . ');" title="'
//         . $strnext . '"><img src= "' . $CFG->pixpath.'/a/r_breadcrumb.gif" alt="' . $strnext . '"/>   </a>&nbsp;';
         echo '&nbsp;<input type="button" src ="' . $CFG->pixpath.'/a/r_breadcrumb.gif" value="' . $strnext .'" class="quiz_page_next cls_button" onclick="javascript:navigate(' . ($page + 1) . ');"/>&nbsp;';
    }

    echo "</div>";
    

    // Print the navigation panel if required
//    if ($numpages > 1 && ($event == QUESTION_EVENTSUBMIT || $quiz->type == 'test')) {
//        quiz_print_navigation_panel($page, $numpages);
//    }
    
    

    // Finish the form
    echo '</div>';
    echo '<input type="hidden" name="timeup" id="timeup" value="0" />';

    // Add a hidden field with questionids. Do this at the end of the form, so 
    // if you navigate before the form has finished loading, it does not wipe all
    // the student's answers.
    echo '<input type="hidden" name="questionids" value="'.$pagelist."\" />\n";

    echo "</form>\n";
// ECHO TIME
    
    

    // Finish the page
    finish_page($course);
    echo '                                                                                
                </td>
                <td valign="top" width="5px"><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BG1_R.jpg" /></td>
            </tr>
            
        </table>
        ';

    
    if (empty($popup)) {
        print_footer($course);
    }
    
function finish_page($course, $pageblocks, $menu) {
    global $THEME;
    global $PAGE;
    global $CFG;
    print_container_end();
    echo $menu;
    echo '</td>';
    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 210);    
    echo '</tr></table>';    
}
?>
