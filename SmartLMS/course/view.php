<?php // $Id: view.php,v 1.106.2.6 2009/02/12 02:29:34 jerome Exp $
require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");



//  Display the course home page.

	require_once('../config.php');
	require_once('lib.php');
	require_once($CFG->libdir.'/blocklib.php');
	require_once($CFG->libdir.'/ajax/ajaxlib.php');
	require_once($CFG->dirroot.'/mod/forum/lib.php');

	$id          = optional_param('id', 0, PARAM_INT);
	$name        = optional_param('name', '', PARAM_RAW);
	$edit        = optional_param('edit', -1, PARAM_BOOL);
	$hide        = optional_param('hide', 0, PARAM_INT);
	$show        = optional_param('show', 0, PARAM_INT);
	$idnumber    = optional_param('idnumber', '', PARAM_RAW);
	$section     = optional_param('section', 0, PARAM_INT);
	$move        = optional_param('move', 0, PARAM_INT);
	$marker      = optional_param('marker',-1 , PARAM_INT);
	$switchrole  = optional_param('switchrole',-1, PARAM_INT);



	if (empty($id) && empty($name) && empty($idnumber)) {
		error("Must specify course id, short name or idnumber");
	}

	if (!empty($name)) {
		if (! ($course = get_record('course', 'shortname', $name)) ) {
			error('Invalid short course name');
		}
	} else if (!empty($idnumber)) {
		if (! ($course = get_record('course', 'idnumber', $idnumber)) ) {
			error('Invalid course idnumber');
		}
	} else {
		if (! ($course = get_record('course', 'id', $id)) ) {
			error('Invalid course id');
		}
	}

	preload_course_contexts($course->id);
	if (!$context = get_context_instance(CONTEXT_COURSE, $course->id)) {
		print_error('nocontext');
	}

	// Remove any switched roles before checking login
	if ($switchrole == 0 && confirm_sesskey()) {
		role_switch($switchrole, $context);
	}

	require_login($course);
	
	

	//print_string('configcontent', 'block_news_items');
	
	
	// Switchrole - sanity check in cost-order...
	$reset_user_allowed_editing = false;
	if ($switchrole > 0 && confirm_sesskey() &&
		has_capability('moodle/role:switchroles', $context)) {
		// is this role assignable in this context?
		// inquiring minds want to know...
		$aroles = get_assignable_roles_for_switchrole($context);
		if (is_array($aroles) && isset($aroles[$switchrole])) {
			role_switch($switchrole, $context);
			// Double check that this role is allowed here
			require_login($course->id);
		}
		// reset course page state - this prevents some weird problems ;-)
		$USER->activitycopy = false;
		$USER->activitycopycourse = NULL;
		unset($USER->activitycopyname);
		unset($SESSION->modform);
		$USER->editing = 0;
		$reset_user_allowed_editing = true;
	}

	//If course is hosted on an external server, redirect to corresponding
	//url with appropriate authentication attached as parameter 
	if (file_exists($CFG->dirroot .'/course/externservercourse.php')) {
		include $CFG->dirroot .'/course/externservercourse.php';
		if (function_exists('extern_server_course')) {
			if ($extern_url = extern_server_course($course)) {
				redirect($extern_url);
			}
		}
	}


	require_once($CFG->dirroot.'/calendar/lib.php');    /// This is after login because it needs $USER
    
    
    

	add_to_log($course->id, 'course', 'view', "view.php?id=$course->id", "$course->id");

	$course->format = clean_param($course->format, PARAM_ALPHA);
	if (!file_exists($CFG->dirroot.'/course/format/'.$course->format.'/format.php')) {
		$course->format = 'weeks';  // Default format is weeks
	}

	$PAGE = page_create_object(PAGE_COURSE_VIEW, $course->id);
	$pageblocks = blocks_setup($PAGE, BLOCKS_PINNED_BOTH);

	if ($reset_user_allowed_editing) {
		// ugly hack
		unset($PAGE->_user_allowed_editing);
	}

	if (!isset($USER->editing)) {
		$USER->editing = 0;
	}
	if ($PAGE->user_allowed_editing()) {
		if (($edit == 1) and confirm_sesskey()) {
			$USER->editing = 1;
		} else if (($edit == 0) and confirm_sesskey()) {
			$USER->editing = 0;
			if(!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
				$USER->activitycopy       = false;
				$USER->activitycopycourse = NULL;
			}
		}

		if ($hide && confirm_sesskey()) {
			set_section_visible($course->id, $hide, '0');
		}

		if ($show && confirm_sesskey()) {
			set_section_visible($course->id, $show, '1');
		}

		if (!empty($section)) {
			if (!empty($move) and confirm_sesskey()) {
				if (!move_section($course, $section, $move)) {
					notify('An error occurred while moving a section');
				}
			}
		}
	} else {
		$USER->editing = 0;
	}

	$SESSION->fromdiscussion = $CFG->wwwroot .'/course/view.php?id='. $course->id;


	if ($course->id == SITEID) {
		// This course is not a real course.
		redirect($CFG->wwwroot .'/');
	}


	// AJAX-capable course format?
	$useajax = false; 
	$ajaxformatfile = $CFG->dirroot.'/course/format/'.$course->format.'/ajax.php';
	$bodytags = '';

	if (empty($CFG->disablecourseajax) and file_exists($ajaxformatfile)) {      // Needs to exist otherwise no AJAX by default

		// TODO: stop abusing CFG global here
		$CFG->ajaxcapable = false;           // May be overridden later by ajaxformatfile
		$CFG->ajaxtestedbrowsers = array();  // May be overridden later by ajaxformatfile

		require_once($ajaxformatfile);

		if (!empty($USER->editing) && $CFG->ajaxcapable && has_capability('moodle/course:manageactivities', $context)) {
															 // Course-based switches

			if (ajaxenabled($CFG->ajaxtestedbrowsers)) {     // Browser, user and site-based switches
				
				require_js(array('yui_yahoo',
								 'yui_dom',
								 'yui_event',
								 'yui_dragdrop',
								 'yui_connection',
								 'ajaxcourse_blocks',
								 'ajaxcourse_sections'));
				
				if (debugging('', DEBUG_DEVELOPER)) {
					require_js(array('yui_logger'));

					$bodytags = 'onload = "javascript:
					show_logger = function() {
						var logreader = new YAHOO.widget.LogReader();
						logreader.newestOnTop = false;
						logreader.setTitle(\'Moodle Debug: YUI Log Console\');
					};
					show_logger();
					"';
				}

				// Okay, global variable alert. VERY UGLY. We need to create
				// this object here before the <blockname>_print_block()
				// function is called, since that function needs to set some
				// stuff in the javascriptportal object.
				$COURSE->javascriptportal = new jsportal();
				$useajax = true;
			}
		}
	}

	$CFG->blocksdrag = $useajax;   // this will add a new class to the header so we can style differently
	
	/**
    * commented by muinx
    * display ajax content
    */
    
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_REQUEST['task'] == 'ajax')
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $courseId = $_REQUEST['id'];
            $sectionId = $_REQUEST['sectionid'];
            
            echo showCourseContentDetail($courseId, $sectionId);
            
        }
        
        exit();
    }
    
    /**
    * end section muinx add code to get ajax content
    */

    $PAGE->print_header(get_string('course').': %fullname%', NULL, '', $bodytags);
        
        
	// Course wrapper start.
	echo '<div class="course-content">';

	$modinfo =& get_fast_modinfo($COURSE);
	get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);
    
    foreach($mods as $modid=>$unused) {
		if (!isset($modinfo->cms[$modid])) {
			rebuild_course_cache($course->id);
			$modinfo =& get_fast_modinfo($COURSE);
			debugging('Rebuilding course cache', DEBUG_DEVELOPER);
			break;
		}
	}

	if (! $sections = get_all_sections($course->id)) {   // No sections found
		// Double-check to be extra sure
		if (! $section = get_record('course_sections', 'course', $course->id, 'section', 0)) {
			$section->course = $course->id;   // Create a default section.
			$section->section = 0;
			$section->visible = 1;
			$section->id = insert_record('course_sections', $section);
		}
		if (! $sections = get_all_sections($course->id) ) {      // Try again
			error('Error finding or creating section structures for this course');
		}
	}
    
	// Include the actual course format.
    //require($CFG->dirroot .'/course/format/'. $course->format .'/format.php');
	require($CFG->dirroot .'/course/format/'. $course->format .'/format_student.php'); //@muinx test
    
    
    
	// Content wrapper end.
	echo "</div>\n\n";


	// Use AJAX?
	if ($useajax && has_capability('moodle/course:manageactivities', $context)) {
		// At the bottom because we want to process sections and activities
		// after the relevant html has been generated. We're forced to do this
		// because of the way in which lib/ajax/ajaxcourse.js is written.
		echo '<script type="text/javascript" ';
		echo "src=\"{$CFG->wwwroot}/lib/ajax/ajaxcourse.js\"></script>\n";

		$COURSE->javascriptportal->print_javascript($course->id);
	}


	print_footer(NULL, $course);

    
    
    
/**
* show course content
* muinx add to get content & use for ajax
* 
* @param mixed $courseId
* @param mixed $sectionId
* 
* @return html to display content
*/
function showCourseContentDetail($courseId = null, $sectionId = null)
{
    global $USER, $CFG;
    
    if(!function_exists('getLessonActivitiesFromSectionId') || !function_exists('getAvgGradeOfAllQuizInActivityOfUser'))
        require_once('../smartcom/util/courseutil.php');
    
    if(!$courseId || !$sectionId) return false;
    
    $listActivities = getLessonActivitiesFromSectionId ($courseId, $sectionId);
    
    //echo '<pre>'; print_r($arrData);
    $str = 
    
        '<table id="table_course_detail" cellpadding="10px" cellspacing="1" width="100%" border="1" style="border-color:#6699CC;" bgcolor="#999999">
            <tr valign="middle">
                <td align="center" class="courseBB" background="'.$CFG->themewww.'/'.current_theme().'/template/images/TB1_HD.jpg">
                    #
                </td>
                <td align="center" class="courseBB" background="'.$CFG->themewww.'/'.current_theme().'/template/images/TB1_HD.jpg">
                    Activitiest
                </td>
                <td align="center" class="courseBB" background="'.$CFG->themewww.'/'.current_theme().'/template/images/TB1_HD.jpg">
                    Contents
                </td>
                <td align="center" class="courseBB" background="'.$CFG->themewww.'/'.current_theme().'/template/images/TB1_HD.jpg">
                    Status
                </td>
                <td align="center" class="courseBB" background="'.$CFG->themewww.'/'.current_theme().'/template/images/TB1_HD.jpg">
                    Result
                </td>
            </tr>';
        
        $i = 1;
        
        foreach($listActivities as $obj)
        {
            $grade = getAvgGradeOfAllQuizInActivityOfUser($courseId, $sectionId, $obj->id, $USER->id);
            
            //print_r($grade);
            $gradePercent = ($grade->avg)*100;
            
            $str .= '
            <tr valign="middle">
                <td height="44px" width="40px" align="center" bgcolor="#EEEEEE" style="font-weight:bold;">
                    '.$i.'
                </td>
                <td align="left" bgcolor="#FFFFFF" class="courseBB">
                    <a href="'.$obj->link.'">'.$obj->name.'</a>
                </td>
                <td align="left" bgcolor="#FFFFFF" class="courseB">
                    '.$obj->content.'
                </td>
                <td align="center" bgcolor="#FFFFFF" class="courseBB">
                    <img title="'.$grade->status.'" src="'.$CFG->themewww.'/'.current_theme().'/template/images/'.$grade->status.'.gif" />
                </td>
                <td align="center" bgcolor="#FFFFFF" class="courseBB">
                    '.$gradePercent.'
                </td>
            </tr>';       
            
            $i ++; 
        }
        
        $str .= '</table>';
                                                                    
    return $str;
} //showCourseConentDetail

echo getNewsItemContent();

function getNewsItemContent() {
    global $CFG, $USER, $COURSE;

    $content = new stdClass;

    if ($COURSE->newsitems) {   // Create a nice listing of recent postings

        require_once($CFG->dirroot.'/mod/forum/lib.php');   // We'll need this

        $text = '';

        if (!$forum = forum_get_course_forum($COURSE->id, 'news')) {
            return '';
        }

        $modinfo = get_fast_modinfo($COURSE);
        if (empty($modinfo->instances['forum'][$forum->id])) {
            return '';
        }
        $cm = $modinfo->instances['forum'][$forum->id];

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    /// User must have perms to view discussions in that forum
        if (!has_capability('mod/forum:viewdiscussion', $context)) {
            return '';
        }

    /// First work out whether we can post to this group and if so, include a link
        $groupmode    = groups_get_activity_groupmode($cm);
        $currentgroup = groups_get_activity_group($cm, true);


        if (forum_user_can_post_discussion($forum, $currentgroup, $groupmode, $cm, $context)) {
            $text .= '<div class="newlink"><a href="'.$CFG->wwwroot.'/mod/forum/post.php?forum='.$forum->id.'">'.
                      get_string('addanewtopic', 'forum').'</a>...</div>';
        }

    /// Get all the recent discussions we're allowed to see

        if (! $discussions = forum_get_discussions($cm, 'p.modified DESC', false, 
                                                   $currentgroup, $COURSE->newsitems) ) {
            $text .= '('.get_string('nonews', 'forum').')';
            $content->text = $text;
            return $content;
        }

    /// Actually create the listing now

        $strftimerecent = get_string('strftimerecent');
        $strmore = get_string('more', 'forum');

    /// Accessibility: markup as a list.
        $text .= "\n<ul class='unlist'>\n";
        foreach ($discussions as $discussion) {

            $discussion->subject = $discussion->name;

            $discussion->subject = format_string($discussion->subject, true, $forum->course);

            $text .= '<li class="post">'.
                     '<div class="head">'.
                     '<div class="date">'.userdate($discussion->modified, $strftimerecent).'</div>'.
                     '<div class="name">'.fullname($discussion).'</div></div>'.
                     '<div class="info">'.$discussion->subject.' '.
                     '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion->discussion.'">'.
                     $strmore.'...</a></div>'.
                     "</li>\n";
        }
        $text .= "</ul>\n";

        $content->text = $text;

        $content->footer = '<a href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id.'">'.
                                  get_string('oldertopics', 'forum').'</a> ...';

    /// If RSS is activated at site and forum level and this forum has rss defined, show link
        if (isset($CFG->enablerssfeeds) && isset($CFG->forum_enablerssfeeds) &&
            $CFG->enablerssfeeds && $CFG->forum_enablerssfeeds && $forum->rsstype && $forum->rssarticles) {
            require_once($CFG->dirroot.'/lib/rsslib.php');   // We'll need this
            if ($forum->rsstype == 1) {
                $tooltiptext = get_string('rsssubscriberssdiscussions','forum',format_string($forum->name));
            } else {
                $tooltiptext = get_string('rsssubscriberssposts','forum',format_string($forum->name));
            }
            if (empty($USER->id)) {
                $userid = 0;
            } else {
                $userid = $USER->id;
            }
            $content->footer .= '<br />'.rss_get_link($COURSE->id, $userid, 'forum', $forum->id, $tooltiptext);
        }

    }

    return $content;
}