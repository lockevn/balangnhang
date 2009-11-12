<?php // $Id: edit.php,v 1.21.2.1 2007/11/02 16:20:22 tjhunt Exp $
/**
* Page to edit the learning objects bank
*
* TODO: add logging
*
* @author danhut. 
* 
* @package smartcom.lomanagement
*/

    require_once("../../config.php");
    require_once("editlib.php");

    global $lotype;
    $lotype = optional_param('lotype', 0, PARAM_TEXT);
    global $allowedLOTypes, $allowedQuizTypes;
    $allowedLOTypes = array('lecture','exercise','practice','test');
    $allowedQuizTypes = array('exercise','practice','test');
    if(empty($lotype) || !in_array($lotype, $allowedLOTypes)) {
    	$lotype = 'lecture';
    }
    
    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) = lo_edit_setup($lotype);       
    $courseid = $thispageurl->params['courseid'];
    $addlo = optional_param('addlo', '', PARAM_TEXT);
    if(!empty($addlo) && !empty($courseid)) {
    	$category = $pagevars['cat'];
    	$categoryparts = explode(',', $category);
    	
    	if($addlo == get_string('addnewlecture', 'smartcom')) {
    		$type = 'html';
    		$add = 'resource';
    	} else if($addlo == get_string('addnewexercise', 'smartcom')) {
    		$type = 'exercise';
    		$add = 'quiz';
    	} else if($addlo == get_string('addnewpractice', 'smartcom')) {
    		$type = 'practice';
    		$add = 'quiz';
    	} else if($addlo == get_string('addnewtest', 'smartcom')) {
    		$type = 'test';
    		$add = 'quiz';
    	}  
    	$beforecm	= optional_param('beforecm', 0, PARAM_INT);
    	$addUrl = $CFG->wwwroot . "/course/modedit.php?add=$add&type=$type&course=$courseid&section=" 
    				. $thispageurl->params['section'] . "&cat=$category&lotype=$lotype&beforecm=$beforecm&return=" . urlencode($thispageurl->out());
    	redirect($addUrl);

    }
    
    

    lo_showbank_actions($thispageurl, $cm);

    $context = $contexts->lowest();
    $streditingquestions = get_string($lotype.'s', "smartcom");
    if ($cm!==null) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $contexts->lowest())
            ? update_module_button($cm->id, $COURSE->id, get_string('modulename', $cm->modname))
            : "";
        $navlinks = array();
        $navlinks[] = array('name' => get_string('lomanagement', 'lomanagement'), 'link' => "$CFG->wwwroot/smartcom/lomanagement/edit.php?courseid=$COURSE->id", 'type' => 'title');
        //$navlinks[] = array('name' => format_string($module->name), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/view.php?id={$cm->id}", 'type' => 'title');
        $navlinks[] = array('name' => $streditingquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);
        print_header_simple($streditingquestions, '', $navigation, "", "", true, $strupdatemodule);

        $currenttab = 'edit';
        $mode = 'questions';
        ${$cm->modname} = $module;
        include($CFG->dirroot."/mod/$cm->modname/tabs.php");
    } else {
        // Print basic page layout.
        $navlinks = array();
        $navlinks[] = array('name' => get_string('lo', 'smartcom'), 'link' => "$CFG->wwwroot/smartcom/lomanagement/edit.php?courseid=$COURSE->id", 'type' => 'title');
        $navlinks[] = array('name' => $streditingquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);

        print_header_simple($streditingquestions, '', $navigation);

        // print tabs
        $currenttab = $lotype;
        echo '
            <table class="boxaligncenter">
                <tr><td>
            ';
        include('tabs.php');
        echo '
                </td></tr>
                </table>
            ';
    }


    echo '<table class="boxaligncenter" border="0" cellpadding="2" cellspacing="0">';
    echo '<tr><td valign="top">';

    lo_showbank($lotype, $contexts, $thispageurl, $cm, $pagevars['qpage'], $pagevars['qperpage'], 
                    $pagevars['cat'], $pagevars['recurse'.$lotype], $pagevars['showhidden'], $pagevars['showquestiontext' . $lotype]);

    echo '</td></tr>';
    echo '</table>';

    print_footer($COURSE);
?>

