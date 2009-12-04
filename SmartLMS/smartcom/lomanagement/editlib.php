<?php // $Id: editlib.php,v 1.76.2.10 2008/11/27 11:50:20 tjhunt Exp $
/**
 * Functions used to show question editing interface
 *
 *
 * @author Martin Dougiamas and many others. This has recently been extensively
 *         rewritten by members of the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */


require_once('lolib.php');
require_once($CFG->dirroot.'/mod/resource/lib.php');
require_once($CFG->dirroot.'/mod/quiz/lib.php');
require_once($CFG->dirroot.'/course/lib.php');



define('DEFAULT_LOS_PER_PAGE', 20);

function get_module_from_cmid($cmid){
    global $CFG;
    if (!$cmrec = get_record_sql("SELECT cm.*, md.name as modname
                               FROM {$CFG->prefix}course_modules cm,
                                    {$CFG->prefix}modules md
                               WHERE cm.id = '$cmid' AND
                                     md.id = cm.module")){
        error('cmunknown');
    } elseif (!$modrec =get_record($cmrec->modname, 'id', $cmrec->instance)) {
        error('cmunknown');
    }
    $modrec->instance = $modrec->id;
    $modrec->cmid = $cmrec->id;
    $cmrec->name = $modrec->name;

    return array($modrec, $cmrec);
}

/**
 * prints a form to choose categories
 */
function lo_category_form($contexts, $pageurl, $current, $recurse=1, $showhidden=false, $showquestiontext=false, $lotype) {
    global $CFG;


/// Get all the existing categories now

    $catmenu = lo_category_options($contexts, false, 0, true, '', $lotype);

    $strcategory = get_string('category', 'smartcom');
    $strshow = get_string('show', 'smartcom');
    $streditcats = get_string('editcategories', 'smartcom');

    global $lotype;
    popup_form ('edit.php?'.$pageurl->get_query_string().'&amp;lotype='. $lotype . '&amp;category=', $catmenu, 'catmenu', $current, '', '', '', false, 'self', "<strong>$strcategory</strong>");

    echo '<form method="get" action="edit.php" id="displayoptions">';
    echo "<fieldset class='invisiblefieldset'>";
    echo $pageurl->hidden_params_out(array('recurse'.$lotype, 'showquestiontext'));
    lo_category_form_checkbox('recurse'.$lotype, $recurse);
    //lo_category_form_checkbox('showhidden', $showhidden);
    lo_category_form_checkbox('showquestiontext'.$lotype, $showquestiontext);
    echo '<input type="hidden" name="lotype" value="'.$lotype.'" />';
    echo '<noscript><div class="centerpara"><input type="submit" value="'. get_string('go') .'" />';
    echo '</div></noscript></fieldset></form>';
}

/**
 * Private funciton to help the preceeding function.
 */
function lo_category_form_checkbox($name, $checked) {
    echo '<div><input type="hidden" id="' . $name . '_off" name="' . $name . '" value="0" />';
    echo '<input type="checkbox" id="' . $name . '_on" name="' . $name . '" value="1"';
    if ($checked) {
        echo ' checked="checked"';
    }
    echo ' onchange="getElementById(\'displayoptions\').submit(); return true;" />';
    echo '<label for="' . $name . '_on">';
    print_string($name, 'smartcom');
    echo "</label></div>\n";
}

/**
* Prints the table of questions in a category with interactions
*
* @param object $course   The course object
* @param int $categoryid  The id of the question category to be displayed
* @param int $cm      The course module record if we are in the context of a particular module, 0 otherwise
* @param int $recurse     This is 1 if subcategories should be included, 0 otherwise
* @param int $page        The number of the page to be displayed
* @param int $perpage     Number of questions to show per page
* @param boolean $showhidden   True if also hidden questions should be displayed
* @param boolean $showquestiontext whether the text of each question should be shown in the list
*/
function lo_list($contexts, $pageurl, $categoryandcontext, $cm = null,
        $recurse=1, $page=0, $perpage=100, $showhidden=false,
        $showquestiontext = false, $addcontexts = array()) {
    global $USER, $CFG, $THEME, $COURSE;
	global $allowedLOTypes, $allowedQuizTypes;
    list($categoryid, $contextid)=  explode(',', $categoryandcontext);

    
    $qtypemenu = lo_type_menu();

    $strcategory = get_string("category", "smartcom");
    $strquestion = get_string("lo", "smartcom");
    $straddquestions = get_string("addquestions", "quiz");
    
    $strselect = get_string("select", "quiz");
    $strselectall = get_string("selectall", "quiz");
    $strselectnone = get_string("selectnone", "quiz");
    $strcreatenewlo = get_string("createnewlo", "smartcom");
    
    
    $strdelete = get_string("delete");
    $stredit = get_string("edit");
    $strmove = get_string('moveqtoanothercontext', 'question');
    $strview = get_string("view");
    $straction = get_string("action");
    $strrestore = get_string('restore');

    $strtype = get_string("type", "quiz");
    $strcreatemultiple = get_string("createmultiple", "quiz");
    $strpreview = get_string("preview","quiz");

    if (!$categoryid) {
        echo "<p style=\"text-align:center;\"><b>";
        print_string("selectcategoryabove", "quiz");
        echo "</b></p>";
        return;
    }

    if (!$category = get_record('question_categories', 'id', $categoryid, 'contextid', $contextid)) {
        notify('Category not found!');
        return;
    }
    $catcontext = get_context_instance_by_id($contextid);
    /*phải có section thì mới add được LO mới*/
    global $section;
    if(isset($pageurl->params['section']))
    	$section = $pageurl->params['section'];
    $canadd = has_capability('moodle/question:add', $catcontext);// && $section;
    //check for capabilities on all questions in category, will also apply to sub cats.
    $caneditall =has_capability('moodle/question:editall', $catcontext);
    $canuseall =has_capability('moodle/question:useall', $catcontext);
    $canmoveall =has_capability('moodle/question:moveall', $catcontext);

    if ($cm AND $cm->modname == 'quiz') {
        $quizid = $cm->instance;
    } else {
        $quizid = 0;
    }
    
    global $lotype;
    
    $strquestionname = get_string($lotype."name", "smartcom");
    
    $returnurl = $pageurl->out() . "&lotype=$lotype";
    
    if($lotype == 'lecture') {
    	$loViewUrl = new moodle_url("$CFG->wwwroot/mod/resource/view.php",
                                array('returnurl' => $returnurl));
    } else if (in_array($lotype, $allowedQuizTypes)) {
    	$loViewUrl = new moodle_url("$CFG->wwwroot/mod/quiz/view.php",
                                array('returnurl' => $returnurl));
    } 
    
    $loEditUrl = new moodle_url("$CFG->wwwroot/course/modedit.php",
                                array('returnurl' => $returnurl));
    
    if ($cm!==null){
        $loViewUrl->param('cmid', $cm->id);
    } else {
        $loViewUrl->param('courseid', $COURSE->id);
    }
    $lomoveurl = new moodle_url("$CFG->wwwroot/smartcom/lomanagement/contextmoveq.php",
                                array('returnurl' => $returnurl));
    if ($cm!==null){
        $lomoveurl->param('cmid', $cm->id);
    } else {
        $lomoveurl->param('courseid', $COURSE->id);
    }
    echo '<div class="boxaligncenter">';
    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    echo format_text($category->info, FORMAT_MOODLE, $formatoptions, $COURSE->id);


    echo '</div>';

    $categorylist = ($recurse) ? question_categorylist($category->id) : $category->id;

    // hide-feature
    $showhidden = $showhidden ? '' : " AND hidden = '0'";

    if (!$totalnumber = count_records_select('lo', "category IN ($categorylist) AND lotype='$lotype'")) {
        echo "<p style=\"text-align:center;\">";
        print_string("no$lotype", "smartcom");
        echo "</p>";
    	    /*thêm add LO nếu có quyền add*/
	    if($canadd) {
	    	echo '<br>';
	    	echo '&nbsp;<strong>'.get_string('createnew' .$lotype, 'smartcom').': </strong> <br>';
	    	echo course_content_structure($COURSE, $lotype, "$categoryid,$contextid");   	
	    }
        return;
    }

    /*nếu không tìm thấy lo nào ở trang $page thì kiểm tra ở trang $page=0*/
    $los = get_records_select('lo', "category IN ($categorylist) AND lotype='$lotype'", '','instance, category, cm', $page*$perpage, $perpage );
    
    if (!$los) {
        // There are no questions on the requested page.
        $page = 0;
        $los = get_records_select('lo', "category IN ($categorylist) AND lotype='$lotype'", '','instance, category, cm', 0, $perpage );
        if (!$los) {
            // There are no questions at all
            echo "<p style=\"text-align:center;\">";
            print_string("nolos", "smartcom");
            echo "</p>";
	        /*thêm add button nếu có quyền add*/
		    echo '<br />';
            /*thêm add LO nếu có quyền add*/
		    if($canadd) {
		    	echo '&nbsp;<strong>'.get_string('createnew' .$lotype, 'smartcom').': </strong> <br>';
		    	echo course_content_structure($COURSE, $lotype, "$categoryid,$contextid");   	
		    }
            return;
        }
    }

    /*nếu tìm thấy lo trong mdl_lo, select vào table chứa LO thực sự*/
    if($lotype == 'lecture') {
    	$tbl = 'resource';
    } else if (in_array($lotype, $allowedQuizTypes)) {
    	$tbl = 'quiz';    	
    } 
    
    $loCategories = array();
    $loCM = array();
    $loIdStr = '';
    foreach ($los as $lo) {
    	$loIdStr .= $lo->instance . ',';
    	$loCategories[$lo->instance] = $lo->category;
    	$loCM[$lo->instance] = $lo->cm;
    }
    if(!empty($loIdStr)) {
    	$loIdStr = substr($loIdStr, 0, strlen($loIdStr) - 1);    	 
    }
    $loInstances = get_records_select($tbl, "lotype='$lotype' AND id in ($loIdStr)", "name asc", '*', $page*$perpage, $perpage );
    
    print_paging_bar($totalnumber, $page, $perpage, $pageurl, 'qpage');



    echo '<form method="post" action="edit.php">';
    echo '<fieldset class="invisiblefieldset" style="display: block;">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    /*lưu section index vào form*/
    echo '<input type="hidden" name="section" value="'. $section .'" />';
    /*lưu category id vào form*/
    if(isset($pageurl->params['cat']))
    	echo '<input type="hidden" name="cat" value="'. $pageurl->params['cat']  .'" />';
    /*lưu lotype id vào form*/
    echo '<input type="hidden" name="lotype" value="'. $lotype .'" />';
    
    
    echo $pageurl->hidden_params_out();
    echo '<table id="categoryquestions" style="width: 100%"><tr>';
    echo "<th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$straction</th>";

    echo "<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\">$strquestionname</th>
    <th style=\"white-space:nowrap; text-align: right;\" class=\"header\" scope=\"col\">$strtype</th>";
    echo "</tr>\n";
    if(is_array($loInstances)) {
	    foreach ($loInstances as $loInstance) {
	        $nameclass = '';
	        $textclass = '';
	        
	        if ($showquestiontext) {
	            $nameclass .= ' header';
	        }
	        if ($nameclass) {
	            $nameclass = 'class="' . $nameclass . '"';
	        }
	        if ($textclass) {
	            $textclass = 'class="' . $textclass . '"';
	        }
	
	        echo "<tr>\n<td style=\"white-space:nowrap;\" $nameclass>\n";
	
	        /*thêm thông tin category, course module cho loInstance*/
	        $loInstance->category = $loCategories[$loInstance->id];
	        $loInstance->cm = $loCM[$loInstance->id];
	        $canuseq = lo_has_capability_on($loInstance, 'use', $loInstance->category, $lotype);
	        if (function_exists('module_specific_actions')) {
	            echo module_specific_actions($pageurl, $loInstance->id, $cm->id, $canuseq);
	        }
	        
	        	
	        // preview
	        if ($canuseq) {
	            $quizorcourseid = $quizid?('&amp;quizid=' . $quizid):('&amp;courseid=' .$COURSE->id);
	            $url = $loViewUrl->out(false, array('id'=>$loInstance->cm));
	            link_to_popup_window($url, 'lopreview',
	                    "<img src=\"$CFG->pixpath/t/preview.gif\" class=\"iconsmall\" alt=\"$strpreview\" />",
	                    0, 0, $strpreview, LO_PREVIEW_POPUP_OPTIONS);
	        }
	        // edit LO
	        if (lo_has_capability_on($loInstance, 'edit', $loInstance->category, $lotype) 
	        	|| lo_has_capability_on($loInstance, 'move', $loInstance->category, $lotype)) {
	            echo "<a title='$stredit' href='" . $loEditUrl->out(false, array('update'=>$loInstance->cm)) . "'\><img
	                    src=' $CFG->pixpath/t/edit.gif' alt='$stredit' /></a>&nbsp;";
	        } 
	
	
	        //move LO to another category
	        if (lo_has_capability_on($loInstance, 'move', $loInstance->category, $lotype) 
	        	&& lo_has_capability_on($loInstance, 'view', $loInstance->category, $lotype)) {
	            echo "<a title=\"$strmove\" href=\"".$loEditUrl->out(false, array('id'=>$loInstance->id, 'movecontext'=>1))."\"><img
	                    src=\"$CFG->pixpath/t/move.gif\" alt=\"$strmove\" /></a>&nbsp;";
	        }
	
	        //delete LO
	        if (lo_has_capability_on($loInstance, 'edit', $loInstance->category, $lotype)) {
	            // hide-feature
	            if(isset($loInstance->hidden) && $loInstance->hidden) {
	                echo "<a title=\"$strrestore\" href=\"edit.php?".$pageurl->get_query_string()."&amp;unhide=$loInstance->id&amp;sesskey=$USER->sesskey\"><img
	                        src=\"$CFG->pixpath/t/restore.gif\" alt=\"$strrestore\" /></a>";
	            } else {
						echo "<a title=\"$strdelete\" href=\"edit.php?courseid=$COURSE->id&deleteselected=$loInstance->id&q$loInstance->id=1&lotype=$lotype\"><img
	                        src=\"$CFG->pixpath/t/delete.gif\" alt=\"$strdelete\" /></a>";
	            }
	        }
	        if ($caneditall || $canmoveall || $canuseall){
	            echo "&nbsp;<input title=\"$strselect\" type=\"checkbox\" name=\"q$loInstance->id\" value=\"1\" />";
	        }
	        echo "</td>\n";
	
	        echo "<td $nameclass>" . format_string($loInstance->name) . "</td>\n";
	        echo "<td $nameclass style='text-align: right'>\n";
	        print_lo_icon($loInstance);
	        echo "</td>\n";
	        echo "</tr>\n";
	        /*nếu lo là quiz và chọn $showquestiontext thì print thêm intro của quiz*/
	        if($showquestiontext){
	            echo '<tr><td colspan="3" ' . $textclass . '>';
	            $formatoptions = new stdClass;
	            $formatoptions->noclean = true;
	            $formatoptions->para = false;
	            if(in_array($lotype, $allowedQuizTypes)) {
	            	echo format_text($loInstance->intro, '',
	                    $formatoptions, $COURSE->id);
	            }
	            else if($lotype == 'lecture') {
	            	echo format_text($loInstance->alltext, '',
	                    $formatoptions, $COURSE->id);
	            }
	            echo "</td></tr>\n";
	        }
	    }
    }
    echo "</table>\n";

    $paging = print_paging_bar($totalnumber, $page, $perpage, $pageurl, 'qpage', false, true);
    if ($totalnumber > DEFAULT_LOS_PER_PAGE) {
        if ($perpage == DEFAULT_LOS_PER_PAGE) {
            $showall = '<a href="edit.php?'.$pageurl->get_query_string(array('qperpage'=>1000)).'">'.get_string('showall', 'moodle', $totalnumber).'</a>';
        } else {
            $showall = '<a href="edit.php?'.$pageurl->get_query_string(array('qperpage'=>DEFAULT_LOS_PER_PAGE)).'">'.get_string('showperpage', 'moodle', DEFAULT_LOS_PER_PAGE).'</a>';
        }
        if ($paging) {
            $paging = substr($paging, 0, strrpos($paging, '</div>'));
            $paging .= "<br />$showall</div>";
        } else {
            $paging = "<div class='paging'>$showall</div>";
        }
    }
    echo $paging;

    if ($caneditall || $canmoveall || $canuseall){
        echo '<a href="javascript:select_all_in(\'TABLE\',null,\'categoryquestions\');">'.$strselectall.'</a> /'.
         ' <a href="javascript:deselect_all_in(\'TABLE\',null,\'categoryquestions\');">'.$strselectnone.'</a>';
        echo '<br />';
        
        echo '<strong>&nbsp;'.get_string('withselected', 'quiz').':</strong><br />';

    	// print delete and move selected question        
        if ($caneditall) {
            echo '<input type="submit" name="deleteselected" value="'.$strdelete."\" /><br>";
        }
        
        /*print target categories*/
   	 	if ($canmoveall && count($addcontexts)) {
   	 		echo '<input type="submit" name="move" value="'.get_string('moveto', 'smartcom')."\" /> &nbsp;";
   	 		//echo '&nbsp;'.get_string('target_category', 'smartcom').':&nbsp; ';            
            lo_category_select_menu($addcontexts, false, 0, "$category->id,$category->contextid", '', -1, $lotype);
            echo '<br>';
        }
        
        /*print current course sections*/
    	if ($canadd) {
        	echo '<input type="hidden" name="target_info" id="target_info" value="0" />';        	
        	echo '<input type="submit" name="copylo" value="'.get_string('copyto', 'smartcom'). '" onclick="javascript:setSelectedCM()"/>&nbsp;';
        	//echo '&nbsp;'.get_string('current_course_section', 'smartcom').':';
        	echo course_content_structure_without_link($COURSE);
        	        	
        }
        

        if (function_exists('module_specific_controls') && $canuseall) {
            echo module_specific_controls($totalnumber, $recurse, $category, $cm->id);
        }
    }
    echo '</fieldset>';       
    echo '<br />';    
    echo "</form>\n";
    
    /*thêm add LO nếu có quyền add*/
    if($canadd) {
    	echo '&nbsp;<strong>'.get_string('createnew' .$lotype, 'smartcom').': </strong> <br>';
    	echo course_content_structure($COURSE, $lotype, "$categoryid,$contextid");   	
    }
    
       
    
}
//function lo_sort_options($pageurl, $sortorder){
//    global $USER;
//    //sort options
//    $html = "<div class=\"mdl-align\">";
//    $html .= '<form method="post" action="edit.php">';
//    $html .= '<fieldset class="invisiblefieldset" style="display: block;">';
//    $html .= '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
//    $html .= $pageurl->hidden_params_out(array('qsortorder'));
//    $sortoptions = array('alpha' => get_string("sortalpha", "smartcom"),
//                         'typealpha' => get_string("sorttypealpha", "smartcom"));
//                         
//    $html .=  choose_from_menu ($sortoptions, 'qsortorder', $sortorder, false, 'this.form.submit();', '0', true);
//    $html .=  '<noscript><div><input type="submit" value="'.get_string("sortsubmit", "smartcom").'" /></div></noscript>';
//    $html .= '</fieldset>';
//    $html .= "</form>\n";
//    $html .= "</div>\n";
//    return $html;
//}

function lo_showbank_actions($pageurl, $cm){
    global $CFG, $COURSE, $USER, $lotype;
    
    
    
    /// Now, check for commands on this page and modify variables as necessary
    if (optional_param('move', false, PARAM_BOOL) and confirm_sesskey()) { /// Move selected questions to new category
        $category = required_param('category', PARAM_SEQUENCE);
        list($tocategoryid, $contextid) = explode(',', $category);
        if (! $tocategory = get_record('question_categories', 'id', $tocategoryid, 'contextid', $contextid)) {
            error('Could not find category record');
        }
        $tocontext = get_context_instance_by_id($contextid);
        require_capability('moodle/question:add', $tocontext);
        $rawdata = (array) data_submitted();
        $loids = array();
        foreach ($rawdata as $key => $value) {    // Parse input for question ids
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                $loids[] = $key;
            }
        }
        if ($loids){
            $loidlist = join($loids, ',');
            $sql = "SELECT l.*, c.contextid FROM {$CFG->prefix}lo l, {$CFG->prefix}question_categories c WHERE l.instance IN ($loidlist) AND l.lotype='$lotype' AND c.id = l.category";
            if (!$los = get_records_sql($sql)){
                print_error('lodoesnotexist', 'smartcom', $pageurl->out());
            }
            //$checkforfiles = false;
            foreach ($los as $lo){
                //check capabilities
                lo_require_capability_on($lo, 'move');
                $fromcontext = get_context_instance_by_id($lo->contextid);
            }
            $returnurl = $pageurl->out(false, array('category'=>"$tocategoryid,$contextid"));           	
            /*nếu chuyển sang 1 category cùng course thì không phải move resource file trong LO*/
            if (!lo_move_los_to_category(implode(',', $loids), $tocategory->id, $lotype)) {
                print_error('errormovinglos', 'smartcom', $returnurl, $loids);
            }
                redirect($returnurl);

        }
    }
    
    if($lotype == 'lecture') {
    	$moduleName = 'resource';
    } else {
    	$moduleName = 'quiz';
    }
    $moduleid = get_field('modules', 'id', 'name', $moduleName );
    
    /*copy lo to course*/
	$copylo = optional_param('copylo', '', PARAM_TEXT);
    if(!empty($copylo)) {
    	$addinstancefunction = $moduleName."_add_instance";
    	$targetInfo = required_param('target_info');
    	$targetInfoParts = explode('#', $targetInfo);
    	$targetCategory = required_param('category', PARAM_SEQUENCE);
    	list($targetCategoryId, $contextid) = explode(',', $targetCategory);
    	$rawdata = (array) data_submitted();
        $loids = array();
        foreach ($rawdata as $key => $value) {    // Parse input for question ids
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                $loids[] = $key;
            }
        }
        if ($loids){
            $loidlist = join($loids, ',');
            $sql = "SELECT l.*, c.contextid FROM {$CFG->prefix}lo l, {$CFG->prefix}question_categories c WHERE l.instance IN ($loidlist) AND lotype='$lotype' AND c.id = l.category";
            if (!$los = get_records_sql($sql)){
                print_error('lodoesnotexist', 'smartcom', $pageurl->out());
            }
            //$checkforfiles = false;
            foreach ($los as $lo){
                //check capabilities
                lo_require_capability_on($lo, 'add', $lotype);
                $fromcontext = get_context_instance_by_id($lo->contextid);
                
                /*get back loInstance*/
                $loInstance = get_record($moduleName, 'id', $lo->instance, 'lotype', $lo->lotype);
                
                /*copy loInstance*/
                $loInstance->course = $COURSE->id; 
                $newInstanceId = $addinstancefunction($loInstance);
                
                if(!empty($newInstanceId)) {
	                /*create new course module for new instance */
	                $newModule = new object();
	                $newModule->course = $COURSE->id;
	                $newModule->module = $moduleid;
	                $newModule->instance = $newInstanceId;
	                $newModule->section =  $targetInfoParts[2]; //với course_module thì dùng section id
	                $newModule->indent = 1;
	                $newCMId = add_course_module($newModule);	               
                }              
                if(!empty($newCMId)) {
                	$newModule->coursemodule = $newCMId;  
                	$newModule->id = $newCMId;               
	            	/*move to the right section position*/
                	$newModule->section =  $targetInfoParts[3]; //với course_section thì dùng section index                
		            if($targetInfoParts[1] == 0) {
		                add_mod_to_section($newModule);
		            } else {
		            	$beforeMod = new object();
		            	$beforeMod->id = $targetInfoParts[1];
		               	add_mod_to_section($newModule, $beforeMod);	                	
		           }
		           /*create lo category info*/
		           add_lo($targetCategoryId, $newInstanceId, $lotype, $newCMId);
                }                               
            }
            $returnurl = $pageurl->out(false, array('category'=>"$targetCategoryId,$contextid"));
            redirect($returnurl);
        }
    }
          
    if (optional_param('deleteselected', false, PARAM_BOOL)) { // delete selected LOs from the category
    	
        if (($confirm = optional_param('confirm', '', PARAM_ALPHANUM)) and confirm_sesskey()) { // teacher has already confirmed the action
        	$deleteinstancefunction = $moduleName."_delete_instance";
            $deleteselected = required_param('deleteselected');
            if ($confirm == md5($deleteselected)) {
                if ($lolist = explode(',', $deleteselected)) {
                    
                    foreach ($lolist as $loid) {
                        lo_require_capability_on($loid, 'edit', $lotype);
                        //lấy về cm của lo
                        $lo = get_record('lo', 'instance', $loid, 'lotype', $lotype);
                        //lấy về $section của $cm ứng với lo
                        $cm = get_record('course_modules', 'id', $lo->cm);
                        // Xóa trong mdl_lo
                        delete_records('lo', 'instance', $loid, 'lotype', $lotype);
                        // Xóa trong table chứa instance
//		                if ($lo->cm and $cw = get_record("course_sections", "id", $cm->section)) {
//		                    $sectionreturn = $cw->section;
//		                }
						
		                if (! $deleteinstancefunction($loid)) {
		                    notify("Could not delete the $moduleName (instance)");
		                }
		                if (! delete_course_module($lo->cm)) {
		                    notify("Could not delete the $moduleName (coursemodule)");
		                }
		                if (! delete_mod_from_section($lo->cm, "$cm->section")) {
		                    notify("Could not delete the $moduleName from that section");		                    		               
                    	}
                    	add_to_log($COURSE->id, "course", "delete mod",
                           "view.php?id=$COURSE->id",
                           "$moduleName $lo->instance", $lo->cm);
                	}
                }     
                rebuild_course_cache($COURSE->id);                         
                redirect($pageurl->out(false, array('lotype'=>$lotype)));
            } else {
                error("Confirmation string was incorrect");
            }
        }
    }
    
    /*sau khi add lo, sẽ redirect về đây để thêm thông tin category cho lo*/
    $opt = optional_param("opt", '', PARAM_TEXT);
    if($opt == "addlosuccess") {
    	$newInstanceId = required_param("instance", PARAM_INT);
    	$cat = required_param('cat', PARAM_TEXT);
    	$catparts = explode(',', $cat);    	
    	$lotype = required_param('lotype', PARAM_TEXT);
    	$newCMId = required_param("cmid", PARAM_INT);
    	$beforeCMId = optional_param('beforecm', 0, PARAM_INT); 
     	/*after insert LO, insert into mdl_lo table*/                       
		//TODO: check permission to add question   		    	
		add_lo($catparts[0], $newInstanceId, $lotype, $newCMId);
		
		/*nếu xác định beoreCMId, move cm vào vị trí trước cm có id=beoreCMId*/
		if(!empty($beforeCMId)) {
			if(! $movingcm = get_record("course_modules", "id", $newCMId)) {
	           error("The moving course module doesn't exist");
	        }
	    	if (! $beforecm = get_record("course_modules", "id", $beforeCMId)) {
	           error("The destination course module doesn't exist");
	        }
	        if (! $section = get_record("course_sections", "id", $beforecm->section)) {
	           error("This section doesn't exist");
	        }
	        //require_login($section->course); // needed to setup proper $COURSE
	        $context = get_context_instance_by_id($catparts[1]);
	        require_capability('moodle/course:manageactivities', $context);	               
	
	        moveto_module($movingcm, $section, $beforecm);
	
	        unset($USER->activitycopy);
	        unset($USER->activitycopycourse);
	        unset($USER->activitycopyname);
	
	        rebuild_course_cache($section->course);
		}
		    
		    
		    //instance=$fromform->instance&cmid=$fromform->coursemodule&beforecm=$fromform->beforecm&opt=addlosuccess");
		    
		    
    }
    
}

/**
 
/**
 * Common setup for all pages for editing LOs.
 * @param string $edittab code for this edit tab
 * @param boolean $requirecmid require cmid? default false
 * @param boolean $requirecourseid require courseid, if cmid is not given? default true
 * @return array $thispageurl, $contexts, $cmid, $cm, $module, $pagevars
 */
function lo_edit_setup($edittab, $requirecmid = false, $requirecourseid = true){
    global $COURSE, $LO_EDITTABCAPS;

    //$thispageurl is used to construct urls for all lo edit pages we link to from this page. It contains an array
    //of parameters that are passed from page to page.
    $thispageurl = new moodle_url();
    /*lưu lotype vào pageurl*/   
    $lotype = $edittab;
    
    
    
    
    /*lưu section vào pageurl nếu có (khi gọi add LO từ ngoài)*/
    $section = optional_param('section', 0, PARAM_INT);
    if($section)
    	$thispageurl->params(compact('section'));
    
//    if ($requirecmid){
//        $cmid =required_param('cmid', PARAM_INT);
//    } else {
//        $cmid = optional_param('cmid', 0, PARAM_INT);
//    }
//    
//    
//    if ($cmid){
//        list($module, $cm) = get_module_from_cmid($cmid);
//        $courseid = $cm->course;
//        $thispageurl->params(compact('cmid'));
//        require_login($courseid, false, $cm);
//        $thiscontext = get_context_instance(CONTEXT_MODULE, $cmid);
//    } else {
        $module = null;
        $cm = null;
        if ($requirecourseid){
            $courseid  = required_param('courseid', PARAM_INT);
        } else {
            $courseid  = optional_param('courseid', 0, PARAM_INT);
        }
        if ($courseid){
            $thispageurl->params(compact('courseid'));
            require_login($courseid, false);
            $thiscontext = get_context_instance(CONTEXT_COURSE, $courseid);
        } else {
            $thiscontext = null;
        }
    //}

    if ($thiscontext){
        $contexts = new lo_edit_contexts($thiscontext);
        $contexts->require_one_edit_tab_cap($edittab);

    } else {
        $contexts = null;
    }



    $pagevars['qpage'] = optional_param('qpage', -1, PARAM_INT);

    //pass 'cat' from page to page and when 'category' comes from a drop down menu
    //then we also reset the qpage so we go to page 1 of
    //a new cat.
    $pagevars['cat'] = optional_param('cat', 0, PARAM_SEQUENCE);// if empty will be set up later
    if  ($category = optional_param('category', 0, PARAM_SEQUENCE)){
        if ($pagevars['cat'] != $category){ // is this a move to a new category?
            $pagevars['cat'] = $category;
            $pagevars['qpage'] = 0;
        }
    }
    if ($pagevars['cat']){
        $thispageurl->param('cat', $pagevars['cat']);
    }
    if ($pagevars['qpage'] > -1) {
        $thispageurl->param('qpage', $pagevars['qpage']);
    } else {
        $pagevars['qpage'] = 0;
    }

    $pagevars['qperpage'] = optional_param('qperpage', -1, PARAM_INT);
    if ($pagevars['qperpage'] > -1) {
        $thispageurl->param('qperpage', $pagevars['qperpage']);
    } else {
        $pagevars['qperpage'] = DEFAULT_LOS_PER_PAGE;
    }

//    $sortoptions = array(
//                          'typealpha' => 'lotype, name ASC',
//                          );

//    if ($sortorder = optional_param('qsortorder', '', PARAM_ALPHA)) {
//        $pagevars['qsortorderdecoded'] = $sortoptions[$sortorder];
//        $pagevars['qsortorder'] = $sortorder;
//        $thispageurl->param('qsortorder', $sortorder);
//    } else {
//        $pagevars['qsortorderdecoded'] = $sortoptions['typealpha'];
//        $pagevars['qsortorder'] = 'typealpha';
//    }

    $cmid = 0;
    $defaultcategory = lo_make_default_categories($contexts->all());

    $contextlistarr = array();
    foreach ($contexts->having_one_edit_tab_cap($edittab) as $context){
        $contextlistarr[] = "'$context->id'";
    }
    $contextlist = join($contextlistarr, ' ,');
    if (!empty($pagevars['cat'])){
        $catparts = explode(',', $pagevars['cat']);
        if (!$catparts[0] || (FALSE !== array_search($catparts[1], $contextlistarr)) || !count_records_select("question_categories", "id = '".$catparts[0]."' AND contextid = $catparts[1]")) {
            print_error('invalidcategory', 'quiz');
        }
    } else {
        $category = $defaultcategory;
        $pagevars['cat'] = "$category->id,$category->contextid";
    }

    if(($recurse = optional_param('recurse'.$lotype, -1, PARAM_BOOL)) != -1) {
        $pagevars['recurse'.$lotype] = $recurse;
        $thispageurl->param('recurse'.$lotype , $recurse);
    } else {
        $pagevars['recurse'.$lotype] = 1;
    }

    if(($showhidden = optional_param('showhidden', -1, PARAM_BOOL)) != -1) {
        $pagevars['showhidden'] = $showhidden;
        $thispageurl->param('showhidden', $showhidden);
    } else {
        $pagevars['showhidden'] = 0;
    }

    if(($showquestiontext = optional_param('showquestiontext'.$lotype, -1, PARAM_BOOL)) != -1) {
        $pagevars['showquestiontext'.$lotype] = $showquestiontext;
        $thispageurl->param('showquestiontext'. $lotype, $showquestiontext);
    } else {
        $pagevars['showquestiontext'.$lotype] = 0;
    }

    //category list page
    $pagevars['cpage'] = optional_param('cpage', 1, PARAM_INT);
    if ($pagevars['cpage'] < 1) {
        $pagevars['cpage'] = 1;
    }
    if ($pagevars['cpage'] != 1){
        $thispageurl->param('cpage', $pagevars['cpage']);
    }


    return array($thispageurl, $contexts, $cmid, $cm, $module, $pagevars);
}
class lo_edit_contexts{
    var $allcontexts;
    /**
     * @param current context
     */
    function lo_edit_contexts($thiscontext){
        $pcontextids = get_parent_contexts($thiscontext);
        $contexts = array($thiscontext);
        foreach ($pcontextids as $pcontextid){
            $contexts[] = get_context_instance_by_id($pcontextid);
        }
        $this->allcontexts = $contexts;
    }
    /**
     * @return array all parent contexts
     */
    function all(){
        return $this->allcontexts;
    }
    /**
     * @return object lowest context which must be either the module or course context
     */
    function lowest(){
        return $this->allcontexts[0];
    }
    /**
     * @param string $cap capability
     * @return array parent contexts having capability, zero based index
     */
    function having_cap($cap){
        $contextswithcap = array();
        foreach ($this->allcontexts as $context){
            if (has_capability($cap, $context)){
                $contextswithcap[] = $context;
            }
        }
        return $contextswithcap;
    }
    /**
     * @param array $caps capabilities
     * @return array parent contexts having at least one of $caps, zero based index
     */
    function having_one_cap($caps){
        $contextswithacap = array();
        foreach ($this->allcontexts as $context){
            foreach ($caps as $cap){
                if (has_capability($cap, $context)){
                    $contextswithacap[] = $context;
                    break; //done with caps loop
                }
            }
        }
        return $contextswithacap;
    }
    /**
     * @param string $tabname edit tab name
     * @return array parent contexts having at least one of $caps, zero based index
     */
    function having_one_edit_tab_cap($tabname){
        global $LO_EDITTABCAPS;
        return $this->having_one_cap($LO_EDITTABCAPS[$tabname]);
    }
    /**
     * Has at least one parent context got the cap $cap?
     *
     * @param string $cap capability
     * @return boolean
     */
    function have_cap($cap){
        return (count($this->having_cap($cap)));
    }

    /**
     * Has at least one parent context got one of the caps $caps?
     *
     * @param string $cap capability
     * @return boolean
     */
    function have_one_cap($caps){
        foreach ($caps as $cap){
            if ($this->have_cap($cap)){
                return true;
            }
        }
        return false;
    }
    /**
     * Has at least one parent context got one of the caps for actions on $tabname
     *
     * @param string $tabname edit tab name
     * @return boolean
     */
    function have_one_edit_tab_cap($tabname){
        global $LO_EDITTABCAPS;
        return $this->have_one_cap($LO_EDITTABCAPS[$tabname]);
    }
    /**
     * Throw error if at least one parent context hasn't got the cap $cap
     *
     * @param string $cap capability
     */
    function require_cap($cap){
        if (!$this->have_cap($cap)){
            print_error('nopermissions', '', '', $cap);
        }
    }
    /**
     * Throw error if at least one parent context hasn't got one of the caps $caps
     *
     * @param array $cap capabilities
     */
     function require_one_cap($caps){
        if (!$this->have_one_cap($caps)){
            $capsstring = join($caps, ', ');
            print_error('nopermissions', '', '', $capsstring);
        }
    }
    /**
     * Throw error if at least one parent context hasn't got one of the caps $caps
     *
     * @param string $tabname edit tab name
     */
     function require_one_edit_tab_cap($tabname){
        if (!$this->have_one_edit_tab_cap($tabname)){
            print_error('nopermissions', '', '', 'access question edit tab '.$tabname);
        }
    }
}

//capabilities for each page of edit tab.
//this determines which contexts' categories are available. At least one
//page is displayed if user has one of the capability on at least one context
$LO_EDITTABCAPS = array(
                            'editq' => array('moodle/question:add',
                                'moodle/question:editmine',
                                'moodle/question:editall',
                                'moodle/question:viewmine',
                                'moodle/question:viewall',
                                'moodle/question:usemine',
                                'moodle/question:useall',
                                'moodle/question:movemine',
                                'moodle/question:moveall'),
                            'lecture'=>array('moodle/question:add',
                                'moodle/question:editmine',
                                'moodle/question:editall',
                                'moodle/question:viewmine',
                                'moodle/question:viewall',
                                'moodle/question:movemine',
                                'moodle/question:moveall'),
							'exercise'=>array('moodle/question:add',
                                'moodle/question:editmine',
                                'moodle/question:editall',
                                'moodle/question:viewmine',
                                'moodle/question:viewall',
                                'moodle/question:movemine',
                                'moodle/question:moveall'),
							'practice'=>array('moodle/question:add',
                                'moodle/question:editmine',
                                'moodle/question:editall',
                                'moodle/question:viewmine',
                                'moodle/question:viewall',
                                'moodle/question:movemine',
                                'moodle/question:moveall'),
							'test'=>array('moodle/question:add',
                                'moodle/question:editmine',
                                'moodle/question:editall',
                                'moodle/question:viewmine',
                                'moodle/question:viewall',
                                'moodle/question:movemine',
                                'moodle/question:moveall'),
                           'categories'=>array('moodle/question:managecategory'));
                           



/**
 * Make sure user is logged in as required in this context.
 */
function require_login_in_context($contextorid = null){
    if (!is_object($contextorid)){
        $context = get_context_instance_by_id($contextorid);
    } else {
        $context = $contextorid;
    }
    if ($context && ($context->contextlevel == CONTEXT_COURSE)) {
        require_login($context->instanceid);
    } else if ($context && ($context->contextlevel == CONTEXT_MODULE)) {
        if ($cm = get_record('course_modules','id',$context->instanceid)) {
            if (!$course = get_record('course', 'id', $cm->course)) {
                error('Incorrect course.');
            }
            require_course_login($course, true, $cm);

        } else {
            error('Incorrect course module id.');
        }
    } else if ($context && ($context->contextlevel == CONTEXT_SYSTEM)) {
        if (!empty($CFG->forcelogin)) {
            require_login();
        }

    } else {
        require_login();
    }
}

/**
 * Shows the question bank editing interface.
 *
 * The function also processes a number of actions:
 *
 * Actions affecting the question pool:
 * move           Moves a question to a different category
 * deleteselected Deletes the selected questions from the category
 * Other actions:
 * category      Chooses the category
 * displayoptions Sets display options
 *
 * @author Martin Dougiamas and many others. This has recently been extensively
 *         rewritten by Gustav Delius and other members of the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * danhut modified
 * @param moodle_url $pageurl object representing this pages url.
 */
function lo_showbank($tabname, $contexts, $pageurl, $cm, $page, $perpage, $cat, $recurse, $showhidden, $showquestiontext){
    global $COURSE;

    
    if (optional_param('deleteselected', false, PARAM_BOOL)){ // teacher still has to confirm
        // make a list of all the questions that are selected
        $rawlos = $_REQUEST; // This code is called by both POST forms and GET links, so cannot use data_submitted.
        $lolist = '';  // comma separated list of ids of questions to be deleted
        $lonames = ''; // string with names of questions separated by <br /> with
                             // an asterix in front of those that are in use
        $inuse = false;      // set to true if at least one of the questions is in use
        $lotype = required_param('lotype', PARAM_TEXT);
        if($lotype == 'lecture') {
        	$tblName = 'resource';
        }
        else {
        	$tblName = 'quiz';
        }
        foreach ($rawlos as $key => $value) {    // Parse input for question ids
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                $lolist .= $key.',';
                lo_require_capability_on($key, 'edit', $lotype);                
                $lonames .= get_field($tblName, 'name', 'id', $key).'<br />';
            }
        }
        if (!$lolist) { // no questions were selected
            redirect($pageurl->out());
        }
        $lolist = rtrim($lolist, ',');

        // Add an explanation about questions in use
        if ($inuse) {
            $lonames .= '<br />'.get_string('losinuse', 'smartcom');
        }
        notice_yesno(get_string("delete" . $lotype . "scheck", "smartcom", $lonames),
                    $pageurl->out_action(array('deleteselected'=>$lolist, 'confirm'=>md5($lolist), 'lotype'=>$lotype)),
                    $pageurl->out_action());

        echo '</td></tr>';
        echo '</table>';
        print_footer($COURSE);
        exit;
    }


    // starts with category selection form
    print_box_start('generalbox questionbank');
    print_heading(get_string($tabname.'bank', 'smartcom'), '', 2);
    $lotype = optional_param('lotype', PARAM_TEXT);
    if(empty($lotype)) {
    	$lotype = 'lecture';
    }
    lo_category_form($contexts->having_one_edit_tab_cap($tabname), $pageurl, $cat, $recurse, $showhidden, $showquestiontext, $lotype);

    // continues with list of los
    lo_list($contexts->having_one_edit_tab_cap($tabname), $pageurl, $cat, isset($cm) ? $cm : null,
            $recurse, $page, $perpage, $showhidden, $showquestiontext,
            $contexts->having_cap('moodle/question:add'));

    print_box_end();
}

/**
 * @param integer $categoryid a category id.
 * @return boolean whether this is the only top-level category in a context.
 */
function lo_is_only_toplevel_category_in_context($categoryid) {
    global $CFG;
    return 1 == count_records_sql("
            SELECT count(*)
              FROM {$CFG->prefix}question_categories c1,
                   {$CFG->prefix}question_categories c2
             WHERE c2.id = $categoryid
               AND c1.contextid = c2.contextid
               AND c1.parent = 0 AND c2.parent = 0");
}


?>
