<?php

require_once($CFG->libdir.'/questionlib.php');

/**
 * The options used when popping up a question preview window in Javascript.
 */
define('LO_PREVIEW_POPUP_OPTIONS', 'scrollbars=yes,resizable=yes,width=700,height=540');

/**
 * Check capability on category
 * @param mixed $question object or id
 * @param string $cap 'add', 'edit', 'view', 'use', 'move'
 * @param integer $cachecat useful to cache all question records in a category
 * @return boolean this user has the capability $cap for this question $question?
 */
function lo_has_capability_on($lo, $cap, $cachecat = -1, $lotype){
    global $USER, $CFG;
    // nicolasconnault@gmail.com In some cases I get $lo === false. Since no such object exists, it can't be deleted, we can safely return true
    if ($lo === false) {
        return true;
    }

    // these are capabilities on existing questions capabilties are
    //set per category. Each of these has a mine and all version. Append 'mine' and 'all'
    $lo_questioncaps = array('edit', 'view', 'use', 'move');
    static $los = array();
    static $categories = array();
    static $cachedcat = array();
    if ($cachecat != -1 && (array_search($cachecat, $cachedcat)===FALSE)){
        $los += get_records('lo', 'category', $cachecat);
        $cachedcat[] = $cachecat;
    }
    if (!is_object($lo)){
        if (!isset($los[$lo])){
        	/*với lotype, select trong table tương ứng để lấy về lo thực sự*/
        	if($lotype == 'lecture') {
        		$tblName = 'resource';
        	} else {
        		$tblName = 'quiz';
        	}
        	
        	$lo_result = get_record_sql("select d.*, l.category from $CFG->prefix$tblName d, $CFG->prefix"."lo l WHERE d.id = l.instance AND l.lotype = '$lotype'");        		
        	if(!$lo_result) {
                print_error('lodoesnotexist', 'smartcom');
            }
            else {
            	$los[$lo] = $lo_result;
            }            
        }
        $lo = $los[$lo];
    }
    if (!isset($categories[$lo->category])){
        if (!$categories[$lo->category] = get_record('question_categories', 'id', $lo->category)){
            print_error('invalidcategory', 'quiz');
        }
    }
    $category = $categories[$lo->category];

    if (array_search($cap, $lo_questioncaps)!== FALSE){
        if (!has_capability('moodle/question:'.$cap.'all', get_context_instance_by_id($category->contextid))){
            if ($lo->createdby == $USER->id){
                return has_capability('moodle/question:'.$cap.'mine', get_context_instance_by_id($category->contextid));
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return has_capability('moodle/question:'.$cap, get_context_instance_by_id($category->contextid));
    }

}

/**
 * @param array $row tab objects
 * @param question_edit_contexts $contexts object representing contexts available from this context
 * @param string $querystring to append to urls
 * */
function lo_management_navigation_tabs(&$row, $contexts, $querystring) {
    global $CFG, $LO_EDITTABCAPS;
    $tabs = array(
            'lecture' =>array("$CFG->wwwroot/smartcom/lomanagement/edit.php?$querystring&lotype=lecture", get_string('lectures', 'smartcom'), get_string('editlecture', 'smartcom')),
		    'exercise' =>array("$CFG->wwwroot/smartcom/lomanagement/edit.php?$querystring&lotype=exercise", get_string('exercises', 'smartcom'), get_string('editexercise', 'smartcom')),
		    'practice' =>array("$CFG->wwwroot/smartcom/lomanagement/edit.php?$querystring&lotype=practice", get_string('practices', 'smartcom'), get_string('editpractice', 'smartcom')),
		    'test' =>array("$CFG->wwwroot/smartcom/lomanagement/edit.php?$querystring&lotype=test", get_string('tests', 'smartcom'), get_string('edittest', 'smartcom')),
            'categories' =>array("$CFG->wwwroot/smartcom/lomanagement/category.php?$querystring", get_string('categories', 'smartcom'), get_string('editqcats', 'quiz'))
            );
    foreach ($tabs as $tabname => $tabparams){
        if ($contexts->have_one_edit_tab_cap($tabname)) {
            $row[] = new tabobject($tabname, $tabparams[0], $tabparams[1], $tabparams[2]);
        }
    }
}

/**
* Gets the default category in the most specific context.
* If no categories exist yet then default ones are created in all contexts.
*
* @param array $contexts  The context objects for this context and all parent contexts.
* @return object The default category - the category in the course context
*/
function lo_make_default_categories($contexts) {
    $toreturn = null;
    // If it already exists, just return it.
    foreach ($contexts as $key => $context) {
        if (!$categoryrs = get_recordset_select("question_categories", "contextid = '{$context->id}'", 'sortorder, name', '*', '', 1)) {
            error('error getting category record');
        } else {
            if (!$category = rs_fetch_record($categoryrs)){
                // Otherwise, we need to make one
                $category = new stdClass;
                $contextname = print_context_name($context, false, true);
                $category->name = addslashes(get_string('defaultfor', 'question', $contextname));
                $category->info = addslashes(get_string('defaultinfofor', 'question', $contextname));
                $category->contextid = $context->id;
                $category->parent = 0;
                $category->sortorder = 999; // By default, all categories get this number, and are sorted alphabetically.
                $category->stamp = make_unique_id_code();
                if (!$category->id = insert_record('question_categories', $category)) {
                    error('Error creating a default category for context '.print_context_name($context));
                }
            }
        }
        if ($context->contextlevel == CONTEXT_COURSE){
            $toreturn = clone($category);
        }
    }


    return $toreturn;
}

/**
 * Output an array of lo categories.
 */
function lo_category_options($contexts, $top = false, $currentcat = 0, $popupform = false, $nochildrenof = -1, $lotype) {
    global $CFG;
    $pcontexts = array();
    foreach($contexts as $context){
        $pcontexts[] = $context->id;
    }
    $contextslist = join($pcontexts, ', ');

    $categories = get_lo_categories_for_contexts($contextslist, 'name ASC', $lotype);

    $categories = question_add_context_in_key($categories);

    if ($top){
        $categories = question_add_tops($categories, $pcontexts);
    }
    $categories = add_indented_names($categories, $nochildrenof);

    //sort cats out into different contexts
    $categoriesarray = array();
    foreach ($pcontexts as $pcontext){
        $contextstring = print_context_name(get_context_instance_by_id($pcontext), true, true);
        foreach ($categories as $category) {
            if ($category->contextid == $pcontext){
                $cid = $category->id;
                if ($currentcat!= $cid || $currentcat==0) {
                    $countstring = (!empty($category->locount))?" ($category->locount)":'';
                    $categoriesarray[$contextstring][$cid] = $category->indentedname.$countstring;
                }
            }
        }
    }
    if ($popupform){
        $popupcats = array();
        foreach ($categoriesarray as $contextstring => $optgroup){
            $popupcats[] = '--'.$contextstring;
            $popupcats = array_merge($popupcats, $optgroup);
            $popupcats[] = '--';
        }
        return $popupcats;
    } else {
        return $categoriesarray;
    }
}

/**
 * danhut added
 *
 * @return array an array of question type names translated to the user's language.
 */
function lo_type_menu() {
    
    static $menu_options = null;
    
    $menu_options = array('lecure' => 'lecture', 'exercise' => 'exercise', 'practice' => 'practice' );
   
    return $menu_options;
}

/**
* Print the icon for the question type
*
* @param object $lo  The lo object for which the icon is required
* @param boolean $return   If true the functions returns the link as a string
*/
function print_lo_icon($lo, $return = false) {
    global $QTYPES, $CFG;

//    if (array_key_exists($question->qtype, $QTYPES)) {
//        $namestr = $QTYPES[$question->qtype]->menu_name();
//    } else {
//        $namestr = 'missingtype';
//    }
	$namestr = $lo->smarttype;
//    $html = '<img src="' . $CFG->wwwroot . '/question/type/' .
//            $lo->qtype . '/icon.gif" alt="' .
//            $namestr . '" title="' . $namestr . '" />';
	$html = $namestr;
    if ($return) {
        return $html;
    } else {
        echo $html;
    }
}

function add_lo($cat, $instance, $lotype, $cm) {
 	$lo = new object();
 	$lo->category = $cat;
 	$lo->instance = $instance;
 	$lo->lotype = $lotype;
 	$lo->cm = $cm;
        
    if (!insert_record("lo", $lo)) {
        //error("Could not insert the new lo '$lo'");
        return false;
    } else {
        return true;
    }	
}

function delete_lo($instance, $lotype) {
	return delete_records("lo", "instance", $instance, "lotype", $lotype);
}

/**
 * Require capability on question.
 */
function lo_require_capability_on($lo, $cap, $lotype){
    if (!lo_has_capability_on($lo, $cap, -1, $lotype)){
        print_error('nopermissions', '', '', $cap);
    }
    return true;
}

/**
 * This function should be considered private to the question bank, it is called from
 * question/editlib.php question/contextmoveq.php and a few similar places to to the work of
 * acutally moving questions and associated data. However, callers of this function also have to
 * do other work, which is why you should not call this method directly from outside the questionbank.
 *
 * @param string $questionids a comma-separated list of question ids.
 * @param integer $newcategory the id of the category to move to.
 */
function lo_move_los_to_category($loids, $newcategory, $lotype) {
    $result = true;

    // Move the questions themselves.
    $result = $result && set_field_select('lo', 'category', $newcategory, "instance IN ($loids) AND lotype='$lotype'");

    // Move any subquestions belonging to them.
    //$result = $result && set_field_select('lo', 'category', $newcategory, "parent IN ($loids)");


    return $result;
}

function lo_file_links_base_url($courseid){
    global $CFG;
    $baseurl = preg_quote("$CFG->wwwroot/file.php", '!');
    $baseurl .= '('.preg_quote('?file=', '!').')?';//may or may not
                                     //be using slasharguments, accept either
    $baseurl .= "/$courseid/";//course directory
    return $baseurl;
}

/*
 * Find all course / site files linked to in a piece of html.
 * @param string html the html to search
 * @param int course search for files for courseid course or set to siteid for
 *              finding site files.
 * @return array files with keys being files.
 */
function lo_find_file_links_from_html($html, $courseid){
    global $CFG;
    $baseurl = lo_file_links_base_url($courseid);
    $searchfor = '!'.
                   '(<\s*(a|img)\s[^>]*(href|src)\s*=\s*")'.$baseurl.'([^"]*)"'.
                   '|'.
                   '(<\s*(a|img)\s[^>]*(href|src)\s*=\s*\')'.$baseurl.'([^\']*)\''.
                  '!i';
    $matches = array();
    $no = preg_match_all($searchfor, $html, $matches);
    if ($no){
        $rawurls = array_filter(array_merge($matches[5], $matches[10]));//array_filter removes empty elements
        //remove any links that point somewhere they shouldn't
        foreach (array_keys($rawurls) as $rawurlkey){
            if (!$cleanedurl = lo_url_check($rawurls[$rawurlkey])){
                unset($rawurls[$rawurlkey]);
            } else {
                $rawurls[$rawurlkey] = $cleanedurl;
            }

        }
        $urls = array_flip($rawurls);// array_flip removes duplicate files
                                            // and when we merge arrays will continue to automatically remove duplicates
    } else {
        $urls = array();
    }
    return $urls;
}
/*
 * Check that url doesn't point anywhere it shouldn't
 *
 * @param $url string relative url within course files directory
 * @return mixed boolean false if not OK or cleaned URL as string if OK
 */
function lo_url_check($url){
    global $CFG;
    if ((substr(strtolower($url), 0, strlen($CFG->moddata)) == strtolower($CFG->moddata)) ||
            (substr(strtolower($url), 0, 10) == 'backupdata')){
        return false;
    } else {
        return clean_param($url, PARAM_PATH);
    }
}

/*
 * Find all course / site files linked to in a piece of html.
 * @param string html the html to search
 * @param int course search for files for courseid course or set to siteid for
 *              finding site files.
 * @return array files with keys being files.
 */
function lo_replace_file_links_in_html($html, $fromcourseid, $tocourseid, $url, $destination, &$changed){
    global $CFG;
    require_once($CFG->libdir .'/filelib.php');
    $tourl = get_file_url("$tocourseid/$destination");
    $fromurl = question_file_links_base_url($fromcourseid).preg_quote($url, '!');
    $searchfor = array('!(<\s*(a|img)\s[^>]*(href|src)\s*=\s*")'.$fromurl.'(")!i',
                   '!(<\s*(a|img)\s[^>]*(href|src)\s*=\s*\')'.$fromurl.'(\')!i');
    $newhtml = preg_replace($searchfor, '\\1'.$tourl.'\\5', $html);
    if ($newhtml != $html){
        $changed = true;
    }
    return $newhtml;
}

function find_file_links($lo, $courseid){
        $urls = array();
        // find links in the answers table.
        if($lo->lotype == 'lecture') {
        	$urls +=  lo_find_file_links_from_html($lo->alltext, $courseid);
        }
        else {
        	$urls +=  lo_find_file_links_from_html($lo->intro, $courseid);	
        }
                
        
        //set all the values of the array to the question id
        if ($urls){
            $urls = array_combine(array_keys($urls), array_fill(0, count($urls), array($lo->id)));
        }
        $urls = array_merge_recursive($urls);
        return $urls;
    }
    


    function replace_file_links($lo, $fromcourseid, $tocourseid, $url, $destination){
        parent_replace_file_links($lo, $fromcourseid, $tocourseid, $url, $destination);
        // replace links in the question_match_sub table.
        // We need to use a separate object, because in load_question_options, $lo->options->answers
        // is changed from a comma-separated list of ids to an array, so calling update_record on
        // $lo->options stores 'Array' in that column, breaking the question.
        $optionschanged = false;
        $newoptions = new stdClass;
        $newoptions->id = $lo->options->id;
        $newoptions->correctfeedback = question_replace_file_links_in_html($lo->options->correctfeedback, $fromcourseid, $tocourseid, $url, $destination, $optionschanged);
        $newoptions->partiallycorrectfeedback  = question_replace_file_links_in_html($lo->options->partiallycorrectfeedback, $fromcourseid, $tocourseid, $url, $destination, $optionschanged);
        $newoptions->incorrectfeedback = question_replace_file_links_in_html($lo->options->incorrectfeedback, $fromcourseid, $tocourseid, $url, $destination, $optionschanged);
        if ($optionschanged){
            if (!update_record('question_multichoice', addslashes_recursive($newoptions))) {
                error('Couldn\'t update \'question_multichoice\' record '.$newoptions->id);
            }
        }
        $answerchanged = false;
        foreach ($lo->options->answers as $answer) {
            $answer->answer = question_replace_file_links_in_html($answer->answer, $fromcourseid, $tocourseid, $url, $destination, $answerchanged);
            if ($answerchanged){
                if (!update_record('question_answers', addslashes_recursive($answer))){
                    error('Couldn\'t update \'question_answers\' record '.$answer->id);
                }
            }
        }
    }
    
    /*
     * Find all course / site files linked from a question.
     *
     * Need to check for links to files in question_answers.answer and feedback
     * and in question table in generalfeedback and questiontext fields. Methods
     * on child classes will also check extra question specific fields.
     *
     * Needs to be overriden for child classes that have extra fields containing
     * html.
     *
     * @param string html the html to search
     * @param int course search for files for courseid course or set to siteid for
     *              finding site files.
     * @return array of files, file name is key and array with one item = question id as value
     */
    function parent_replace_file_links($lo, $fromcourseid, $tocourseid, $url, $destination){
        global $CFG;
        $updateqrec = false;

    /// Question image
        if (!empty($lo->image)){
            //support for older questions where we have a complete url in image field
            if (substr(strtolower($lo->image), 0, 7) == 'http://') {
                $loimage = preg_replace('!^'.question_file_links_base_url($fromcourseid).preg_quote($url, '!').'$!i', $destination, $lo->image, 1);
            } else {
                $loimage = preg_replace('!^'.preg_quote($url, '!').'$!i', $destination, $lo->image, 1);
            }
            if ($loimage != $lo->image){
                $lo->image = $loimage;
                $updateqrec = true;
            }
        }

    /// Questiontext and general feedback.
        $lo->questiontext = question_replace_file_links_in_html($lo->questiontext, $fromcourseid, $tocourseid, $url, $destination, $updateqrec);
        $lo->generalfeedback = question_replace_file_links_in_html($lo->generalfeedback, $fromcourseid, $tocourseid, $url, $destination, $updateqrec);

    /// If anything has changed, update it in the database.
        if ($updateqrec){
            if (!update_record('question', addslashes_recursive($lo))){
                error ('Couldn\'t update question '.$lo->name);
            }
        }


    /// Answers, if this question uses them.
        if (isset($lo->options->answers)){
            //answers that do not need updating have been unset
            foreach ($lo->options->answers as $answer){
                $answerchanged = false;
            /// URLs in the answers themselves, if appropriate.
                if ($this->has_html_answers()) {
                    $answer->answer = question_replace_file_links_in_html($answer->answer, $fromcourseid, $tocourseid, $url, $destination, $answerchanged);
                }
            /// URLs in the answer feedback.
                $answer->feedback = question_replace_file_links_in_html($answer->feedback, $fromcourseid, $tocourseid, $url, $destination, $answerchanged);
            /// If anything has changed, update it in the database.
                if ($answerchanged){
                    if (!update_record('question_answers', addslashes_recursive($answer))){
                        error ('Couldn\'t update question ('.$lo->name.') answer '.$answer->id);
                    }
                }
            }
        }
    }


/**
 * Given a course and a (current) coursemodule
 * This function returns a small popup menu with all the
 * course activity modules in it, as a navigation menu
 * The data is taken from the serialised array stored in
 * the course record
 *
 * @param course $course A {@link $COURSE} object.
 * @param course $cm A {@link $COURSE} object.
 * @param string $targetwindow ?
 * @return string
 * @todo Finish documenting this function
 */
function course_content_structure($course, $lotype, $category, $cm=NULL, $targetwindow='self'){

	global $CFG, $THEME, $USER;

	if (empty($THEME->navmenuwidth)) {
		$width = 50;
	} else {
		$width = $THEME->navmenuwidth;
	}

	

	if ($course->format == 'weeks') {
		$strsection = get_string('week');
	} else {
		$strsection = get_string('topic');
	}
	$strjumpto = get_string('createnew' . $lotype . 'in' , 'smartcom');

	$modinfo = get_fast_modinfo($course);
	$context = get_context_instance(CONTEXT_COURSE, $course->id);

	$section = -1;
	$selected = '';
	$url = '';
	$previousmod = NULL;
	$backmod = NULL;
	$nextmod = NULL;
	$selectmod = NULL;
	$logslink = NULL;
	$flag = false;
	$menu = array();
	$menustyle = array();

	$sections = get_records('course_sections','course',$course->id,'section','section,visible,summary');

	if (!empty($THEME->makenavmenulist)) {   /// A hack to produce an XHTML navmenu list for use in themes
		$THEME->navmenulist = navmenulist($course, $sections, $modinfo, $strsection, $strjumpto, $width, $cm);
	}

	$activityArr = array();
	foreach ($modinfo->cms as $mod) {
		if ($mod->modname != 'label' || (isset($mod->indent) && $mod->indent > 0)) {
			continue;
		}

		if ($mod->sectionnum > $course->numsections) {   /// Don't show excess hidden sections
			break;
		}

		if (!$mod->uservisible) { // do not icnlude empty sections at all
			continue;
		}
	
		if (!empty($previousmod)) { // lưu current mod thành next mod của previous mod
			$previousmod->next = $mod;
		} 
		$localname = $mod->name;
		if ($cm == $mod->id) {
			$selected = $url;
			$selectmod = $mod;			
			$localname = $strjumpto;
			$strjumpto = '';
			
		} else {
			$localname = strip_tags(format_string($localname,true));
			$tl=textlib_get_instance();
			if ($tl->strlen($localname) > ($width+5)) {
				$localname = $tl->substr($localname, 0, $width).'...';
			}
			if (!$mod->visible) {
				$localname = '('.$localname.')';
			}
		}
		$mod->name = $localname;
		if (empty($THEME->navmenuiconshide)) {
			$menustyle[$url] = 'style="background-image: url('.$CFG->modpixpath.'/'.$mod->modname.'/icon.gif);"';  // Unfortunately necessary to do this here
		}
		$previousmod = $mod;
		$activityArr[] = $mod;
			
	}
	//Accessibility: added Alt text, replaced &gt; &lt; with 'silent' character and 'accesshide' text.

	foreach($activityArr as $mod) {
		if ($mod->sectionnum > 0 and $section != $mod->sectionnum) {
			$thissection = $sections[$mod->sectionnum];

			if ($thissection->visible or !$course->hiddensections or
			has_capability('moodle/course:viewhiddensections', $context)) {
				$thissection->summary = strip_tags(format_string($thissection->summary,true));
				if ($course->format == 'weeks' or empty($thissection->summary)) {
					$menu[] = '--'.$strsection ." ". $mod->sectionnum;
				} else {
					if (strlen($thissection->summary) < ($width-3)) {
						$menu[] = '--'.$thissection->summary;
					} else {
						$menu[] = '--'.substr($thissection->summary, 0, $width).'...';
					}
				}
				$section = $mod->sectionnum;
			} else {
				// no activities from this hidden section shown
				continue;
			}
		}
		if(!empty($mod->next)) {
			$beforecm = "&beforecm=". $mod->next->id;
		}
		else {
			$beforecm = "";
		}
		$url = "edit.php?addlo=" . get_string("addnew$lotype", "smartcom"). "&courseid=$course->id&lotype=$lotype&cat=$category&section=$section$beforecm&cm=$mod->id";
		$menu[$url] = $mod->name;
	}

	
	return '<div class="navigation">'."\n".popup_form($CFG->wwwroot .'/smartcom/lomanagement/', $menu, 'navmenupopup', $selected, $strjumpto,
                       '', '', true, $targetwindow)."\n".'</div>';
	
}

function course_content_structure_without_link($course){

	global $CFG, $THEME, $USER, $lotype;

	if (empty($THEME->navmenuwidth)) {
		$width = 50;
	} else {
		$width = $THEME->navmenuwidth;
	}

	

	if ($course->format == 'weeks') {
		$strsection = get_string('week');
	} else {
		$strsection = get_string('topic');
	}
	$strjumpto = get_string('createnew' . $lotype . 'in' , 'smartcom');

	$modinfo = get_fast_modinfo($course);
	$context = get_context_instance(CONTEXT_COURSE, $course->id);

	$section = -1;
	$selected = '';
	$previousmod = NULL;
	$backmod = NULL;
	$nextmod = NULL;
	$selectmod = NULL;
	$logslink = NULL;
	$flag = false;
	$menu = array();
	$menustyle = array();

	$sections = get_records('course_sections','course',$course->id,'section','section,visible,summary,id');

	if (!empty($THEME->makenavmenulist)) {   /// A hack to produce an XHTML navmenu list for use in themes
		$THEME->navmenulist = navmenulist($course, $sections, $modinfo, $strsection, $strjumpto, $width, $cm);
	}

	$activityArr = array();
	foreach ($modinfo->cms as $mod) {
		if ($mod->modname != 'label' || (isset($mod->indent) && $mod->indent > 0)) {
			continue;
		}

		if ($mod->sectionnum > $course->numsections) {   /// Don't show excess hidden sections
			break;
		}

		if (!$mod->uservisible) { // do not icnlude empty sections at all
			continue;
		}
		
		if (!empty($previousmod)) { // lưu current mod thành next mod của previous mod
			$previousmod->next = $mod;
		} 
		
		
		$localname = strip_tags(format_string($mod->name,true));
		$tl=textlib_get_instance();
		if ($tl->strlen($localname) > ($width+5)) {
			$localname = $tl->substr($localname, 0, $width).'...';
		}
		if (!$mod->visible) {
			$localname = '('.$localname.')';
		}
		
		$mod->name = $localname;
		
		$previousmod = $mod;
		$activityArr[] = $mod;
			
	}
	//Accessibility: added Alt text, replaced &gt; &lt; with 'silent' character and 'accesshide' text.

	foreach($activityArr as $mod) {
		if ($mod->sectionnum > 0 and $section != $mod->sectionnum) {
			$thissection = $sections[$mod->sectionnum];

			if ($thissection->visible or !$course->hiddensections or
			has_capability('moodle/course:viewhiddensections', $context)) {
				$thissection->summary = strip_tags(format_string($thissection->summary,true));
				if ($course->format == 'weeks' or empty($thissection->summary)) {
					$gLabel = '--'.$strsection ." ". $mod->sectionnum;
				} else {
					if (strlen($thissection->summary) < ($width-3)) {
						$gLabel = '--'.$thissection->summary;
					} else {
						$gLabel = '--'.substr($thissection->summary, 0, $width).'...';
					}
				}
				$menu[$gLabel] = array();
				$section = $mod->sectionnum;
			} else {
				// no activities from this hidden section shown
				continue;
			}
		}
		if(!empty($mod->next)) {
			$beforecm = $mod->next->id;
		}
		else {
			$beforecm = "0";
		}
		$key = $mod->id . "#" . $beforecm . "#" . $thissection->id . "#" . $mod->sectionnum;
		$menu[$gLabel][$key] = $mod->name;
	}
	
	$returnStr = 
		"<script>
			function setSelectedCM() {			  
      			var dropdown = document.getElementById('menutarget_course_module');   
      			var index = dropdown.selectedIndex;   
      			var value = dropdown[index].value;
      			var beforecm = document.getElementById('target_info');
      			beforecm.value = value;
			}	
		</script>		
		";
	
	$returnStr .= '<div class="select_target_cm">'."\n".choose_from_menu_nested($menu, 'target_course_module', '', '', 'javascript:setSelectedCM()' )."\n".'</div>'; 
	
	return $returnStr;
	
}

/**
 * Get all the category objects, including a count of the number of questions in that category,
 * for all the categories in the lists $contexts.
 *
 * @param mixed $contexts either a single contextid, or a comma-separated list of context ids.
 * @param string $sortorder used as the ORDER BY clause in the select statement.
 * @return array of category objects.
 */
function get_lo_categories_for_contexts($contexts, $sortorder = 'parent, sortorder, name ASC', $lotype) {
    global $CFG;
    return get_records_sql("
            SELECT c.*, (SELECT count(1) FROM {$CFG->prefix}lo l
                    WHERE c.id = l.category AND l.lotype='$lotype') as locount
            FROM {$CFG->prefix}question_categories c
            WHERE c.contextid IN ($contexts)
            ORDER BY $sortorder");
}

/**
 * Check whether this user is allowed to delete this category.
 *
 * @param integer $todelete a category id.
 */
function lo_can_delete_cat($todelete) {
    if (lo_is_only_toplevel_category_in_context($todelete)) {
        error('You can\'t delete that category it is the default category for this context.');
    } else {
        $contextid = get_field('question_categories', 'contextid', 'id', $todelete);
        require_capability('moodle/question:managecategory', get_context_instance_by_id($contextid));
    }
}


?>