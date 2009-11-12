<script language="javascript">
    $().ready(function(){
        $('span[sectionid][class*="course"]').click(function(){
            //$('#current_unit_active').attr('id','')
            //$(this).parents('table').attr('id','current_unit_active');    
            doStuff($(this));
        });
    });
    
    function doStuff(el)
    {
    	var courseid = $(el).attr('courseid');
        var sectionid = $(el).attr('sectionid');
        
        if($('#course-tab-'+courseid+'-'+sectionid).hasClass('course-tab-actived'))
        {
        	return false;
        }
        
        var number = $(el).attr('number');
        var label = $(el).html();
        
        //change focus!        
        $unactiveSummary = $('#course-tab-'+courseid+'-'+sectionid).find('.courseW:first').html();
        $activeSummary = $('.course-tab-actived .courseG:first').html();
        
        $activeCid = $('.course-tab-actived').attr('courseid');
        $activeSid = $('.course-tab-actived').attr('sectionid');
        $activeNumber = $('.course-tab-actived .courseWB').html();
        $activeLabel = $('.course-tab-actived .courseGB').html();
        
        $('.course-tab-actived').html(unactiveTable($activeCid, $activeSid, $activeLabel, $activeNumber, $activeSummary)).removeClass('course-tab-actived').addClass('course-tab-unactive');
        $('#course-tab-'+courseid+'-'+sectionid).html(activeTable(courseid, sectionid, label, number, $unactiveSummary)).removeClass('course-tab-unactive').addClass('course-tab-actived');
        
        $('span[sectionid][class*="course"]').click(function(){
            //$('#current_unit_active').attr('id','')
            //$(this).parents('table').attr('id','current_unit_active');    
            doStuff($(this));
        });
        
        //end change focus
        
        var htmlWaiting = '<div style="float:left; width:350px;">Waiting ... </div>'; 
        //htmlWaiting += '<tr><td style="padding:10px">Waiting...</td></tr><table>';
        
        //$('#table_course_detail').attr('style', 'display:none;');
        $('#list_activities').html(htmlWaiting);
        
        $.post(
                '<?php echo $CFG->wwwroot .'/course/view.php?id='.$_REQUEST['id'].'&task=ajax' ?>',
                {courseid:courseid, sectionid:sectionid},
                function(data)
                {
                    $('#list_activities').html(data);
                }
        );
    }
    
    function activeTable(courseid, sectionid, label, number, summary)
    {
        return '<table cellpadding="0" cellspacing="0" width="100%" id="current_unit_active"><tr class="h-border"><td><img class="h-border" src="<?php echo $CFG->themewww."/".current_theme(); ?>/template/images/BG3_TL.gif" /></td><td colspan="4" bgcolor="#EEEEDD"> </td></tr><tr><td rowspan="2" width="5px" bgcolor="#EEEEDD" class="v-border"/><td rowspan="2" valign="top" style="background:#EED url(<?php echo $CFG->themewww."/".current_theme(); ?>/template/images/CircleBG.gif) top center no-repeat" width="15px" align="center"><span class="courseWB">'+number+'</span></td><td rowspan="2" width="10px" bgcolor="#EEEEDD"/><td height="20px" valign="top" bgcolor="#EEEEDD"><span courseid="'+courseid+'" sectionid="'+sectionid+'" number="'+number+'" class="courseGB" title="'+label+'">'+label+'</span></td></tr><tr><td bgcolor="#EEEEDD"><span class="courseG">'+summary+'</span></td></tr><tr><td bgcolor="#EEEEDD"><span class="courseG"></span></td></tr><tr class="h-border"><td class="h-border" vaglin="top"><img  src="<?php echo $CFG->themewww."/".current_theme(); ?>/template/images/BG3_BL.gif" /></td><td colspan="4" bgcolor="#EEEEDD" class="h-border"></td></tr><tr><td colspan="2" height="10px"/></tr></table>';
    }
    function unactiveTable(courseid, sectionid, label, number, summary)
    {
    	return '<table cellpadding="0" cellspacing="0" width="100%"><tr><td rowspan="2" width="5px"/><td rowspan="2" valign="top" style="background:url(<?php echo $CFG->themewww.'/'.current_theme(); ?>/template/images/CircleBW.gif) top center no-repeat" width="15px" align="center"><span class="courseGB">'+number+'</span></td><td rowspan="2" width="10px"/><td height="20px" valign="top"><span courseid="'+courseid+'" sectionid="'+sectionid+'" number="'+number+'" class="courseWB" title="'+label+'">'+label+'</span></td></tr><tr><td><span class="courseW">'+summary+'</span></td></tr><tr><td><span class="courseW"></span></td><tr><tr><td colspan="2" height="10px"/></tr><tr><td/><td colspan="3" background="<?php echo$CFG->themewww.'/'.current_theme();?>/template/images/BG2_Split.gif" height="1px"/></tr><tr><td colspan="2" height="10px"/></tr></table>';
    }
</script>
<?php // $Id: format.php,v 1.2 2008/09/11 22:19:02 stronk7 Exp $

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
// Collapse idea & code from:                                            //
//   http://www.howtocreate.co.uk/tutorials/jsexamples/listCollapseExample.html
//   http://odyniec.net/articles/turning-lists-into-trees/               //
///////////////////////////////////////////////////////////////////////////

// This course format is one "clone" of the standard "topics" format. Its
// main (unique) difference is that, following the indentation in activities performed
// in edit mode, it displays the course in a tree way.

    require_once($CFG->libdir.'/ajax/ajaxlib.php');

    global $CFG;
    echo '<script type="text/javascript" ';
    echo "src=\"{$CFG->wwwroot}/course/format/topicstree/list.js\"></script>\n";

    echo '<script type="text/javascript" ';
    echo "src=\"{$CFG->wwwroot}/course/format/topicstree/cookie.js\"></script>\n";

    echo '<script type="text/javascript" ';
    echo "src=\"{$CFG->wwwroot}/course/format/topicstree/loadunload.js\"></script>\n";

    /************************
    * @desc GURUCORE hack
    */
    echo '<script type="text/javascript" src="/mod/smartcom/js/course_view_grade_percent.js"></script>';
    echo "<script type='text/javascript'>
        var CURRENT_COURSEID = $course->id;
        var CURRENT_USERID = $USER->id;
    </script>";
    /************************
    * @desc GURUCORE hack
    */

    
    // Define if we want tree in section 0
    $topicstree_tree_in_section0 = false;

    $topic = optional_param('topic', -1, PARAM_INT);

    // Bounds for block widths
    // more flexible for theme designers taken from theme config.php
    $lmin = (empty($THEME->block_l_min_width)) ? 100 : $THEME->block_l_min_width;
    $lmax = (empty($THEME->block_l_max_width)) ? 210 : $THEME->block_l_max_width;
    $rmin = (empty($THEME->block_r_min_width)) ? 100 : $THEME->block_r_min_width;
    $rmax = (empty($THEME->block_r_max_width)) ? 210 : $THEME->block_r_max_width;

    define('BLOCK_L_MIN_WIDTH', $lmin);
    define('BLOCK_L_MAX_WIDTH', $lmax);
    define('BLOCK_R_MIN_WIDTH', $rmin);
    define('BLOCK_R_MAX_WIDTH', $rmax);

    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),  
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 
                                            BLOCK_R_MAX_WIDTH);

    if ($topic != -1) {
        $displaysection = course_set_display($course->id, $topic);
    } else {
        if (isset($USER->display[$course->id])) {       // for admins, mostly
            $displaysection = $USER->display[$course->id];
        } else {
            $displaysection = course_set_display($course->id, 0);
        }
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    if (($marker >=0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
        $course->marker = $marker;
        if (! set_field("course", "marker", $marker, "id", $course->id)) {
            error("Could not mark that topic for this course");
        }
    }

    $streditsummary   = get_string('editsummary');
    $stradd           = get_string('add');
    $stractivities    = get_string('activities');
    $strshowalltopics = get_string('showalltopics');
    $strtopic         = get_string('topic');
    $strgroups        = get_string('groups');
    $strgroupmy       = get_string('groupmy');
    $editing          = $PAGE->user_is_editing();

    if ($editing) {
        $strstudents = moodle_strtolower($course->students);
        $strtopichide = get_string('topichide', '', $strstudents);
        $strtopicshow = get_string('topicshow', '', $strstudents);
        $strmarkthistopic = get_string('markthistopic');
        $strmarkedthistopic = get_string('markedthistopic');
        $strmoveup = get_string('moveup');
        $strmovedown = get_string('movedown');
    }


/// Layout the whole page as three big columns.

    echo '<table id="layout-table" border="0" cellspacing="0" summary="'.get_string('layouttable').'"><tr>';

/// The left column ...
    $lt = (empty($THEME->layouttable)) ? array('left', 'middle', 'right') : $THEME->layouttable;
    foreach ($lt as $column) {
        switch ($column) {
            case 'left':

    if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
        echo '<td style="width:'.$preferred_width_left.'px" id="left-column">';
        print_container_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        print_container_end();
        echo '</td>';
    }

            break;
            case 'middle':
/// Start main column
    echo '<td id="middle-column">';
    
    print_container_start();
    echo skip_main_destination();

    
    
    print_heading_block(get_string('bulletin', 'format_topicstree'));    
    
    $courseId = $_REQUEST['id'];
    
    //latest news
    $latestNews = getNewsItemContent();
    
    echo '<table cellpadding="10px" cellspacing="0" width="100%" style="background:url('.$CFG->themewww.'/'.current_theme().'/template/images/BG1_M.jpg) top repeat-x" height="100px">
            <tr>
            ';
            
//    news->subject
// * 		->modified: last modified time in miliseconds
// * 		->firstname: first name của user edit post cuối cùng
// * 		->lastname: last name của user edit post cuối cùng
// * 		->message: nội dung news
    if(!empty($latestNews)) 
    {
            foreach($latestNews as $objNews)
            {
            	$usernameLink = $CFG->wwwroot . "/user/view.php?id=$objNews->userid&course=1";
            	$titleLink = $CFG->wwwroot. "/mod/forum/discuss.php?d=$objNews->discussionid";
                
                echo '
                <td valign="top" width="33%">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr><td>
                            <a href="'. $titleLink . '" class="titleText">'.$objNews->subject.'</a></div>
                        </td></td>
                        <tr><td>
                            <a href="' . $usernameLink . '" class="username">'.$objNews->firstname.' '.$objNews->lastname.'</a> <span class="datetime">('.date('d M, H:i', $objNews->timemodified).')</span>
                        </td></tr>
                        <tr><td height="10px"></td></tr>
                        <tr><td height="1px" bgcolor="#CCCCCC"></td></tr>
                        <tr><td height="10px"></td></tr>
                        <tr><td align="justify">
                            '.$objNews->message.'
                        </td></tr>
                    </table>
                </td>';
            }
    }
            echo '
            </tr>
        </table>';
    
    $sectionListCourse = getSectionListOfCourse($courseId);
    
    //echo '<pre>';
    //print_r($sectionListCourse);
    
    $myCourseList = getMyCourseList($USER->id);
    
    //print_r($myCourseList);
    
    echo '
    <div style="clear:both;"></div>
    
                    <div class="newsarea">
                        <table cellpadding="0" cellspacing="0" width="100%" >
                            <tr><td height="30px" colspan="3">
                                <div>' . print_heading_block(get_string('topicoutline', "format_topicstree")) . '</div>
                                <div class="selectcourse">
                                    <select name="current_course" style="width:225px; height:21px" onchange="location.href=this.value">';
    									if(!empty($myCourseList)) {
    										foreach($myCourseList as $course)
    										{
    											if($course->id == $_REQUEST['id'])
    											$selected = 'selected';
    											else
    											$selected = '';

    											echo '<option value="/course/view.php?id='.$course->id.'" '.$selected.'>'.$course->name.'</option>';
    										}
    									}

    echo '
                                    </select>
                                </div>
                            </td></tr>
                            <tr>
                                <td valign="top" width="5px" bgcolor="#88e25c"><img src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG2_L.jpg" /></td>
                                <td valign="top">
                                    <table cellpadding="0" cellspacing="0" width="100%" style="background:#88e25c url('.$CFG->themewww.'/'.current_theme().'/template/images/BG2_M.jpg) top repeat-x" height="350px" >
                                        <tr><td valign="top">
                                            <div style="margin:10px 5px 10px 5px">
                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                    <tr><td valign="top" width="180px">
														<div class="leftInner" style="width:180px;">
                                                        ';
                                                        $i = 1;
                                                        foreach($sectionListCourse as $objCourse)
                                                        {
                                                            if($objCourse->label != '')
                                                            {
                                                                
                                                                $sectionId = $objCourse->id;
                                                                //echo '<pre>'; print_r($activitiesList); die;
                                                                if($i  == 1)
                                                                {
                                                                    $currentSectionId = $objCourse->id;
                                                                    
                                                                    echo 
                                                        '<div id="course-tab-'.$courseId.'-'.$sectionId.'" class="course-tab-actived" courseid="'.$courseId.'" sectionid="'.$sectionId.'">
                                                        <table cellpadding="0" cellspacing="0" width="100%" id="current_unit_active">
                                                            <tr class="h-border">
                                                                <td><img class="h-border" src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG3_TL.gif" /></td>
                                                                <td colspan="4" bgcolor="#EEEEDD"> </td>
                                                            </tr>
                                                            <tr>
                                                                <td rowspan="2" width="5px" bgcolor="#EEEEDD" class="v-border"/>
                                                                <td rowspan="2" valign="top" style="background:#EED url('.$CFG->themewww.'/'.current_theme().'/template/images/CircleBG.gif) top center no-repeat" width="15px" align="center">
                                                                    <span class="courseWB">'.$i.'</span>
                                                                </td>
                                                                <td rowspan="2" width="10px" bgcolor="#EEEEDD"/>
                                                                <td height="20px" valign="top" bgcolor="#EEEEDD">
                                                                    <span courseid="'.$courseId.'" sectionid="'.$sectionId.'" class="courseGB" title="'.$objCourse->label.'">'.$objCourse->label.'</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td bgcolor="#EEEEDD">
                                                                    <span class="courseG">'.$objCourse->summary.'</span>
                                                                </td>
                                                            </tr>
                                                            <tr class="h-border">
                                                                <td class="h-border" vaglin="top"><img  src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG3_BL.gif" /></td>
                                                                <td colspan="4" bgcolor="#EEEEDD" class="h-border"></td>
                                                            </tr>
                                                            <tr><td colspan="2" height="10px"/></tr>
                                                        </table>
                                                        </div>';
                                                                }
                                                                else
                                                                {
                                                                    echo 
                                                        '<div id="course-tab-'.$courseId.'-'.$sectionId.'" class="course-tab-unactive" courseid="'.$courseId.'" sectionid="'.$sectionId.'">
                                                        <table cellpadding="0" cellspacing="0" width="100%">
                                                            <tr>
                                                                <td rowspan="2" width="5px"/>
                                                                <td rowspan="2" valign="top" style="background:url('.$CFG->themewww.'/'.current_theme().'/template/images/CircleBW.gif) top center no-repeat" width="15px" align="center">
                                                                    <span class="courseGB">'.$i.'</span>
                                                                </td>
                                                                <td rowspan="2" width="10px"/>
                                                                <td height="20px" valign="top">
                                                                    <span courseid="'.$courseId.'" sectionid="'.$sectionId.'" number="'.$i.'" class="courseWB" title="'.$objCourse->label.'">'.$objCourse->label.'</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="courseW">'.$objCourse->summary.'</span>
                                                                </td>
                                                            <tr>
                                                            <tr><td colspan="2" height="10px"/></tr>
                                                            <tr><td/><td colspan="3" background="'.$CFG->themewww.'/'.current_theme().'/template/images/BG2_Split.gif" height="1px"/></tr>            
                                                            <tr><td colspan="2" height="10px"/></tr>
                                                        </table>
                                                        </div>';
                                                                } //end if
                                                                $i ++;
                                                                
                                                                echo '<!------------------------------------------------>';
                                                            }  //end if
                                                            
                                                        } //end for
                                                        
                                                        echo ' 
                                                        
                                                     </div> <!-- leftInner--->       
                                                    </td><td valign="top">
														  <table cellpadding="0" cellspacing="0" width="100%">
                                                            <tr class="h-border">
                                                                <td width="5px"><img src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG3_TL.gif" /></td>
                                                                <td bgcolor="#EEEEDD" />
                                                                <td width="5px"><img src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG3_TR.gif" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td bgcolor="#EEEEDD" />
                                                                <td bgcolor="#EEEEDD">
																<div id="list_activities" class="rightInner">
                                                                    '.showCourseContentDetail($courseId, $currentSectionId).'</div>
                                                                <!--rightInner-->	
                                                                </td>
                                                                <td bgcolor="#EEEEDD" />
                                                            </tr>
                                                            <tr class="h-border">
                                                                <td width="5px"><img src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG3_BL.gif" /></td>
                                                                <td bgcolor="#EEEEDD" />
                                                                <td width="5px"><img src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG3_BR.gif" /></td>
                                                            </tr>
                                                        </table>
                                                    </td></tr>
                                                </table>
                                            </div>
                                        </td></tr>    
                                    </table>
                                </td>
                                <td valign="top" width="5px" bgcolor="#88e25c"><img src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG2_R.jpg" /></td>
                            </tr>
                            <tr class="h-border">
                                <td valign="top" width="5px"><img src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG2_BL.jpg" /></td>
                                <td valign="top" bgcolor="#88e25c"></td>
                                <td valign="top" width="5px"><img src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG2_BR.jpg" /></td>
                        </table>
                    </div></div>';
                    
                    
    //suggest course
    $recommendCourse = getRecommendCourseList($courseId);
    
    //print_r($recommendCourse);
    
    if(is_array($recommendCourse))
    {
    
        echo '
                <div class="newsarea">
                <table cellpadding="0" cellspacing="0" width="100%" >
                    <tr><td height="30px" colspan="3">
                        <div class="title">' . get_string("suggest_courses", "format_topicstree") . '</div>                       
                    </td></tr>
                    <tr>
                        <td valign="top" width="5px"><img src="'.$CFG->themewww.'/'.current_theme().'/template/images/BG1_L.jpg" /></td>
                        <td valign="top">
                            <table cellpadding="10px" cellspacing="0" width="100%" style="background:url('.$CFG->themewww.'/'.current_theme().'/template/images/BG1_M.jpg) top repeat-x" height="100px">
                                <tr>
                                ';
                                foreach($recommendCourse as $rCourse)
                                {
                                    echo '
                                    <td valign="top" width="33%">
                                        <table cellpadding="0" cellspacing="0" width="100%">
                                            <tr><td>
                                                <a href="/course/view.php?id='.$rCourse->id.'" class="titleText">'.$rCourse->fullname.'</a></div>
                                            </td></td>
                                            <tr><td height="10px"></td></tr>
                                            <tr><td height="1px" bgcolor="#CCCCCC"></td></tr>
                                            <tr><td height="10px"></td></tr>
                                            <tr><td align="justify">
                                                '.$rCourse->summary.'
                                            </td></tr>
                                        </table>
                                    </td>
                                    ';
                                }
                                echo '
                                </tr>
                            </table>
                        </td>
                        <td valign="top" width="5px"><img src="template/images/BG1_R.jpg" /></td>
                    </tr>
                </table>
            </div>
        ';
    }

/// If currently moving a file then show the current clipboard
    if (ismoving($course->id)) {
        $stractivityclipboard = strip_tags(get_string('activityclipboard', '', addslashes($USER->activitycopyname)));
        $strcancel= get_string('cancel');
        echo '<tr class="clipboard">';
        echo '<td colspan="3">';
        echo $stractivityclipboard.'&nbsp;&nbsp;(<a href="mod.php?cancelcopy=true&amp;sesskey='.$USER->sesskey.'">'.$strcancel.'</a>)';
        echo '</td>';
        echo '</tr>';
    }

/// Print Section 0

    $section = 0;
    $thissection = $sections[$section];

    


/// Now all the normal modules by topic
/// Everything below uses "section" terminology - each "section" is a topic.

    $timenow = time();
    $section = 1;
    $sectionmenu = array();

    echo '</table>';

    if (!empty($sectionmenu)) {
        echo '<div align="center" class="jumpmenu">';
        echo popup_form($CFG->wwwroot.'/course/view.php?id='.$course->id.'&amp;', $sectionmenu,
                   'sectionmenu', '', get_string('jumpto'), '', '', true);
        echo '</div>';
    }

    print_container_end();
    
    echo '</td>';

            break;
            case 'right':
    // The right column
    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing) {
        echo '<td style="width:'.$preferred_width_right.'px" id="right-column">';
        print_container_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        print_container_end();
        echo '</td>';
    }

            break;
        }
    }
    
    echo '</tr></table>';    

    
    

/**
 * get list of latest news of course
 *
 * @return array of news Obj
 * news->subject
 * 		->modified: last modified time in miliseconds
 * 		->firstname: first name của user edit post cuối cùng
 * 		->lastname: last name của user edit post cuối cùng
 * 		->message: nội dung news
 * 		->discussionid: id discussion
 * 		->userid
 */	
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



    /// Get all the recent discussions we're allowed to see
		$discussions = forum_get_discussions($cm, 'p.modified DESC', false, $currentgroup, $COURSE->newsitems); 
                                                    
        if (empty($discussions))  {
            return false;
        }
        return $discussions;
    
    }

}
