<?php
    require_once('../../config.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/calendar/lib.php');
    require_once($CFG->dirroot.'/blocks/calendar_month_2/lib/yuiCalLib.php');
    $cal_m = optional_param( 'cal_m', 0, PARAM_INT );
    $cal_y = optional_param( 'cal_y', 0, PARAM_INT );
    $courseshown = optional_param( 'course', 0, PARAM_INT );
    calendar_set_filters($courses, $group, $user, $filtercourse, $groupeventsfrom, false);
        if ($courseshown == SITEID) {
            // For the front page
            $content .= calendar_overlib_html();//TODO what does this do?
            $content .= calendar_top_controls_yui('frontpage', array('id' => $courseshown, 'm' => $cal_m, 'y' => $cal_y));   
            $content .= calendar_get_mini_yui($courses, $group, $user, $cal_m, $cal_y);
            // No filters for now

        } else {
            // For any other course
            $content .= calendar_overlib_html();
            $content .= calendar_top_controls_yui('course', array('id' => $courseshown, 'm' => $cal_m, 'y' => $cal_y));
            $content .= calendar_get_mini_yui($courses, $group, $user, $cal_m, $cal_y);
            $content .= '<h3 class="eventskey">'.get_string('eventskey', 'calendar').'</h3>';
            //$content .= '<div class="filters">'.calendar_filter_controls('course', '', $COURSE).'</div>';
            $content .= '<div class="filters">'.calendar_filter_controls_yui('course', '', $COURSE,'', $courseshown).'</div>';
            
        }
            // MDL-9059, unset this so that it doesn't stay in session
        if (!empty($courseset)) {
            unset($SESSION->cal_courses_shown[$COURSE->id]);
        }
    echo $content;
?>